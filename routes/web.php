<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\RecipientImportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Hanya admin yang boleh akses route ini
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/recipients/print-all', [RecipientController::class, 'printAllQrCodes'])->name('recipients.printAll');
    Route::resource('recipients', RecipientController::class);

    Route::resource('recipients', RecipientController::class);
    Route::get('/recipients/{recipient}/qr-code', [RecipientController::class, 'generateQrCode'])->name('recipients.qr-code');
    Route::get('/recipients/{recipient}/qr-print', [RecipientController::class, 'printQrCode'])->name('recipients.qr-print');
    Route::get('/scan', [RecipientController::class, 'scanQr'])->name('recipients.scan');
    Route::post('/verify-qr', [RecipientController::class, 'verifyQr'])->name('recipients.verify-qr');
    Route::post('/recipients/{recipient}/distribute', [RecipientController::class, 'distribute'])->name('recipients.distribute');
    Route::get('/recipients/{recipient}/receipt', [RecipientController::class, 'generateReceipt'])->name('recipients.receipt');
    Route::get('/recipients/{recipient}/signature', [RecipientController::class, 'generateSignatureForm'])->name('recipients.signature');
    Route::get('/report', [RecipientController::class, 'generateReport'])->name('recipients.report');
    Route::get('/recipient/import', [RecipientImportController::class, 'form']);
    Route::post('/recipient/import', [RecipientImportController::class, 'import'])->name('recipients.import');
    Route::post('/verify-qr-registration', [RecipientController::class, 'verifyQrRegistration']);
    Route::post('/mark-registered', [RecipientController::class, 'markRegistered']);
    Route::get('/registration', function () {
        return view('registration');
    })->name('registration');

    Route::post('/registration/verify', [RegistrationController::class, 'verifyRegistrationQr'])->name('registration.verify');
    Route::post('/registration/confirm', [RegistrationController::class, 'confirmRegistration'])->name('registration.confirm');
});



Auth::routes();

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('userdashboard');
    Route::get('/list', [UserController::class, 'list'])->name('list');
});
