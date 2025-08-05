<?php

use App\Http\Controllers\Api\AuthentificationController;
use App\Http\Controllers\Api\PersonnaliseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\TagController;
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

// � Post - Routes publiques
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);
Route::post('/posts', [PostController::class, 'store']);
Route::put('/posts/{post}', [PostController::class, 'update']);
Route::delete('/posts/{post}', [PostController::class, 'destroy']);

// 🛒 COMMANDES - Routes publiques (pour test uniquement)
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);
Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
Route::put('/orders/{order}', [OrderController::class, 'update']);


Route::post('/tags/attach', [TagController::class, 'attachTag']);// donner et cree un tage pour un post ou product
Route::delete('/tags/detach', [TagController::class, 'detachTag']);


Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
Route::put('/categories/{category}', [CategoryController::class, 'update']);

Route::get('/subcategories', [SubcategoryController::class, 'index']);
Route::get('/subcategories/{subcategory}', [SubcategoryController::class, 'show']);
Route::post('/subcategories', [SubcategoryController::class, 'store']);
Route::delete('/subcategories/{subcategory}', [SubcategoryController::class, 'destroy']);
Route::put('/subcategories/{subcategory}', [SubcategoryController::class, 'update']);

// ==========================================
// NOUVELLES ROUTES API PERSONNALISÉES
// ==========================================

// ====================
// AUTHENTICATION PERSONNALISÉ
// ====================
Route::post('/auth/login', [PersonnaliseController::class, 'login']);
Route::post('/auth/register', [PersonnaliseController::class, 'register']);

// ====================
// USER PROFILE
// ====================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/profile', [PersonnaliseController::class, 'getUserProfile']);
    Route::put('/users/profile', [PersonnaliseController::class, 'updateUserProfile']);
});

Route::get('/users/{userId}/posts', [PersonnaliseController::class, 'getUserPosts']);

// ====================
// MAIN CONTENT / FEED
// ====================
Route::get('/feed/home', [PersonnaliseController::class, 'getHomeFeed']);
Route::get('/feed/explore', [PersonnaliseController::class, 'getExploreFeed']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts/create', [PersonnaliseController::class, 'createPost']);
    Route::post('/posts/{postId}/like', [PersonnaliseController::class, 'likePost']);
});

// ====================
// CATEGORIES PERSONNALISÉES
// ====================
Route::get('/categories/list', [PersonnaliseController::class, 'getCategories']);
Route::get('/categories/{category}/content', [PersonnaliseController::class, 'getCategoryContent']);

// ====================
// SOCIAL / USER RELATIONS
// ====================
Route::get('/users/{userId}/followers', [PersonnaliseController::class, 'getUserFollowers']);
Route::get('/users/{userId}/following', [PersonnaliseController::class, 'getUserFollowing']);
Route::post('/users/{userId}/follow', [PersonnaliseController::class, 'followUser']);
Route::get('/posts/saved', [PersonnaliseController::class, 'getSavedPosts']);
Route::put('/users/avatar', [PersonnaliseController::class, 'updateAvatar']);
Route::put('/users/cover-photo', [PersonnaliseController::class, 'updateCoverPhoto']);

// ====================
// POSTS
// ====================
Route::post('/posts/{postId}/comments', [PersonnaliseController::class, 'addCommentToPost']);
Route::post('/posts/{postId}/share', [PersonnaliseController::class, 'sharePost']);

// ====================
// HASHTAGS
// ====================
Route::get('/hashtags/{hashtag}/posts', [PersonnaliseController::class, 'getHashtagPosts']);

// ====================
// STORE / E-COMMERCE
// ====================
Route::get('/store/categories', [PersonnaliseController::class, 'getStoreCategories']);
Route::get('/store/brands', [PersonnaliseController::class, 'getStoreBrands']);
Route::post('/store/cart', [PersonnaliseController::class, 'addToCart']);
Route::post('/store/wishlist/{productId}', [PersonnaliseController::class, 'addToWishlist']);
Route::get('/store/cart', [PersonnaliseController::class, 'getCart']);
Route::put('/store/cart/{itemId}', [PersonnaliseController::class, 'updateCartItem']);
Route::delete('/store/cart/{itemId}', [PersonnaliseController::class, 'removeCartItem']);
Route::post('/store/orders', [PersonnaliseController::class, 'createOrder']);
Route::get('/store/orders', [PersonnaliseController::class, 'getOrders']);
Route::put('/store/orders/{orderId}/cancel', [PersonnaliseController::class, 'cancelOrder']);
Route::get('/store/orders/{orderId}', [PersonnaliseController::class, 'getOrderDetails']);
Route::post('/store/orders/{orderId}/review', [PersonnaliseController::class, 'reviewOrder']);
Route::get('/store/products', [PersonnaliseController::class, 'getStoreProducts']);

// ====================
// UPLOAD IMAGE
// ====================
Route::post('/upload/image', [PersonnaliseController::class, 'uploadImage']);





