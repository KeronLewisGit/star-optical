<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\PatientNoteController;
use App\Http\Controllers\Admin\PrescriptionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');
Route::post('/book', [BookingController::class, 'store'])->middleware('throttle:booking')->name('booking.store');
Route::get('/thank-you/{booking:reference}', [BookingController::class, 'thanks'])->name('booking.thanks');

// SEO landing pages for the main search terms (see PageController for content)
Route::get('/free-eye-exam-cunupia-trinidad', [PageController::class, 'show'])->defaults('slug', 'eye-exams')->name('page.eye-exams');
Route::get('/eyeglasses-frames-trinidad', [PageController::class, 'show'])->defaults('slug', 'eyeglasses')->name('page.eyeglasses');
Route::get('/sunglasses-trinidad', [PageController::class, 'show'])->defaults('slug', 'sunglasses')->name('page.sunglasses');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| Two-factor challenge (after password, before the admin area)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('two-factor', [TwoFactorChallengeController::class, 'create'])->name('two-factor.challenge');
    Route::post('two-factor', [TwoFactorChallengeController::class, 'store'])->middleware('throttle:two-factor');
});

/*
|--------------------------------------------------------------------------
| Admin / CRM
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'two-factor'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    // Bookings (leads)
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::get('bookings/{booking}/edit', [AdminBookingController::class, 'edit'])->name('bookings.edit');
    Route::put('bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
    Route::post('bookings/{booking}/convert', [AdminBookingController::class, 'convert'])->name('bookings.convert');
    Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])->middleware('admin')->name('bookings.destroy');

    // Patients (CRM records)
    Route::resource('patients', PatientController::class)->except('destroy');
    Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->middleware('admin')->name('patients.destroy');
    Route::post('patients/{patient}/notes', [PatientNoteController::class, 'store'])->name('patients.notes.store');
    Route::delete('patients/{patient}/notes/{note}', [PatientNoteController::class, 'destroy'])->name('patients.notes.destroy');
    Route::post('patients/{patient}/prescriptions', [PrescriptionController::class, 'store'])->name('patients.prescriptions.store');
    Route::put('patients/{patient}/prescriptions/{prescription}', [PrescriptionController::class, 'update'])->name('patients.prescriptions.update');
    Route::delete('patients/{patient}/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])->name('patients.prescriptions.destroy');

    // Promotions shown on the website
    Route::resource('promotions', PromotionController::class)->except('show');

    // Own profile + two-factor setup
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/two-factor', [TwoFactorController::class, 'enable'])->name('profile.two-factor.enable');
    Route::post('profile/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('profile.two-factor.confirm');
    Route::post('profile/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('profile.two-factor.recovery');
    Route::delete('profile/two-factor', [TwoFactorController::class, 'disable'])->name('profile.two-factor.disable');

    // Administrator-only
    Route::middleware('admin')->group(function () {
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::delete('users/{user}/two-factor', [UserController::class, 'resetTwoFactor'])->name('users.two-factor.reset');
        Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
    });
});

require __DIR__.'/auth.php';
