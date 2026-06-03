<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CleanGo')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 font-sans">

@include('layouts.sidebar')

<div class="min-h-screen flex-1 md:ml-64 flex flex-col">
    {{-- TOPBAR --}}
    <div class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200 px-6 py-4 flex items-center justify-between">
        <div class="text-sm md:text-base font-semibold text-slate-900 flex items-center">
            @yield('topbar-icon')
            @yield('topbar-title', 'CleanGo Laundry')
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-slate-500">Halo, <strong>{{ session('nama') }}</strong>!</span>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="p-5 md:p-8">
        @if(session('flash'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border bg-emerald-50 text-emerald-700 border-emerald-200 px-4 py-3 text-sm font-medium">
            <i class="fas fa-check-circle"></i> {!! session('flash') !!}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-5 flex items-center gap-3 rounded-2xl border bg-rose-50 text-rose-700 border-rose-200 px-4 py-3 text-sm font-medium">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
        @endif

        @yield('content')
    </div>
</div>

@if(session('flash') || $errors->any())
<div class="fixed bottom-5 right-5 z-50 w-full max-w-sm px-4 pointer-events-none">
  <div id="cg-toast" class="pointer-events-auto overflow-hidden rounded-3xl border bg-white shadow-2xl ring-1 ring-slate-900/5 transition-transform duration-300 transform opacity-0 translate-y-6">
    <div class="flex items-start gap-3 p-4">
      <div class="mt-1">
        @if(session('flash'))
          <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
            <i class="fas fa-check"></i>
          </div>
        @else
          <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
        @endif
      </div>
      <div class="min-w-0 flex-1">
        <div class="text-sm font-semibold text-slate-900">
          @if(session('flash'))
            Berhasil
          @else
            Terjadi Kesalahan
          @endif
        </div>
        <div class="mt-1 text-sm leading-6 text-slate-600">
          @if(session('flash'))
            {!! session('flash') !!}
          @else
            {{ $errors->first() }}
          @endif
        </div>
      </div>
      <button type="button" data-close-toast class="text-slate-400 hover:text-slate-700 transition rounded-full p-2">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toast = document.getElementById('cg-toast');
    if (!toast) return;
    requestAnimationFrame(() => {
      toast.classList.remove('opacity-0', 'translate-y-6');
      toast.classList.add('opacity-100');
    });

    const closeBtn = document.querySelector('[data-close-toast]');
    closeBtn?.addEventListener('click', function () {
      toast.classList.add('opacity-0', 'translate-y-6');
    });

    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-6');
    }, 5000);
  });
</script>
@endif

@stack('scripts')
</body>
</html>
