<?php

use App\Http\Controllers\Api\AuthentificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommunityController;
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
   
   // ========== NOUVELLES ROUTES SYSTÈME SOCIAL ==========
   
   // Routes pour les catégories
   Route::apiResource('categories', CategoryController::class);
   
   // Routes pour les sous-catégories
   Route::apiResource('subcategories', SubcategoryController::class);
   
   // Routes pour le contenu
   Route::apiResource('content', ContentController::class);
   
   // Routes pour les médias
   Route::apiResource('media', MediaController::class);
   
   // Routes pour les posts
   Route::apiResource('posts', PostController::class);
   
   // Routes pour les favoris
   Route::apiResource('favorites', FavoriteController::class);
   
   // Routes pour les évaluations
   Route::apiResource('ratings', RatingController::class);
   
   // Routes pour les tags
   Route::apiResource('tags', TagController::class);
   
   // Routes pour les communautés
   Route::apiResource('communities', CommunityController::class);
});

// Routes publiques
Route::post('/login', [AuthentificationController::class, 'login']);
Route::post('/register', [AuthentificationController::class, 'register']);

// ========== ROUTES PUBLIQUES POUR TESTS ==========

// Route de test simple
Route::get('/test', function () {
    return response()->json([
        'message' => 'API FanRadar fonctionne !',
        'timestamp' => now(),
        'status' => 'success'
    ]);
});

// Routes publiques pour consultation (sans authentification)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::get('/subcategories', [SubcategoryController::class, 'index']);
Route::get('/content', [ContentController::class, 'index']);
Route::get('/tags', [TagController::class, 'index']);


