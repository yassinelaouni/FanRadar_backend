<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PersonnaliseController extends Controller
{
    // ====================
    // AUTHENTICATION 
    // ====================

    /**
     * Connexion utilisateur
     * Route: POST /api/auth/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Email et mot de passe requis',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Email ou mot de passe invalide'
                ]
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'avatar' => $user->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . '+' . $user->last_name),
                    'followers' => 0, // À calculer selon vos relations
                    'following' => 0, // À calculer selon vos relations
                    'posts' => 0 // À calculer selon vos relations
                ],
                'token' => $token
            ]
        ]);
    }

    /**
     * Inscription utilisateur
     * Route: POST /api/auth/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Données invalides',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        // Séparer le nom complet en prénom et nom
        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_image' => 'default.png'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Please verify your email',
            'data' => [
                'email' => $user->email,
                'verificationRequired' => true
            ]
        ], 201);
    }

    // ====================
    // USER PROFILE
    // ====================

    /**
     * Obtenir le profil utilisateur
     * Route: GET /api/users/profile
     */
    public function getUserProfile()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'username' => strtolower($user->first_name . $user->last_name),
                'email' => $user->email,
                'avatar' => $user->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . '+' . $user->last_name),
                'coverPhoto' => null,
                'bio' => null,
                'followers' => 0,
                'following' => 0,
                'posts' => 0,
                'joinedDate' => $user->created_at->toISOString(),
                'isVerified' => false
            ]
        ]);
    }

    /**
     * Mettre à jour le profil utilisateur
     * Route: PUT /api/users/profile
     */
    public function updateUserProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string|max:500',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'categories' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Données invalides',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $user->update([
            'first_name' => $request->has('name') ? explode(' ', $request->name, 2)[0] : $user->first_name,
            'last_name' => $request->has('name') ? (explode(' ', $request->name, 2)[1] ?? '') : $user->last_name,
            'email' => $request->email ?? $user->email
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'username' => strtolower($user->first_name . $user->last_name),
                    'email' => $user->email,
                    'bio' => $request->bio ?? null,
                    'avatar' => $user->profile_image,
                    'updatedAt' => $user->updated_at->toISOString()
                ]
            ]
        ]);
    }

    /**
     * Obtenir les posts d'un utilisateur
     * Route: GET /api/users/{userId}/posts
     */
    public function getUserPosts($userId, Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $posts = Post::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => [
                'posts' => $posts->items(),
                'pagination' => [
                    'page' => $posts->currentPage(),
                    'limit' => $posts->perPage(),
                    'total' => $posts->total(),
                    'pages' => $posts->lastPage()
                ]
            ]
        ]);
    }

    // ====================
    // MAIN CONTENT
    // ====================

    /**
     * Obtenir le feed principal
     * Route: GET /api/feed/home
     */
    public function getHomeFeed(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $posts = Post::with('user')
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);

        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'content' => $post->content,
                'media' => $post->image ? [$post->image] : [],
                'author' => [
                    'id' => $post->user->id,
                    'name' => $post->user->first_name . ' ' . $post->user->last_name,
                    'username' => strtolower($post->user->first_name . $post->user->last_name),
                    'avatar' => $post->user->profile_image
                ],
                'tags' => [],
                'likes' => 0,
                'comments' => 0,
                'shares' => 0,
                'isLiked' => false,
                'createdAt' => $post->created_at->toISOString()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'posts' => $formattedPosts,
                'pagination' => [
                    'page' => $posts->currentPage(),
                    'limit' => $posts->perPage(),
                    'hasNext' => $posts->hasMorePages()
                ]
            ]
        ]);
    }

    /**
     * Créer un nouveau post
     * Route: POST /api/posts
     */
    public function createPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
            'media' => 'sometimes|array',
            'tags' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Contenu requis',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $user = Auth::user();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => substr($request->content, 0, 100),
            'content' => $request->content,
            'image' => $request->media[0] ?? null,
            'content_status' => 'published'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'post' => [
                    'id' => $post->id,
                    'content' => $post->content,
                    'media' => $post->image ? [$post->image] : [],
                    'author' => [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'username' => strtolower($user->first_name . $user->last_name),
                        'avatar' => $user->profile_image
                    ],
                    'tags' => $request->tags ?? [],
                    'likes' => 0,
                    'comments' => 0,
                    'shares' => 0,
                    'createdAt' => $post->created_at->toISOString()
                ]
            ]
        ], 201);
    }

    /**
     * Liker un post
     * Route: POST /api/posts/{postId}/like
     */
    public function likePost($postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Post non trouvé'
                ]
            ], 404);
        }

        // Logique de like à implémenter avec une table pivot

        return response()->json([
            'success' => true,
            'data' => [
                'liked' => true,
                'likesCount' => 1, // À calculer selon vos relations
                'message' => 'Post liké avec succès'
            ]
        ]);
    }

    /**
     * Obtenir le feed d'exploration
     * Route: GET /api/feed/explore
     */
    public function getExploreFeed(Request $request)
    {
        $posts = Post::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'content' => $post->content,
                'media' => $post->image ? [$post->image] : [],
                'author' => [
                    'id' => $post->user->id,
                    'name' => $post->user->first_name . ' ' . $post->user->last_name,
                    'username' => strtolower($post->user->first_name . $post->user->last_name),
                    'avatar' => $post->user->profile_image
                ],
                'tags' => [],
                'likes' => rand(50, 500),
                'comments' => rand(10, 100),
                'shares' => rand(5, 50),
                'isLiked' => false,
                'createdAt' => $post->created_at->toISOString()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'posts' => $formattedPosts,
                'trending' => true
            ]
        ]);
    }

    // ====================
    // SOCIAL / USER RELATIONS
    // ====================
    public function getUserFollowers($userId) {
        return response()->json([
            'success' => true,
            'data' => [
                'followers' => [],
                'total' => 0
            ]
        ]);
    }
    public function getUserFollowing($userId) {
        return response()->json([
            'success' => true,
            'data' => [
                'following' => [],
                'total' => 0
            ]
        ]);
    }
    public function followUser($userId) {
        return response()->json([
            'success' => true,
            'message' => 'User followed successfully',
            'data' => [
                'isFollowing' => true,
                'followersCount' => 1
            ]
        ]);
    }
public function getSavedPosts(Request $request)
{
    $user = $request->user();

    $posts = $user->savedPosts()
        ->with(['media', 'user'])
        ->get()
        ->map(function ($post) {
            return [
                'id' => $post->id,
                'content' => $post->content,
                'media' => $post->media->pluck('file_path')->toArray(),
                'author' => [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                    'username' => $post->user->username,
                    'avatar' => $post->user->profile_image,
                ],
                'likes' => $post->likes_count ?? 0,
                'comments' => $post->comments_count ?? 0,
                'shares' => $post->shares_count ?? 0,
                'savedAt' => optional($post->pivot)->created_at ? $post->pivot->created_at->toIso8601String() : null,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => [
            'posts' => $posts
        ]
    ]);
}

    public function updateAvatar(Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'avatarUrl' => 'https://example.com/new_avatar.jpg',
                'message' => 'Profile picture updated successfully'
            ]
        ]);
    }
    public function updateCoverPhoto(Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'coverPhotoUrl' => 'https://example.com/new_cover.jpg',
                'message' => 'Cover photo updated successfully'
            ]
        ]);
    }

    // ====================
    // POSTS
    // ====================
    public function addCommentToPost($postId, Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'comment' => [
                    'id' => 1,
                    'content' => $request->content ?? '',
                    'author' => [
                        'id' => 1,
                        'name' => 'John Doe',
                        'username' => 'johndoe',
                        'avatar' => 'https://example.com/avatar.jpg'
                    ],
                    'createdAt' => now()->toISOString(),
                    'likes' => 0
                ],
                'totalComments' => 1
            ]
        ]);
    }
    public function sharePost($postId, Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'shared' => true,
                'sharesCount' => 1,
                'shareId' => 1,
                'message' => 'Post shared successfully'
            ]
        ]);
    }

    // ====================
    // HASHTAGS
    // ====================
    public function getHashtagPosts($hashtag) {
        return response()->json([
            'success' => true,
            'data' => [
                'posts' => [],
                'stats' => [
                    'totalPosts' => 0,
                    'growth' => '+0%',
                    'category' => 'General'
                ]
            ]
        ]);
    }

    // ====================
    // STORE / E-COMMERCE
    // ====================
    public function getStoreCategories() {
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => []
            ]
        ]);
    }
    public function getStoreBrands() {
        return response()->json([
            'success' => true,
            'data' => [
                'brands' => []
            ]
        ]);
    }
    public function addToCart(Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'data' => [
                'cartItem' => [
                    'id' => 1,
                    'productId' => $request->productId ?? 1,
                    'quantity' => $request->quantity ?? 1,
                    'size' => $request->size ?? 'L',
                    'color' => $request->color ?? 'Black',
                    'price' => 45.99,
                    'subtotal' => 45.99
                ],
                'cartTotal' => 45.99,
                'itemsCount' => 1
            ]
        ]);
    }
    public function addToWishlist($productId) {
        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist',
            'data' => [
                'wishlistId' => 1,
                'productId' => $productId,
                'addedAt' => now()->toISOString()
            ]
        ]);
    }
    public function getCart() {
        return response()->json([
            'success' => true,
            'data' => [
                'items' => [],
                'summary' => [
                    'subtotal' => 0,
                    'shipping' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'itemsCount' => 0
                ],
                'estimatedDelivery' => now()->addDays(5)->toDateString()
            ]
        ]);
    }
    public function updateCartItem($itemId, Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'item' => [
                    'id' => $itemId,
                    'quantity' => $request->quantity ?? 1,
                    'subtotal' => 45.99
                ],
                'cartTotal' => 45.99
            ]
        ]);
    }
    public function removeCartItem($itemId) {
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => [
                'removedItemId' => $itemId,
                'cartTotal' => 0,
                'itemsCount' => 0
            ]
        ]);
    }
    public function createOrder(Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'order' => [
                    'id' => 'ORD-2024-001',
                    'status' => 'processing',
                    'total' => 45.99,
                    'estimatedDelivery' => now()->addDays(7)->toDateString(),
                    'createdAt' => now()->toISOString()
                ]
            ]
        ]);
    }
    public function getOrders() {
        return response()->json([
            'success' => true,
            'data' => [
                'orders' => [],
                'pagination' => [
                    'page' => 1,
                    'limit' => 10,
                    'total' => 0
                ]
            ]
        ]);
    }
    public function cancelOrder($orderId, Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => [
                'orderId' => $orderId,
                'status' => 'cancelled',
                'refundAmount' => 45.99,
                'refundEstimate' => '3-5 business days'
            ]
        ]);
    }
    public function getOrderDetails($orderId) {
        return response()->json([
            'success' => true,
            'data' => [
                'order' => [
                    'id' => $orderId,
                    'status' => 'delivered',
                    'total' => 45.99,
                    'orderDate' => now()->subDays(5)->toISOString(),
                    'deliveredDate' => now()->toISOString(),
                    'trackingNumber' => 'TN123456789',
                    'estimatedDelivery' => now()->toISOString(),
                    'items' => []
                ]
            ]
        ]);
    }
    public function reviewOrder($orderId, Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => [
                'reviewId' => 1,
                'rating' => $request->rating ?? 5,
                'submittedAt' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Route: POST /api/upload/image
     * Upload an image and return its URL
     */
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'No image file provided.'
            ], 422);
        }

        $file = $request->file('image');
        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image file.'
            ], 422);
        }

        $path = $file->store('uploads', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }

    /**
     * Obtenir le contenu d'une catégorie
     * Route: GET /api/categories/{category}/content
     */
    public function getCategoryContent($category, Request $request)
    {
        // Recherche la catégorie par ID ou slug
        $cat = Category::where('id', $category)->orWhere('slug', $category)->first();
        if (!$cat) {
            return response()->json([
                'success' => false,
                'error' => 'Catégorie non trouvée'
            ], 404);
        }
        // Exemple: retourne les posts associés à la catégorie
        $posts = Post::where('category_id', $cat->id)->get();
        return response()->json([
            'success' => true,
            'data' => [
                'category' => $cat->name,
                'posts' => $posts
            ]
        ]);
    }

    /**
     * Obtenir la liste des produits du store
     * Route: GET /api/store/products
     */
    public function getStoreProducts(Request $request)
    {
        // Exemple: retourne tous les produits
        $products = Product::all();
        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products
            ]
        ]);
    }

    public function savePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $user = $request->user();
        $user->savedPosts()->syncWithoutDetaching($request->post_id);

        return response()->json([
            'success' => true,
            'message' => 'Post saved successfully',
        ]);
    }
}
