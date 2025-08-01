<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    // Afficher toutes les sous-catégories
    public function index()
    {
        return response()->json(Subcategory::with('category')->get());
    }

    // Créer une nouvelle sous-catégorie
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory = Subcategory::create($validated);

        return response()->json([
            'message' => 'Sous-catégorie créée avec succès.',
            'subcategory' => $subcategory
        ], 201);
    }

    // Afficher une seule sous-catégorie
    public function show($id)
    {
        $subcategory = Subcategory::with('category')->find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Sous-catégorie non trouvée'], 404);
        }

        return response()->json($subcategory);
    }

    // Mettre à jour une sous-catégorie
    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Sous-catégorie non trouvée'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        $subcategory->update($validated);

        return response()->json([
            'message' => 'Sous-catégorie mise à jour avec succès.',
            'subcategory' => $subcategory
        ]);
    }

    // Supprimer une sous-catégorie
    public function destroy($id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Sous-catégorie non trouvée'], 404);
        }

        $subcategory->delete();

        return response()->json(['message' => 'Sous-catégorie supprimée avec succès.']);
    }
}
