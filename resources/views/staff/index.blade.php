@extends('layouts.app')

@section('title', 'Staff - CleanGo')
@section('sidebar-icon')<i class="fas fa-user-tie"></i>@endsection
@section('sidebar-panel')Panel Staff@endsection

@section('sidebar-nav')
@php
$staffCount = isset($masuk) ? $masuk->count() : 0;
$confirmCount = isset($konfBayar) ? $konfBayar->count() : 0;
$kelolaCount = isset($kelolaCount) ? $kelolaCount : 0;
$menu = [
  ['dashboard',       'fa-th-large',   'Dashboard'],
  ['order_masuk',     'fa-inbox',      'Order Masuk', $staffCount],
  ['kelola_order',    'fa-tasks',      'Kelola Order', $kelolaCount],
  ['status_laundry',  'fa-sync-alt',   'Update Status'],
  ['konfirmasi_bayar','fa-check-circle','Konfirmasi Bayar', $confirmCount],
  ['history',         'fa-history',    'History Selesai'],
];
@endphp
@foreach($menu as $item)
@php [$key,$icon,$label] = $item; $badge = $item[3] ?? 0; @endphp
<a href="{{ route('staff.'.$key) }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $page === $key ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
  <i class="fas {{ $icon }} w-4 text-center"></i> {{ $label }}
  @if($badge > 0)
  <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5">{{ $badge }}</span>
  @endif
</a>
@endforeach
@endsection

@section('topbar-icon')<i class="fas fa-user-tie mr-2 text-emerald-500"></i>@endsection
@section('topbar-title')
  @php $titles = ['dashboard'=>'Dashboard Staff','order_masuk'=>'Order Masuk','kelola_order'=>'Kelola Order','status_laundry'=>'Update Status Laundry','konfirmasi_bayar'=>'Konfirmasi Pembayaran','history'=>'History Order Selesai']; @endphp
  {{ $titles[$page] ?? 'Dashboard' }}
@endsection

@section('content')

{{-- ═══ DASHBOARD ═══ --}}
@if($page === 'dashboard')
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
  @foreach([['📦','Order Masuk',$masuk->count(),'amber'],['🔄','Diproses',$diproses->count(),'blue'],['⚖️','Perlu Timbang',$needWeight->count(),'orange'],['💳','Konfirmasi Bayar',$konfBayar->count(),'emerald']] as [$e,$l,$v,$c])
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
    <div class="text-2xl">{{ $e }}</div>
    <div><div class="text-[11px] text-slate-500">{{ $l }}</div><div class="text-xl font-bold text-{{ $c }}-600">{{ $v }}</div></div>
  </div>
  @endforeach
</div>

@if($masuk->count())
<div class="mb-6 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 bg-amber-50"><span class="font-semibold text-amber-800">📦 Order Menunggu Konfirmasi</span></div>
  @foreach($masuk as $o)
  <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between hover:bg-slate-50 transition">
    <div>
      <span class="font-mono text-sm font-bold">{{ $o->kode_order }}</span>
      <span class="ml-2 text-xs text-slate-500">{{ $o->nama_cust }}</span>
      <span class="ml-2 text-xs text-slate-400">{{ $o->nama_layanan }}</span>
    </div>
    <form method="POST" action="{{ route('staff.ambil_order') }}" class="inline">
      @csrf
      <input type="hidden" name="id_order" value="{{ $o->id_order }}">
      <button type="submit" class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-emerald-600 transition">Ambil Order</button>
    </form>
  </div>
  @endforeach
</div>
@endif

@if($konfBayar->count())
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 bg-emerald-50"><span class="font-semibold text-emerald-800">💳 Konfirmasi Pembayaran</span></div>
  @foreach($konfBayar as $p)
  <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between hover:bg-slate-50 transition">
    <div>
      <span class="font-mono text-sm font-bold">{{ $p->kode_order }}</span>
      <span class="ml-2 text-xs text-slate-500">{{ $p->nama_cust }}</span>
      <span class="ml-2 text-xs font-semibold text-emerald-600">Rp {{ number_format($p->jumlah,0,',','.') }}</span>
    </div>
    <form method="POST" action="{{ route('staff.konfirmasi_bayar.do') }}" class="inline">
      @csrf
      <input type="hidden" name="id_bayar" value="{{ $p->id_bayar }}">
      <button type="submit" class="text-xs bg-blue-500 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-blue-600 transition">Konfirmasi</button>
    </form>
  </div>
  @endforeach
</div>
@endif

{{-- ═══ ORDER MASUK ═══ --}}
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
        <button type="submit" class="text-xs bg-emerald-500 text-white px-4 py-2 rounded-xl font-semibold hover:bg-emerald-600 transition">
          <i class="fas fa-car mr-1"></i>Ambil Order
        </button>
      </form>
    </div>
  </div>
  @empty
  <div class="py-16 text-center text-slate-400">Tidak ada order masuk saat ini ✅</div>
  @endforelse
</div>

{{-- ═══ KELOLA ORDER ═══ --}}
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

        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white font-semibold rounded-xl text-sm hover:opacity-90 transition">
          <i class="fas fa-check mr-2"></i>Simpan & Kirim Tagihan ke Customer
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
        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-500 to-blue-800 text-white font-semibold rounded-xl text-sm hover:opacity-90 transition">
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
      <button type="submit" class="text-xs bg-emerald-500 text-white px-4 py-2 rounded-xl font-semibold hover:bg-emerald-600 transition">
        <i class="fas fa-check mr-1"></i>Konfirmasi Lunas
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
@endif

@endsection
