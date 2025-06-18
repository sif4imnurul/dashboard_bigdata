<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\YFinanceController;
use App\Http\Controllers\FinancialReportController;
use App\Http.Controllers\GrafikController;

/*
|--------------------------------------------------------------------------
| Route Asli (Dipertahankan)
|--------------------------------------------------------------------------
*/
// Halaman utama dan dashboard akan tetap menampilkan berita
Route::get('/', [NewsController::class, 'index']);
Route::get('/dashboard', [NewsController::class, 'index'])->name('dashboard');

// Route untuk Berita
Route::get('/berita', [NewsController::class, 'showAllNews'])->name('news.index');
Route::get('/berita/search', [NewsController::class, 'showAllNews'])->name('news.search');

// Route untuk Daftar Emiten (dari file XLSX)
Route::get('/emiten', [YFinanceController::class, 'index'])->name('emiten.index');

// Route untuk Laporan Keuangan
Route::get('/financial-reports', [FinancialReportController::class, 'index'])->name('financial_reports.index');


/*
|--------------------------------------------------------------------------
| Route untuk Halaman Grafik (Telah Dimodifikasi menjadi Dinamis)
|--------------------------------------------------------------------------
| Route ini diubah agar bisa menerima kode saham.
*/
// Contoh: /grafik/BBCA atau /grafik/AALI
// Jika kode saham tidak ada, controller akan menggunakan default.
Route::get('/grafik/{stock_code?}', [GrafikController::class, 'index'])->name('grafik.index');

// Endpoint AJAX untuk mengambil data chart dinamis saat periode diubah
Route::get('/ajax/chart-data/{stock_code}', [GrafikController::class, 'getChartData'])->name('grafik.data');

