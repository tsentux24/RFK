<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\RfkController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
require __DIR__.'/auth.php';

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

        Route::get('/dataopd', [OpdController::class, 'index']);
        Route::post('/opd', [OpdController::class, 'store'])->name('opd.store');
        Route::get('/opd/{id}/get-data', [OpdController::class, 'getData'])->name('opd.get-data');
        Route::get('/opd/{id}/edit', [OpdController::class, 'edit'])->name('opd.edit');
        Route::put('/opd/{id}', [OpdController::class, 'update'])->name('opd.update');
        Route::post('/opd/{id}', [OpdController::class, 'update']);
        Route::delete('/opd/{id}', [OpdController::class, 'destroy'])->name('opd.destroy');
        #Route::delete('opd/{id}', 'OpdController@destroy')->name('opd.destroy');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
         // API endpoints untuk AJAX
    Route::get('/dashboard/users/data', [UserController::class, 'getData'])->name('users.data');
    Route::get('/dashboard/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/dashboard/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/dashboard/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/dashboard/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/dashboard/users/export', [UserController::class, 'export'])->name('users.export');
     // RFK Routes
    Route::get('/dashboard/rfk', [RfkController::class, 'index'])->name('rfk.index');
    Route::post('/dashboard/rfk/store', [RfkController::class, 'store'])->name('rfk.store');
    Route::get('/dashboard/rfk/data', [RfkController::class, 'getData'])->name('rfk.data');
    Route::put('/dashboard/rfk/{id}', [RfkController::class, 'update'])->name('rfk.update');
    Route::delete('/dashboard/rfk/{id}', [RfkController::class, 'destroy'])->name('rfk.destroy');

    // RFK Laporan Routes
    Route::get('/dashboard/laporan', [RfkController::class, 'laporanPage'])->name('laporan.index');
    Route::get('/dashboard/laporan/data', [RfkController::class, 'getLaporanData'])->name('laporan.data');
    Route::post('/dashboard/laporan/generate-pdf', [RfkController::class, 'generateLaporanPdf'])->name('laporan.pdf');
    Route::get('/dashboard/stats', [RfkController::class, 'getDashboardStats'])->name('dashboard.stats');
    Route::get('/dashboard/superadmin/data', [RfkController::class, 'getSuperadminData'])->name('superadmin.data');

    // RFK Realisasi & Approval Routes
    Route::get('/dashboard/rfk/audit', [RfkController::class, 'auditPage'])->name('rfk.audit');
    Route::get('/dashboard/rfk/pending', [RfkController::class, 'getPendingApproval'])->name('rfk.pending');
    Route::get('/dashboard/rfk/history', [RfkController::class, 'getHistory'])->name('rfk.history');
    Route::post('/dashboard/rfk/{id}/realisasi', [RfkController::class, 'storeRealisasi'])->name('rfk.storeRealisasi');
    Route::put('/dashboard/rfk/realisasi/{id}', [RfkController::class, 'updateRealisasi'])->name('rfk.updateRealisasi');
    Route::post('/dashboard/rfk/realisasi/{id}/approve', [RfkController::class, 'approveRealisasi'])->name('rfk.approveRealisasi');
    Route::post('/dashboard/rfk/realisasi/{id}/reject', [RfkController::class, 'rejectRealisasi'])->name('rfk.rejectRealisasi');
    Route::post('/dashboard/rfk/{id}/change-status', [RfkController::class, 'changeStatusMaster'])->name('rfk.changeStatusMaster');

    // RFK Validation Engine
    Route::get('/dashboard/validation-engine', [RfkController::class, 'validationEnginePage'])->name('validation.engine');
    Route::get('/dashboard/validation-engine/run', [RfkController::class, 'runValidationEngine'])->name('validation.run');

});
