<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Helpers\CleanGoHelper as CG;
use App\Models\AppSetting;
use App\Models\Katalog;
use App\Models\Layanan;
use App\Models\Order;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class OwnerController extends Controller
{
    // Ambil user id (owner) dari session
    private function id()   { return Session::get('user_id'); }
    // Ambil nama owner dari session
    private function nama() { return Session::get('nama'); }

    private function allOrders()
    {
        // Helper: query semua order untuk view owner
        return DB::table('orders as o')
            ->join('layanan as l', 'l.id_layanan', '=', 'o.id_layanan')
            ->join('users as u', 'u.id_cust', '=', 'o.id_cust')
            ->leftJoin('staff as s', 's.id_staff', '=', 'o.id_staff')
            ->leftJoin('pembayaran as p', 'p.id_order', '=', 'o.id_order')
            ->select('o.*', 'l.nama_layanan', 'u.nama_cust', 'u.notelp_cust',
                     's.nama as nama_staff', 'p.jumlah as jumlah_bayar', 'p.status_bayar')
            ->orderByDesc('o.tanggal_pesan')->get();
    }

    /** Helper: ambil semua settings sebagai key=>value */
    private function settings()
    {
        // Mengambil app settings yang disimpan di DB sebagai key=>value
        return DB::table('app_settings')->pluck('value', 'key')->toArray();
    }

    public function dashboard()
    {
        // Dashboard owner: ringkasan order, omzet, dll.
        $id     = $this->id();
        $orders = $this->allOrders();
        $staffList = DB::table('staff')->orderBy('nama')->get();

        return view('owner.index', [
            'page'      => 'dashboard',
            'ownerName' => $this->nama(),
            'allOrders' => $orders,
            'staffList' => $staffList,
            'totalOrder'   => $orders->count(),
            'orderAktif'   => $orders->filter(fn($o) => !in_array($o->status_order, ['Selesai','Dibatalkan']))->count(),
            'orderSelesai' => $orders->filter(fn($o) => $o->status_order === 'Selesai')->count(),
            'totalOmzet'   => $orders->filter(fn($o) => $o->status_bayar === 'Lunas')->sum('jumlah_bayar'),
            'unreadCount'  => CG::countUnread('owner', $id),
        ]);
    }

    public function semuaOrder(Request $request)
    {
        // Halaman semua order: mendukung filtering dan peninjauan detail serta tracking order
        $id      = $this->id();
        $orders  = $this->allOrders();
        $fStatus = $request->query('status', '');
        $selId   = (int)$request->query('id', 0);
        $selOrder = $selTracking = null;

        if ($selId) {
            // Ambil detail order jika id order dipilih dari UI
            $selOrder = DB::table('orders as o')
                ->join('layanan as l','l.id_layanan','=','o.id_layanan')
                ->join('users as u','u.id_cust','=','o.id_cust')
                ->leftJoin('staff as s','s.id_staff','=','o.id_staff')
                ->leftJoin('order_detail as od','od.id_order','=','o.id_order')
                ->leftJoin('katalog as k','k.id_katalog','=','od.id_katalog')
                ->leftJoin('pembayaran as p','p.id_order','=','o.id_order')
                ->select('o.*','l.nama_layanan','u.nama_cust','u.notelp_cust','u.alamat_cust',
                         's.nama as nama_staff','od.berat','od.qty','od.subtotal',
                         'k.varian','k.satuan','p.jumlah as jumlah_bayar','p.status_bayar','p.metode')
                ->where('o.id_order', $selId)->first();
            if ($selOrder) {
                // Ambil history tracking untuk order yang dipilih
                $selTracking = DB::table('tracking')->where('id_order',$selId)->orderBy('waktu_update')->get();
            }
        }

        return view('owner.index', [
            'page'       => 'semua_order',
            'ownerName'  => $this->nama(),
            'allOrders'  => $orders,
            'fStatus'    => $fStatus,
            'selId'      => $selId,
            'selOrder'   => $selOrder,
            'selTracking'=> $selTracking,
            'unreadCount'=> CG::countUnread('owner', $id),
        ]);
    }

    public function batalkanOrder(Request $request)
    {
        // Cancel order oleh owner: update status order dan tulis history tracking
        $id      = $this->id();
        $idOrder = (int)$request->input('id_order', 0);

        DB::transaction(function () use ($idOrder) {
            DB::table('orders')->where('id_order', $idOrder)->update(['status_order' => 'Dibatalkan', 'updated_at' => now()]);
            DB::table('tracking')->insert(['id_order' => $idOrder, 'status' => 'Dibatalkan', 'keterangan' => 'Dibatalkan oleh owner', 'waktu_update' => now()]);
        });

        // Ambil info customer untuk notifikasi
        $oi = DB::table('orders as o')->join('users as u','u.id_cust','=','o.id_cust')
            ->select('o.kode_order','u.id_cust')->where('o.id_order',$idOrder)->first();
        if ($oi) {
            // Beri tahu customer dan staff bahwa order dibatalkan
            CG::sendNotification('customer',$oi->id_cust,'❌ Order Dibatalkan',
                "Order {$oi->kode_order} dibatalkan oleh pengelola.",route('customer.riwayat'));
            CG::notifyAllStaff('❌ Order Dibatalkan',"Owner membatalkan order {$oi->kode_order}.",route('staff.order_masuk'));
        }
        return redirect()->route('owner.semua_order')->with('flash','Order berhasil dibatalkan.');
    }

    public function katalog()
    {
        $id = $this->id();
        return view('owner.index', [
            'page'        => 'katalog',
            'ownerName'   => $this->nama(),
            // Ambil semua katalog aktif termasuk nama layanan, lalu buang duplikat dengan kombinasi layanan|varian|harga
            'katalogList' => DB::table('katalog as k')->join('layanan as l','l.id_layanan','=','k.id_layanan')
                ->select('k.*','l.nama_layanan')->orderBy('l.id_layanan')->orderBy('k.varian')->get()
                ->unique(fn($it) => ($it->id_layanan ?? '') . '|' . ($it->varian ?? '') . '|' . ($it->harga ?? ''))
                ->values(),
            'layananList' => DB::table('layanan')->orderBy('id_layanan')->get(),
            'settings'    => $this->settings(),
            'unreadCount' => CG::countUnread('owner', $id),
        ]);
    }

    public function storeKatalog(Request $request)
    {
        // Validasi input katalog termasuk file image yang di-upload
        $data = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'id_layanan' => ['required', 'integer', 'exists:layanan,id_layanan'],
            'varian' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        // Upload foto ke disk publik dan simpan path file ke DB
        $fotoPath = $request->file('foto')->store('katalog', 'public');

        Katalog::create([
            'id_layanan' => $data['id_layanan'],
            'jenis_layanan' => '',
            'varian' => $data['varian'],
            'harga' => (float)$data['harga'],
            'satuan' => $data['satuan'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'foto' => $fotoPath,
            'status' => $data['status'],
        ]);

        return redirect()->route('owner.katalog')->with('flash', 'Katalog baru berhasil ditambahkan.');
    }

    public function updateKatalog(Request $request, $id)
    {
        // Validasi update katalog; foto adalah opsional saat edit
        $data = $request->validate([
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'id_layanan' => ['required', 'integer', 'exists:layanan,id_layanan'],
            'varian' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        $updateData = [
            'id_layanan' => $data['id_layanan'],
            'varian' => $data['varian'],
            'harga' => (float)$data['harga'],
            'satuan' => $data['satuan'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'status' => $data['status'],
            'updated_at' => now(),
        ];

        // Jika foto baru di-upload, hapus file lama untuk menghindari file orphan
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $old = Katalog::where('id_katalog', $id)->value('foto');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $updateData['foto'] = $request->file('foto')->store('katalog', 'public');
        }

        Katalog::where('id_katalog', $id)->update($updateData);
        return redirect()->route('owner.katalog')->with('flash', 'Katalog berhasil diperbarui.');
    }

    public function deleteKatalog($id)
    {
        // Cek apakah katalog masih terkait dengan order
        $orderCount = DB::table('order_detail')->where('id_katalog', $id)->count();
        if ($orderCount > 0) {
            return redirect()->route('owner.katalog')->withErrors([
                'status' => 'Katalog ini tidak bisa dihapus karena sudah digunakan di order. Hapus order yang terkait terlebih dahulu.',
            ]);
        }

        // Hapus file foto lama jika ada
        $old = Katalog::where('id_katalog', $id)->value('foto');
        if ($old) Storage::disk('public')->delete($old);

        // Hapus entri katalog dari database
        Katalog::where('id_katalog', $id)->delete();
        return redirect()->route('owner.katalog')->with('flash', 'Katalog dihapus.');
    }

    /** Upload foto antar jemput & update teks settings */
    public function updateSettings(Request $request)
    {
        // Jika ada file foto antar jemput, simpan file baru dan hapus file lama
        if ($request->hasFile('antar_jemput_foto') && $request->file('antar_jemput_foto')->isValid()) {
            $old = AppSetting::where('key', 'antar_jemput_foto')->value('value');
            if ($old) Storage::disk('public')->delete($old);

            $path = $request->file('antar_jemput_foto')->store('settings', 'public');
            AppSetting::where('key', 'antar_jemput_foto')->update(['value' => $path]);
        }

        // Update nilai teks setting jika field diisi
        foreach (['antar_jemput_judul', 'antar_jemput_desc'] as $key) {
            if ($request->filled($key)) {
                AppSetting::where('key', $key)->update(['value' => $request->input($key)]);
            }
        }

        return redirect()->route('owner.katalog')->with('flash', 'Setting Antar Jemput berhasil disimpan.');
    }

    public function layanan()
    {
        $id = $this->id();
        return view('owner.index', [
            'page'        => 'layanan',
            'ownerName'   => $this->nama(),
            'layananList' => DB::table('layanan')->orderBy('id_layanan')->get(),
            'unreadCount' => CG::countUnread('owner', $id),
        ]);
    }

    public function storeLayanan(Request $request)
    {
        // Validasi dan simpan layanan baru
        $request->validate([
            'nama_layanan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Layanan::create([
            'nama_layanan' => $request->input('nama_layanan'),
            'deskripsi' => $request->input('deskripsi'),
            'is_active' => 1,
        ]);

        return redirect()->route('owner.layanan')->with('flash', 'Layanan baru ditambahkan.');
    }

    public function updateLayanan(Request $request, $id)
    {
        // Validasi data update layanan
        $request->validate([
            'nama_layanan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'in:0,1'],
        ]);

        // Update record layanan berdasarkan id
        Layanan::where('id_layanan', $id)->update([
            'nama_layanan' => $request->input('nama_layanan'),
            'deskripsi' => $request->input('deskripsi'),
            'is_active' => (int)$request->input('status', 1),
        ]);

        return redirect()->route('owner.layanan')->with('flash', 'Layanan berhasil diperbarui.');
    }

    public function deleteLayanan($id)
    {
        // Pastikan layanan tidak memiliki katalog terkait sebelum dihapus
        $katalogCount = DB::table('katalog')->where('id_layanan', $id)->count();
        if ($katalogCount > 0) {
            return redirect()->route('owner.layanan')->withErrors([
                'status' => 'Untuk menghapus layanan ini, hapus dahulu semua katalog yang terkait dengan layanan tersebut. Jumlah katalog terkait: ' . $katalogCount,
            ]);
        }

        DB::table('layanan')->where('id_layanan', $id)->delete();
        return redirect()->route('owner.layanan')->with('flash', 'Layanan dihapus.');
    }

    public function staff()
    {
        $id = $this->id();
        return view('owner.index', [
            'page'       => 'staff',
            'ownerName'  => $this->nama(),
            'staffList'  => DB::table('staff')->orderBy('nama')->get(),
            'unreadCount'=> CG::countUnread('owner', $id),
        ]);
    }

    public function storeStaff(Request $request)
    {
        // Validasi input staff baru
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('staff', 'username')],
            'notelp' => ['required', 'digits_between:6,20'],
            'sandi' => ['required', 'string', 'min:6'],
            'alamat' => ['nullable', 'string', 'max:1000'],
        ], [
            'notelp.digits_between' => 'No. Telepon harus berisi angka antara 6 sampai 20 digit.',
        ]);

        // Simpan staff baru dengan password terenkripsi
        Staff::create([
            'nama' => trim($data['nama']),
            'username' => trim($data['username']),
            'notelp' => trim($data['notelp']),
            'sandi' => Hash::make($data['sandi']),
            'alamat' => trim($data['alamat'] ?? ''),
            'is_active' => 1,
        ]);

        return redirect()->route('owner.staff')->with('flash', 'Staff baru berhasil ditambahkan.');
    }

    public function editStaff($id)
    {
        // Ambil data staff untuk diedit. Jika tidak ditemukan, kembali ke daftar staff.
        $staff = DB::table('staff')->where('id_staff', $id)->first();
        if (!$staff) return redirect()->route('owner.staff')->with('flash', 'Staff tidak ditemukan.');

        return view('owner.index', [
            'page'       => 'staff_edit',
            'ownerName'  => $this->nama(),
            'staff'      => $staff,
            'unreadCount'=> \App\Helpers\CleanGoHelper::countUnread('owner', $this->id()),
        ]);
    }

    public function updateStaff(Request $request, $id)
    {
        // Validasi update data staff dan username unik kecuali untuk current id
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('staff', 'username')->ignore($id, 'id_staff')],
            'notelp' => ['required', 'digits_between:6,20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'in:0,1'],
        ], [
            'notelp.digits_between' => 'No. Telepon harus berisi angka antara 6 sampai 20 digit.',
        ]);

        $update = [
            'nama' => trim($data['nama']),
            'username' => trim($data['username']),
            'notelp' => trim($data['notelp']),
            'alamat' => trim($data['alamat'] ?? ''),
            'is_active' => $data['is_active'] ?? 1,
            'updated_at' => now(),
        ];

        // Jika password baru diisi, enkripsi dan masukkan ke update array
        if ($request->filled('sandi')) {
            $request->validate(['sandi' => 'string|min:6'], ['sandi.min' => 'Password minimal 6 karakter.']);
            $update['sandi'] = Hash::make($request->input('sandi'));
        }

        Staff::where('id_staff', $id)->update($update);

        return redirect()->route('owner.staff')->with('flash', 'Data staff berhasil diperbarui.');
    }

    public function deleteStaff($id)
    {
        DB::table('staff')->where('id_staff', $id)->delete();
        return redirect()->route('owner.staff')->with('flash', 'Staff berhasil dihapus.');
    }

    public function invoice()
    {
        $id = $this->id();
        // Ambil daftar invoice terbaru dengan informasi order dan customer
        $invoices = DB::table('invoice as i')
            ->join('pembayaran as p','p.id_bayar','=','i.id_bayar')
            ->join('orders as o','o.id_order','=','p.id_order')
            ->join('users as u','u.id_cust','=','o.id_cust')
            ->select('i.*','p.jumlah','p.metode','o.kode_order','u.nama_cust')
            ->orderByDesc('i.tgl_invoice')->limit(30)->get();

        return view('owner.index', [
            'page'       => 'invoice',
            'ownerName'  => $this->nama(),
            'invoices'   => $invoices,
            'unreadCount'=> CG::countUnread('owner', $id),
        ]);
    }

    public function printInvoice($id)
    {
        // Ambil satu invoice lengkap untuk preview cetak
        $invoice = DB::table('invoice as i')
            ->join('pembayaran as p', 'p.id_bayar', '=', 'i.id_bayar')
            ->join('orders as o', 'o.id_order', '=', 'p.id_order')
            ->join('order_detail as od', 'od.id_order', '=', 'o.id_order')
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
                'u.notelp_cust'
            )
            ->where('i.id_invoice', $id)
            ->first();

        // Jika invoice tidak ditemukan, tampilkan 404
        if (!$invoice) {
            abort(404);
        }

        return view('invoice.print', [
            'invoice' => $invoice,
            'downloadRoute' => route('owner.invoice.download', $id),
            'backRoute' => route('owner.invoice'),
        ]);
    }

    public function downloadInvoice($id)
    {
        // Ambil invoice dan data order untuk generate PDF nota
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
                'o.id_order'
            )
            ->where('i.id_invoice', $id)
            ->first();

        if (!$invoice) {
            abort(404);
        }

        // Ambil detail order untuk dimasukkan ke PDF
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

    public function laporan()
    {
        $id = $this->id();
        // Hitung laporan per bulan, jumlah order, omzet lunas, dan order selesai
        $laporan = DB::table('orders as o')
            ->leftJoin('pembayaran as p', function($join) {
                $join->on('p.id_order', '=', 'o.id_order')
                     ->where('p.status_bayar', '=', 'Lunas');
            })
            ->selectRaw("DATE_FORMAT(o.tanggal_pesan,'%Y-%m') as bulan, COUNT(*) as total_order, COALESCE(SUM(p.jumlah),0) as total_omzet, SUM(CASE WHEN o.status_order='Selesai' THEN 1 ELSE 0 END) as selesai")
            ->groupBy('bulan')->orderByDesc('bulan')->limit(12)->get();

        return view('owner.index', [
            'page'        => 'laporan',
            'ownerName'   => $this->nama(),
            'laporanBulan'=> $laporan,
            'maxOmzet'    => $laporan->max('total_omzet') ?: 1,
            'unreadCount' => CG::countUnread('owner', $id),
        ]);
    }

    /**
     * Export laporan ke PDF menggunakan barryvdh/laravel-dompdf
     */
    public function laporanPdf()
    {
        $laporan = DB::table('orders as o')
            ->leftJoin('pembayaran as p', function($join) {
                $join->on('p.id_order', '=', 'o.id_order')
                     ->where('p.status_bayar', '=', 'Lunas');
            })
            ->selectRaw("DATE_FORMAT(o.tanggal_pesan,'%Y-%m') as bulan, COUNT(*) as total_order, COALESCE(SUM(p.jumlah),0) as total_omzet, SUM(CASE WHEN o.status_order='Selesai' THEN 1 ELSE 0 END) as selesai")
            ->groupBy('bulan')->orderByDesc('bulan')->limit(12)->get();

        $pdf = Pdf::loadView('owner.pdf.laporan', [
            'laporanBulan' => $laporan,
            'maxOmzet'     => $laporan->max('total_omzet') ?: 1,
            'ownerName'    => $this->nama(),
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-cleango-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export laporan ke Excel menggunakan maatwebsite/excel
     */
    public function laporanExcel()
    {
        $filename = 'laporan-cleango-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new LaporanExport(), $filename);
    }
}
