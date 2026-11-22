<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
Route::get('/items/{item}/row', [ItemController::class, 'row'])->name('items.row');
Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
