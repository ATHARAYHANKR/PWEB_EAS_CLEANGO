<?php

namespace App\Http\Controllers;

use App\Helpers\CleanGoHelper as CG;
use App\Models\AppSetting;
use App\Models\Katalog;
use App\Models\Layanan;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pembayaran;
use App\Models\Tracking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    private function id()   { return Session::get('user_id'); }
    private function nama() { return Session::get('nama'); }

    // ── DASHBOARD ────────────────────────────────────────────
    public function dashboard()
    {
        $id     = $this->id();
        $orders = $this->myOrders($id);
        $bayar  = $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0);

        return view('customer.index', [
            'page'         => 'dashboard',
            'customerName' => $this->nama(),
            'myOrders'     => $orders,
            'ordersBayar'  => $bayar,
            'statAktif'    => $orders->filter(fn($o) => !in_array($o->status_order, ['Selesai','Dibatalkan']))->count(),
            'statSelesai'  => $orders->filter(fn($o) => $o->status_order === 'Selesai')->count(),
            'statTotal'    => $orders->sum('jumlah_bayar'),
            'unreadCount'  => CG::countUnread('customer', $id),
        ]);
    }

    // ── BOOKING ──────────────────────────────────────────────
    public function booking()
    {
        $profil = User::find($this->id());
        $settings = AppSetting::pluck('value', 'key')->toArray();

        return view('customer.index', [
            'page'         => 'booking',
            'customerName' => $this->nama(),
            'layananList'  => Layanan::where('is_active', 1)->get(),
            'katalogList'  => Katalog::join('layanan', 'layanan.id_layanan', '=', 'katalog.id_layanan')
                ->where('katalog.status', 'Aktif')
                ->select('katalog.*', 'layanan.nama_layanan')
                ->orderBy('layanan.nama_layanan')->orderBy('katalog.varian')
                ->get(),
            'profil'       => $profil,
            'ordersBayar'  => collect(),
            'settings'     => $settings,
            'unreadCount'  => CG::countUnread('customer', $this->id()),
        ]);
    }

    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'id_layanan'   => ['required', 'integer', 'exists:layanan,id_layanan'],
            'id_katalog'   => ['required', 'integer', 'exists:katalog,id_katalog'],
            'alamat'       => ['required', 'string', 'max:1000'],
            'tanggal_jemput' => ['required', 'date'],
            'sesi_jemput'  => ['required', 'string', 'max:20'],
            'catatan'      => ['nullable', 'string', 'max:1000'],
        ]);

        $id = $this->id();
        $waktu = substr($data['sesi_jemput'], 0, 5);
        $jadwal = $data['tanggal_jemput'] . ' ' . $waktu . ':00';
        $kode = CG::generateKodeOrder();

        $order = Order::create([
            'kode_order' => $kode,
            'id_cust' => $id,
            'id_layanan' => $data['id_layanan'],
            'alamat_penjemputan' => $data['alamat'],
            'jadwal_jemput' => $jadwal,
            'catatan' => $data['catatan'] ?? null,
            'status_order' => 'Menunggu Konfirmasi',
            'tanggal_pesan' => now(),
        ]);

        $katalog = Katalog::find($data['id_katalog']);
        if ($katalog) {
            OrderDetail::create([
                'id_order' => $order->id_order,
                'id_katalog' => $katalog->id_katalog,
                'harga_satuan' => $katalog->harga,
                'subtotal' => 0,
            ]);
        }

        Tracking::create([
            'id_order' => $order->id_order,
            'status' => 'Menunggu Konfirmasi',
            'keterangan' => 'Order masuk dari customer',
            'waktu_update' => now(),
        ]);

        $custName = $this->nama();
        CG::notifyAllStaff(
            '📦 Order Baru Masuk!',
            "Order {$kode} dari {$custName} menunggu konfirmasi. Segera proses!",
            route('staff.order_masuk')
        );
        CG::notifyAllOwner(
            "📦 Order Baru: {$kode}",
            "Customer {$custName} membuat order baru ({$kode}).",
            route('owner.semua_order')
        );

        return redirect()->route('customer.riwayat')
            ->with('flash', "Booking <strong>{$kode}</strong> berhasil dibuat!");
    }

    // ── EDIT BOOKING ─────────────────────────────────────────
    public function editBooking($id)
    {
        $custId = $this->id();
        $order = Order::where('id_order', $id)->where('id_cust', $custId)->first();

        if (!$order || !in_array($order->status_order, ['Menunggu Konfirmasi'])) {
            return redirect()->route('customer.riwayat')->withErrors(['edit' => 'Booking tidak bisa diedit. Hanya booking yang belum dikonfirmasi yang bisa diedit.']);
        }

        $profil = User::find($custId);
        $settings = AppSetting::pluck('value', 'key')->toArray();

        return view('customer.index', [
            'page'         => 'booking_edit',
            'customerName' => $this->nama(),
            'layananList'  => Layanan::where('is_active', 1)->get(),
            'katalogList'  => Katalog::join('layanan', 'layanan.id_layanan', '=', 'katalog.id_layanan')
                ->where('katalog.status', 'Aktif')
                ->select('katalog.*', 'layanan.nama_layanan')
                ->orderBy('layanan.nama_layanan')->orderBy('katalog.varian')
                ->get(),
            'profil'       => $profil,
            'editOrder'    => $order,
            'editDetail'   => OrderDetail::where('id_order', $id)->first(),
            'ordersBayar'  => collect(),
            'settings'     => $settings,
            'unreadCount'  => CG::countUnread('customer', $custId),
        ]);
    }

    // ── UPDATE BOOKING ───────────────────────────────────────
    public function updateBooking(Request $request, $id)
    {
        $custId = $this->id();
        $order = Order::where('id_order', $id)->where('id_cust', $custId)->first();

        if (!$order || !in_array($order->status_order, ['Menunggu Konfirmasi'])) {
            return redirect()->route('customer.riwayat')->withErrors(['edit' => 'Booking tidak bisa diedit.']);
        }

        $data = $request->validate([
            'id_layanan'   => ['required', 'integer', 'exists:layanan,id_layanan'],
            'id_katalog'   => ['required', 'integer', 'exists:katalog,id_katalog'],
            'alamat'       => ['required', 'string', 'max:1000'],
            'tanggal_jemput' => ['required', 'date'],
            'sesi_jemput'  => ['required', 'string', 'max:20'],
            'catatan'      => ['nullable', 'string', 'max:1000'],
        ]);

        $waktu = substr($data['sesi_jemput'], 0, 5);
        $jadwal = $data['tanggal_jemput'] . ' ' . $waktu . ':00';

        $order->update([
            'id_layanan' => $data['id_layanan'],
            'alamat_penjemputan' => $data['alamat'],
            'jadwal_jemput' => $jadwal,
            'catatan' => $data['catatan'] ?? null,
        ]);

        $katalog = Katalog::find($data['id_katalog']);
        if ($katalog) {
            OrderDetail::where('id_order', $id)->update([
                'id_katalog' => $katalog->id_katalog,
                'harga_satuan' => $katalog->harga,
            ]);
        }

        return redirect()->route('customer.riwayat')
            ->with('flash', "Booking <strong>{$order->kode_order}</strong> berhasil diperbarui!");
    }

    // ── DELETE BOOKING ───────────────────────────────────────
    public function deleteBooking(Request $request, $id)
    {
        $custId = $this->id();
        $order = Order::where('id_order', $id)->where('id_cust', $custId)->first();

        if (!$order || !in_array($order->status_order, ['Menunggu Konfirmasi'])) {
            return redirect()->route('customer.riwayat')->withErrors(['delete' => 'Booking tidak bisa dihapus. Hanya booking yang belum dikonfirmasi yang bisa dihapus.']);
        }

        $kodeOrder = $order->kode_order;
        
        DB::transaction(function () use ($id, $custId) {
            OrderDetail::where('id_order', $id)->delete();
            Tracking::where('id_order', $id)->delete();
            Order::where('id_order', $id)->where('id_cust', $custId)->delete();
        });

        CG::notifyAllOwner(
            "🗑️ Booking Dibatalkan: {$kodeOrder}",
            "Customer {$this->nama()} membatalkan booking {$kodeOrder}.",
            route('owner.semua_order')
        );

        return redirect()->route('customer.riwayat')
            ->with('flash', "Booking <strong>{$kodeOrder}</strong> berhasil dihapus!");
    }

    // ── RIWAYAT ──────────────────────────────────────────────
    public function riwayat(Request $request)
    {
        $id     = $this->id();
        $orders = $this->myOrders($id);
        $selId  = (int)$request->query('id', 0);
        $selOrder = $selTracking = null;

        if ($selId) {
            $selOrder = DB::table('orders as o')
                ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
                ->leftJoin('order_detail as od', 'od.id_order', '=', 'o.id_order')
                ->leftJoin('katalog as k', 'k.id_katalog', '=', 'od.id_katalog')
                ->leftJoin('staff as s', 's.id_staff', '=', 'o.id_staff')
                ->select('o.*', 'l.nama_layanan', 'k.varian', 'k.satuan', 'od.berat', 'od.qty', 's.nama as nama_staff')
                ->where('o.id_order', $selId)->where('o.id_cust', $id)->first();

            if ($selOrder) {
                $selTracking = DB::table('tracking')->where('id_order', $selId)->orderBy('waktu_update')->get();
            }
        }

        return view('customer.index', [
            'page'         => 'riwayat',
            'customerName' => $this->nama(),
            'myOrders'     => $orders,
            'ordersBayar'  => $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0),
            'selOrder'     => $selOrder,
            'selTracking'  => $selTracking,
            'unreadCount'  => CG::countUnread('customer', $id),
            'statusSteps'  => ['Menunggu Konfirmasi'=>0,'Dijemput'=>1,'Dicuci'=>2,'Disetrika'=>3,'Dikirim'=>4,'Selesai'=>5,'Dibatalkan'=>-1],
            'statusLabels' => ['Menunggu Konfirmasi','Dijemput','Dicuci','Disetrika','Dikirim','Selesai'],
            'statusIcons'  => ['⏳','🚗','🧺','✨','📦','✅'],
        ]);
    }

    // ── PEMBAYARAN ───────────────────────────────────────────
    public function pembayaran(Request $request)
    {
        $id     = $this->id();
        $orders = $this->myOrders($id);
        $payId  = (int)$request->query('id', 0);
        $selOrder = $selPayment = null;

        if ($payId) {
            $selOrder   = DB::table('orders as o')
                ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
                ->leftJoin('order_detail as od', 'od.id_order', '=', 'o.id_order')
                ->leftJoin('katalog as k', 'k.id_katalog', '=', 'od.id_katalog')
                ->select('o.*', 'l.nama_layanan', 'k.varian', 'k.satuan', 'od.berat', 'od.qty')
                ->where('o.id_order', $payId)->where('o.id_cust', $id)->first();
            $selPayment = DB::table('pembayaran')->where('id_order', $payId)->first();
        }

        return view('customer.index', [
            'page'          => 'pembayaran',
            'customerName'  => $this->nama(),
            'myOrders'      => $orders,
            'ordersBayar'   => $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0),
            'pendingOrders' => $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0),
            'waitingOrders' => $orders->filter(fn($o) => $o->status_bayar === 'Menunggu Konfirmasi'),
            'doneOrders'    => $orders->filter(fn($o) => $o->status_bayar === 'Lunas'),
            'selOrder'      => $selOrder,
            'selPayment'    => $selPayment,
            'payId'         => $payId,
            'unreadCount'   => CG::countUnread('customer', $id),
        ]);
    }

    public function uploadBukti(Request $request)
    {
        $data = $request->validate([
            'id_order' => ['required', 'integer', 'exists:orders,id_order'],
            'catatan_bayar' => ['nullable', 'string', 'max:1000'],
        ]);

        $updated = Pembayaran::where('id_order', $data['id_order'])
            ->where('status_bayar', 'Pending')
            ->update(['status_bayar' => 'Menunggu Konfirmasi', 'catatan' => $data['catatan_bayar'] ?? null, 'waktu_bayar' => now()]);

        if ($updated) {
            $oi = Order::with('customer')->find($data['id_order']);
            if ($oi && $oi->customer) {
                CG::notifyAllStaff('💳 Pembayaran Masuk!',
                    "Customer {$oi->customer->nama_cust} sudah upload bukti bayar untuk order {$oi->kode_order}.",
                    route('staff.konfirmasi_bayar'));
                CG::notifyAllOwner('💳 Bukti Bayar Diterima',
                    "Order {$oi->kode_order} — {$oi->customer->nama_cust} mengirimkan bukti pembayaran.",
                    route('owner.semua_order'));
            }
            return redirect()->route('customer.pembayaran')->with('flash', 'Bukti pembayaran berhasil dikirim.');
        }

        return redirect()->route('customer.pembayaran')->withErrors(['bayar' => 'Gagal mengirim bukti.']);
    }

    // ── TRACKING ─────────────────────────────────────────────
    public function tracking()
    {
        $id     = $this->id();
        $orders = $this->myOrders($id);
        $active = $orders->filter(fn($o) => !in_array($o->status_order, ['Selesai','Dibatalkan']));

        $trackingData = [];
        foreach ($active as $o) {
            $trackingData[$o->id_order] = DB::table('tracking')
                ->where('id_order', $o->id_order)
                ->orderByDesc('waktu_update')->limit(5)->get();
        }

        return view('customer.index', [
            'page'         => 'tracking',
            'customerName' => $this->nama(),
            'myOrders'     => $orders,
            'ordersBayar'  => $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0),
            'activeOrders' => $active,
            'trackingData' => $trackingData,
            'unreadCount'  => CG::countUnread('customer', $id),
            'statusSteps'  => ['Menunggu Konfirmasi'=>0,'Dijemput'=>1,'Dicuci'=>2,'Disetrika'=>3,'Dikirim'=>4,'Selesai'=>5,'Dibatalkan'=>-1],
            'statusLabels' => ['Menunggu Konfirmasi','Dijemput','Dicuci','Disetrika','Dikirim','Selesai'],
            'statusIcons'  => ['⏳','🚗','🧺','✨','📦','✅'],
        ]);
    }

    // ── INVOICE ──────────────────────────────────────────────
    public function invoice()
    {
        $id = $this->id();
        $invoices = DB::table('invoice as i')
            ->join('pembayaran as p', 'p.id_bayar', '=', 'i.id_bayar')
            ->join('orders as o', 'o.id_order', '=', 'p.id_order')
            ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
            ->select('i.*', 'p.jumlah', 'p.metode', 'p.status_bayar', 'o.kode_order', 'o.status_order', 'l.nama_layanan')
            ->where('o.id_cust', $id)
            ->orderByDesc('i.tgl_invoice')->get();

        $orders = $this->myOrders($id);
        return view('customer.index', [
            'page'         => 'invoice',
            'customerName' => $this->nama(),
            'invoices'     => $invoices,
            'ordersBayar'  => $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0),
            'unreadCount'  => CG::countUnread('customer', $id),
        ]);
    }

    public function printInvoice($id)
    {
        $invoice = DB::table('invoice as i')
            ->join('pembayaran as p', 'p.id_bayar', '=', 'i.id_bayar')
            ->join('orders as o', 'o.id_order', '=', 'p.id_order')
            ->leftJoin('order_detail as od', 'od.id_order', '=', 'o.id_order')
            ->join('users as u', 'u.id_cust', '=', 'o.id_cust')
            ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
            ->select(
                'i.*',
                'p.jumlah',
                'p.metode',
                'p.status_bayar',
                'o.kode_order',
                'o.status_order',
                'od.berat',
                'od.qty',
                'o.tanggal_pesan',
                'l.nama_layanan',
                'u.nama_cust',
                'u.notelp_cust',
                'o.id_cust',
                'o.id_order'
            )
            ->where('i.id_invoice', $id)
            ->first();

        if (!$invoice || $invoice->id_cust !== $this->id()) {
            abort(404);
        }

        return view('invoice.print', [
            'invoice' => $invoice,
            'downloadRoute' => route('customer.invoice.download', $id),
            'backRoute' => route('customer.invoice'),
        ]);
    }

    public function downloadInvoice($id)
    {
        $invoice = DB::table('invoice as i')
            ->join('pembayaran as p', 'p.id_bayar', '=', 'i.id_bayar')
            ->join('orders as o', 'o.id_order', '=', 'p.id_order')
            ->join('users as u', 'u.id_cust', '=', 'o.id_cust')
            ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
            ->select(
                'i.*',
                'p.jumlah',
                'p.metode',
                'p.status_bayar',
                'o.kode_order',
                'o.status_order',
                'o.tanggal_pesan',
                'l.nama_layanan',
                'u.nama_cust',
                'u.notelp_cust',
                'o.id_cust',
                'o.id_order'
            )
            ->where('i.id_invoice', $id)
            ->first();

        if (!$invoice || $invoice->id_cust !== $this->id()) {
            abort(404);
        }

        $details = DB::table('order_detail as od')
            ->leftJoin('katalog as k', 'k.id_katalog', '=', 'od.id_katalog')
            ->select('od.berat', 'od.qty', 'od.harga_satuan', 'od.subtotal', 'k.varian', 'k.satuan')
            ->where('od.id_order', $invoice->id_order)
            ->get();

        $filename = 'nota-' . $invoice->no_invoice . '.pdf';
        return Pdf::loadView('invoice.pdf', [
            'invoice' => $invoice,
            'details' => $details,
        ])->setPaper('a4', 'portrait')->download($filename);
    }

    // ── PROFIL ───────────────────────────────────────────────
    public function profil()
    {
        $id     = $this->id();
        $profil = DB::table('users')->where('id_cust', $id)->first();
        $orders = $this->myOrders($id);
        return view('customer.index', [
            'page'         => 'profil',
            'customerName' => $this->nama(),
            'profil'       => $profil,
            'myOrders'     => $orders,
            'ordersBayar'  => $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0),
            'unreadCount'  => CG::countUnread('customer', $id),
        ]);
    }

    public function updateProfil(Request $request)
    {
        $id = $this->id();
        $data = $request->validate([
            'nama_cust' => ['required', 'string', 'max:100'],
            'notelp_cust' => ['required', 'digits_between:6,20'],
            'alamat_cust' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::find($id);
        if ($user) {
            $user->update([
                'nama_cust' => $data['nama_cust'],
                'notelp_cust' => $data['notelp_cust'],
                'alamat_cust' => $data['alamat_cust'] ?? null,
            ]);
            Session::put('nama', $data['nama_cust']);
        }

        return redirect()->route('customer.profil')->with('flash', 'Profil berhasil diperbarui.');
    }

    // ── PRIVATE ──────────────────────────────────────────────
    private function myOrders(int $id)
    {
        return DB::table('orders as o')
            ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
            ->leftJoin('order_detail as od', 'od.id_order', '=', 'o.id_order')
            ->leftJoin('katalog as k', 'k.id_katalog', '=', 'od.id_katalog')
            ->leftJoin('staff as s', 's.id_staff', '=', 'o.id_staff')
            ->leftJoin('pembayaran as p', 'p.id_order', '=', 'o.id_order')
            ->select('o.*', 'l.nama_layanan', 'k.varian', 'k.satuan', 'k.harga',
                     'od.berat', 'od.qty', 'od.harga_satuan as harga_od', 'od.subtotal',
                     's.nama as nama_staff',
                     'p.id_bayar', 'p.jumlah as jumlah_bayar', 'p.status_bayar', 'p.metode')
            ->where('o.id_cust', $id)
            ->orderByDesc('o.tanggal_pesan')
            ->get();
    }
}
