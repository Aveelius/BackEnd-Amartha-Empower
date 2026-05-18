<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('landing');
});

Route::get('/user', function () {
    return view('user');
});

Route::get('/user/profile', function () {
    return view('user-profile');
});

Route::get('/user/login', function () {
    return view('user-login');
});

Route::get('/user/register', function () {
    return view('user-register');
});

Route::get('/user/quick-loan', function () {
    return view('user-quick-loan');
});

Route::get('/user/payment', function () {
    return view('user-payment');
});

Route::get('/user/ruang-belajar', function () {
    return view('user-ruang-belajar');
});

Route::get('/user/komunitas', function () {
    return view('user-komunitas');
});

Route::get('/admin', function () {
    return view('admin');
});

Route::get('/admin/loans', function () {
    return view('admin-loans');
});

Route::get('/admin/payments', function () {
    return view('admin-payments');
});
