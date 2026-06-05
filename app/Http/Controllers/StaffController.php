<?php

namespace App\Http\Controllers;

use App\Helpers\CleanGoHelper as CG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StaffController extends Controller
{
    private function id()   { return Session::get('user_id'); }
    private function nama() { return Session::get('nama'); }

    private function allActiveOrders()
    {
        return DB::table('orders as o')
            ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
            ->join('users as u', 'u.id_cust', '=', 'o.id_cust')
            ->leftJoin('order_detail as od', 'od.id_order', '=', 'o.id_order')
            ->leftJoin('katalog as k', 'k.id_katalog', '=', 'od.id_katalog')
            ->leftJoin('pembayaran as p', 'p.id_order', '=', 'o.id_order')
            ->select('o.*', 'l.nama_layanan', 'u.nama_cust', 'u.notelp_cust',
                     'od.id_katalog', 'od.berat', 'od.qty', 'od.harga_satuan as harga_od',
                     'k.varian', 'k.satuan',
                     'p.id_bayar', 'p.jumlah as jumlah_bayar', 'p.status_bayar')
            ->whereNotIn('o.status_order', ['Selesai','Dibatalkan'])
            ->orderBy('o.tanggal_pesan')
            ->get();
    }

    private function kelolaCount()
    {
        $orders = $this->allActiveOrders();
        return $orders->filter(fn($o) =>
            ($o->status_order === 'Dijemput' && (!$o->total_harga || $o->total_harga == 0))
            || ($o->status_bayar === 'Lunas' && in_array($o->status_order, ['Dijemput', 'Dicuci', 'Disetrika', 'Dikirim']))
        )->count();
    }

    public function dashboard()
    {
        $id     = $this->id();
        $orders = $this->allActiveOrders();
        $masuk      = $orders->filter(fn($o) => $o->status_order === 'Menunggu Konfirmasi');
        $diproses   = $orders->filter(fn($o) => in_array($o->status_order, ['Dijemput','Dicuci','Disetrika','Dikirim']));
        $needWeight = $orders->filter(fn($o) => $o->status_order === 'Dijemput' && (!$o->total_harga || $o->total_harga == 0));
        $paid       = $orders->filter(fn($o) => $o->status_bayar === 'Lunas' && in_array($o->status_order, ['Dijemput','Dicuci','Disetrika','Dikirim']));
        $konfBayar  = DB::table('pembayaran as p')
            ->join('orders as o', 'o.id_order', '=', 'p.id_order')
            ->join('users as u', 'u.id_cust', '=', 'o.id_cust')
            ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
            ->select('p.*', 'o.kode_order', 'u.nama_cust', 'l.nama_layanan')
            ->where('p.status_bayar', 'Menunggu Konfirmasi')->get();
        $selesai = DB::table('orders as o')
            ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
            ->join('users as u', 'u.id_cust', '=', 'o.id_cust')
            ->select('o.*', 'l.nama_layanan', 'u.nama_cust')
            ->where('o.status_order', 'Selesai')->where('o.id_staff', $id)
            ->orderByDesc('o.updated_at')->limit(20)->get();

        return view('staff.index', compact(
            'masuk','diproses','needWeight','paid','konfBayar','selesai'
        ) + [
            'page'        => 'dashboard',
            'staffName'   => $this->nama(),
            'unreadCount' => CG::countUnread('staff', $id),
            'kelolaCount' => $this->kelolaCount(),
        ]);
    }

    public function orderMasuk()
    {
        $id    = $this->id();
        $masuk = $this->allActiveOrders()->filter(fn($o) => $o->status_order === 'Menunggu Konfirmasi');
        return view('staff.index', [
            'page'        => 'order_masuk',
            'staffName'   => $this->nama(),
            'masuk'       => $masuk,
            'unreadCount' => CG::countUnread('staff', $id),
            'kelolaCount' => $this->kelolaCount(),
        ]);
    }

    public function ambilOrder(Request $request)
    {
        // ✅ REVISI: Validasi server-side
        $request->validate([
            'id_order' => ['required', 'integer', 'exists:orders,id_order'],
        ], [
            'id_order.required' => 'ID order tidak valid!',
            'id_order.exists'   => 'Order tidak ditemukan!',
        ]);

        $id      = $this->id();
        $idOrder = (int)$request->input('id_order');

        DB::transaction(function () use ($idOrder, $id) {
            DB::table('orders')->where('id_order', $idOrder)
                ->update(['id_staff' => $id, 'status_order' => 'Dijemput', 'updated_at' => now()]);
            DB::table('tracking')->insert([
                'id_order'    => $idOrder,
                'status'      => 'Dijemput',
                'keterangan'  => 'Staff menjemput laundry',
                'updated_by'  => $id,
                'waktu_update'=> now(),
            ]);
        });

        $oi = DB::table('orders as o')->join('users as u','u.id_cust','=','o.id_cust')
            ->select('o.kode_order','u.id_cust','u.nama_cust')->where('o.id_order',$idOrder)->first();
        if ($oi) {
            CG::sendNotification('customer',$oi->id_cust,'🚗 Laundry Sedang Dijemput!',
                "Order {$oi->kode_order} sedang dijemput oleh staff kami.",route('customer.tracking'));
            CG::notifyAllOwner("🚗 Order Dijemput: {$oi->kode_order}",
                "Staff {$this->nama()} menjemput laundry {$oi->kode_order} dari {$oi->nama_cust}.",route('owner.semua_order'));
        }

        return redirect()->route('staff.order_masuk')->with('flash', 'Order berhasil diambil → <strong>Dijemput</strong>.');
    }

    public function kelolaOrder(Request $request)
    {
        $id      = $this->id();
        $orders  = $this->allActiveOrders();
        $needWeight = $orders->filter(fn($o) => $o->status_order === 'Dijemput' && (!$o->total_harga || $o->total_harga == 0));
        $paid       = $orders->filter(fn($o) => $o->status_bayar === 'Lunas' && in_array($o->status_order,['Dijemput','Dicuci','Disetrika','Dikirim']));
        $waiting    = $orders->filter(fn($o) =>
            !$needWeight->pluck('id_order')->contains($o->id_order) &&
            !$paid->pluck('id_order')->contains($o->id_order) &&
            in_array($o->status_order,['Dijemput','Dicuci','Disetrika','Dikirim'])
        );

        $selId    = (int)$request->query('id', 0);
        $selOrder = null;
        if ($selId) {
            $selOrder = DB::table('orders as o')
                ->join('layanan as l','l.id_layanan','=','o.id_layanan')
                ->join('users as u','u.id_cust','=','o.id_cust')
                ->leftJoin('order_detail as od','od.id_order','=','o.id_order')
                ->leftJoin('katalog as k','k.id_katalog','=','od.id_katalog')
                ->leftJoin('pembayaran as p','p.id_order','=','o.id_order')
                ->select('o.*','l.nama_layanan','u.nama_cust','u.notelp_cust','u.alamat_cust',
                         'od.id_katalog','od.berat','od.qty','od.harga_satuan as harga_od',
                         'k.varian','k.satuan','k.harga as harga_katalog','p.status_bayar')
                ->where('o.id_order', $selId)->first();
        }

        return view('staff.index', [
            'page'       => 'kelola_order',
            'staffName'  => $this->nama(),
            'needWeight' => $needWeight,
            'paid'       => $paid,
            'waiting'    => $waiting,
            'selOrder'   => $selOrder,
            'katalogAll' => DB::table('katalog as k')->join('layanan as l','l.id_layanan','=','k.id_layanan')
                ->where('k.status','Aktif')->select('k.*','l.nama_layanan')->get(),
            'unreadCount'=> CG::countUnread('staff', $id),
            'kelolaCount' => $needWeight->count() + $paid->count(),
        ]);
    }

    public function setBerat(Request $request)
    {
        // ✅ REVISI: Validasi server-side
        $request->validate([
            'id_order'    => ['required', 'integer', 'exists:orders,id_order'],
            'id_katalog'  => ['required', 'integer', 'exists:katalog,id_katalog'],
            'harga_satuan'=> ['required', 'numeric', 'min:0'],
            'satuan'      => ['required', 'in:kg,pcs'],
            'berat'       => ['nullable', 'numeric', 'min:0'],
            'qty'         => ['nullable', 'integer', 'min:0'],
        ], [
            'id_order.required'     => 'Order tidak valid!',
            'id_order.exists'       => 'Order tidak ditemukan!',
            'id_katalog.required'   => 'Katalog harus dipilih!',
            'id_katalog.exists'     => 'Katalog tidak ditemukan!',
            'harga_satuan.required' => 'Harga satuan harus diisi!',
            'harga_satuan.min'      => 'Harga satuan tidak boleh negatif!',
            'satuan.required'       => 'Satuan harus dipilih!',
            'satuan.in'             => 'Satuan hanya boleh kg atau pcs!',
            'berat.min'             => 'Berat tidak boleh negatif!',
            'qty.min'               => 'Qty tidak boleh negatif!',
        ]);

        $id        = $this->id();
        $idOrder   = (int)$request->input('id_order');
        $berat     = (float)$request->input('berat', 0);
        $qty       = $request->input('qty') ? (int)$request->input('qty') : null;
        $hargaSat  = (float)$request->input('harga_satuan');
        $satuan    = $request->input('satuan');
        $subtotal  = $satuan === 'kg' ? $berat * $hargaSat : ($qty ?? 0) * $hargaSat;

        DB::transaction(function () use ($idOrder, $id, $berat, $qty, $hargaSat, $subtotal, $satuan) {
            DB::table('order_detail')->where('id_order', $idOrder)
                ->update(['berat' => $berat ?: null, 'qty' => $qty, 'harga_satuan' => $hargaSat, 'subtotal' => $subtotal]);
            DB::table('orders')->where('id_order', $idOrder)
                ->update(['total_harga' => $subtotal, 'updated_at' => now()]);

            $cek = DB::table('pembayaran')->where('id_order', $idOrder)->first();
            if ($cek) {
                DB::table('pembayaran')->where('id_order', $idOrder)
                    ->update(['jumlah' => $subtotal, 'status_bayar' => 'Pending', 'updated_at' => now()]);
            } else {
                DB::table('pembayaran')->insert(['id_order' => $idOrder, 'metode' => 'QRIS', 'jumlah' => $subtotal, 'status_bayar' => 'Pending', 'created_at' => now()]);
            }

            DB::table('tracking')->insert(['id_order' => $idOrder, 'status' => 'Dijemput',
                'keterangan' => 'Berat diverifikasi, tagihan dikirim ke customer', 'updated_by' => $id, 'waktu_update' => now()]);
        });

        $oi = DB::table('orders as o')->join('users as u','u.id_cust','=','o.id_cust')
            ->select('o.kode_order','u.id_cust','u.nama_cust')->where('o.id_order',$idOrder)->first();
        if ($oi) {
            CG::sendNotification('customer',$oi->id_cust,'💳 Tagihan Laundry Kamu Sudah Siap!',
                "Order {$oi->kode_order} — Tagihan sebesar " . CG::rupiah($subtotal) . " sudah dimasukkan.",
                route('customer.pembayaran'));
            CG::notifyAllOwner("📊 Tagihan Dibuat: {$oi->kode_order}",
                "Staff {$this->nama()} memasukkan tagihan " . CG::rupiah($subtotal) . " untuk {$oi->nama_cust}.",
                route('owner.semua_order'));
        }

        return redirect()->route('staff.kelola_order')->with('flash', '✅ Berat terverifikasi & tagihan dikirim ke customer.');
    }

    public function statusLaundry(Request $request)
    {
        $id     = $this->id();
        $orders = $this->allActiveOrders();
        $paid   = $orders->filter(fn($o) => $o->status_bayar === 'Lunas' && in_array($o->status_order,['Dijemput','Dicuci','Disetrika','Dikirim']));

        $selId    = (int)$request->query('id', 0);
        $selOrder = null;
        if ($selId) {
            $selOrder = DB::table('orders as o')
                ->join('layanan as l','l.id_layanan','=','o.id_layanan')
                ->join('users as u','u.id_cust','=','o.id_cust')
                ->leftJoin('order_detail as od','od.id_order','=','o.id_order')
                ->leftJoin('katalog as k','k.id_katalog','=','od.id_katalog')
                ->leftJoin('pembayaran as p','p.id_order','=','o.id_order')
                ->select('o.*','l.nama_layanan','u.nama_cust','u.notelp_cust','k.varian','k.satuan','p.status_bayar')
                ->where('o.id_order',$selId)->first();
        }

        $nextStatusMap = ['Dijemput'=>'Dicuci','Dicuci'=>'Disetrika','Disetrika'=>'Dikirim','Dikirim'=>'Selesai'];

        return view('staff.index', [
            'page'         => 'status_laundry',
            'staffName'    => $this->nama(),
            'paid'         => $paid,
            'selOrder'     => $selOrder,
            'nextStatusMap'=> $nextStatusMap,
            'unreadCount'  => CG::countUnread('staff', $id),
            'kelolaCount'  => $this->kelolaCount(),
        ]);
    }

    public function advanceStatus(Request $request)
    {
        // ✅ REVISI: Validasi server-side
        $request->validate([
            'id_order'   => ['required', 'integer', 'exists:orders,id_order'],
            'new_status' => ['required', 'in:Dicuci,Disetrika,Dikirim,Selesai,Dibatalkan'],
        ], [
            'id_order.required'   => 'ID order tidak valid!',
            'id_order.exists'     => 'Order tidak ditemukan!',
            'new_status.required' => 'Status baru harus diisi!',
            'new_status.in'       => 'Status tidak valid!',
        ]);

        $id        = $this->id();
        $idOrder   = (int)$request->input('id_order');
        $newStatus = $request->input('new_status');

        $bayar = DB::table('pembayaran')->where('id_order', $idOrder)->first();
        if (!$bayar || $bayar->status_bayar !== 'Lunas') {
            return redirect()->route('staff.status_laundry')->withErrors(['status' => 'Customer belum melunasi pembayaran!']);
        }

        DB::transaction(function () use ($idOrder, $newStatus, $id) {
            DB::table('orders')->where('id_order', $idOrder)->update(['status_order' => $newStatus, 'updated_at' => now()]);
            DB::table('tracking')->insert(['id_order' => $idOrder, 'status' => $newStatus,
                'keterangan' => 'Status diperbarui oleh staff', 'updated_by' => $id, 'waktu_update' => now()]);

            if ($newStatus === 'Selesai') {
                $bayarRow = DB::table('pembayaran')->where('id_order', $idOrder)->where('status_bayar','Lunas')->first();
                if ($bayarRow) {
                    $exists = DB::table('invoice')->where('id_bayar', $bayarRow->id_bayar)->exists();
                    if (!$exists) {
                        $noInv = CG::generateNoInvoice();
                        $cust  = DB::table('orders as o')->join('users as u','u.id_cust','=','o.id_cust')
                            ->select('u.notelp_cust')->where('o.id_order', $idOrder)->first();
                        $noWa = $cust ? '62' . ltrim($cust->notelp_cust, '0') : '';
                        DB::table('invoice')->insert(['id_bayar' => $bayarRow->id_bayar, 'no_invoice' => $noInv, 'nomor_wa' => $noWa, 'tgl_invoice' => now(), 'created_at' => now()]);
                    }
                }
            }
        });

        $oi = DB::table('orders as o')->join('users as u','u.id_cust','=','o.id_cust')
            ->select('o.kode_order','u.id_cust')->where('o.id_order',$idOrder)->first();
        if ($oi) {
            $msg = match($newStatus) {
                'Dicuci'    => "Order {$oi->kode_order} sedang dicuci. Proses laundry sedang berjalan!",
                'Disetrika' => "Order {$oi->kode_order} sedang disetrika. Hampir selesai!",
                'Dikirim'   => "Order {$oi->kode_order} sedang dalam perjalanan ke alamatmu.",
                'Selesai'   => "Order {$oi->kode_order} sudah selesai! Terima kasih sudah menggunakan CleanGo.",
                default     => "Status order {$oi->kode_order} diperbarui ke: {$newStatus}",
            };
            CG::sendNotification('customer',$oi->id_cust,"📦 Update Order: {$newStatus}",$msg,route('customer.tracking'));
            CG::notifyAllOwner("📦 Order {$oi->kode_order}: {$newStatus}",
                "Staff {$this->nama()} mengupdate status ke {$newStatus}.", route('owner.semua_order'));
        }

        return redirect()->route('staff.status_laundry')->with('flash', "Status berhasil diupdate ke <strong>{$newStatus}</strong>.");
    }

    public function konfirmasiBayar()
    {
        $id = $this->id();
        $konfBayar = DB::table('pembayaran as p')
            ->join('orders as o','o.id_order','=','p.id_order')
            ->join('users as u','u.id_cust','=','o.id_cust')
            ->join('layanan as l','l.id_layanan','=','o.id_layanan')
            ->select('p.*','o.kode_order','u.nama_cust','l.nama_layanan')
            ->where('p.status_bayar','Menunggu Konfirmasi')->get();

        return view('staff.index', [
            'page'        => 'konfirmasi_bayar',
            'staffName'   => $this->nama(),
            'konfBayar'   => $konfBayar,
            'unreadCount' => CG::countUnread('staff', $id),
            'kelolaCount' => $this->kelolaCount(),
        ]);
    }

    public function doKonfirmasi(Request $request)
    {
        // ✅ REVISI: Validasi server-side
        $request->validate([
            'id_bayar' => ['required', 'integer', 'exists:pembayaran,id_bayar'],
        ], [
            'id_bayar.required' => 'ID pembayaran tidak valid!',
            'id_bayar.exists'   => 'Data pembayaran tidak ditemukan!',
        ]);

        $id      = $this->id();
        $idBayar = (int)$request->input('id_bayar');

        DB::transaction(function () use ($idBayar, $id) {
            DB::table('pembayaran')->where('id_bayar', $idBayar)->update([
                'status_bayar'      => 'Lunas',
                'dikonfirmasi_oleh' => $id,
                'waktu_bayar'       => now(),
                'updated_at'        => now(),
            ]);
        });

        $oi = DB::table('pembayaran as p')->join('orders as o','o.id_order','=','p.id_order')
            ->join('users as u','u.id_cust','=','o.id_cust')
            ->select('o.kode_order','u.id_cust')->where('p.id_bayar',$idBayar)->first();
        if ($oi) {
            CG::sendNotification('customer',$oi->id_cust,'✅ Pembayaran Dikonfirmasi!',
                "Pembayaran untuk order {$oi->kode_order} sudah dikonfirmasi.",route('customer.tracking'));
            CG::notifyAllOwner("✅ Bayar Lunas: {$oi->kode_order}",
                "Staff {$this->nama()} mengkonfirmasi pembayaran order {$oi->kode_order}.",route('owner.semua_order'));
        }

        return redirect()->route('staff.konfirmasi_bayar')->with('flash', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function history()
    {
        $id      = $this->id();
        $selesai = DB::table('orders as o')
            ->join('layanan as l','l.id_layanan','=','o.id_layanan')
            ->join('users as u','u.id_cust','=','o.id_cust')
            ->select('o.*','l.nama_layanan','u.nama_cust')
            ->where('o.status_order','Selesai')->where('o.id_staff',$id)
            ->orderByDesc('o.updated_at')->limit(50)->get();

        return view('staff.index', [
            'page'        => 'history',
            'staffName'   => $this->nama(),
            'selesai'     => $selesai,
            'unreadCount' => CG::countUnread('staff', $id),
            'kelolaCount' => $this->kelolaCount(),
        ]);
    }

    // ── PROFIL ───────────────────────────────────────────────
    public function profil()
    {
        $id    = $this->id();
        $staff = DB::table('staff')->where('id_staff', $id)->first();

        return view('staff.index', [
            'page'        => 'profil',
            'staffName'   => $this->nama(),
            'staff'       => $staff,
            'unreadCount' => CG::countUnread('staff', $id),
            'kelolaCount' => $this->kelolaCount(),
        ]);
    }

    // ── UPDATE PROFIL ────────────────────────────────────────
    public function updateProfil(Request $request)
    {
        $id = $this->id();
        $data = $request->validate([
            'nama'        => ['required', 'string', 'max:100'],
            'notelp'      => ['required', 'digits_between:6,20'],
            'alamat'      => ['nullable', 'string', 'max:1000'],
            'sandi'       => ['nullable', 'string', 'min:6', 'max:100'],
            'sandi_confirm' => ['nullable', 'string', 'min:6', 'max:100'],
        ]);

        // Validasi password match
        if ($data['sandi'] && $data['sandi'] !== $data['sandi_confirm']) {
            return redirect()->route('staff.profil')->withErrors(['sandi' => 'Password tidak cocok.']);
        }

        DB::transaction(function () use ($id, $data) {
            DB::table('staff')->where('id_staff', $id)->update([
                'nama' => $data['nama'],
                'notelp' => $data['notelp'],
                'alamat' => $data['alamat'] ?? null,
            ]);

            if ($data['sandi']) {
                DB::table('staff')->where('id_staff', $id)->update([
                    'sandi' => bcrypt($data['sandi']),
                ]);
            }
        });

        Session::put('nama', $data['nama']);

        return redirect()->route('staff.profil')->with('flash', 'Profil berhasil diperbarui.');
    }
}
