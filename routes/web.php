<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReceiptController;

Route::get('/', [ReceiptController::class, 'uploadForm'])->name('form');
Route::post('/process', [ReceiptController::class, 'process'])->name('process');
