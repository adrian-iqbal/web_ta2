<?php

use App\Http\Controllers\ExportTransaksiController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/export/transaksi/{id}', [ExportTransaksiController::class, 'export'])->name('export.transaksi');

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});