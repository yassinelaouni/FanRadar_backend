<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ✅ GET /api/products — Liste paginée des produits avec leurs médias
    public function index()
    {
        $products = Product::with(['medias', 'subcategory'])->paginate(10);
        return response()->json($products);
    }

    // ✅ POST /api/products — Création d’un produit + médias
   public function store(Request $request)
{
    // Validation de base, sans medias.*.file_path en string mais en fichiers
    $validated = $request->validate([
        'product_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'promotion' => 'nullable|integer|min:0|max:100',
        'user_id' => 'required|exists:users,id',
        'subcategory_id' => 'nullable|exists:subcategories,id',
        'type' => 'nullable|string|max:100',
        'content_status' => 'nullable|in:active,inactive,draft',
        'sale_start_date' => 'nullable|date',
        'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
        'medias' => 'nullable|array',
    ]);

    $product = Product::create(array_merge($validated, [
        'content_status' => $validated['content_status'] ?? 'active',
        'revenue' => ($validated['price'] * $validated['stock']), // Calcul automatique du revenue
    ]));

    // Gestion de l'upload des fichiers médias
    if ($request->hasFile('medias')) {
        foreach ($request->file('medias') as $file) {
            // Détection automatique du type media via extension mime
            $mimeType = $file->getMimeType();
            $mediaType = str_starts_with($mimeType, 'image/') ? 'image' : 'video';

            // Dossier selon type media
            $folder = $mediaType === 'image' ? 'products/images' : 'products/videos';

            // Stockage du fichier dans public disk
            $path = $file->store($folder, 'public');

            // Création de l'enregistrement media lié au produit
            $product->medias()->create([
                'file_path' => $path,
                'media_type' => $mediaType,
            ]);
        }
    }

    return response()->json([
        'message' => 'Produit créé avec succès.',
        'product' => $product->load(['medias', 'subcategory'])
    ], 201);
}

    // ✅ GET /api/products/{product} — Afficher un seul produit
    public function show(Product $product)
    {
        return response()->json($product->load(['medias', 'subcategory']));
    }

    // ✅ PUT/PATCH /api/products/{product} — Modifier un produit (pas les médias ici)
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'promotion' => 'nullable|integer|min:0|max:100',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'type' => 'nullable|string|max:100',
            'content_status' => 'nullable|in:active,inactive,draft',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
        ]);

        // Recalculer le revenue si price ou stock changent
        if (isset($validated['price']) || isset($validated['stock'])) {
            $price = $validated['price'] ?? $product->price;
            $stock = $validated['stock'] ?? $product->stock;
            $validated['revenue'] = $price * $stock;
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Produit mis à jour.',
            'product' => $product->load(['medias', 'subcategory'])
        ]);
    }

    // ✅ DELETE /api/products/{product} — Supprimer produit + médias
    public function destroy(Product $product)
    {
        $product->medias()->delete(); // Supprime les médias liés
        $product->delete();

        return response()->json([
            'message' => 'Produit et ses médias supprimés.'
        ]);
    }
}
