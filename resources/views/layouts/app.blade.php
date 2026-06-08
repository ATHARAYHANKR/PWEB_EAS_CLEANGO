<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CleanGo')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#f0f4f8] text-slate-800">
{{-- Layout utama: sidebar + konten. Semua halaman role-authenticated menggunakan layout ini. --}}

{{-- Sidebar kiri yang dinamis berdasarkan role user. --}}
@include('layouts.sidebar')

@php
$role = session('role', 'customer');
$topbarConfig = [
    'owner'    => ['icon' => 'fa-crown',    'color' => 'text-indigo-500'],
    'staff'    => ['icon' => 'fa-user-tie', 'color' => 'text-cyan-500'],
    'customer' => ['icon' => 'fa-tshirt',   'color' => 'text-blue-500'],
];
$tb = $topbarConfig[$role] ?? $topbarConfig['customer'];
@endphp

<div class="min-h-screen flex-1 md:ml-[240px] flex flex-col">

    {{-- TOPBAR: menampilkan role icon, title, dan nama pengguna dari session. --}}
    <header class="sticky top-0 z-30 bg-white border-b border-slate-200/80 px-5 md:px-7 h-16 flex items-center justify-between shadow-[0_1px_0_0_rgba(0,0,0,0.04)]">
        <div class="flex items-center gap-3">
            <button class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg hover:bg-slate-100 transition text-slate-600" onclick="toggleSidebar()">
                <i class="fas fa-bars text-sm"></i>
            </button>
            <div class="flex items-center gap-2 text-[15px] font-semibold text-slate-900">
                <i class="fas {{ $tb['icon'] }} {{ $tb['color'] }} text-sm mr-0.5"></i>
                @yield('topbar-title', 'CleanGo')
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-1.5 text-sm text-slate-500">
                <span>Halo,</span>
                <span class="font-semibold text-slate-800">{{ session('nama') }}</span>
            </div>
            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold text-white">
                {{ mb_strtoupper(mb_substr(session('nama','?'), 0, 1)) }}
            </div>
        </div>
    </header>

    {{-- CONTENT: slot utama untuk setiap halaman yang mewarisi layout ini. --}}
    <main class="flex-1 p-5 md:p-7">
        @yield('content')
    </main>
</div>

{{-- TOAST: notifikasi flash message atau error message. --}}
@if(session('flash') || $errors->any())
<div class="fixed bottom-5 right-5 z-50 w-full max-w-sm px-4 pointer-events-none">
  <div id="cg-toast" class="pointer-events-auto overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-900/10 transition-all duration-300 opacity-0 translate-y-4">
    <div class="flex items-center gap-3 p-4">
      @if(session('flash'))
        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
          <i class="fas fa-check text-sm"></i>
        </div>
      @else
        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
          <i class="fas fa-exclamation text-sm"></i>
        </div>
      @endif
      <div class="flex-1 min-w-0">
        <div class="text-sm font-semibold text-slate-900">{{ session('flash') ? 'Berhasil' : 'Terjadi Kesalahan' }}</div>
        {{-- {!! !!} agar tag HTML seperti <strong> dirender dengan benar --}}
        <div class="text-xs text-slate-500 mt-0.5 leading-relaxed">
          @if(session('flash'))
            {!! session('flash') !!}
          @else
            {{ $errors->first() }}
          @endif
        </div>
      </div>
      <button type="button" data-close-toast class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition shrink-0">
        <i class="fas fa-times text-xs"></i>
      </button>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toast = document.getElementById('cg-toast');
    if (!toast) return;
    requestAnimationFrame(() => { toast.classList.remove('opacity-0','translate-y-4'); });
    document.querySelector('[data-close-toast]')?.addEventListener('click', () => {
      toast.classList.add('opacity-0','translate-y-4');
    });
    setTimeout(() => { toast.classList.add('opacity-0','translate-y-4'); }, 5000);
  });
</script>
@endif

{{-- CONFIRM MODAL: digunakan oleh tombol yang memerlukan konfirmasi sebelum submit form. --}}
<div id="cg-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
  <div class="w-full max-w-sm rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/10 overflow-hidden">
    <div class="p-5">
      <div class="text-base font-bold text-slate-900 mb-1" id="cg-confirm-title">Konfirmasi</div>
      <p class="text-sm text-slate-500 leading-relaxed" id="cg-confirm-message">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
    </div>
    <div class="flex gap-2 p-4 pt-0">
      <button type="button" id="cg-confirm-cancel" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
      <button type="button" id="cg-confirm-ok" class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 transition">Lanjutkan</button>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('cg-confirm-modal');
    if (!modal) return;
    const titleEl   = document.getElementById('cg-confirm-title');
    const messageEl = document.getElementById('cg-confirm-message');
    const okBtn     = document.getElementById('cg-confirm-ok');
    const cancelBtn = document.getElementById('cg-confirm-cancel');
    let pendingForm = null;
    function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); pendingForm = null; }
    document.body.addEventListener('click', function (e) {
      const target = e.target.closest('[data-confirm-message]');
      if (!target) return;
      const form = target.closest('form');
      if (!form) return;
      e.preventDefault();
      pendingForm = form;
      titleEl.textContent   = target.dataset.confirmTitle   || 'Konfirmasi';
      messageEl.textContent = target.dataset.confirmMessage || 'Apakah Anda yakin?';
      modal.classList.remove('hidden'); modal.classList.add('flex');
    });
    okBtn.addEventListener('click', function () { if (pendingForm) pendingForm.submit(); closeModal(); });
    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
  });
</script>
@stack('scripts')
</body>
</html>
