@extends('layouts.guest')
@section('title', 'Daftar - CleanGo')

@section('content')
<div class="flex bg-white rounded-3xl overflow-hidden shadow-2xl w-full max-w-3xl" style="min-height:520px">

  {{-- Brand Panel: sisi kiri menampilkan informasi marketing dan brand CleanGo. --}}
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
        Daftar sekarang dan nikmati kemudahan layanan laundry premium kami.
      </p>
    </div>

    <div class="relative z-10 text-xs text-blue-200 opacity-50">&copy; {{ date('Y') }} CleanGo</div>
  </div>

  {{-- Register Panel --}}
  <div class="flex-1 flex flex-col justify-center px-10 py-10">
    <h2 class="text-2xl font-bold text-slate-800 mb-1">Buat Akun Baru</h2>
    <p class="text-slate-400 text-sm mb-6">Isi data diri kamu untuk mendaftar sebagai customer.</p>

    @if($errors->has('register'))
    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3 mb-4">
      <i class="fas fa-exclamation-circle"></i> {{ $errors->first('register') }}
    </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
      @csrf

      @foreach([['nama','Nama Lengkap','fa-id-card','text','Nama lengkap kamu'],['username','Username','fa-at','text','Hanya huruf, angka, underscore'],['notelp','No. Telepon','fa-phone','tel','08xxxxxxxxxx'],['alamat','Alamat','fa-map-marker-alt','text','Alamat lengkap (opsional)']] as [$name,$label,$icon,$type,$ph])
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">{{ $label }}</label>
        <div class="relative">
          <i class="fas {{ $icon }} absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
          <input type="{{ $type }}" name="{{ $name }}" placeholder="{{ $ph }}" value="{{ old($name) }}"
            class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition">
        </div>
      </div>
      @endforeach

      @foreach([['password','Password','fa-lock','Minimal 6 karakter'],['password_confirmation','Konfirmasi Password','fa-lock','Ulangi password']] as [$name,$label,$icon,$ph])
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">{{ $label }}</label>
        <div class="relative">
          <i class="fas {{ $icon }} absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
          <input type="password" name="{{ $name }}" placeholder="{{ $ph }}"
            class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition">
        </div>
      </div>
      @endforeach

      <button type="submit"
        class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-900 text-white font-semibold rounded-xl text-sm tracking-wide hover:opacity-90 hover:shadow-lg hover:shadow-blue-200 transition-all duration-200">
        <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
      </button>
    </form>

    {{-- Link ke halaman login jika pengguna sudah memiliki akun. --}}
    <p class="text-center text-xs text-slate-400 mt-5">
      Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-500 font-semibold hover:text-blue-700 hover:underline transition">Login di sini</a>
    </p>
  </div>
</div>
@endsection
