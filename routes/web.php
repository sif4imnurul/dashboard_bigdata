<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\YFinanceController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\GrafikController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Option 2: Keep '/' unnamed, and add a separate /dashboard route
Route::get('/', [NewsController::class, 'index']); // Unnamed root
Route::get('/dashboard', [NewsController::class, 'index'])->name('dashboard'); // Named dashboard
// berita 
Route::get('/berita', [NewsController::class, 'showAllNews'])->name('news.index');
Route::get('/berita/search', [NewsController::class, 'showAllNews'])->name('news.search');

Route::get('/emiten', [YFinanceController::class, 'index'])->name('emiten.index');

// idx
Route::get('/financial-reports', [FinancialReportController::class, 'index'])->name('financial_reports.index');
Route::get('/financial-reports/test-api', [FinancialReportController::class, 'testApi']);
Route::get('/financial-reports/periods/{year}', [FinancialReportController::class, 'getPeriodsForYear']);

// Route untuk halaman grafik saham
Route::get('/grafik-saham', [GrafikController::class, 'index'])->name('grafik.index');
// Route untuk mendapatkan data grafik berdasarkan periode
Route::get('/grafik-saham/data', [GrafikController::class, 'getChartData'])->name('grafik.data');
// Route untuk pencarian saham
Route::get('/grafik-saham/search', [GrafikController::class, 'search'])->name('grafik.search');