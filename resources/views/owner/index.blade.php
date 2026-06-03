@extends('layouts.app')

@section('title', 'Owner - CleanGo')
@section('sidebar-icon')<i class="fas fa-crown"></i>@endsection
@section('sidebar-panel')Panel Owner@endsection

@section('sidebar-nav')
@php
$menu = [
  ['dashboard',    'fa-th-large',    'Dashboard'],
  ['semua_order',  'fa-list-alt',    'Semua Order'],
  ['katalog',      'fa-tag',         'Katalog Harga'],
  ['layanan',      'fa-concierge-bell','Jenis Layanan'],
  ['staff',        'fa-user-tie',    'Manajemen Staff'],
  ['invoice',      'fa-file-invoice','Invoice'],
  ['laporan',      'fa-chart-bar',   'Laporan'],
];
@endphp
@foreach($menu as [$key,$icon,$label])
<a href="{{ route('owner.'.$key) }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $page === $key ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
  <i class="fas {{ $icon }} w-4 text-center"></i> {{ $label }}
</a>
@endforeach
@endsection

@section('topbar-icon')<i class="fas fa-crown mr-2 text-violet-500"></i>@endsection
@section('topbar-title')
  @php $titles = ['dashboard'=>'Dashboard Owner','semua_order'=>'Semua Order','katalog'=>'Katalog Harga','layanan'=>'Jenis Layanan','staff'=>'Manajemen Staff','invoice'=>'Invoice','laporan'=>'Laporan']; @endphp
  {{ $titles[$page] ?? 'Dashboard' }}
@endsection

@section('content')

{{-- ═══ DASHBOARD ═══ --}}
@if($page === 'dashboard')
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
  @foreach([['📦','Total Order',$totalOrder,'slate'],['🔄','Aktif',$orderAktif,'blue'],['✅','Selesai',$orderSelesai,'emerald'],['💰','Total Omzet','Rp '.number_format($totalOmzet,0,',','.'),'violet']] as [$e,$l,$v,$c])
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
    <div class="text-2xl">{{ $e }}</div>
    <div><div class="text-[11px] text-slate-500">{{ $l }}</div><div class="text-lg font-bold text-{{ $c }}-600">{{ $v }}</div></div>
  </div>
  @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
      <span class="font-semibold">Order Terbaru</span>
      <a href="{{ route('owner.semua_order') }}" class="text-xs text-violet-600 hover:underline">Lihat semua</a>
    </div>
    @foreach($allOrders->take(8) as $o)
    <div class="px-5 py-3 border-b border-slate-50 flex items-center justify-between hover:bg-slate-50 transition">
      <div>
        <span class="font-mono text-xs font-bold">{{ $o->kode_order }}</span>
        <span class="ml-1 text-xs text-slate-500">{{ $o->nama_cust }}</span>
      </div>
      <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold
        {{ $o->status_order === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : ($o->status_order === 'Dibatalkan' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
        {{ $o->status_order }}</span>
    </div>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
      <span class="font-semibold">Daftar Staff</span>
      <a href="{{ route('owner.staff') }}" class="text-xs text-violet-600 hover:underline">Kelola</a>
    </div>
    @foreach($staffList as $s)
    <div class="px-5 py-3 border-b border-slate-50 flex items-center gap-3 hover:bg-slate-50 transition">
      <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-sm font-bold text-emerald-700">
        {{ mb_strtoupper(mb_substr($s->nama, 0, 1)) }}
      </div>
      <div>
        <div class="text-sm font-semibold text-slate-800">{{ $s->nama }}</div>
        <div class="text-xs text-slate-400">{{ $s->username }}</div>
      </div>
      <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $s->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
        {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
    </div>
    @endforeach
  </div>
</div>

{{-- ═══ SEMUA ORDER ═══ --}}
@elseif($page === 'semua_order')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
  <div class="lg:col-span-2">
    <div class="flex gap-2 mb-3 overflow-x-auto pb-1">
      @foreach([''=>'Semua','Menunggu Konfirmasi'=>'Menunggu','Dijemput'=>'Dijemput','Dicuci'=>'Dicuci','Selesai'=>'Selesai','Dibatalkan'=>'Dibatalkan'] as $sv => $sl)
      <a href="{{ route('owner.semua_order', ['status' => $sv]) }}"
         class="whitespace-nowrap text-xs px-3 py-1.5 rounded-xl font-semibold border transition {{ $fStatus === $sv ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-500 border-slate-200 hover:border-violet-300' }}">
        {{ $sl }}
      </a>
      @endforeach
    </div>

    <div class="space-y-2 max-h-[70vh] overflow-y-auto">
      @foreach($allOrders->when($fStatus, fn($c) => $c->where('status_order', $fStatus)) as $o)
      <a href="{{ route('owner.semua_order', ['id' => $o->id_order, 'status' => $fStatus]) }}"
         class="block bg-white border {{ $selId == $o->id_order ? 'border-violet-400 ring-2 ring-violet-100' : 'border-slate-100' }} rounded-2xl p-3 shadow-sm hover:border-violet-300 transition">
        <div class="flex items-center justify-between mb-0.5">
          <span class="font-mono text-xs font-bold">{{ $o->kode_order }}</span>
          <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold
            {{ $o->status_order === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : ($o->status_order === 'Dibatalkan' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
            {{ $o->status_order }}</span>
        </div>
        <div class="text-xs text-slate-500">{{ $o->nama_cust }} • {{ $o->nama_layanan }}</div>
        <div class="text-xs font-semibold text-violet-600 mt-0.5">{{ $o->jumlah_bayar ? 'Rp '.number_format($o->jumlah_bayar,0,',','.') : '-' }}</div>
      </a>
      @endforeach
    </div>
  </div>

  <div class="lg:col-span-3">
    @if(isset($selOrder) && $selOrder)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div class="flex items-start justify-between mb-4">
        <div>
          <div class="font-mono text-lg font-bold text-slate-800">{{ $selOrder->kode_order }}</div>
          <div class="text-xs text-slate-500">{{ $selOrder->nama_layanan }} • {{ $selOrder->nama_cust }}</div>
        </div>
        @if(!in_array($selOrder->status_order, ['Selesai','Dibatalkan']))
        <form method="POST" action="{{ route('owner.order.batalkan') }}">
          @csrf
          <input type="hidden" name="id_order" value="{{ $selOrder->id_order }}">
          <button type="submit" onclick="return confirm('Batalkan order ini?')"
            class="text-xs bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1.5 rounded-lg font-semibold transition">
            ❌ Batalkan
          </button>
        </form>
        @endif
      </div>

      <div class="grid grid-cols-2 gap-3 text-xs mb-5">
        @foreach([['Customer',$selOrder->nama_cust],['Telepon',$selOrder->notelp_cust],['Staff',$selOrder->nama_staff ?? 'Belum'],['Status Bayar',$selOrder->status_bayar ?? '-'],['Berat',$selOrder->berat ? $selOrder->berat.' kg' : ($selOrder->qty ? $selOrder->qty.' pcs' : '-')],['Total','Rp '.number_format($selOrder->jumlah_bayar ?? 0,0,',','.')]] as [$k,$v])
        <div><span class="text-slate-400">{{ $k }}</span><br><strong>{{ $v }}</strong></div>
        @endforeach
      </div>

      @if($selTracking && $selTracking->count())
      <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Timeline</h4>
      <div class="relative pl-5 max-h-48 overflow-y-auto">
        <div class="absolute left-2 top-0 bottom-0 w-px bg-slate-200"></div>
        @foreach($selTracking as $t)
        <div class="relative mb-3 pl-4">
          <div class="absolute -left-1 top-1 w-2.5 h-2.5 rounded-full {{ $loop->last ? 'bg-violet-500' : 'bg-slate-300' }} border-2 border-white"></div>
          <div class="text-xs font-bold">{{ $t->status }}</div>
          <div class="text-xs text-slate-400">{{ $t->keterangan }}</div>
          <div class="text-[10px] text-slate-300">{{ \Carbon\Carbon::parse($t->waktu_update)->format('d M Y H:i') }}</div>
        </div>
        @endforeach
      </div>
      @endif
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
      <div class="text-4xl mb-3">📋</div>
      <div class="text-slate-400 text-sm">Pilih order untuk melihat detail</div>
    </div>
    @endif
  </div>
</div>

{{-- ═══ KATALOG ═══ --}}
@elseif($page === 'katalog')

{{-- ══ SETTING ANTAR JEMPUT ══ --}}
@php
  $ajFoto  = $settings['antar_jemput_foto']  ?? null;
  $ajJudul = $settings['antar_jemput_judul'] ?? 'Antar Jemput';
  $ajDesc  = $settings['antar_jemput_desc']  ?? '';
@endphp
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-6 overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
    <i class="fas fa-motorcycle text-emerald-500"></i>
    <span class="font-semibold text-slate-800">Setting Antar Jemput</span>
    <span class="ml-auto text-xs text-slate-400">Foto & teks tampilan di halaman booking customer</span>
  </div>
  <form method="POST" action="{{ route('owner.settings.update') }}" enctype="multipart/form-data">
    @csrf
    <div class="p-5 grid grid-cols-1 lg:grid-cols-3 gap-5">
      {{-- Preview Foto --}}
      <div class="flex flex-col items-center gap-3">
        <div class="w-full h-36 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
          @if($ajFoto)
            <img src="{{ Storage::url($ajFoto) }}" alt="Antar Jemput" class="w-full h-full object-cover" id="ajPreview">
          @else
            <div class="w-full h-full flex items-center justify-center" id="ajPreview">
              <i class="fas fa-motorcycle text-4xl text-slate-300"></i>
            </div>
          @endif
        </div>
        <label class="w-full cursor-pointer">
          <div class="w-full py-2 border-2 border-dashed border-slate-300 rounded-xl text-center text-xs text-slate-500 hover:border-emerald-400 hover:text-emerald-600 transition">
            <i class="fas fa-upload mr-1"></i> Ganti Foto Antar Jemput
          </div>
          <input type="file" name="antar_jemput_foto" accept="image/*" class="hidden" onchange="previewImg(this,'ajPreview')">
        </label>
      </div>
      {{-- Teks Settings --}}
      <div class="lg:col-span-2 space-y-3">
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Judul</label>
          <input type="text" name="antar_jemput_judul" value="{{ $ajJudul }}"
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Deskripsi (pisahkan baris dengan Enter)</label>
          <textarea name="antar_jemput_desc" rows="4"
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">{{ $ajDesc }}</textarea>
        </div>
        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-semibold rounded-xl text-sm hover:bg-emerald-700 transition">
          <i class="fas fa-save mr-2"></i>Simpan Setting Antar Jemput
        </button>
      </div>
    </div>
  </form>
</div>

{{-- ══ DAFTAR KATALOG + TAMBAH ══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Daftar --}}
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100"><span class="font-semibold">Daftar Katalog Harga</span></div>
    @foreach($katalogList->groupBy('nama_layanan') as $namaLayanan => $items)
    <div class="px-5 py-2 bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">{{ $namaLayanan }}</div>
    @foreach($items as $k)
    <div class="px-5 py-3 border-b border-slate-50 hover:bg-slate-50 transition">
      <div class="flex items-center gap-3">
        {{-- Thumbnail --}}
        <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200">
          @if($k->foto)
            <img src="{{ Storage::url($k->foto) }}" class="w-full h-full object-cover" alt="{{ $k->varian }}">
          @else
            <div class="w-full h-full flex items-center justify-center"><i class="fas fa-image text-slate-300 text-xl"></i></div>
          @endif
        </div>
        <div class="flex-1 min-w-0">
          <div class="font-semibold text-sm text-slate-800">{{ $k->varian }}</div>
          <div class="text-xs text-slate-400">Rp {{ number_format($k->harga,0,',','.') }}/{{ $k->satuan }}</div>
          @if($k->deskripsi)<div class="text-xs text-slate-400 truncate">{{ $k->deskripsi }}</div>@endif
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $k->status === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $k->status }}</span>
          {{-- Tombol Edit Foto --}}
          <button onclick="openEditKatalog({{ $k->id_katalog }}, {{ $k->id_layanan }}, '{{ $k->varian }}', {{ $k->harga }}, '{{ $k->satuan }}', '{{ $k->deskripsi }}', '{{ $k->status }}', '{{ $k->foto ? Storage::url($k->foto) : '' }}')"
            class="text-xs text-blue-400 hover:text-blue-600 transition px-1" title="Edit">
            <i class="fas fa-edit"></i>
          </button>
          <form method="POST" action="{{ route('owner.katalog.delete', $k->id_katalog) }}" class="inline">
            @csrf @method('DELETE')
            <button onclick="return confirm('Hapus katalog ini?')" class="text-xs text-red-400 hover:text-red-600 transition px-1" title="Hapus">
              <i class="fas fa-trash"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
    @endforeach
    @endforeach
  </div>

  {{-- Form Tambah --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <h3 class="font-bold text-slate-800 mb-4">+ Tambah Katalog</h3>
    <form method="POST" action="{{ route('owner.katalog.store') }}" enctype="multipart/form-data" class="space-y-3">
      @csrf
      {{-- Preview Foto Tambah --}}
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Foto Katalog</label>
        <div class="w-full h-28 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 mb-2 flex items-center justify-center" id="addPreviewWrapper">
          <i class="fas fa-image text-3xl text-slate-300" id="addPreviewIcon"></i>
          <img id="addPreviewImg" class="hidden w-full h-full object-cover" src="" alt="">
        </div>
        <label class="cursor-pointer block">
          <div class="w-full py-2 border-2 border-dashed border-slate-300 rounded-xl text-center text-xs text-slate-500 hover:border-violet-400 hover:text-violet-600 transition">
            <i class="fas fa-upload mr-1"></i> Pilih Foto
          </div>
          <input type="file" name="foto" accept="image/*" class="hidden" onchange="previewAdd(this)">
        </label>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Layanan</label>
        <select name="id_layanan" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
          <option value="">-- Pilih --</option>
          @foreach($layananList as $l)
          <option value="{{ $l->id_layanan }}">{{ $l->nama_layanan }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Varian</label>
        <select name="varian" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
          <option value="Regular">Regular</option>
          <option value="Express">Express</option>
          <option value="Hemat">Hemat</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Harga</label>
        <input type="number" name="harga" min="0" required placeholder="Contoh: 7000" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Satuan</label>
        <select name="satuan" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
          <option value="kg">kg</option>
          <option value="pcs">pcs</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
        <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
          <option value="Aktif">Aktif</option>
          <option value="Nonaktif">Nonaktif</option>
        </select>
      </div>
      <input type="text" name="deskripsi" placeholder="Deskripsi (opsional)" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
      <button type="submit" class="w-full py-2 bg-violet-600 text-white font-semibold rounded-xl text-sm hover:bg-violet-700 transition">
        <i class="fas fa-plus mr-2"></i>Tambah Katalog
      </button>
    </form>
  </div>
</div>

{{-- ══ MODAL EDIT KATALOG ══ --}}
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-slate-800">Edit Katalog</h3>
      <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times text-lg"></i></button>
    </div>
    <form method="POST" id="editForm" enctype="multipart/form-data" class="space-y-3">
      @csrf @method('PUT')
      {{-- Preview Foto Edit --}}
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Foto Katalog</label>
        <div class="w-full h-32 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 mb-2">
          <img id="editPreviewImg" src="" alt="" class="w-full h-full object-cover">
        </div>
        <label class="cursor-pointer block">
          <div class="w-full py-2 border-2 border-dashed border-slate-300 rounded-xl text-center text-xs text-slate-500 hover:border-violet-400 hover:text-violet-600 transition">
            <i class="fas fa-upload mr-1"></i> Ganti Foto
          </div>
          <input type="file" name="foto" accept="image/*" class="hidden" onchange="previewImgEl(this, document.getElementById('editPreviewImg'))">
        </label>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Layanan</label>
        <select name="id_layanan" id="editLayanan" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
          @foreach($layananList as $l)
          <option value="{{ $l->id_layanan }}">{{ $l->nama_layanan }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Varian</label>
        <select name="varian" id="editVarian" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50">
          <option value="Regular">Regular</option>
          <option value="Express">Express</option>
          <option value="Hemat">Hemat</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Harga</label>
        <input type="number" name="harga" id="editHarga" min="0" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Satuan</label>
        <select name="satuan" id="editSatuan" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50">
          <option value="kg">kg</option>
          <option value="pcs">pcs</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Deskripsi</label>
        <input type="text" name="deskripsi" id="editDeskripsi" placeholder="Deskripsi (opsional)" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
        <select name="status" id="editStatus" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50">
          <option value="Aktif">Aktif</option>
          <option value="Nonaktif">Nonaktif</option>
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeEditModal()" class="flex-1 py-2 border border-slate-200 text-slate-600 font-semibold rounded-xl text-sm hover:bg-slate-50 transition">Batal</button>
        <button type="submit" class="flex-1 py-2 bg-violet-600 text-white font-semibold rounded-xl text-sm hover:bg-violet-700 transition">Simpan</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function previewImg(input, previewId) {
  var el = document.getElementById(previewId);
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      if (el.tagName === 'IMG') {
        el.src = e.target.result;
        el.classList.remove('hidden');
      } else {
        el.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
      }
    }
    reader.readAsDataURL(input.files[0]);
  }
}
function previewImgEl(input, imgEl) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) { imgEl.src = e.target.result; }
    reader.readAsDataURL(input.files[0]);
  }
}
function previewAdd(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('addPreviewIcon').classList.add('hidden');
      var img = document.getElementById('addPreviewImg');
      img.src = e.target.result;
      img.classList.remove('hidden');
    }
    reader.readAsDataURL(input.files[0]);
  }
}
function openEditKatalog(id, idLayanan, varian, harga, satuan, deskripsi, status, fotoUrl) {
  var form = document.getElementById('editForm');
  form.action = '/owner/katalog/' + id;
  document.getElementById('editLayanan').value  = idLayanan;
  document.getElementById('editVarian').value   = varian;
  document.getElementById('editHarga').value    = harga;
  document.getElementById('editSatuan').value   = satuan;
  document.getElementById('editDeskripsi').value= deskripsi;
  document.getElementById('editStatus').value   = status;
  var img = document.getElementById('editPreviewImg');
  img.src = fotoUrl || '';
  img.style.display = fotoUrl ? 'block' : 'none';
  document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
document.getElementById('editModal').addEventListener('click', function(e) {
  if (e.target === this) closeEditModal();
});
</script>
@endpush

{{-- ═══ LAYANAN ═══ --}}
@elseif($page === 'layanan')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100"><span class="font-semibold">Daftar Jenis Layanan</span></div>
    @forelse($layananList as $l)
    <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between hover:bg-slate-50 transition">
      <div>
        <div class="font-semibold text-sm text-slate-800">{{ $l->nama_layanan }}</div>
        <div class="text-xs text-slate-400">{{ $l->deskripsi }}</div>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $l->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $l->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        <form method="POST" action="{{ route('owner.layanan.delete', $l->id_layanan) }}">
          @csrf @method('DELETE')
          <button onclick="return confirm('Hapus layanan ini?')" class="text-xs text-red-400 hover:text-red-600 transition"><i class="fas fa-trash"></i></button>
        </form>
      </div>
    </div>
    @empty
    <div class="py-12 text-center text-slate-400">Belum ada layanan</div>
    @endforelse
  </div>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <h3 class="font-bold text-slate-800 mb-4">+ Tambah Layanan</h3>
    <form method="POST" action="{{ route('owner.layanan.store') }}" class="space-y-3">
      @csrf
      <input type="text" name="nama_layanan" required placeholder="Nama layanan" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
      <textarea name="deskripsi" rows="2" placeholder="Deskripsi (opsional)" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100"></textarea>
      <button type="submit" class="w-full py-2 bg-violet-600 text-white font-semibold rounded-xl text-sm hover:bg-violet-700 transition">
        <i class="fas fa-plus mr-2"></i>Tambah Layanan
      </button>
    </form>
  </div>
</div>

{{-- ═══ STAFF ═══ --}}
@elseif($page === 'staff')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100"><span class="font-semibold">Daftar Staff ({{ $staffList->count() }})</span></div>
    @forelse($staffList as $s)
    <div class="px-5 py-4 border-b border-slate-50 flex items-center gap-4 hover:bg-slate-50 transition">
      <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center font-bold text-emerald-700 text-lg">
        {{ mb_strtoupper(mb_substr($s->nama,0,1)) }}
      </div>
      <div class="flex-1">
        <div class="font-semibold text-slate-800">{{ $s->nama }}</div>
        <div class="text-xs text-slate-400">{{ $s->username }} • {{ $s->notelp }}</div>
      </div>
      <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $s->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
    </div>
    @empty
    <div class="py-12 text-center text-slate-400">Belum ada staff</div>
    @endforelse
  </div>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <h3 class="font-bold text-slate-800 mb-4">+ Tambah Staff</h3>
    <form method="POST" action="{{ route('owner.staff.store') }}" class="space-y-3">
      @csrf
      @foreach([['nama','Nama Lengkap','text'],['username','Username','text'],['notelp','No. Telepon','tel'],['sandi','Password','password'],['alamat','Alamat','text']] as [$n,$l,$t])
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ $l }}</label>
        <input type="{{ $t }}" name="{{ $n }}" placeholder="{{ $l }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
      </div>
      @endforeach
      <button type="submit" class="w-full py-2 bg-violet-600 text-white font-semibold rounded-xl text-sm hover:bg-violet-700 transition">
        <i class="fas fa-user-plus mr-2"></i>Tambah Staff
      </button>
    </form>
  </div>
</div>

{{-- ═══ INVOICE ═══ --}}
@elseif($page === 'invoice')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100"><span class="font-semibold">Semua Invoice ({{ $invoices->count() }})</span></div>
  @forelse($invoices as $inv)
  <div class="px-5 py-4 border-b border-slate-50 hover:bg-slate-50 transition flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-2">
        <div>
          <div class="font-mono font-bold text-slate-800">{{ $inv->no_invoice }}</div>
          <div class="text-xs text-slate-500">{{ $inv->kode_order }} • {{ $inv->nama_cust }}</div>
          <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($inv->tgl_invoice)->format('d M Y H:i') }}</div>
        </div>
      </div>
    </div>
    <div class="text-right">
      <div class="flex items-center justify-end gap-2">
        <div class="font-bold text-emerald-600">Rp {{ number_format($inv->jumlah,0,',','.') }}</div>
        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">Lunas</span>
        <a href="{{ route('owner.invoice.print', $inv->id_invoice) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 rounded-xl hover:bg-slate-200 transition">
          <i class="fas fa-print"></i> Cetak
        </a>
      </div>
      <div class="text-xs text-slate-400">{{ $inv->metode }}</div>
    </div>
  </div>
  @empty
  <div class="py-16 text-center text-slate-400">Belum ada invoice</div>
  @endforelse
</div>

{{-- ═══ LAPORAN ═══ --}}
@elseif($page === 'laporan')

{{-- Data untuk Chart.js --}}
@php
  $chartLabels  = $laporanBulan->pluck('bulan')->toArray();
  $chartOmzet   = $laporanBulan->pluck('total_omzet')->toArray();
  $chartOrder   = $laporanBulan->pluck('total_order')->toArray();
  $chartSelesai = $laporanBulan->pluck('selesai')->toArray();
  $totalOmzet   = array_sum($chartOmzet);
  $totalOrder   = array_sum($chartOrder);
  $totalSelesai = array_sum($chartSelesai);
@endphp

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <p class="text-xs text-slate-500 font-medium mb-1">Total Omzet (12 Bln)</p>
    <p class="text-2xl font-bold text-violet-600">Rp {{ number_format($totalOmzet,0,',','.') }}</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <p class="text-xs text-slate-500 font-medium mb-1">Total Order</p>
    <p class="text-2xl font-bold text-fuchsia-600">{{ number_format($totalOrder,0,',','.') }}</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <p class="text-xs text-slate-500 font-medium mb-1">Order Selesai</p>
    <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalSelesai,0,',','.') }}</p>
  </div>
</div>

{{-- Charts + Export --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

  {{-- Bar Chart Omzet --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-slate-800 text-sm">📊 Omzet per Bulan</h3>
      <div class="flex gap-2">
        <a href="{{ route('owner.laporan.pdf') }}" target="_blank"
           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-semibold transition-colors">
          <i class="fas fa-file-pdf text-xs"></i> PDF
        </a>
        <a href="{{ route('owner.laporan.excel') }}"
           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition-colors">
          <i class="fas fa-file-excel text-xs"></i> Excel
        </a>
      </div>
    </div>
    <div class="relative" style="height:220px">
      <canvas id="chartOmzet"></canvas>
    </div>
  </div>

  {{-- Line Chart Order --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="font-bold text-slate-800 text-sm mb-4">📈 Jumlah Order per Bulan</h3>
    <div class="relative" style="height:220px">
      <canvas id="chartOrder"></canvas>
    </div>
  </div>

</div>

{{-- Tabel Detail --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
  <h3 class="font-bold text-slate-800 text-sm mb-4">📋 Rincian per Bulan</h3>
  @if($laporanBulan->count())
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-xs text-slate-500 border-b border-slate-100">
          <th class="text-left pb-3 font-semibold">Bulan</th>
          <th class="text-right pb-3 font-semibold">Total Order</th>
          <th class="text-right pb-3 font-semibold">Selesai</th>
          <th class="text-right pb-3 font-semibold">Omzet</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($laporanBulan as $row)
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="py-2.5 font-medium text-slate-700">{{ $row->bulan }}</td>
          <td class="py-2.5 text-right text-slate-600">{{ $row->total_order }}</td>
          <td class="py-2.5 text-right">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
              {{ $row->selesai }}
            </span>
          </td>
          <td class="py-2.5 text-right font-bold text-violet-600">Rp {{ number_format($row->total_omzet,0,',','.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="text-center py-12 text-slate-400">Belum ada data laporan</div>
  @endif
</div>

@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var chartOmzetCtx = document.getElementById('chartOmzet');
  var chartOrderCtx = document.getElementById('chartOrder');
  
  if (!chartOmzetCtx || !chartOrderCtx) return;
  
  // Data dari PHP
  var labels  = @json($chartLabels ?? []);
  var omzet   = @json($chartOmzet ?? []);
  var orders  = @json($chartOrder ?? []);
  var selesai = @json($chartSelesai ?? []);

  // Bar Chart - Omzet per Bulan
  if (chartOmzetCtx && labels.length > 0) {
    new Chart(chartOmzetCtx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Omzet (Rp)',
          data: omzet,
          backgroundColor: 'rgba(139, 92, 246, 0.8)',
          borderColor: 'rgba(139, 92, 246, 1)',
          borderWidth: 1,
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                return 'Rp ' + ctx.raw.toLocaleString('id-ID');
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(val) {
                if (val >= 1000000) return 'Rp ' + (val/1000000).toFixed(1) + 'jt';
                if (val >= 1000) return 'Rp ' + (val/1000).toFixed(0) + 'rb';
                return 'Rp ' + val;
              },
              font: { size: 10 }
            },
            grid: { color: 'rgba(0,0,0,0.05)' }
          },
          x: { ticks: { font: { size: 10 } }, grid: { display: false } }
        }
      }
    });
  }

  // Line Chart - Jumlah Order per Bulan
  if (chartOrderCtx && labels.length > 0) {
    new Chart(chartOrderCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Total Order',
            data: orders,
            borderColor: 'rgba(217, 70, 239, 1)',
            backgroundColor: 'rgba(217, 70, 239, 0.1)',
            borderWidth: 2.5,
            pointBackgroundColor: 'rgba(217, 70, 239, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4,
          },
          {
            label: 'Selesai',
            data: selesai,
            borderColor: 'rgba(16, 185, 129, 1)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 2.5,
            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: { font: { size: 11 }, boxWidth: 12, padding: 15 }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { font: { size: 10 }, stepSize: 1 },
            grid: { color: 'rgba(0,0,0,0.05)' }
          },
          x: { ticks: { font: { size: 10 } }, grid: { display: false } }
        }
      }
    });
  }
});
</script>

@endsection
