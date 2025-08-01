<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ✅ GET /api/products — Liste paginée des produits avec leurs médias
    public function index()
    {
        $products = Product::with('medias')->paginate(10);
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
        'sale_start_date' => 'nullable|date',
        'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
        'medias' => 'nullable|array',
    ]);

    $product = Product::create($validated);

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
        'product' => $product->load('medias')
    ], 201);
}

    // ✅ GET /api/products/{product} — Afficher un seul produit
    public function show(Product $product)
    {
        return response()->json($product->load('medias'));
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
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Produit mis à jour.',
            'product' => $product->load('medias')
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
