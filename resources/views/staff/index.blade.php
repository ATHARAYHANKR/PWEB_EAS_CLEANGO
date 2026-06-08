@extends('layouts.app')

@section('title', 'Staff - CleanGo')
@section('topbar-title')
  @php $titles = ['dashboard'=>'Dashboard Staff','order_masuk'=>'Order Masuk','kelola_order'=>'Kelola Order','status_laundry'=>'Update Status Laundry','konfirmasi_bayar'=>'Konfirmasi Pembayaran','history'=>'History Order Selesai','profil'=>'Profil Saya']; @endphp
  {{ $titles[$page] ?? 'Dashboard' }}
@endsection

@section('content')
{{-- Halaman utama Staff: menampilkan dashboard dan akses ke fitur order seperti ambil order,
     verifikasi berat, update status, konfirmasi pembayaran, history, dan profil. --}}

{{-- ═══ DASHBOARD ═══ --}}
@if($page === 'dashboard')
{{-- ── STAT CARDS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-7">
  <div class="bg-white rounded-2xl border border-amber-200/60 p-5 flex flex-col gap-3 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold text-amber-500 uppercase tracking-wider">Order Masuk</span>
      <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 text-sm">
        <i class="fas fa-inbox"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-amber-600 leading-none">{{ $masuk->count() }}</div>
    <div class="text-[11px] text-slate-400">Menunggu diambil</div>
  </div>
  <div class="bg-white rounded-2xl border border-blue-200/60 p-5 flex flex-col gap-3 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Diproses</span>
      <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-sm">
        <i class="fas fa-sync-alt"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-blue-600 leading-none">{{ $diproses->count() }}</div>
    <div class="text-[11px] text-slate-400">Sedang dikerjakan</div>
  </div>
  <div class="bg-white rounded-2xl border border-orange-200/60 p-5 flex flex-col gap-3 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold text-orange-500 uppercase tracking-wider">Perlu Timbang</span>
      <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 text-sm">
        <i class="fas fa-weight"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-orange-600 leading-none">{{ $needWeight->count() }}</div>
    <div class="text-[11px] text-slate-400">Verifikasi berat</div>
  </div>
  <div class="bg-cyan-700 rounded-2xl p-5 flex flex-col gap-3 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold text-cyan-200 uppercase tracking-wider">Konfirmasi Bayar</span>
      <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white text-sm">
        <i class="fas fa-credit-card"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-white leading-none">{{ $konfBayar->count() }}</div>
    <div class="text-[11px] text-cyan-200">Menunggu konfirmasi</div>
  </div>
</div>

{{-- ── CONTENT PANELS ── --}}
{{-- Panel ringkasan order menunggu dan pembayaran menunggu.
     Data disediakan oleh controller: $masuk untuk order baru dan $konfBayar untuk pembayaran. --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

@if($masuk->count())
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
    <div>
      <div class="text-sm font-bold text-slate-900 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
        Order Menunggu Konfirmasi
      </div>
      <div class="text-[11px] text-slate-400 mt-0.5">{{ $masuk->count() }} order baru masuk</div>
    </div>
  </div>
  @foreach($masuk as $o)
  <div class="px-5 py-4 border-b border-slate-50 hover:bg-slate-50/70 transition">
    <div class="flex items-start justify-between gap-3">
      <div class="min-w-0">
        <div class="font-mono text-sm font-bold text-slate-800">{{ $o->kode_order }}</div>
        <div class="text-xs text-slate-500 mt-0.5">{{ $o->nama_cust }} • {{ $o->nama_layanan }}</div>
      </div>
      <form method="POST" action="{{ route('staff.ambil_order') }}" class="shrink-0">
        @csrf
        <input type="hidden" name="id_order" value="{{ $o->id_order }}">
        <button type="submit" data-confirm-title="Ambil Order" data-confirm-message="Apakah Anda yakin ingin mengambil order ini?"
          class="inline-flex items-center gap-1.5 text-xs bg-cyan-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-cyan-700 transition">
          <i class="fas fa-car text-[10px]"></i> Ambil
        </button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8 flex flex-col items-center justify-center text-center">
  <div class="w-12 h-12 rounded-2xl bg-cyan-50 flex items-center justify-center text-cyan-500 text-xl mb-3">
    <i class="fas fa-check-circle"></i>
  </div>
  <div class="text-sm font-semibold text-slate-700">Tidak Ada Order Masuk</div>
  <div class="text-xs text-slate-400 mt-1">Semua order sudah diambil</div>
</div>
@endif

@if($konfBayar->count())
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100">
    <div class="text-sm font-bold text-slate-900 flex items-center gap-2">
      <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
      Konfirmasi Pembayaran
    </div>
    <div class="text-[11px] text-slate-400 mt-0.5">{{ $konfBayar->count() }} pembayaran menunggu</div>
  </div>
  @foreach($konfBayar as $p)
  <div class="px-5 py-4 border-b border-slate-50 hover:bg-slate-50/70 transition">
    <div class="flex items-start justify-between gap-3">
      <div class="min-w-0">
        <div class="font-mono text-sm font-bold text-slate-800">{{ $p->kode_order }}</div>
        <div class="text-xs text-slate-500 mt-0.5">{{ $p->nama_cust }}</div>
        <div class="text-sm font-bold text-emerald-600 mt-1">Rp {{ number_format($p->jumlah,0,',','.') }}</div>
      </div>
      <form method="POST" action="{{ route('staff.konfirmasi_bayar.do') }}" class="shrink-0">
        @csrf
        <input type="hidden" name="id_bayar" value="{{ $p->id_bayar }}">
        <button type="submit" data-confirm-title="Konfirmasi Pembayaran" data-confirm-message="Konfirmasi pembayaran order ini sebagai lunas?"
          class="inline-flex items-center gap-1.5 text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-blue-700 transition">
          <i class="fas fa-check text-[10px]"></i> Konfirmasi
        </button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8 flex flex-col items-center justify-center text-center">
  <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 text-xl mb-3">
    <i class="fas fa-check-double"></i>
  </div>
  <div class="text-sm font-semibold text-slate-700">Tidak Ada Pembayaran Pending</div>
  <div class="text-xs text-slate-400 mt-1">Semua pembayaran sudah dikonfirmasi</div>
</div>
@endif

</div>

{{-- ═══ ORDER MASUK ═══ --}}
{{-- Halaman order masuk: staff memilih order untuk mulai diproses.
     Controller menyediakan $masuk sebagai daftar order belum diambil. --}}
@elseif($page === 'order_masuk')
@if($masuk->count())
<div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 flex items-center gap-3">
  <i class="fas fa-inbox"></i>
  <span>Ada {{ $masuk->count() }} order baru menunggu konfirmasi. Segera ambil order untuk diproses.</span>
</div>
@endif
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100"><span class="font-semibold">Order Menunggu Konfirmasi ({{ $masuk->count() }})</span></div>
  @forelse($masuk as $o)
  <div class="px-5 py-4 border-b border-slate-50 hover:bg-slate-50 transition">
    <div class="flex items-start justify-between">
      <div>
        <div class="font-mono font-bold text-slate-800">{{ $o->kode_order }}</div>
        <div class="text-sm text-slate-600 mt-0.5">{{ $o->nama_cust }} • {{ $o->notelp_cust }}</div>
        <div class="text-xs text-slate-500">{{ $o->nama_layanan }} • Jadwal: {{ $o->jadwal_jemput ? \Carbon\Carbon::parse($o->jadwal_jemput)->format('d M Y H:i') : '-' }}</div>
        @if($o->catatan)<div class="text-xs italic text-slate-400 mt-0.5">"{{ $o->catatan }}"</div>@endif
      </div>
      <form method="POST" action="{{ route('staff.ambil_order') }}">
        @csrf
        <input type="hidden" name="id_order" value="{{ $o->id_order }}">
        <button type="submit" data-confirm-title="Ambil Order" data-confirm-message="Apakah Anda yakin ingin mengambil order ini?" class="inline-flex items-center gap-2 text-xs bg-cyan-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-cyan-700 transition">
          <i class="fas fa-car text-[10px]"></i>Ambil Order
        </button>
      </form>
    </div>
  </div>
  @empty
  <div class="py-16 text-center text-slate-400">Tidak ada order masuk saat ini ✅</div>
  @endforelse
</div>

{{-- ═══ KELOLA ORDER ═══ --}}
{{-- Halaman kelola order: isi berat atau qty untuk order yang sudah diambil.
     Mendukung order dengan status perlu timbang dan order lunas yang siap diproses. --}}
@elseif($page === 'kelola_order')
@if($needWeight->count() || $paid->count())
<div class="mb-4 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
  <div class="flex flex-col gap-1">
    <div class="font-semibold">Notifikasi Kelola Order</div>
    <div>
      @if($needWeight->count())
      <span class="font-semibold">{{ $needWeight->count() }} order</span> perlu verifikasi berat.
      @endif
      @if($paid->count())
      <span class="font-semibold">{{ $paid->count() }} order</span> sudah lunas dan siap diproses.
      @endif
    </div>
  </div>
</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
  <div class="lg:col-span-2 space-y-3">
    @if($needWeight->count())
    <h4 class="text-xs font-bold text-orange-600 uppercase tracking-wider">⚖️ Perlu Verifikasi Berat ({{ $needWeight->count() }})</h4>
    @foreach($needWeight as $o)
    <a href="{{ route('staff.kelola_order', ['id' => $o->id_order]) }}"
       class="block bg-orange-50 border {{ isset($selOrder) && $selOrder->id_order == $o->id_order ? 'border-orange-400 ring-2 ring-orange-100' : 'border-orange-200' }} rounded-2xl p-3 hover:border-orange-300 transition">
      <div class="font-mono text-xs font-bold text-slate-700">{{ $o->kode_order }}</div>
      <div class="text-xs text-slate-500">{{ $o->nama_cust }} • {{ $o->nama_layanan }}</div>
    </a>
    @endforeach
    @endif

    @if($paid->count())
    <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider mt-3">✅ Sudah Lunas — Siap Diproses ({{ $paid->count() }})</h4>
    @foreach($paid as $o)
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-3">
      <div class="font-mono text-xs font-bold text-slate-700">{{ $o->kode_order }}</div>
      <div class="text-xs text-slate-500">{{ $o->nama_cust }} • {{ $o->status_order }}</div>
    </div>
    @endforeach
    @endif

    @if(!$needWeight->count() && !$paid->count())
    <div class="text-center py-10 text-slate-400 text-sm">Tidak ada order aktif</div>
    @endif
  </div>

  <div class="lg:col-span-3">
    @if(isset($selOrder) && $selOrder)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <h3 class="font-bold text-slate-800 mb-4">⚖️ Verifikasi Berat / Qty — {{ $selOrder->kode_order }}</h3>
      <div class="bg-slate-50 rounded-xl p-3 text-xs mb-4 space-y-1">
        <div class="flex justify-between"><span class="text-slate-400">Customer</span><strong>{{ $selOrder->nama_cust }}</strong></div>
        <div class="flex justify-between"><span class="text-slate-400">Layanan</span><strong>{{ $selOrder->nama_layanan }}</strong></div>
        <div class="flex justify-between"><span class="text-slate-400">Paket</span><strong>{{ $selOrder->varian ?? '-' }}</strong></div>
        <div class="flex justify-between"><span class="text-slate-400">Harga Satuan</span><strong>Rp {{ number_format($selOrder->harga_katalog ?? $selOrder->harga_od,0,',','.') }}/{{ $selOrder->satuan ?? 'kg' }}</strong></div>
      </div>

      <form method="POST" action="{{ route('staff.set_berat') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="id_order" value="{{ $selOrder->id_order }}">
        <input type="hidden" name="id_katalog" value="{{ $selOrder->id_katalog }}">
        <input type="hidden" name="satuan" value="{{ $selOrder->satuan ?? 'kg' }}">
        <input type="hidden" name="harga_satuan" value="{{ $selOrder->harga_katalog ?? $selOrder->harga_od }}">

        @if(($selOrder->satuan ?? 'kg') === 'kg')
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Berat (kg)</label>
          <input type="number" name="berat" step="0.1" min="0.1" required placeholder="Contoh: 3.5"
            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>
        @else
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Jumlah (pcs)</label>
          <input type="number" name="qty" min="1" required placeholder="Contoh: 2"
            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>
        @endif

        <button type="submit" class="w-full py-2.5 bg-cyan-700 text-white font-semibold rounded-xl text-sm hover:bg-cyan-800 transition inline-flex items-center justify-center gap-2">
          <i class="fas fa-check text-[11px]"></i>Simpan & Kirim Tagihan ke Customer
        </button>
      </form>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
      <div class="text-4xl mb-3">⚖️</div>
      <div class="text-slate-400 text-sm">Pilih order untuk mengisi berat/qty</div>
    </div>
    @endif
  </div>
</div>

{{-- ═══ STATUS LAUNDRY ═══ --}}
@elseif($page === 'status_laundry')
@php $statusFlow = ['Dijemput'=>'Dicuci','Dicuci'=>'Disetrika','Disetrika'=>'Dikirim','Dikirim'=>'Selesai']; @endphp
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
  <div class="lg:col-span-2 space-y-3">
    @forelse($paid as $o)
    <a href="{{ route('staff.status_laundry', ['id' => $o->id_order]) }}"
       class="block bg-white border {{ isset($selOrder) && $selOrder->id_order == $o->id_order ? 'border-blue-400 ring-2 ring-blue-100' : 'border-slate-100' }} rounded-2xl p-3 shadow-sm hover:border-blue-300 transition">
      <div class="flex items-center justify-between">
        <div>
          <div class="font-mono text-xs font-bold text-slate-700">{{ $o->kode_order }}</div>
          <div class="text-xs text-slate-500">{{ $o->nama_cust }}</div>
        </div>
        <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">{{ $o->status_order }}</span>
      </div>
    </a>
    @empty
    <div class="text-center py-10 text-slate-400 text-sm">Tidak ada order yang siap diproses.<br><span class="text-xs">Order perlu sudah lunas terlebih dahulu.</span></div>
    @endforelse
  </div>

  <div class="lg:col-span-3">
    @if(isset($selOrder) && $selOrder)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <h3 class="font-bold text-slate-800 mb-4">📦 Update Status — {{ $selOrder->kode_order }}</h3>
      <div class="bg-slate-50 rounded-xl p-3 text-xs mb-5 space-y-1">
        <div class="flex justify-between"><span class="text-slate-400">Customer</span><strong>{{ $selOrder->nama_cust }}</strong></div>
        <div class="flex justify-between"><span class="text-slate-400">Layanan</span><strong>{{ $selOrder->nama_layanan }}</strong></div>
        <div class="flex justify-between"><span class="text-slate-400">Status Sekarang</span>
          <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">{{ $selOrder->status_order }}</span>
        </div>
      </div>

      @if(isset($nextStatusMap[$selOrder->status_order]))
      <form method="POST" action="{{ route('staff.advance_status') }}">
        @csrf
        <input type="hidden" name="id_order" value="{{ $selOrder->id_order }}">
        <input type="hidden" name="new_status" value="{{ $nextStatusMap[$selOrder->status_order] }}">
        <button type="submit" data-confirm-title="Update Status Laundry" data-confirm-message="Ubah status order ini menjadi {{ $nextStatusMap[$selOrder->status_order] }}?" class="w-full py-2.5 bg-blue-700 text-white font-semibold rounded-xl text-sm hover:bg-blue-800 transition inline-flex items-center justify-center gap-2">
          <i class="fas fa-arrow-right mr-2"></i>Lanjut ke: <strong>{{ $nextStatusMap[$selOrder->status_order] }}</strong>
        </button>
      </form>
      @else
      <div class="text-center text-emerald-600 font-semibold py-4">✅ Order sudah selesai</div>
      @endif
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
      <div class="text-4xl mb-3">📦</div>
      <div class="text-slate-400 text-sm">Pilih order untuk mengupdate status</div>
    </div>
    @endif
  </div>
</div>

{{-- ═══ KONFIRMASI BAYAR ═══ --}}
@elseif($page === 'konfirmasi_bayar')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100"><span class="font-semibold">Menunggu Konfirmasi Pembayaran ({{ $konfBayar->count() }})</span></div>
  @forelse($konfBayar as $p)
  <div class="px-5 py-4 border-b border-slate-50 hover:bg-slate-50 transition flex items-center justify-between">
    <div>
      <div class="font-mono font-bold text-slate-800">{{ $p->kode_order }}</div>
      <div class="text-sm text-slate-600">{{ $p->nama_cust }}</div>
      <div class="text-xs text-slate-400">{{ $p->nama_layanan }} • Rp {{ number_format($p->jumlah,0,',','.') }}</div>
    </div>
    <form method="POST" action="{{ route('staff.konfirmasi_bayar.do') }}">
      @csrf
      <input type="hidden" name="id_bayar" value="{{ $p->id_bayar }}">
      <button type="submit" data-confirm-title="Konfirmasi Lunas" data-confirm-message="Apakah Anda yakin pembayaran ini sudah lunas dan siap dikonfirmasi?" class="inline-flex items-center gap-2 text-xs bg-cyan-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-cyan-700 transition">
        <i class="fas fa-check text-[10px]"></i>Konfirmasi Lunas
      </button>
    </form>
  </div>
  @empty
  <div class="py-16 text-center text-slate-400">Tidak ada pembayaran yang menunggu konfirmasi ✅</div>
  @endforelse
</div>

{{-- ═══ HISTORY ═══ --}}
@elseif($page === 'history')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100"><span class="font-semibold">Order Selesai oleh Saya ({{ $selesai->count() }})</span></div>
  @forelse($selesai as $o)
  <div class="px-5 py-4 border-b border-slate-50 hover:bg-slate-50 transition">
    <div class="flex items-center justify-between">
      <div>
        <span class="font-mono text-sm font-bold">{{ $o->kode_order }}</span>
        <span class="ml-2 text-xs text-slate-500">{{ $o->nama_cust }} • {{ $o->nama_layanan }}</span>
      </div>
      <div class="text-right">
        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">Selesai</span>
        <div class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($o->updated_at)->format('d M Y') }}</div>
      </div>
    </div>
  </div>
  @empty
  <div class="py-16 text-center text-slate-400">Belum ada order yang diselesaikan</div>
  @endforelse
</div>

{{-- ═══ PROFIL ═══ --}}
@elseif($page === 'profil')
<div class="max-w-2xl mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md:p-8">
  <h3 class="text-lg font-bold text-slate-800 mb-6"><i class="fas fa-user-circle text-blue-500 mr-2"></i>Profil Saya</h3>

  @if($errors->any())
  <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm">
    <ul class="list-disc list-inside space-y-1">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  @if(session('flash'))
  <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
    {!! session('flash') !!}
  </div>
  @endif

  <form method="POST" action="{{ route('staff.profil.update') }}" class="space-y-5">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <!-- Nama -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $staff->nama ?? '') }}" required maxlength="100" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
      </div>

      <!-- Nomor Telepon -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
        <input type="text" name="notelp" value="{{ old('notelp', $staff->notelp ?? '') }}" required pattern="[0-9]{6,20}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
      </div>
    </div>

    <!-- Alamat -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat (Opsional)</label>
      <textarea name="alamat" maxlength="1000" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm resize-none">{{ old('alamat', $staff->alamat ?? '') }}</textarea>
    </div>

    <!-- Password Section -->
    <div class="border-t border-slate-200 pt-5">
      <h4 class="text-sm font-semibold text-slate-700 mb-4">Ubah Password (Opsional)</h4>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Password Baru -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">Password Baru</label>
          <input type="password" name="sandi" minlength="6" maxlength="100" placeholder="Biarkan kosong jika tidak ingin mengubah" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
        </div>

        <!-- Konfirmasi Password -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
          <input type="password" name="sandi_confirm" minlength="6" maxlength="100" placeholder="Ulangi password baru" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
        </div>
      </div>
    </div>

    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
      <i class="fas fa-info-circle mr-2"></i>
      <strong>Catatan:</strong> Data profil akan diperbarui setelah Anda menyimpan formulir ini.
    </div>

    <!-- Buttons -->
    <div class="flex gap-3 pt-4 border-t border-slate-200">
      <button type="submit" class="flex-1 py-3 bg-blue-700 text-white font-semibold rounded-lg text-sm hover:bg-blue-800 transition inline-flex items-center justify-center gap-2">
        <i class="fas fa-save mr-2"></i>Simpan Perubahan
      </button>
      <a href="{{ route('staff.dashboard') }}" class="px-6 py-3 bg-slate-200 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-300 transition">
        <i class="fas fa-times mr-1"></i>Batal
      </a>
    </div>
  </form>
</div>
@endif

@endsection
