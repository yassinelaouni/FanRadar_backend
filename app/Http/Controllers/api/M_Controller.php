<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        // Nécessite le package spatie/laravel-permission
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
        $result = $roles->map(function($role) {
            return [
                'role' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray()
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
        $users = \App\Models\User::with('roles')->get()->map(function($user) {
            return [
                'id' => $user->id,
                'username' => $user->username ?? strtolower($user->first_name . $user->last_name),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'status' => $user->status ?? 'active',
                'join_date' => $user->created_at,
                'role' => $user->roles->pluck('name')->first() ?? null,
                'image' => $user->profile_image
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
            ->with('roles')
            ->first();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'username' => $user->username ?? strtolower($user->first_name . $user->last_name),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'status' => $user->status ?? 'active',
                'join_date' => $user->created_at,
                'role' => $user->roles->pluck('name')->first() ?? null,
                'image' => $user->profile_image
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
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
        $user = \App\Models\User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'status' => 'active',
            'profile_image' => $request->profile_image ?? 'default.png',
            'password' => bcrypt($request->password)
        ]);
        $user->assignRole($request->role);
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
        $user->update($request->only(['first_name', 'last_name', 'email', 'status', 'profile_image']));
        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * a. Get categories (id, name)
     * Route: GET /api/categories-simple
     */
    public function getCategoriesSimple()
    {
        $categories = \App\Models\Category::select('id', 'name')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    /**
     * b. Get subcategories (id, cat_id, name)
     * Route: GET /api/subcategories-simple
     */
    public function getSubcategoriesSimple()
    {
        $subcategories = \App\Models\SubCategory::select('id', 'category_id as cat_id', 'name')->get();
        return response()->json(['success' => true, 'data' => $subcategories]);
    }

    /**
     * c. Get categories with subcategories
     * Route: GET /api/categories-with-subs
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
     * d. Add category (name)
     * Route: POST /api/categories-simple
     */
    public function addCategorySimple(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $cat = \App\Models\Category::create(['name' => $request->name]);
        return response()->json(['success' => true, 'data' => $cat], 201);
    }

    /**
     * e. Add subcategory (name, id_cat)
     * Route: POST /api/subcategories-simple
     */
    public function addSubcategorySimple(Request $request)
    {
        $request->validate(['name' => 'required|string', 'id_cat' => 'required|integer|exists:categories,id']);
        $sub = \App\Models\SubCategory::create(['name' => $request->name, 'category_id' => $request->id_cat]);
        return response()->json(['success' => true, 'data' => $sub], 201);
    }

    /**
     * f. Delete category (id), delete subcategory (id)
     * Route: DELETE /api/categories-simple/{id}, DELETE /api/subcategories-simple/{id}
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
        $tags = \App\Models\Tag::select('id', 'tag_name')->get();
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
        $posts = \App\Models\Post::with(['user:id,first_name', 'category:id,name', 'media'])
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
                    'media' => ($post->media && $post->media->count() > 0) ? ['url' => $post->media->first()->url] : null
                ];
            });
        return response()->json(['success' => true, 'data' => $posts]);
    }

    // b. Add post
    public function addPostSimple(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'subcategory_id' => 'nullable|integer|exists:subcategories,id',
            'media' => 'nullable|array',
        ]);
        $post = \App\Models\Post::create([
            'user_id' => $request->author_id,
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
        ]);
        // Media attach (if any)
        if ($request->has('media')) {
            foreach ($request->media as $mediaUrl) {
                $post->media()->create(['url' => $mediaUrl]);
            }
        }
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
        $post->update($request->only(['title', 'content', 'category_id', 'subcategory_id']));
        return response()->json(['success' => true, 'data' => $post]);
    }

    // e. Get posts by tag
    public function getPostsByTagSimple($tag)
    {
        $posts = \App\Models\Post::whereHas('tags', function($q) use ($tag) {
            $q->where('name', $tag);
        })->get();
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
        $posts = $query->get();
        return response()->json(['success' => true, 'data' => $posts]);
    }

    /**
     * PRODUCTS
     */
    // a. Get products (only products no drops / no end date)
    public function getAllProductsSimple()
    {
        $products = \App\Models\Product::whereNull('sale_end_date')
            ->select('id', 'product_name', 'type', 'price', 'stock', 'promotion', 'sale_start_date as date', 'revenue', 'description')
            ->get();
        return response()->json(['success' => true, 'data' => $products]);
    }

    // b. Add product
    public function addProductSimple(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'type' => 'required|string',
            'sale_start_date' => 'required|date',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);
        $product = \App\Models\Product::create([
            'product_name' => $request->product_name,
            'type' => $request->type,
            'sale_start_date' => $request->sale_start_date,
            'price' => $request->price,
            'stock' => $request->stock,
            'content_status' => 'active',
        ]);
        return response()->json(['success' => true, 'data' => $product], 201);
    }

    // c. Get drops (start and end date)
    public function getDropsSimple()
    {
        $drops = \App\Models\Product::whereNotNull('sale_start_date')->whereNotNull('sale_end_date')
            ->select('id', 'product_name', 'type', 'price', 'stock', 'promotion', 'sale_start_date', 'sale_end_date', 'revenue')
            ->get();
        return response()->json(['success' => true, 'data' => $drops]);
    }

    // d. Add drop
    public function addDropSimple(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
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
            'product_name' => $request->product_name,
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

    // e. Update product
    public function updateProductSimple(Request $request, $id)
    {
        $product = \App\Models\Product::find($id);
        if (!$product) return response()->json(['success' => false, 'error' => 'Product not found'], 404);
        $product->update($request->only([
            'product_name', 'type', 'price', 'stock', 'promotion', 'sale_start_date', 'sale_end_date', 'description', 'revenue'
        ]));
        return response()->json(['success' => true, 'data' => $product]);
    }

    // e. Delete product
    public function deleteProductSimple($id)
    {
        $product = \App\Models\Product::find($id);
        if (!$product) return response()->json(['success' => false, 'error' => 'Product not found'], 404);
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }
}
