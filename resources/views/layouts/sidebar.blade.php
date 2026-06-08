@php
use Illuminate\Support\Facades\Route;

{{-- Role saat ini diambil dari session. Jika tidak tersedia, gunakan customer sebagai fallback. --}}
$role = session('role', 'customer');

{{-- Tema visual untuk masing-masing role: background, warna aktif/inaktif, icon, dan label panel. --}}
$themes = [
    'customer' => [
        'bg'       => 'bg-[#1e3a5f]',
        'active'   => 'bg-[#2563eb]/40 text-white border border-[#3b82f6]/50',
        'inactive' => 'text-white/60 hover:text-white hover:bg-[#2563eb]/20 border border-transparent',
        'ring'     => 'ring-[#3b82f6]/50',
        'icon'     => 'text-[#93c5fd]',
        'logo'     => 'fa-tshirt',
        'panel'    => 'Customer Panel',
    ],
    'staff' => [
        'bg'       => 'bg-[#1e3a5f]',
        'active'   => 'bg-[#2563eb]/40 text-white border border-[#3b82f6]/50',
        'inactive' => 'text-white/60 hover:text-white hover:bg-[#2563eb]/20 border border-transparent',
        'ring'     => 'ring-[#3b82f6]/50',
        'icon'     => 'text-[#93c5fd]',
        'logo'     => 'fa-user-tie',
        'panel'    => 'Staff Panel',
    ],
    'owner' => [
        'bg'       => 'bg-[#1e3a5f]',
        'active'   => 'bg-[#2563eb]/40 text-white border border-[#3b82f6]/50',
        'inactive' => 'text-white/60 hover:text-white hover:bg-[#2563eb]/20 border border-transparent',
        'ring'     => 'ring-[#3b82f6]/50',
        'icon'     => 'text-[#93c5fd]',
        'logo'     => 'fa-crown',
        'panel'    => 'Owner Panel',
    ],
];
$t = $themes[$role] ?? $themes['customer'];

{{-- Menu per role — route name, icon, label, badge_count (opsional) --}}
$menus = [
    'owner' => [
        ['owner.dashboard',   'fa-th-large',       'Dashboard'],
        ['owner.semua_order', 'fa-list-alt',        'Semua Order'],
        ['owner.katalog',     'fa-tag',             'Katalog Harga'],
        ['owner.layanan',     'fa-concierge-bell',  'Jenis Layanan'],
        ['owner.staff',       'fa-user-tie',        'Manajemen Staff'],
        ['owner.invoice',     'fa-file-invoice',    'Invoice'],
        ['owner.laporan',     'fa-chart-bar',       'Laporan'],
    ],
    'staff' => [
        ['staff.dashboard',        'fa-th-large',    'Dashboard'],
        ['staff.order_masuk',      'fa-inbox',       'Order Masuk',      isset($masuk) ? $masuk->count() : 0],
        ['staff.kelola_order',     'fa-tasks',       'Kelola Order'],
        ['staff.status_laundry',   'fa-sync-alt',    'Update Status'],
        ['staff.konfirmasi_bayar', 'fa-check-circle','Konfirmasi Bayar', isset($konfBayar) ? $konfBayar->count() : 0],
        ['staff.history',          'fa-history',     'History Selesai'],
        ['staff.profil',           'fa-user-circle', 'Profil Saya'],
    ],
    'customer' => [
        ['customer.dashboard',  'fa-th-large',      'Dashboard'],
        ['customer.booking',    'fa-plus-circle',   'Booking Baru'],
        ['customer.riwayat',    'fa-history',       'Riwayat Order'],
        ['customer.pembayaran', 'fa-credit-card',   'Pembayaran',  isset($ordersBayar) ? $ordersBayar->count() : 0],
        ['customer.tracking',   'fa-map-marker-alt','Tracking'],
        ['customer.invoice',    'fa-file-invoice',  'Invoice'],
        ['customer.profil',     'fa-user-circle',   'Profil Saya'],
    ],
];

$navItems = $menus[$role] ?? [];

{{-- Deteksi current route untuk active state agar menu yang sedang dibuka bisa ditandai. --}}
$currentRoute = Route::currentRouteName();
@endphp

<aside id="sidebar" style="background-color:#1e3a5f;" class="fixed inset-y-0 left-0 w-[240px] text-white flex flex-col z-40 border-r border-white/[0.06] transition-transform duration-300 -translate-x-full md:translate-x-0">

    {{-- LOGO --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-white/[0.06] shrink-0">
        <div style="background:rgba(255,255,255,0.12);box-shadow:0 0 0 1px rgba(59,130,246,0.45);" class="w-9 h-9 rounded-xl flex items-center justify-center">
            <i class="fas {{ $t['logo'] }} text-sm" style="color:#93c5fd;"></i>
        </div>
        <div>
            <div class="text-[15px] font-bold tracking-tight leading-tight">CleanGo</div>
            <div class="text-[10px] text-white/40 font-medium uppercase tracking-widest leading-tight">{{ $t['panel'] }}</div>
        </div>
    </div>

    {{-- NAV --}}
    <nav class="flex-1 flex flex-col gap-0.5 px-3 py-4 overflow-y-auto">
        @foreach($navItems as $item)
        @php
            [$routeName, $icon, $label] = $item;
            $badge = $item[3] ?? 0;
            $isActive = $currentRoute === $routeName;
        @endphp
        <a href="{{ route($routeName) }}"
           style="{{ $isActive ? 'background:rgba(37,99,235,0.35);border:1px solid rgba(59,130,246,0.5);color:#fff;' : 'border:1px solid transparent;color:rgba(255,255,255,0.65);' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 hover:!text-white"
           onmouseover="if(!this.style.background.includes('0.35'))this.style.background='rgba(37,99,235,0.2)'"
           onmouseout="if(!this.style.background.includes('0.35'))this.style.background='transparent'">
            <i class="fas {{ $icon }} w-4 text-center text-[13px] shrink-0"></i>
            <span class="flex-1 truncate">{{ $label }}</span>
            @if($badge > 0)
            <span class="shrink-0 bg-rose-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">{{ $badge }}</span>
            @endif
        </a>
        @endforeach
    </nav>

    {{-- USER CARD --}}
    <div class="px-3 pb-4 border-t border-white/[0.06] pt-3 shrink-0">
        <div class="flex items-center gap-2.5 px-2 py-2 mb-2">
            <div style="background:rgba(255,255,255,0.15);color:#93c5fd;" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0">
                {{ mb_strtoupper(mb_substr(session('nama', '?'), 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="text-sm font-semibold truncate">{{ session('nama') }}</div>
                <div class="text-[10px] text-white/40 uppercase tracking-wider">{{ ucfirst(session('role')) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 rounded-xl px-3 py-2.5 text-xs font-semibold bg-white/5 hover:bg-rose-500/20 hover:text-rose-300 text-white/50 transition-all duration-200 border border-white/[0.06]">
                <i class="fas fa-arrow-right-from-bracket text-[11px]"></i> Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 hidden md:hidden" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('sidebar-overlay');
    s.classList.toggle('-translate-x-full');
    o.classList.toggle('hidden');
}
</script>
