@php
$role = session('role', 'customer');
$colors = [
    'customer' => ['sidebar' => 'from-sky-950 via-blue-800 to-sky-500', 'active' => 'bg-white/20'],
    'staff'    => ['sidebar' => 'from-emerald-950 via-emerald-800 to-emerald-500', 'active' => 'bg-white/20'],
    'owner'    => ['sidebar' => 'from-violet-950 via-violet-800 to-fuchsia-600', 'active' => 'bg-white/20'],
];
$c = $colors[$role] ?? $colors['customer'];
@endphp

<div class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b {{ $c['sidebar'] }} text-white flex flex-col p-5 z-40 overflow-y-auto">

    {{-- LOGO --}}
    <div class="flex items-center gap-3 px-2 pb-6 mb-6 border-b border-white/15">
        <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center text-xl">
            @yield('sidebar-icon')
        </div>
        <div>
            <div class="text-2xl font-extrabold tracking-tight">CleanGo</div>
            <div class="text-xs text-white/70">@yield('sidebar-panel')</div>
        </div>
    </div>

    {{-- NAV --}}
    <nav class="flex flex-col gap-1">
        @yield('sidebar-nav')
    </nav>

    {{-- BOTTOM --}}
    <div class="mt-auto pt-5 border-t border-white/15">
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold">
                {{ mb_strtoupper(mb_substr(session('nama','?'), 0, 1)) }}
            </div>
            <div>
                <div class="text-sm font-semibold">{{ session('nama') }}</div>
                <div class="text-xs text-white/70">{{ ucfirst(session('role')) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold bg-rose-500/30 hover:bg-rose-500/40 text-white transition">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </button>
        </form>
    </div>
</div>
