<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HobbyController;
use App\Http\Controllers\UserHobbyController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route:: middleware('auth:api')->group(function(){
    Route::get('/userid', [AuthController::class, 'userid']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('user-hobby/users', [UserHobbyController::class, 'index']);
    Route::get('user-hobby/user-hobbies/{id}', [UserHobbyController::class, 'show']);
    Route::post('user-hobby/add-user', [UserHobbyController::class, 'store']);
    Route::put('user-hobby/update-user/{id}', [UserHobbyController::class, 'update']);
    Route::delete('user-hobby/delete-user/{id}', [UserHobbyController::class, 'destroy']);
    Route::get('hobby/get-all', [HobbyController::class, 'index']);
    Route::post('hobby/add-hobby', [HobbyController::class, 'store']);
    Route::put('hobby/update-hobby/{id}', [HobbyController::class, 'update']);
    Route::delete('hobby/delete-hobby/{id}', [HobbyController::class, 'destroy']);

});