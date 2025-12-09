<?php

use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;


Route::get('/', [DataController::class, 'index'])->name('beranda');
Route::get('/search', [DataController::class, 'search'])->name('search');
Route::post('/upload-spk', [DataController::class, 'uploadSpk'])->name('upload.spk');