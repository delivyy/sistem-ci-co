<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InputKodeController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\RoomListController;
use App\Http\Middleware\RoleRedirect;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\dinasApprovalController;
use App\Models\Absen;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\DutyOfficerController;
use App\Exports\BookingsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SDGController;




// udah fix jangan kerubah 
Route::get('/', function () {
    return view('welcome', [
        'absen' => Absen::all()
    ]);
})->name('dashboard');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware(RoleRedirect::class);
Route::get('/front-office/match', [InputKodeController::class, 'match'])->name('inputkode.match');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// front_office
Route::middleware(['auth', 'role:frontoffice,IT'])->group(function () {

    Route::get('/front-office/dashboard', [BookingsController::class, 'index'])->name('front_office.dashboard');
    Route::post('/bookings/{id}/update-status', [BookingsController::class, 'updateStatus']);
    Route::post('/update-duty-officer', [BookingsController::class, 'updateDutyOfficer'])
        ->middleware('auth') // Tambahkan middleware di sini
        ->name('update-duty-officer');
    Route::get('export-bookings', [BookingsController::class, 'exportBookings'])->name('bookings.export');
    Route::get('/front-office/roomlist', [RoomListController::class, 'index'])->name('front_office.index');
    Route::get('/front-office/roomlist', [RoomListController::class, 'filter'])->name('front_office.roomList');
    Route::get('/inputkode/validate-role', [InputKodeController::class, 'validateRole'])->name('inputkode.validate.role');
    Route::post('/duty-officer/store', [DutyOfficerController::class, 'storeDutyOfficer'])->name('dutyofficer.store');
});

// diskopindag
Route::middleware(['auth', 'role:kabid,kadin,IT'])->group(function () {
    Route::get('/dinas/approve', [dinasApprovalController::class, 'index'])->name('dinas.approve');
    Route::post('/approval/store', [dinasApprovalController::class, 'store'])->name('approval.store');
    Route::post('/approve-kabid', [dinasApprovalController::class, 'approveKabid'])->name('approve.kabid');
    Route::post('/approve-kadin', [dinasApprovalController::class, 'approveKadin'])->name('approve.kadin');

});

// register
Route::get('/front-office/register', [RegistrationController::class, 'showRegistrationForm'])->name('front_office.register');
Route::post('/front-office/register', [RegistrationController::class, 'register'])->name('front_office.register.post');

// user biasa dan fo
Route::post('/store', [AbsenController::class, 'store'])->name('store');
Route::get('/booking/details/{kode_booking}', [BookingsController::class, 'showDetails'])->name('booking.details');
Route::post('/checkin/store', [AbsenController::class, 'checkinstore'])->name('checkin.store');
Route::match(['get', 'post'], '/peminjaman/{kode_booking}', [InputKodeController::class, 'showPeminjaman'])->name('peminjaman.show');
Route::get('/front-office/inputkode', [InputKodeController::class, 'show'])->name('front_office.inputkode');
Route::get('/front-office/inputkode', [InputKodeController::class, 'show'])->name('inputkode.show');
Route::post('/front-office/match', [InputKodeController::class, 'match'])->name('inputkode.match');
Route::post('/booking/complete-check-in/{kode_booking}', [InputKodeController::class, 'completeCheckIn'])->name('booking.completeCheckIn');
Route::post('/checkout', [InputKodeController::class, 'checkout'])->name('inputkode.checkout');


//marketing

Route::middleware(['auth', 'role:marketing,IT'])->group(function () {
    Route::get('/marketing/peminjaman', [MarketingController::class, 'index'])->name('marketing.peminjaman');
    Route::post('/marketing/store', [MarketingController::class, 'store'])->name('marketing.store');
    Route::delete('/items/{id}', [MarketingController::class, 'destroy'])->name('items.destroy');
    // Route untuk update dan tambah barang
    Route::post('/peminjaman/update', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/create/{nama_event}', [PeminjamanController::class, 'showEdit'])->name('peminjaman.create');
    Route::get('/marketing/history', [MarketingController::class, 'history'])->name('marketing.history');

});


// production 
Route::middleware(['auth', 'role:produksi,IT'])->group(function () {

    Route::get('/production/peminjaman', [ProductionController::class, 'index'])->name('production.peminjaman');
});
Route::get('/generate-qrcode/{bookingCode}', [QRCodeController::class, 'generateQRCode']);
// Route::get('/generate-qrcode/{bookingCode}', [QRCodeController::class, 'sendQRCode']);

Route::post('/send-qrcode-email', [QRCodeController::class, 'sendQRCodeEmail'])->name('send.qrcode.email');
Route::get('/qrcode/{bookingCode}', [QRCodeController::class, 'showQRCodePage'])->name('qrcode.page');
Route::middleware(['auth', 'role:IT'])->group(function () {
    Route::get('/it/users', [UserController::class, 'index'])->name('it.index');
    Route::get('/it/users/create', [UserController::class, 'create'])->name('it.users.create');
    Route::post('/it/users', [UserController::class, 'store'])->name('it.users.store');
    Route::get('/it/users/{id}/edit', [UserController::class, 'edit'])->name('it.users.edit');
    Route::put('/it/users/{id}', [UserController::class, 'update'])->name('it.users.update');
    Route::delete('/it/users/{id}', [UserController::class, 'destroy'])->name('it.users.destroy');
});

Route::get('/sdgs-quiz', [SDGController::class, 'sdgsQuiz']);
Route::get('/result', [SDGController::class, 'result']);
Route::post('/get-sdg-data', [SDGController::class, 'getSDGData']);
Route::get('/dashboard', [SDGController::class, 'dashboard'])->name('all.Dashboard');
Route::get('/detail-dashboard/{sdg_id}', [SDGController::class, 'detailDashboard'])->name('all.detail.Dashboard');
Route::post('/save-sdg-result', [SDGController::class, 'saveSDGResult']);
Route::get('/lantai/{lantai}', [SDGController::class, 'detailLantai']);
Route::get('/lantai/{lantai}/ruangan/{ruangan}', [SDGController::class, 'roomDetail']);

Route::get('/test-dashboard', function () {
    return view('test'); 
});