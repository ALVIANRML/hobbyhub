<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/users', function () {
    return view('users.index');
})->name('users.index');

Route::get('/hobbies', function () {
    return view('hobbies.index');
})->name('hobbies.index');
