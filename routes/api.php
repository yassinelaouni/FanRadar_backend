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
});


// Routes publiques
Route::post('/login', [AuthentificationController::class, 'login']);
Route::post('/register', [AuthentificationController::class, 'register']);

// ========== ROUTES PUBLIQUES POUR E-COMMERCE ==========

// � PRODUITS - Routes publiques
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

// 🛒 COMMANDES - Routes publiques (pour test uniquement)
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'destroy']);
Route::get('/orders/{order}', [OrderController::class, 'update']);

// 🔗 PRODUITS DE COMMANDE - Routes publiques (pour test uniquement)
Route::get('/order-products', [OrderProductController::class, 'index']);
Route::get('/order-products/{orderProduct}', [OrderProductController::class, 'show']);


