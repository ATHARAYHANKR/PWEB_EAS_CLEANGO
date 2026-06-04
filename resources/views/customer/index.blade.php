@extends('layouts.app')

@section('title', 'Customer - CleanGo')

@section('sidebar-icon')<i class="fas fa-tshirt"></i>@endsection
@section('sidebar-panel')Panel Customer@endsection

@section('sidebar-nav')
@php
$menu = [
  ['dashboard',       'fa-th-large',     'Dashboard'],
  ['booking',         'fa-plus-circle',  'Booking Baru'],
  ['riwayat',         'fa-history',      'Riwayat Order'],
  ['pembayaran',      'fa-credit-card',  'Pembayaran'],
  ['tracking',        'fa-map-marker-alt','Tracking'],
  ['invoice',         'fa-file-invoice', 'Invoice'],
  ['profil',          'fa-user-circle',  'Profil Saya'],
];
@endphp
@foreach($menu as [$key,$icon,$label])
<a href="{{ route('customer.' . $key) }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $page === $key ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
  <i class="fas {{ $icon }} w-4 text-center"></i> {{ $label }}
  @if($key === 'pembayaran' && isset($ordersBayar) && $ordersBayar->count())
    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5">{{ $ordersBayar->count() }}</span>
  @endif
</a>
@endforeach
@endsection

@section('topbar-icon')<i class="fas fa-tshirt mr-2 text-blue-500"></i>@endsection
@section('topbar-title')
  @php $titles = ['dashboard'=>'Dashboard','booking'=>'Booking Baru','riwayat'=>'Riwayat Order','pembayaran'=>'Pembayaran','tracking'=>'Tracking Order','invoice'=>'Invoice','profil'=>'Profil Saya']; @endphp
  {{ $titles[$page] ?? 'Dashboard' }}
@endsection

@section('content')

{{-- ═══════════════ DASHBOARD ═══════════════ --}}
@if($page === 'dashboard')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
  @foreach([['📦','Order Aktif',$statAktif,'blue'],['✅','Selesai',$statSelesai,'emerald'],['💰','Total Bayar','Rp '.number_format($statTotal,0,',','.'),'violet']] as [$emoji,$label,$val,$color])
  <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 flex items-center gap-4">
    <div class="text-3xl">{{ $emoji }}</div>
    <div>
      <div class="text-xs text-slate-500 font-medium mb-1">{{ $label }}</div>
      <div class="text-xl font-bold text-{{ $color }}-600">{{ $val }}</div>
    </div>
  </div>
  @endforeach
</div>

@if($ordersBayar->count())
<div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-4">
  <p class="text-sm font-bold text-amber-800 mb-3"><i class="fas fa-bell mr-2"></i>Pembayaran Menunggu</p>
  @foreach($ordersBayar as $o)
  <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-amber-100 mb-2">
    <div>
      <div class="text-sm font-bold">{{ $o->kode_order }}</div>
      <div class="text-xs text-slate-500">{{ $o->nama_layanan }} • Rp {{ number_format($o->jumlah_bayar,0,',','.') }}</div>
    </div>
    <a href="{{ route('customer.pembayaran', ['id' => $o->id_order]) }}"
       class="text-xs bg-amber-500 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition">Bayar</a>
  </div>
  @endforeach
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
    <span class="font-semibold text-slate-800">Order Terbaru</span>
    <a href="{{ route('customer.riwayat') }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-xs text-slate-500 uppercase">
        <tr>
          @foreach(['Kode','Layanan','Jadwal Jemput','Status','Bayar',''] as $h)
          <th class="px-4 py-3 text-left">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($myOrders->take(5) as $o)
        <tr class="hover:bg-slate-50 transition">
          <td class="px-4 py-3 font-mono text-xs font-bold">{{ $o->kode_order }}</td>
          <td class="px-4 py-3">{{ $o->nama_layanan }}</td>
          <td class="px-4 py-3 text-xs">{{ $o->jadwal_jemput ? \Carbon\Carbon::parse($o->jadwal_jemput)->format('d M Y H:i') : '-' }}</td>
          <td class="px-4 py-3"><span class="text-xs px-2 py-1 rounded-full font-semibold
            {{ in_array($o->status_order,['Selesai']) ? 'bg-emerald-100 text-emerald-700' : (in_array($o->status_order,['Dibatalkan']) ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
            {{ $o->status_order }}</span></td>
          <td class="px-4 py-3 text-xs">
            @if($o->status_bayar === 'Lunas') <span class="text-emerald-600 font-semibold">Lunas</span>
            @elseif($o->status_bayar === 'Pending' && $o->jumlah_bayar > 0) <span class="text-amber-600 font-semibold">Belum Bayar</span>
            @else <span class="text-slate-400">-</span> @endif
          </td>
          <td class="px-4 py-3">
            <a href="{{ route('customer.riwayat', ['id' => $o->id_order]) }}" class="text-xs text-blue-600 hover:underline">Detail</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400 text-sm">Belum ada order. <a href="{{ route('customer.booking') }}" class="text-blue-600">Buat booking pertama!</a></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ═══════════════ BOOKING ═══════════════ --}}
@elseif($page === 'booking')

{{-- ══ SECTION: DAFTAR HARGA KATALOG ══ --}}
<div class="mb-10">
  <h2 class="text-2xl font-bold text-slate-800 mb-1">Harga<span class="text-slate-400">*</span></h2>
  <p class="text-xs text-slate-400 mb-6">*Harga dapat berubah sewaktu-waktu. Hubungi kami untuk informasi terkini.</p>

  @php $katalogGrouped = $katalogList->unique('id_katalog')->groupBy('nama_layanan'); @endphp

  @foreach($katalogGrouped as $namaLayanan => $items)
  <div class="mb-8">
    <h3 class="text-base font-semibold text-slate-700 mb-3 flex items-center gap-2"><i class="fas fa-tag text-blue-400 text-xs"></i> {{ $namaLayanan }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($items as $k)
      <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer katalog-card"
           data-id-katalog="{{ $k->id_katalog }}"
           data-id-layanan="{{ $k->id_layanan }}"
           data-harga="{{ $k->harga }}"
           data-satuan="{{ $k->satuan }}"
           data-nama="{{ $k->nama_layanan }} — {{ $k->varian }}">

        {{-- Foto Service --}}
        <div class="relative h-44 bg-slate-200 overflow-hidden">
          @if($k->foto)
            <img src="{{ Storage::url($k->foto) }}" alt="{{ $k->nama_layanan }}" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-300 to-slate-400">
              <i class="fas fa-tshirt text-5xl text-white/60"></i>
            </div>
          @endif
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent flex items-end p-4">
            <span class="text-white font-bold text-base leading-tight drop-shadow">{{ $k->nama_layanan }}<br><span class="font-normal text-sm opacity-90">{{ $k->varian }}</span></span>
          </div>
        </div>

        {{-- Info Harga --}}
        <div class="p-4 flex flex-col flex-1 border-t border-slate-100">
          <span class="text-xs text-slate-400 underline underline-offset-2 mb-1">Mulai Dari</span>
          <div class="text-base font-bold text-slate-800 mb-3">
            Rp {{ number_format($k->harga,0,',','.') }} / {{ $k->satuan }}
          </div>
          <div class="space-y-1 text-sm text-slate-700 mb-4">
            <div class="flex items-center gap-2"><i class="fas fa-motorcycle text-slate-400 w-4 text-center text-xs"></i> Gratis Antar Jemput**</div>
            <div class="flex items-center gap-2"><i class="fas fa-weight-hanging text-slate-400 w-4 text-center text-xs"></i> Minimum 3 Kg</div>
            @if($k->deskripsi)
            <div class="flex items-start gap-2"><i class="fas fa-info-circle text-slate-400 w-4 text-center text-xs mt-0.5"></i> <span class="text-xs text-slate-500">{{ $k->deskripsi }}</span></div>
            @endif
          </div>
          <button type="button"
            class="mt-auto w-full py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold text-sm rounded-xl transition"
            onclick="pilihKatalog({{ $k->id_katalog }}, {{ $k->id_layanan }})">
            <i class="fas fa-plus-circle mr-1.5"></i>Pesan Sekarang
          </button>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endforeach

  <p class="text-xs text-slate-400 mt-2">**Gratis antar jemput berlaku sesuai syarat & ketentuan</p>
</div>

{{-- ══ SECTION: INFO ANTAR JEMPUT ══ --}}
@php
  $ajFoto  = $settings['antar_jemput_foto']  ?? null;
  $ajJudul = $settings['antar_jemput_judul'] ?? 'Antar Jemput';
  $ajDesc  = $settings['antar_jemput_desc']  ?? '';
@endphp
<div class="mb-10 rounded-2xl overflow-hidden bg-white border border-slate-100 shadow-sm">
  <div class="flex flex-col md:flex-row">
    <div class="flex-1 p-7 flex flex-col justify-center">
      <h2 class="text-2xl font-bold text-slate-800 mb-3">{{ $ajJudul }}</h2>
      <div class="space-y-3 text-sm text-slate-600 mb-5">
        @foreach(explode("
", $ajDesc) as $line)
          @if(trim($line))<p>{{ trim($line) }}</p>@endif
        @endforeach
      </div>
      <div>
        <span class="inline-block bg-emerald-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl">
          <i class="fas fa-motorcycle mr-2"></i>S&K Antar Jemput
        </span>
      </div>
    </div>
    <div class="w-full md:w-80 h-52 md:h-auto flex-shrink-0 bg-slate-200 overflow-hidden">
      @if($ajFoto)
        <img src="{{ Storage::url($ajFoto) }}" alt="Antar Jemput" class="w-full h-full object-cover">
      @else
        <div class="w-full h-full min-h-[200px] flex items-center justify-center bg-gradient-to-br from-emerald-100 to-slate-200">
          <i class="fas fa-motorcycle text-6xl text-emerald-400/60"></i>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- ══ SECTION: FORM BOOKING ══ --}}
<div class="max-w-xl mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md:p-8" id="formBooking">
  <h3 class="text-lg font-bold text-slate-800 mb-5"><i class="fas fa-plus-circle text-blue-500 mr-2"></i>Form Booking Laundry</h3>

  @if($errors->has('booking'))
  <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm">{{ $errors->first('booking') }}</div>
  @endif

  {{-- Info paket terpilih --}}
  <div id="selectedInfo" class="hidden mb-4 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-700">
    <i class="fas fa-check-circle mr-2"></i><span id="selectedInfoText">Paket terpilih</span>
  </div>

  <form method="POST" action="{{ route('customer.booking.store') }}" class="space-y-4">
    @csrf

    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Jenis Layanan</label>
      <select name="id_layanan" id="layananSel" onchange="filterKatalog()" required
        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
        <option value="">-- Pilih Layanan --</option>
        @foreach($layananList as $l)
        <option value="{{ $l->id_layanan }}">{{ $l->nama_layanan }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Paket / Varian</label>
      <select name="id_katalog" id="katalogSel" required
        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
        <option value="">-- Pilih Paket --</option>
        @foreach($katalogList as $k)
        <option value="{{ $k->id_katalog }}" data-layanan="{{ $k->id_layanan }}" data-harga="{{ $k->harga }}" data-satuan="{{ $k->satuan }}">
          {{ $k->nama_layanan }} — {{ $k->varian }} (Rp {{ number_format($k->harga,0,',','.') }}/{{ $k->satuan }})
        </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Alamat Penjemputan</label>
      <textarea name="alamat" rows="2" placeholder="Alamat lengkap untuk dijemput" required
        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">{{ $profil->alamat_cust ?? '' }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tanggal Jemput</label>
        <input type="date" name="tanggal_jemput" required min="{{ date('Y-m-d') }}"
          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sesi Jemput</label>
        <select name="sesi_jemput" required
          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
          <option value="08:00-10:00">08:00–10:00 (Pagi)</option>
          <option value="10:00-12:00">10:00–12:00 (Pagi)</option>
          <option value="13:00-15:00">13:00–15:00 (Siang)</option>
          <option value="15:00-17:00">15:00–17:00 (Sore)</option>
        </select>
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Catatan (opsional)</label>
      <input type="text" name="catatan" placeholder="Misal: hati-hati baju putih"
        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
    </div>

    <button type="submit" data-confirm-title="Kirim Booking" data-confirm-message="Pastikan semua data booking sudah benar sebelum dikirim." class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-800 text-white font-semibold rounded-xl text-sm hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
      <i class="fas fa-paper-plane mr-2"></i>Kirim Booking
    </button>
  </form>
</div>

@push('scripts')
<script>
function filterKatalog() {
  var sel = document.getElementById('layananSel').value;
  var opts = document.getElementById('katalogSel').options;
  for (var i = 1; i < opts.length; i++) {
    opts[i].style.display = (sel === '' || opts[i].dataset.layanan === sel) ? '' : 'none';
  }
  document.getElementById('katalogSel').value = '';
}

function pilihKatalog(idKatalog, idLayanan) {
  // Set layanan
  var layananSel = document.getElementById('layananSel');
  layananSel.value = idLayanan;
  filterKatalog();

  // Set katalog
  var katalogSel = document.getElementById('katalogSel');
  for (var i = 0; i < katalogSel.options.length; i++) {
    if (katalogSel.options[i].value == idKatalog) {
      katalogSel.options[i].style.display = '';
      katalogSel.value = idKatalog;
      break;
    }
  }

  // Tampilkan info
  var txt = katalogSel.options[katalogSel.selectedIndex].text;
  document.getElementById('selectedInfoText').textContent = 'Paket dipilih: ' + txt;
  document.getElementById('selectedInfo').classList.remove('hidden');

  // Scroll ke form
  document.getElementById('formBooking').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>
@endpush

{{-- ═══════════════ RIWAYAT ═══════════════ --}}
@elseif($page === 'riwayat')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
  <div class="lg:col-span-2 space-y-3">
    <h3 class="font-semibold text-slate-700 mb-2">Semua Order</h3>
    @forelse($myOrders as $o)
    <a href="{{ route('customer.riwayat', ['id' => $o->id_order]) }}"
       class="block bg-white rounded-2xl border {{ isset($selOrder) && $selOrder->id_order == $o->id_order ? 'border-blue-400 ring-2 ring-blue-100' : 'border-slate-100' }} shadow-sm p-4 hover:border-blue-300 transition">
      <div class="flex items-center justify-between mb-1">
        <span class="font-mono text-xs font-bold text-slate-700">{{ $o->kode_order }}</span>
        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold
          {{ $o->status_order === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : ($o->status_order === 'Dibatalkan' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
          {{ $o->status_order }}</span>
      </div>
      <div class="text-xs text-slate-500">{{ $o->nama_layanan }} • {{ $o->varian ?? '-' }}</div>
      <div class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($o->tanggal_pesan)->format('d M Y') }}</div>
    </a>
    @empty
    <div class="text-center py-12 text-slate-400">Belum ada order</div>
    @endforelse
  </div>

  <div class="lg:col-span-3">
    @if(isset($selOrder) && $selOrder)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div class="flex items-start justify-between mb-4">
        <div>
          <div class="font-mono text-lg font-bold text-slate-800">{{ $selOrder->kode_order }}</div>
          <div class="text-xs text-slate-500">{{ $selOrder->nama_layanan }} • {{ $selOrder->varian ?? '-' }}</div>
        </div>
        <span class="text-xs px-3 py-1 rounded-full font-semibold
          {{ $selOrder->status_order === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : ($selOrder->status_order === 'Dibatalkan' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
          {{ $selOrder->status_order }}</span>
      </div>

      <div class="grid grid-cols-2 gap-3 text-xs mb-5">
        <div><span class="text-slate-400">Jadwal Jemput</span><br><strong>{{ $selOrder->jadwal_jemput ? \Carbon\Carbon::parse($selOrder->jadwal_jemput)->format('d M Y H:i') : '-' }}</strong></div>
        <div><span class="text-slate-400">Staff</span><br><strong>{{ $selOrder->nama_staff ?? 'Belum ditugaskan' }}</strong></div>
        <div><span class="text-slate-400">Berat / Qty</span><br><strong>{{ $selOrder->berat ? $selOrder->berat.' kg' : ($selOrder->qty ? $selOrder->qty.' pcs' : 'Belum diverifikasi') }}</strong></div>
        <div><span class="text-slate-400">Total</span><br><strong>{{ $selOrder->total_harga > 0 ? 'Rp '.number_format($selOrder->total_harga,0,',','.') : 'Belum dihitung' }}</strong></div>
      </div>

      @if($selTracking && $selTracking->count())
      <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Timeline</h4>
      <div class="relative pl-5">
        <div class="absolute left-2 top-0 bottom-0 w-px bg-slate-200"></div>
        @foreach($selTracking as $t)
        <div class="relative mb-4 pl-4">
          <div class="absolute -left-1 top-1 w-2.5 h-2.5 rounded-full {{ $loop->last ? 'bg-blue-500' : 'bg-slate-300' }} border-2 border-white"></div>
          <div class="text-xs font-bold text-slate-700">{{ $t->status }}</div>
          <div class="text-xs text-slate-500">{{ $t->keterangan }}</div>
          <div class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($t->waktu_update)->format('d M Y H:i') }}</div>
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

{{-- ═══════════════ PEMBAYARAN ═══════════════ --}}
@elseif($page === 'pembayaran')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
  <div class="lg:col-span-2 space-y-3">
    @if($pendingOrders->count())
    <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider">⚠ Perlu Dibayar ({{ $pendingOrders->count() }})</h4>
    @foreach($pendingOrders as $o)
    <a href="{{ route('customer.pembayaran', ['id' => $o->id_order]) }}"
       class="block bg-amber-50 border {{ $payId == $o->id_order ? 'border-amber-400 ring-2 ring-amber-100' : 'border-amber-200' }} rounded-2xl p-4 hover:border-amber-300 transition">
      <div class="font-mono text-xs font-bold text-slate-700">{{ $o->kode_order }}</div>
      <div class="text-xs text-slate-500 mt-0.5">{{ $o->nama_layanan }} • Rp {{ number_format($o->jumlah_bayar,0,',','.') }}</div>
      <span class="inline-block mt-1 text-[10px] bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full font-semibold">Pending</span>
    </a>
    @endforeach
    @endif

    @if($waitingOrders->count())
    <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider mt-4">🕐 Menunggu Konfirmasi ({{ $waitingOrders->count() }})</h4>
    @foreach($waitingOrders as $o)
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
      <div class="font-mono text-xs font-bold text-slate-700">{{ $o->kode_order }}</div>
      <div class="text-xs text-slate-500 mt-0.5">Rp {{ number_format($o->jumlah_bayar,0,',','.') }}</div>
      <span class="inline-block mt-1 text-[10px] bg-blue-200 text-blue-800 px-2 py-0.5 rounded-full font-semibold">Menunggu Konfirmasi</span>
    </div>
    @endforeach
    @endif

    @if(!$pendingOrders->count() && !$waitingOrders->count())
    <div class="text-center py-10 text-slate-400 text-sm">Tidak ada tagihan aktif 🎉</div>
    @endif
  </div>

  <div class="lg:col-span-3">
    @if($selOrder && $payId)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <h3 class="font-bold text-slate-800 mb-4"><i class="fas fa-credit-card text-blue-500 mr-2"></i>Detail Pembayaran</h3>
      <div class="bg-slate-50 rounded-xl p-4 text-sm mb-4 space-y-1">
        <div class="flex justify-between"><span class="text-slate-500">Order</span><span class="font-mono font-bold">{{ $selOrder->kode_order }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Layanan</span><span>{{ $selOrder->nama_layanan }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Paket</span><span>{{ $selOrder->varian ?? '-' }}</span></div>
        @if($selOrder->berat)<div class="flex justify-between"><span class="text-slate-500">Berat</span><span>{{ $selOrder->berat }} kg</span></div>@endif
        <div class="flex justify-between font-bold text-base border-t border-slate-200 pt-2 mt-1"><span>Total</span><span class="text-blue-600">Rp {{ number_format($selOrder->total_harga,0,',','.') }}</span></div>
      </div>

      @if(!$selPayment || $selPayment->status_bayar === 'Pending')
      <div class="mb-4 bg-blue-50 rounded-xl p-4 text-center">
        <p class="text-xs text-slate-500 mb-2">Transfer via QRIS ke rekening CleanGo</p>
        <div class="w-32 h-32 bg-white mx-auto rounded-xl border-2 border-dashed border-slate-300 flex items-center justify-center">
          <i class="fas fa-qrcode text-5xl text-slate-300"></i>
        </div>
        <p class="text-xs text-slate-400 mt-2">Scan & bayar Rp {{ number_format($selOrder->total_harga,0,',','.') }}</p>
      </div>

      <form method="POST" action="{{ route('customer.pembayaran.upload') }}">
        @csrf
        <input type="hidden" name="id_order" value="{{ $selOrder->id_order }}">
        <input type="text" name="catatan_bayar" placeholder="Catatan (opsional)" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 mb-3 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        <button type="submit" data-confirm-title="Konfirmasi Pembayaran" data-confirm-message="Pastikan Anda telah melakukan pembayaran sebelum mengonfirmasinya." class="w-full py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white font-semibold rounded-xl text-sm hover:opacity-90 transition">
          <i class="fas fa-check mr-2"></i>Konfirmasi Sudah Bayar
        </button>
      </form>
      @else
      <div class="text-center py-6">
        <div class="text-3xl mb-2">{{ $selPayment->status_bayar === 'Lunas' ? '✅' : '🕐' }}</div>
        <div class="font-semibold text-slate-700">{{ $selPayment->status_bayar }}</div>
        <div class="text-xs text-slate-400 mt-1">{{ $selPayment->status_bayar === 'Lunas' ? 'Pembayaran dikonfirmasi' : 'Menunggu konfirmasi staff' }}</div>
      </div>
      @endif
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
      <div class="text-4xl mb-3">💳</div>
      <div class="text-slate-400 text-sm">Pilih tagihan untuk melakukan pembayaran</div>
    </div>
    @endif
  </div>
</div>

{{-- ═══════════════ TRACKING ═══════════════ --}}
@elseif($page === 'tracking')
@if($activeOrders->count())
<div class="space-y-5">
  @foreach($activeOrders as $o)
  @php $step = $statusSteps[$o->status_order] ?? 0; @endphp
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <div class="flex items-center justify-between mb-4">
      <div>
        <span class="font-mono font-bold text-slate-800">{{ $o->kode_order }}</span>
        <span class="ml-2 text-xs text-slate-400">{{ $o->nama_layanan }}</span>
      </div>
      <span class="text-xs px-2 py-1 rounded-full font-semibold bg-blue-100 text-blue-700">{{ $o->status_order }}</span>
    </div>

    <div class="flex items-center justify-between mb-4 overflow-x-auto pb-1">
      @foreach($statusLabels as $i => $s)
      <div class="flex flex-col items-center min-w-[60px]">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-base
          {{ $step >= $i ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-400' }}">
          {{ $statusIcons[$i] }}
        </div>
        <div class="text-[9px] text-center mt-1 {{ $step >= $i ? 'text-blue-600 font-semibold' : 'text-slate-400' }}">{{ $s }}</div>
      </div>
      @if(!$loop->last)
      <div class="flex-1 h-px {{ $step > $i ? 'bg-blue-400' : 'bg-slate-200' }} mx-1"></div>
      @endif
      @endforeach
    </div>

    @if(isset($trackingData[$o->id_order]) && $trackingData[$o->id_order]->count())
    <div class="text-xs text-slate-500 bg-slate-50 rounded-xl px-3 py-2">
      <span class="font-semibold">Update terakhir:</span> {{ $trackingData[$o->id_order]->first()->keterangan }}
      <span class="text-slate-400">— {{ \Carbon\Carbon::parse($trackingData[$o->id_order]->first()->waktu_update)->diffForHumans() }}</span>
    </div>
    @endif
  </div>
  @endforeach
</div>
@else
<div class="text-center py-20">
  <div class="text-5xl mb-4">🎉</div>
  <div class="text-slate-500">Tidak ada order aktif saat ini.</div>
  <a href="{{ route('customer.booking') }}" class="mt-4 inline-block bg-blue-500 text-white text-sm px-5 py-2 rounded-xl font-semibold hover:bg-blue-600 transition">Booking Sekarang</a>
</div>
@endif

{{-- ═══════════════ INVOICE ═══════════════ --}}
@elseif($page === 'invoice')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100"><span class="font-semibold">Daftar Invoice</span></div>
  @forelse($invoices as $inv)
  <div class="px-5 py-4 border-b border-slate-50 hover:bg-slate-50 transition flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-2">
        <div>
          <div class="font-mono text-sm font-bold text-slate-800">{{ $inv->no_invoice }}</div>
          <div class="text-xs text-slate-500">{{ $inv->kode_order }} • {{ \Carbon\Carbon::parse($inv->tgl_invoice)->format('d M Y') }}</div>
        </div>
      </div>
    </div>
    <div class="text-right">
      <div class="flex items-center justify-end gap-2">
        <div class="font-bold text-emerald-600">Rp {{ number_format($inv->jumlah,0,',','.') }}</div>
        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">Lunas</span>
        <a href="{{ route('customer.invoice.print', $inv->id_invoice) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 rounded-xl hover:bg-slate-200 transition">
          <i class="fas fa-print"></i> Cetak
        </a>
      </div>
    </div>
  </div>
  @empty
  <div class="py-16 text-center text-slate-400">Belum ada invoice</div>
  @endforelse
</div>

{{-- ═══════════════ PROFIL ═══════════════ --}}
@elseif($page === 'profil')
<div class="max-w-lg mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md:p-8">
  <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-2xl font-bold text-blue-600">
      {{ mb_strtoupper(mb_substr($profil->nama_cust ?? '?', 0, 1)) }}
    </div>
    <div>
      <div class="text-xl font-bold text-slate-800">{{ $profil->nama_cust }}</div>
      <div class="text-sm text-slate-500">{{ $profil->username }}</div>
      <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">Customer</span>
    </div>
  </div>

  <form method="POST" action="{{ route('customer.profil.update') }}" class="space-y-4">
    @csrf
    @foreach([['nama_cust','Nama Lengkap','fa-id-card'],['notelp_cust','No. Telepon','fa-phone']] as [$field,$label,$icon])
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">{{ $label }}</label>
      <div class="relative">
        <i class="fas {{ $icon }} absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
        <input type="text" name="{{ $field }}" value="{{ $profil->$field ?? '' }}"
          class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
      </div>
    </div>
    @endforeach
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Alamat</label>
      <textarea name="alamat_cust" rows="2" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">{{ $profil->alamat_cust ?? '' }}</textarea>
    </div>
    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-500 to-blue-800 text-white font-semibold rounded-xl text-sm hover:opacity-90 transition">
      <i class="fas fa-save mr-2"></i>Simpan Perubahan
    </button>
  </form>
</div>
@endif

@endsection
