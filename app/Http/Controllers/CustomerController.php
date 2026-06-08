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
    // Ambil user id yang tersimpan di session (server-side session)
    private function id()   { return Session::get('user_id'); }
    // Ambil nama user yang tersimpan di session; diperbarui saat update profil
    private function nama() { return Session::get('nama'); }

    // ── DASHBOARD ────────────────────────────────────────────
    // Menyiapkan data ringkasan untuk halaman dashboard
    public function dashboard()
    {
        // Ambil id user dari session
        $id     = $this->id();
        // Ambil daftar order user (private helper myOrders)
        $orders = $this->myOrders($id);
        // Filter order yang membutuhkan pembayaran
        $bayar  = $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0);

        // Kembalikan view dengan variabel yang diperlukan
        return view('customer.index', [
            // Flag view bagian yang ditampilkan: dashboard
            'page'         => 'dashboard',
            // Nama customer ditampilkan di header
            'customerName' => $this->nama(),
            // Semua order customer untuk tampilan ringkasan
            'myOrders'     => $orders,
            // Hanya order yang perlu segera dibayar
            'ordersBayar'  => $bayar,
            // Statistik: order aktif (bukan selesai/dibatalkan)
            'statAktif'    => $orders->filter(fn($o) => !in_array($o->status_order, ['Selesai','Dibatalkan']))->count(),
            // Statistik: order yang selesai
            'statSelesai'  => $orders->filter(fn($o) => $o->status_order === 'Selesai')->count(),
            // Total jumlah yang harus dibayar (aggregate)
            'statTotal'    => $orders->sum('jumlah_bayar'),
            // Jumlah notifikasi belum dibaca (helper CleanGoHelper)
            'unreadCount'  => CG::countUnread('customer', $id),
        ]);
    }

    // ── BOOKING ──────────────────────────────────────────────
    // Menampilkan halaman booking: daftar layanan/katalog dan form booking
    public function booking()
    {
        // Ambil data profil user untuk pra-isian alamat di form
        $profil = User::find($this->id());
        // Ambil setting aplikasi (mis. teks antar-jemput) sebagai array key=>value
        $settings = AppSetting::pluck('value', 'key')->toArray();

        // Kirim data ke view booking
        return view('customer.index', [
            'page'         => 'booking',
            'customerName' => $this->nama(),
            // Daftar layanan aktif (mis. reguler/express)
            'layananList'  => Layanan::where('is_active', 1)->get(),
            // Katalog: paket layanan yang aktif, join untuk menampilkan nama layanan
            'katalogList'  => Katalog::join('layanan', 'layanan.id_layanan', '=', 'katalog.id_layanan')
                ->where('katalog.status', 'Aktif')
                ->select('katalog.*', 'layanan.nama_layanan')
                ->orderBy('layanan.nama_layanan')->orderBy('katalog.varian')
                ->get(),
            // Profil untuk pra-isian form
            'profil'       => $profil,
            // Tidak ada alert pembayaran di halaman booking
            'ordersBayar'  => collect(),
            'settings'     => $settings,
            'unreadCount'  => CG::countUnread('customer', $this->id()),
        ]);
    }

    public function storeBooking(Request $request)
    {
        // Validasi input request: server-side validation wajib
        $data = $request->validate([
            'id_layanan'   => ['required', 'integer', 'exists:layanan,id_layanan'],
            'id_katalog'   => ['required', 'integer', 'exists:katalog,id_katalog'],
            'alamat'       => ['required', 'string', 'max:1000'],
            'tanggal_jemput' => ['required', 'date'],
            'sesi_jemput'  => ['required', 'string', 'max:20'],
            'catatan'      => ['nullable', 'string', 'max:1000'],
        ]);

        // Siapkan data order
        $id = $this->id();
        // Ambil jam dari sesi (format disimpan di UI seperti "08:00 - 10:00")
        $waktu = substr($data['sesi_jemput'], 0, 5);
        // Gabungkan tanggal + jam -> datetime untuk kolom jadwal_jemput
        $jadwal = $data['tanggal_jemput'] . ' ' . $waktu . ':00';
        // Generate kode order unik per hari
        $kode = CG::generateKodeOrder();

        // Buat record Order (mass-assignment menggunakan $fillable di model)
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

        // Tambahkan OrderDetail berdasarkan katalog yang dipilih (harga dari katalog)
        $katalog = Katalog::find($data['id_katalog']);
        if ($katalog) {
            OrderDetail::create([
                'id_order' => $order->id_order,
                'id_katalog' => $katalog->id_katalog,
                'harga_satuan' => $katalog->harga,
                'subtotal' => 0,
            ]);
        }

        // Buat entri tracking awal
        Tracking::create([
            'id_order' => $order->id_order,
            'status' => 'Menunggu Konfirmasi',
            'keterangan' => 'Order masuk dari customer',
            'waktu_update' => now(),
        ]);

        // Kirim notifikasi ke staff dan owner agar segera menangani order baru
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

        // Redirect ke riwayat dengan pesan sukses
        return redirect()->route('customer.riwayat')
            ->with('flash', "Booking <strong>{$kode}</strong> berhasil dibuat!");
    }

    // ── EDIT BOOKING ─────────────────────────────────────────
    public function editBooking($id)
    {
        // Ambil id customer dari session
        $custId = $this->id();
        // Cari order yang dimiliki customer ini
        $order = Order::where('id_order', $id)->where('id_cust', $custId)->first();

        // Hanya izinkan edit jika status masih 'Menunggu Konfirmasi'
        if (!$order || !in_array($order->status_order, ['Menunggu Konfirmasi'])) {
            return redirect()->route('customer.riwayat')->withErrors(['edit' => 'Booking tidak bisa diedit. Hanya booking yang belum dikonfirmasi yang bisa diedit.']);
        }

        // Ambil profil customer dan settings untuk menampilkan form edit
        $profil = User::find($custId);
        $settings = AppSetting::pluck('value', 'key')->toArray();

        // Kembalikan view dengan data order dan detailnya untuk diedit
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
            // Detail order (berat/qty) bila ada
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
        // Cari order milik customer
        $order = Order::where('id_order', $id)->where('id_cust', $custId)->first();

        // Validasi: hanya bisa update jika belum dikonfirmasi
        if (!$order || !in_array($order->status_order, ['Menunggu Konfirmasi'])) {
            return redirect()->route('customer.riwayat')->withErrors(['edit' => 'Booking tidak bisa diedit.']);
        }

        // Validasi input update
        $data = $request->validate([
            'id_layanan'   => ['required', 'integer', 'exists:layanan,id_layanan'],
            'id_katalog'   => ['required', 'integer', 'exists:katalog,id_katalog'],
            'alamat'       => ['required', 'string', 'max:1000'],
            'tanggal_jemput' => ['required', 'date'],
            'sesi_jemput'  => ['required', 'string', 'max:20'],
            'catatan'      => ['nullable', 'string', 'max:1000'],
        ]);

        // Format jadwal jemput kembali
        $waktu = substr($data['sesi_jemput'], 0, 5);
        $jadwal = $data['tanggal_jemput'] . ' ' . $waktu . ':00';

        // Update order utama
        $order->update([
            'id_layanan' => $data['id_layanan'],
            'alamat_penjemputan' => $data['alamat'],
            'jadwal_jemput' => $jadwal,
            'catatan' => $data['catatan'] ?? null,
        ]);

        // Update order detail (katalog/harga) bila katalog berubah
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
        // Pastikan order dimiliki oleh customer dan masih bisa dihapus
        $order = Order::where('id_order', $id)->where('id_cust', $custId)->first();

        if (!$order || !in_array($order->status_order, ['Menunggu Konfirmasi'])) {
            return redirect()->route('customer.riwayat')->withErrors(['delete' => 'Booking tidak bisa dihapus. Hanya booking yang belum dikonfirmasi yang bisa dihapus.']);
        }

        $kodeOrder = $order->kode_order;

        // Hapus order dan relasinya dalam transaksi untuk menjaga konsistensi
        DB::transaction(function () use ($id, $custId) {
            OrderDetail::where('id_order', $id)->delete();
            Tracking::where('id_order', $id)->delete();
            Order::where('id_order', $id)->where('id_cust', $custId)->delete();
        });

        // Notifikasi ke owner agar tahu booking dibatalkan
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
        // Ambil semua order milik customer
        $orders = $this->myOrders($id);
        // Jika query param id dikirim, tampilkan detail order dan tracking
        $selId  = (int)$request->query('id', 0);
        $selOrder = $selTracking = null;

        if ($selId) {
            // Ambil detail order termasuk layanan, katalog, dan staf yang menangani
            $selOrder = DB::table('orders as o')
                ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
                ->leftJoin('order_detail as od', 'od.id_order', '=', 'o.id_order')
                ->leftJoin('katalog as k', 'k.id_katalog', '=', 'od.id_katalog')
                ->leftJoin('staff as s', 's.id_staff', '=', 'o.id_staff')
                ->select('o.*', 'l.nama_layanan', 'k.varian', 'k.satuan', 'od.berat', 'od.qty', 's.nama as nama_staff')
                ->where('o.id_order', $selId)->where('o.id_cust', $id)->first();

            if ($selOrder) {
                // Ambil riwayat tracking untuk order tersebut
                $selTracking = DB::table('tracking')->where('id_order', $selId)->orderBy('waktu_update')->get();
            }
        }

        // Kirim data ke view riwayat
        return view('customer.index', [
            'page'         => 'riwayat',
            'customerName' => $this->nama(),
            'myOrders'     => $orders,
            'ordersBayar'  => $orders->filter(fn($o) => $o->status_bayar === 'Pending' && $o->jumlah_bayar > 0),
            'selOrder'     => $selOrder,
            'selTracking'  => $selTracking,
            'unreadCount'  => CG::countUnread('customer', $id),
            // Data bantu untuk menampilkan status stepper di UI
            'statusSteps'  => ['Menunggu Konfirmasi'=>0,'Dijemput'=>1,'Dicuci'=>2,'Disetrika'=>3,'Dikirim'=>4,'Selesai'=>5,'Dibatalkan'=>-1],
            'statusLabels' => ['Menunggu Konfirmasi','Dijemput','Dicuci','Disetrika','Dikirim','Selesai'],
            'statusIcons'  => ['⏳','🚗','🧺','✨','📦','✅'],
        ]);
    }

    // ── PEMBAYARAN ───────────────────────────────────────────
    public function pembayaran(Request $request)
    {
        $id     = $this->id();
        // Ambil semua order customer
        $orders = $this->myOrders($id);
        // Jika ada id order di query string, ambil detail order dan pembayaran terkait
        $payId  = (int)$request->query('id', 0);
        $selOrder = $selPayment = null;

        if ($payId) {
            $selOrder   = DB::table('orders as o')
                ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
                ->leftJoin('order_detail as od', 'od.id_order', '=', 'o.id_order')
                ->leftJoin('katalog as k', 'k.id_katalog', '=', 'od.id_katalog')
                ->select('o.*', 'l.nama_layanan', 'k.varian', 'k.satuan', 'od.berat', 'od.qty')
                ->where('o.id_order', $payId)->where('o.id_cust', $id)->first();
            // Ambil record pembayaran (jika ada)
            $selPayment = DB::table('pembayaran')->where('id_order', $payId)->first();
        }

        // Kembalikan view pembayaran dengan pengelompokan status pembayaran
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
        // Validasi input upload bukti (id order + catatan opsional)
        $data = $request->validate([
            'id_order' => ['required', 'integer', 'exists:orders,id_order'],
            'catatan_bayar' => ['nullable', 'string', 'max:1000'],
        ]);

        // Update record pembayaran: ubah status dari Pending -> Menunggu Konfirmasi
        $updated = Pembayaran::where('id_order', $data['id_order'])
            ->where('status_bayar', 'Pending')
            ->update(['status_bayar' => 'Menunggu Konfirmasi', 'catatan' => $data['catatan_bayar'] ?? null, 'waktu_bayar' => now()]);

        if ($updated) {
            // Jika berhasil, beri notifikasi ke staff/owner
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

        // Jika update gagal, kembalikan error
        return redirect()->route('customer.pembayaran')->withErrors(['bayar' => 'Gagal mengirim bukti.']);
    }

    // ── TRACKING ─────────────────────────────────────────────
    public function tracking()
    {
        $id     = $this->id();
        // Ambil semua order customer
        $orders = $this->myOrders($id);
        // Filter order aktif (belum selesai/dibatalkan)
        $active = $orders->filter(fn($o) => !in_array($o->status_order, ['Selesai','Dibatalkan']));

        // Ambil tracking terbaru (limit 5) untuk setiap aktif order
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
        // Ambil daftar invoice yang terkait customer ini
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

        // Verifikasi ownership invoice
        if (!$invoice || $invoice->id_cust !== $this->id()) {
            abort(404);
        }

        // Render halaman print invoice (HTML) — bisa di-print oleh browser
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

        // Verifikasi ownership
        if (!$invoice || $invoice->id_cust !== $this->id()) {
            abort(404);
        }

        // Ambil detail order untuk lampiran nota
        $details = DB::table('order_detail as od')
            ->leftJoin('katalog as k', 'k.id_katalog', '=', 'od.id_katalog')
            ->select('od.berat', 'od.qty', 'od.harga_satuan', 'od.subtotal', 'k.varian', 'k.satuan')
            ->where('od.id_order', $invoice->id_order)
            ->get();

        // Generate PDF dan download
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
        // Ambil data profil langsung dari tabel users
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
        // Validasi input profil
        $data = $request->validate([
            'nama_cust' => ['required', 'string', 'max:100'],
            'notelp_cust' => ['required', 'digits_between:6,20'],
            'alamat_cust' => ['nullable', 'string', 'max:1000'],
        ]);

        // Update model User dan perbarui session nama
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
        // Helper mengembalikan koleksi order lengkap untuk customer beserta detail dan pembayaran
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
