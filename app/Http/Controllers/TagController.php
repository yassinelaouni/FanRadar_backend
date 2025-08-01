<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function attachTag(Request $request)
        {
            $validated = $request->validate([
                'tag_name' => 'required|string|max:255',
                'taggable_id' => 'required|integer',
                'taggable_type' => 'required|in:post,product',
            ]);

            // Vérifie si le tag existe, sinon le crée
            $tag = Tag::firstOrCreate(['tag_name' => $validated['tag_name']]);

            // Détermine le modèle lié
            $taggableClass = $validated['taggable_type'] === 'post' ? \App\Models\Post::class : \App\Models\Product::class;
            $taggable = $taggableClass::findOrFail($validated['taggable_id']);

            // Attache le tag au modèle (évite les doublons avec syncWithoutDetaching)
            $taggable->tags()->syncWithoutDetaching([$tag->id]);

            return response()->json([
                'message' => 'Tag attaché avec succès.',
                'tag' => $tag,
            ]);
        }

       public function detachTag(Request $request)
        {
            $validated = $request->validate([
                'tag_id' => 'required|exists:tags,id',
                'taggable_id' => 'required|integer',
                'taggable_type' => 'required|in:post,product',
            ]);

            $taggableClass = $validated['taggable_type'] === 'post' ? \App\Models\Post::class : \App\Models\Product::class;
            $taggable = $taggableClass::findOrFail($validated['taggable_id']);

            // Vérifie si la relation existe avant de la supprimer
            if ($taggable->tags()->where('tags.id', $validated['tag_id'])->exists()) {
                $taggable->tags()->detach($validated['tag_id']);
                return response()->json([
                    'message' => 'Tag détaché avec succès.',
                ]);
            } else {
                return response()->json([
                    'message' => 'Aucune relation trouvée entre ce contenu et ce tag.',
                ], 404);
            }
        }



}
