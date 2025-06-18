<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\YFinanceController;
use App\Http\Controllers\FinancialReportController;

/*
|--------------------------------------------------------------------------
| Halaman Utama & Detail Saham -> DITANGANI OLEH GRAFIKCONTROLLER
|--------------------------------------------------------------------------
*/
Route::get('/{stock_code?}', [GrafikController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Route Pendukung Lainnya
|--------------------------------------------------------------------------
*/
// Endpoint AJAX untuk mengambil data chart dinamis dari halaman dashboard
Route::get('/ajax/chart-data/{stock_code}', [GrafikController::class, 'getChartData'])->name('grafik.data');

// Halaman untuk menampilkan SEMUA berita dengan paginasi
Route::get('/news/all', [NewsController::class, 'showAllNews'])->name('news.index');

// Halaman untuk daftar emiten dari file Excel
Route::get('/emiten/list', [YFinanceController::class, 'index'])->name('emiten.index');

// Halaman untuk laporan keuangan dari API IDX
Route::get('/reports/financial', [FinancialReportController::class, 'index'])->name('financial_reports.index');

