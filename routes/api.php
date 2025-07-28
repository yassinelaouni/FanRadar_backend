<?php

use App\Http\Controllers\Api\AuthentificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// !!!All this routes starts with api/

// Routes protégées par l'authentification
Route::middleware('auth:sanctum')->group(function () {
   Route::post('/logout', [AuthentificationController::class, 'logout']);
   Route::post('/logoutAllDevices', [AuthentificationController::class, 'logoutfromAllDevices']);//all auth token will be deleted

   // Routes pour les produits
   Route::apiResource('products', ProductController::class);
   
   // Routes pour les commandes
   Route::apiResource('orders', OrderController::class);
   
   // Routes pour les produits de commande
   Route::apiResource('order-products', OrderProductController::class);
});

// Routes publiques
Route::post('/login', [AuthentificationController::class, 'login']);
Route::post('/register', [AuthentificationController::class, 'register']);

// Routes publiques pour les produits (consultation uniquement)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);


