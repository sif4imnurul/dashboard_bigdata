<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\YFinanceController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [NewsController::class, 'index']);

// berita 
Route::get('/berita', [NewsController::class, 'showAllNews'])->name('news.index');
Route::get('/berita/search', [NewsController::class, 'showAllNews'])->name('news.search');
Route::get('/emiten', [YFinanceController::class, 'index'])->name('emiten.index');
