<?php

use App\Http\Controllers\Api\AuthentificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// !!!All this routes starts with api/




Route::middleware('auth:sanctum')->group(function () {
   Route::post('/logout', [AuthentificationController::class, 'logout']);
      Route::post('/logoutAllDevices', [AuthentificationController::class, 'logoutfromAllDevices']);//all auth token will be deleted
});

Route::post('/login', [AuthentificationController::class, 'login']);
Route::post('/register', [AuthentificationController::class, 'register']);


