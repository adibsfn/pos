<?php

use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/notif-count', function () {
    return request()->user()->unreadNotifications()->count();
})->middleware('auth');

Route::get('/pos-app', [PosController::class, 'index'])->name('pos.app');
