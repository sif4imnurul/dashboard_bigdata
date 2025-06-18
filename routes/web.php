<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\YFinanceController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\GrafikController;

/*
|--------------------------------------------------------------------------
| Halaman Utama & Detail Saham
|--------------------------------------------------------------------------
| Route ini diubah agar halaman utama menampilkan dasbor lengkap dari GrafikController.
*/
Route::get('/{stock_code?}', [GrafikController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Route Pendukung Lainnya
|--------------------------------------------------------------------------
*/

// Endpoint AJAX untuk mengambil data chart dinamis
Route::get('/ajax/chart-data/{stock_code}', [GrafikController::class, 'getChartData'])->name('grafik.data');

// Route untuk halaman khusus Berita
Route::get('/berita/semua', [NewsController::class, 'showAllNews'])->name('news.index');

// Route untuk Daftar Emiten
Route::get('/emiten/list', [YFinanceController::class, 'index'])->name('emiten.index');

// Route untuk Laporan Keuangan
Route::get('/reports/financial', [FinancialReportController::class, 'index'])->name('financial_reports.index');

