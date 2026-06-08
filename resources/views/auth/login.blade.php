@extends('layouts.guest')
@section('title', 'Login - CleanGo Laundry')

@section('content')
<div class="flex bg-white rounded-3xl overflow-hidden shadow-2xl w-full max-w-3xl" style="min-height:480px">

  {{-- Brand Panel: tampilan informasi aplikasi dan identitas CleanGo di sisi kiri. --}}
  <div class="hidden md:flex flex-col justify-between w-64 shrink-0 p-10 text-white relative overflow-hidden bg-gradient-to-b from-blue-900 to-blue-500">
    <div class="absolute rounded-full bg-white opacity-5 w-48 h-48" style="top:-60px;right:-60px"></div>
    <div class="absolute rounded-full bg-white opacity-5 w-32 h-32" style="top:130px;left:-40px"></div>
    <div class="absolute rounded-full bg-white opacity-5 w-56 h-56" style="bottom:-50px;right:-50px"></div>

    <div class="relative z-10">
      <div class="w-14 h-14 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center text-2xl mb-6">
        <i class="fas fa-soap"></i>
      </div>
      <h1 class="text-2xl font-extrabold mb-3 tracking-tight">CleanGo</h1>
      <div class="w-9 h-0.5 bg-white bg-opacity-40 rounded-full mb-4"></div>
      <p class="text-sm leading-relaxed text-blue-100">
        Sistem manajemen laundry terintegrasi. Dari pemesanan hingga invoice semua dalam satu platform.
      </p>
    </div>

    <div class="relative z-10 text-xs text-blue-200 opacity-50">&copy; {{ date('Y') }} CleanGo</div>
  </div>

  {{-- Login Panel --}}
  <div class="flex-1 flex flex-col justify-center px-10 py-10">
    <h2 class="text-2xl font-bold text-slate-800 mb-1">Selamat Datang</h2>
    <p class="text-slate-400 text-sm mb-7">Masuk ke akun CleanGo Anda</p>

    @if($errors->has('login'))
    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3 mb-5">
      <i class="fas fa-exclamation-circle"></i> {{ $errors->first('login') }}
    </div>
    @endif

    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-5">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
      @csrf
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Username</label>
        <div class="relative">
          <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
          <input type="text" name="username" placeholder="Masukkan username" autocomplete="username"
            value="{{ old('username', $rememberUsername ?? '') }}"
            class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition">
        </div>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Password</label>
        <div class="relative">
          <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
          <input type="password" name="password" id="pwd" placeholder="Masukkan password"
            class="w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition">
          <button type="button" onclick="togglePwd()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-blue-500 transition">
            <i class="fas fa-eye" id="eyeIco"></i>
          </button>
        </div>
      </div>
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-xs text-slate-500 cursor-pointer">
          <input type="checkbox" name="remember_me" class="w-4 h-4 accent-blue-600"> Ingat saya
        </label>
        <a href="{{ route('register') }}" class="text-xs text-blue-500 hover:text-blue-700 hover:underline transition">Daftar akun baru</a>
      </div>
      <button type="submit"
        class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-900 text-white font-semibold rounded-xl text-sm tracking-wide hover:opacity-90 hover:shadow-lg hover:shadow-blue-200 transition-all duration-200">
        <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Dashboard
      </button>
    </form>
  </div>
</div>

{{-- Script kecil untuk toggle password visibility pada form login. --}}
<script>
function togglePwd(){
  var i=document.getElementById('pwd'),e=document.getElementById('eyeIco');
  i.type=i.type==='password'?'text':'password';
  e.className=i.type==='password'?'fas fa-eye':'fas fa-eye-slash';
}
</script>
@endsection
