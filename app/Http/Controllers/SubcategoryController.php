<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;

/**
 * CONTRÔLEUR SUBCATEGORY (GESTION DES SOUS-CATÉGORIES)
 */
class SubcategoryController extends Controller
{
    /**
     * LISTER TOUTES LES SOUS-CATÉGORIES
     * Route : GET /api/subcategories
     */
    public function index()
    {
        $subcategories = Subcategory::with(['category', 'communities'])->get();
        return response()->json($subcategories);
    }

    /**
     * CRÉER UNE NOUVELLE SOUS-CATÉGORIE
     * Route : POST /api/subcategories
     */
    public function store(Request $request)
    {
        $request->validate([
            'subcategory_name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7', // Format hex color
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory = Subcategory::create($request->all());
        return response()->json($subcategory->load('category'), 201);
    }

    /**
     * AFFICHER UNE SOUS-CATÉGORIE SPÉCIFIQUE
     * Route : GET /api/subcategories/{id}
     */
    public function show(Subcategory $subcategory)
    {
        return response()->json($subcategory->load(['category', 'communities']));
    }

    /**
     * MODIFIER UNE SOUS-CATÉGORIE
     * Route : PUT /api/subcategories/{id}
     */
    public function update(Request $request, Subcategory $subcategory)
    {
        $request->validate([
            'subcategory_name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory->update($request->all());
        return response()->json($subcategory->load('category'));
    }

    /**
     * SUPPRIMER UNE SOUS-CATÉGORIE
     * Route : DELETE /api/subcategories/{id}
     */
    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();
        return response()->json(['message' => 'Subcategory deleted successfully']);
    }
}
