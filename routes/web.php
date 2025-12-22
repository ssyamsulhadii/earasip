<?php

use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;


Route::get('/', [DataController::class, 'index'])->name('beranda');
Route::get('/search', [DataController::class, 'search'])->name('search');
Route::get('/cetak-spk/{no_peserta}/{nik}', [DataController::class, 'cetakSPK'])->name('cetak.spk');
Route::post('/upload-spk', [DataController::class, 'uploadSpk'])->name('upload.spk');
Route::get('/update-data', [DataController::class, 'updateData']);
Route::get('/lihat/{no_peserta}/{nik}', [DataController::class, 'lihatSPK'])->name('lihat.spk');
Route::get('/cetak-spp/{no_peserta}/{nik}', [DataController::class, 'cetakSPP'])->name('cetak.spp');
