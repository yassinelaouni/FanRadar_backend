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

// ========== ROUTES DE TEST (GET) ==========

// Route de test simple
Route::get('/test', function () {
    return response()->json([
        'message' => 'API FanRadar fonctionne parfaitement !',
        'timestamp' => now(),
        'status' => 'success',
        'version' => '1.0.0'
    ]);
});

// Test de base de données
Route::get('/test-db', function () {
    try {
        $userCount = \App\Models\User::count();
        $productCount = \App\Models\Product::count();
        
        return response()->json([
            'message' => 'Base de données connectée !',
            'users_count' => $userCount,
            'products_count' => $productCount,
            'status' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur de base de données',
            'error' => $e->getMessage(),
            'status' => 'error'
        ], 500);
    }
});

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

// 🔗 PRODUITS DE COMMANDE - Routes publiques (pour test uniquement)
Route::get('/order-products', [OrderProductController::class, 'index']);
Route::get('/order-products/{orderProduct}', [OrderProductController::class, 'show']);

// ========== ROUTES DE DIAGNOSTIC ==========

// Liste de toutes les tables disponibles
Route::get('/tables', function () {
    return response()->json([
        'message' => 'Tables FanRadar disponibles (Système E-commerce)',
        'tables' => [
            'users' => '/api/users (via auth)',
            'products' => '/api/products',
            'orders' => '/api/orders', 
            'order_products' => '/api/order-products'
        ],
        'test_routes' => [
            'api_test' => '/api/test',
            'db_test' => '/api/test-db',
            'tables_list' => '/api/tables'
        ],
        'auth_routes' => [
            'login' => 'POST /api/login',
            'register' => 'POST /api/register'
        ],
        'system_type' => 'E-commerce Backend'
    ]);
});


