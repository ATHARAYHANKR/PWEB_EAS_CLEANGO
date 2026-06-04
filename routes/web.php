<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// ─── AUTH ───────────────────────────────────────────────────
Route::get('/',         [LoginController::class, 'showLogin'])->name('login');
Route::get('/login',    [LoginController::class, 'showLogin'])->name('login');
Route::post('/login',   [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',  [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::post('/register',[LoginController::class, 'register'])->name('register.post');

// ─── CUSTOMER ────────────────────────────────────────────────
Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard',       [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/booking',         [CustomerController::class, 'booking'])->name('booking');
    Route::post('/booking',        [CustomerController::class, 'storeBooking'])->name('booking.store');
    Route::get('/booking/{id}/edit', [CustomerController::class, 'editBooking'])->name('booking.edit');
    Route::put('/booking/{id}',    [CustomerController::class, 'updateBooking'])->name('booking.update');
    Route::delete('/booking/{id}', [CustomerController::class, 'deleteBooking'])->name('booking.delete');
    Route::get('/riwayat',         [CustomerController::class, 'riwayat'])->name('riwayat');
    Route::get('/pembayaran',      [CustomerController::class, 'pembayaran'])->name('pembayaran');
    Route::post('/pembayaran',     [CustomerController::class, 'uploadBukti'])->name('pembayaran.upload');
    Route::get('/tracking',        [CustomerController::class, 'tracking'])->name('tracking');
    Route::get('/invoice',         [CustomerController::class, 'invoice'])->name('invoice');
    Route::get('/invoice/{id}/print', [CustomerController::class, 'printInvoice'])->name('invoice.print');
    Route::get('/invoice/{id}/download', [CustomerController::class, 'downloadInvoice'])->name('invoice.download');
    Route::get('/profil',          [CustomerController::class, 'profil'])->name('profil');
    Route::post('/profil',         [CustomerController::class, 'updateProfil'])->name('profil.update');
});

// ─── STAFF ───────────────────────────────────────────────────
Route::middleware(['role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard',        [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/order-masuk',      [StaffController::class, 'orderMasuk'])->name('order_masuk');
    Route::post('/ambil-order',     [StaffController::class, 'ambilOrder'])->name('ambil_order');
    Route::get('/kelola-order',     [StaffController::class, 'kelolaOrder'])->name('kelola_order');
    Route::post('/set-berat',       [StaffController::class, 'setBerat'])->name('set_berat');
    Route::get('/status-laundry',   [StaffController::class, 'statusLaundry'])->name('status_laundry');
    Route::post('/advance-status',  [StaffController::class, 'advanceStatus'])->name('advance_status');
    Route::get('/konfirmasi-bayar', [StaffController::class, 'konfirmasiBayar'])->name('konfirmasi_bayar');
    Route::post('/konfirmasi-bayar',[StaffController::class, 'doKonfirmasi'])->name('konfirmasi_bayar.do');
    Route::get('/history',          [StaffController::class, 'history'])->name('history');
    Route::get('/profil',           [StaffController::class, 'profil'])->name('profil');
    Route::post('/profil',          [StaffController::class, 'updateProfil'])->name('profil.update');
});

// ─── OWNER ───────────────────────────────────────────────────
Route::middleware(['role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard',          [OwnerController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders',             [OwnerController::class, 'semuaOrder'])->name('semua_order');
    Route::get('/orders/{id}',        [OwnerController::class, 'detailOrder'])->name('order.detail');
    Route::post('/orders/batalkan',   [OwnerController::class, 'batalkanOrder'])->name('order.batalkan');
    Route::get('/katalog',            [OwnerController::class, 'katalog'])->name('katalog');
    Route::post('/katalog',           [OwnerController::class, 'storeKatalog'])->name('katalog.store');
    Route::put('/katalog/{id}',       [OwnerController::class, 'updateKatalog'])->name('katalog.update');
    Route::delete('/katalog/{id}',    [OwnerController::class, 'deleteKatalog'])->name('katalog.delete');
    Route::post('/settings',           [OwnerController::class, 'updateSettings'])->name('settings.update');
    Route::get('/layanan',            [OwnerController::class, 'layanan'])->name('layanan');
    Route::post('/layanan',           [OwnerController::class, 'storeLayanan'])->name('layanan.store');
    Route::put('/layanan/{id}',       [OwnerController::class, 'updateLayanan'])->name('layanan.update');
    Route::delete('/layanan/{id}',    [OwnerController::class, 'deleteLayanan'])->name('layanan.delete');
    Route::get('/staff',              [OwnerController::class, 'staff'])->name('staff');
    Route::post('/staff',             [OwnerController::class, 'storeStaff'])->name('staff.store');
    Route::get('/staff/{id}/edit',    [OwnerController::class, 'editStaff'])->name('staff.edit');
    Route::put('/staff/{id}',         [OwnerController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/{id}',      [OwnerController::class, 'deleteStaff'])->name('staff.delete');
    Route::get('/invoice',            [OwnerController::class, 'invoice'])->name('invoice');
    Route::get('/invoice/{id}/print',  [OwnerController::class, 'printInvoice'])->name('invoice.print');
    Route::get('/invoice/{id}/download', [OwnerController::class, 'downloadInvoice'])->name('invoice.download');
    Route::get('/laporan',                [OwnerController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/export-pdf',     [OwnerController::class, 'laporanPdf'])->name('laporan.pdf');
    Route::get('/laporan/export-excel',   [OwnerController::class, 'laporanExcel'])->name('laporan.excel');
});

// ─── NOTIFICATIONS ───────────────────────────────────────────
Route::middleware(['role:customer,staff,owner'])->group(function () {
    Route::get('/notifications',       [NotificationController::class, 'get'])->name('notifications.get');
    Route::post('/notifications/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});
