<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class M_Controller extends Controller
{
    /**
     * Exemple de méthode d'API
     */


    /**
     * Obtenir tous les rôles et permissions
     * Route: GET /api/roles-permissions
     */
    public function getAllRolesAndPermissions()
    {
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
        $result = $roles->map(function($role) {
            // Correction : $role->permissions peut être une string (json) ou une relation
            $permissions = is_iterable($role->permissions) ? collect($role->permissions)->pluck('name')->toArray() : [];
            $permissions_array = is_string($role->permissions) ? json_decode($role->permissions, true) : [];
            return [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'description' => $role->description,
                'permissions' => $permissions,
                'permissions_array' => $permissions_array,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * a. Get all users (id, username, first_name, last_name, email, status, join_date, role, image)
     * Route: GET /api/users
     */
    public function getAllUsers()
    {
        $users = \App\Models\User::all()->map(function($user) {
            return [
                'id' => $user->id,

                'username' => $user->username ?? null,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'status' => $user->status ?? 'active',
                'join_date' => $user->created_at ? $user->created_at->toDateString() : null,
                'role' => $user->role ?? null,
                'image' => $user->profile_image,
            ];
        });
        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * b. Get user by id, email
     * Route: GET /api/user/{value}
     */
    public function getUser($value)
    {
        $user = \App\Models\User::where('id', $value)
            ->orWhere('email', $value)
            ->first();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,

                'username' => $user->username ?? null,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'status' => $user->status ?? 'active',
                'join_date' => $user->created_at ? $user->created_at->toDateString() : null,
                'role' => $user->role ?? null,
                'image' => $user->profile_image,
            ]
        ]);
    }

    /**
     * c. Add user (first_name, last_name, role, email)
     * Route: POST /api/users
     */
    public function addUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',

            'email' => 'required|email|unique:users,email',            'role' => 'in:user,admin',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:user,admin',
            'password' => 'required|string|min:6',
        ]);
        $user = \App\Models\User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'profile_image' => $request->profile_image ?? null,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'user', // <-- PREND LA VALEUR ENVOYÉE


            'status' => 'active',
            'profile_image' => $request->profile_image ?? null,
            'password' => bcrypt($request->password),
            'role' => $request->role,

        ]);
        return response()->json(['success' => true, 'data' => $user], 201);
    }

    /**
     * d. Update user (id)
     * Route: PUT /api/users/{id}
     */
    public function updateUser(Request $request, $id)
    {
        $user = \App\Models\User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }
        // Met à jour tous les champs envoyés dans la requête, sans toucher aux autres
        $updatable = ['first_name', 'last_name', 'email', 'status', 'profile_image', 'role', 'password'];
        foreach ($updatable as $field) {
            if ($request->has($field)) {
                if ($field === 'password') {
                    $user->password = bcrypt($request->password);
                } else {
                    $user->$field = $request->$field;
                }
            }
        }
        $user->save();
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * f. Get categories (id, name)
     * URL: GET /api/categories-simple
     */
    public function getCategoriesSimple()
    {
        $categories = \App\Models\Category::select('id', 'name')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    } 

    /**
     * g. Get subcategories (id, cat_id, name)
     * URL: GET /api/subcategories-simple
     */
    public function getSubcategoriesSimple()
    {
        $subcategories = \App\Models\SubCategory::select('id', 'category_id as cat_id', 'name')->get();
        return response()->json(['success' => true, 'data' => $subcategories]);
    }

    /**
     * h. Get categories with sub category (id_cat, name, sub : [id, name])
     * URL: GET /api/categories-with-subs
     */
    public function getCategoriesWithSubs()
    {
        $categories = \App\Models\Category::with(['subcategories:id,category_id,name'])->get(['id','name']);
        $result = $categories->map(function($cat) {
            return [
                'id_cat' => $cat->id,
                'name' => $cat->name,
                'sub' => $cat->subcategories->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name
                    ];
                })
            ];
        });
        return response()->json(['success' => true, 'data' => $result]);
    }

    /**
     * i. Add category (name)
     * URL: POST /api/categories-simple
     */
    public function addCategorySimple(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $cat = \App\Models\Category::create(['name' => $request->name]);
        return response()->json(['success' => true, 'data' => $cat], 201);
    }

    /**
     * j. Add subcategory (name, id_cat)
     * URL: POST /api/subcategories-simple
     */
    public function addSubcategorySimple(Request $request)
    {
        $request->validate(['name' => 'required|string', 'id_cat' => 'required|integer|exists:categories,id']);
        $sub = \App\Models\SubCategory::create(['name' => $request->name, 'category_id' => $request->id_cat]);
        return response()->json(['success' => true, 'data' => $sub], 201);
    }

    /**
     * k. Delete category (id), delete subcategory (id)
     * URL: DELETE /api/categories-simple/{id}, DELETE /api/subcategories-simple/{id}
     */
    public function deleteCategorySimple($id)
    {
        $cat = \App\Models\Category::find($id);
        if (!$cat) return response()->json(['success' => false, 'error' => 'Category not found'], 404);
        $cat->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }
    public function deleteSubcategorySimple($id)
    {
        $sub = \App\Models\SubCategory::find($id);
        if (!$sub) return response()->json(['success' => false, 'error' => 'Subcategory not found'], 404);
        $sub->delete();
        return response()->json(['success' => true, 'message' => 'Subcategory deleted']);
    }

    /**
     * Get all tags (id, tag)
     * Route: GET /api/tags-simple
     */
    public function getAllTagsSimple()
    {
        $tags = \App\Models\Tag::select('id', 'tag_name as tag')->get();
        return response()->json(['success' => true, 'data' => $tags]);
    }

    /**
     * Add tag (tag)
     * Route: POST /api/tags-simple
     */
    public function addTagSimple(Request $request)
    {
        $request->validate(['tag' => 'required|string|unique:tags,tag_name']);
        $tag = \App\Models\Tag::create(['tag_name' => $request->tag]);
        return response()->json(['success' => true, 'data' => $tag], 201);
    }

    /**
     * POSTS
     */
    // a. Get posts (id, author, fandom, date, likes, category, title, content, media)
    public function getAllPostsSimple()
    {
        $posts = \App\Models\Post::with(['user:id,first_name', 'category:id,name'])
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'author' => $post->user ? trim($post->user->first_name . ' ' . ($post->user->last_name ?? '')) : null,
                    'fandom' => $post->fandom ?? 'general',
                    'date' => $post->created_at,
                    'likes' => $post->likes ?? 0,
                    'category' => $post->category->name ?? null,
                    'title' => $post->title,
                    'content' => $post->content,
                    'media' => $post->media // Utilise directement le champ media JSON
                ];
            });
        return response()->json(['success' => true, 'data' => $posts]);
    }

    // b. Add post
    public function addPostSimple(Request $request)
    {
        $request->validate([
            'author_id' => 'required|integer|exists:users,id',
            'title' => 'required|string',
            'content' => 'required|string',
            // Pas besoin de valider 'media' ici
        ]);

        $mediaUrls = [];
        // Correction : récupérer tous les fichiers envoyés sous "media" ou "media[]"
        if ($request->hasFile('media')) {
            $files = $request->file('media');
            // Si $files n'est pas un tableau, le transformer en tableau
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('images', 'public');
                    $mediaUrls[] = asset('storage/' . $path);
                }
            }
        }

        $post = \App\Models\Post::create([
            'user_id' => $request->author_id,
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id ?? null,
            'subcategory_id' => $request->subcategory_id ?? null,
            'media' => json_encode($mediaUrls), // Stocke les URLs en JSON
        ]);

        return response()->json(['success' => true, 'data' => $post], 201);
    }

    // c. Delete post
    public function deletePostSimple($id)
    {
        $post = \App\Models\Post::find($id);
        if (!$post) return response()->json(['success' => false, 'error' => 'Post not found'], 404);
        $post->delete();
        return response()->json(['success' => true, 'message' => 'Post deleted']);
    }

    // d. Update post
    public function updatePostSimple(Request $request, $id)
    {
        $post = \App\Models\Post::find($id);
        if (!$post) return response()->json(['success' => false, 'error' => 'Post not found'], 404);
        
        $updateData = $request->only(['title', 'content', 'category_id', 'subcategory_id']);
        if ($request->has('media')) {
            $updateData['media'] = $request->media;
        }
        
        $post->update($updateData);
        
        // Mettre à jour les tags si fournis
        if ($request->has('tags') && is_array($request->tags)) {
            $post->tags()->detach(); // Supprimer tous les tags existants
            foreach ($request->tags as $tagName) {
                $tag = \App\Models\Tag::firstOrCreate(['tag_name' => $tagName]);
                $post->tags()->attach($tag->id);
            }
        }
        
        return response()->json(['success' => true, 'data' => $post]);
    }

    // e. Get posts by tag
    public function getPostsByTagSimple($tag)
    {
        $posts = \App\Models\Post::whereHas('tags', function($q) use ($tag) {
            $q->where('tag_name', $tag);
        })->with(['user:id,first_name', 'category:id,name'])
        ->get()
        ->map(function($post) {
            return [
                'id' => $post->id,
                'author' => $post->user ? trim($post->user->first_name . ' ' . ($post->user->last_name ?? '')) : null,
                'fandom' => $post->fandom ?? 'general',
                'date' => $post->created_at,
                'likes' => $post->likes ?? 0,
                'category' => $post->category->name ?? null,
                'title' => $post->title,
                'content' => $post->content,
                'media' => $post->media
            ];
        });
        return response()->json(['success' => true, 'data' => $posts]);
    }

    // f. Get posts by category, subcategory
    public function getPostsByCategorySubSimple(Request $request)
    {
        $query = \App\Models\Post::query();
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }
        $posts = $query->with(['user:id,first_name', 'category:id,name'])
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'author' => $post->user ? trim($post->user->first_name . ' ' . ($post->user->last_name ?? '')) : null,
                    'fandom' => $post->fandom ?? 'general',
                    'date' => $post->created_at,
                    'likes' => $post->likes ?? 0,
                    'category' => $post->category->name ?? null,
                    'title' => $post->title,
                    'content' => $post->content,
                    'media' => $post->media
                ];
            });
        return response()->json(['success' => true, 'data' => $posts]);
    }

    // g. Get posts by category only
    public function getPostsByCategorySimple($category_id)
    {
        $posts = \App\Models\Post::where('category_id', $category_id)
            ->with(['user:id,first_name', 'category:id,name'])
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'author' => $post->user ? trim($post->user->first_name . ' ' . ($post->user->last_name ?? '')) : null,
                    'fandom' => $post->fandom ?? 'general',
                    'date' => $post->created_at,
                    'likes' => $post->likes ?? 0,
                    'category' => $post->category->name ?? null,
                    'title' => $post->title,
                    'content' => $post->content,
                    'media' => $post->media
                ];
            });
        return response()->json(['success' => true, 'data' => $posts]);
    }

    // h. Get posts by subcategory only
    public function getPostsBySubcategorySimple($subcategory_id)
    {
        $posts = \App\Models\Post::where('subcategory_id', $subcategory_id)
            ->with(['user:id,first_name', 'category:id,name'])
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'author' => $post->user ? trim($post->user->first_name . ' ' . ($post->user->last_name ?? '')) : null,
                    'fandom' => $post->fandom ?? 'general',
                    'date' => $post->created_at,
                    'likes' => $post->likes ?? 0,
                    'category' => $post->category->name ?? null,
                    'title' => $post->title,
                    'content' => $post->content,
                    'media' => $post->media
                ];
            });
        return response()->json(['success' => true, 'data' => $posts]);
    }

    /**
     * PRODUCTS
     */
    // a. Get products (id, name, type, price, stock, date, revenue, description) (no drops)
    // URL: GET /api/products-simple
    public function getAllProductsSimple()
    {
        $products = \App\Models\Product::whereNull('sale_end_date')
            ->select('id', 'product_name as name', 'type', 'price', 'stock', 'sale_start_date as date', 'revenue', 'description')
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'type' => $product->type,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'date' => $product->date,
                    'revenue' => $product->revenue ?? 0,

                ];
            });
        return response()->json(['success' => true, 'data' => $products]);
    }

    // add product (name, type, date, price, stock) - revenue calculé automatiquement
    // URL: POST /api/products-simple
    public function addProductSimple(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'date' => 'required|date',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);
        // Exemple de calcul automatique du revenu (prix * stock)
        $revenue = $request->price * $request->stock;
        $product = \App\Models\Product::create([
            'product_name' => $request->name,
            'type' => $request->type,
            'sale_start_date' => $request->date,
            'price' => $request->price,
            'stock' => $request->stock,
            'revenue' => $revenue,
            'content_status' => 'active',
        ]);
        return response()->json(['success' => true, 'data' => $product], 201);
    }

    // get drops (start and end date)
    // URL: GET /api/drops-simple
    public function getDropsSimple()
    {
        $drops = \App\Models\Product::whereNotNull('sale_start_date')->whereNotNull('sale_end_date')
            ->select('id', 'product_name as name', 'type', 'price', 'stock', 'promotion', 'sale_start_date', 'sale_end_date', 'revenue', 'description')
            ->get();
        return response()->json(['success' => true, 'data' => $drops]);
    }

    // add drop
    // URL: POST /api/drops-simple
    public function addDropSimple(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'promotion' => 'nullable|numeric',
            'sale_start_date' => 'required|date',
            'sale_end_date' => 'required|date|after_or_equal:sale_start_date',
            'description' => 'nullable|string',
            'revenue' => 'nullable|numeric',
        ]);
        $drop = \App\Models\Product::create([
            'product_name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'stock' => $request->stock,
            'promotion' => $request->promotion,
            'sale_start_date' => $request->sale_start_date,
            'sale_end_date' => $request->sale_end_date,
            'description' => $request->description,
            'revenue' => $request->revenue,
            'content_status' => 'active',
        ]);
        return response()->json(['success' => true, 'data' => $drop], 201);
    }

    // update product or drop
    // URL: PUT /api/products-simple/{id}
    public function updateProductSimple(Request $request, $id)
    {
        $product = \App\Models\Product::find($id);
        if (!$product) return response()->json(['success' => false, 'error' => 'Product not found'], 404);
        $fields = ['product_name', 'type', 'price', 'stock', 'promotion', 'sale_start_date', 'sale_end_date', 'description', 'revenue'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $product->$field = $request->$field;
            }
        }
        $product->save();
        return response()->json(['success' => true, 'data' => $product]);
    }

    // delete product or drop
    // URL: DELETE /api/products-simple/{id}
    public function deleteProductSimple($id)
    {
        $product = \App\Models\Product::find($id);
        if (!$product) return response()->json(['success' => false, 'error' => 'Product not found'], 404);
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }

    /**
     * Upload image
     * Route: POST /api/upload-image
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
        $path = $file->store('images', 'public');

        return response()->json([
            'success' => true,
            'path' => $path
        ]);
    }
}
