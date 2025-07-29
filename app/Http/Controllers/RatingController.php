<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

/**
 * CONTRÔLEUR RATING (GESTION DES ÉVALUATIONS)
 */
class RatingController extends Controller
{
    /**
     * LISTER TOUTES LES ÉVALUATIONS
     * Route : GET /api/ratings
     */
    public function index()
    {
        $ratings = Rating::with(['user', 'content'])->get();
        return response()->json($ratings);
    }

    /**
     * CRÉER UNE NOUVELLE ÉVALUATION
     * Route : POST /api/ratings
     */
    public function store(Request $request)
    {
        $request->validate([
            'comments' => 'required|integer|min:0',
            'stars' => 'required|integer|min:1|max:5', // Étoiles entre 1 et 5
            'user_id' => 'required|exists:users,id',
            'content_id' => 'required|exists:content,id',
        ]);

        $rating = Rating::create($request->all());
        return response()->json($rating->load(['user', 'content']), 201);
    }

    /**
     * AFFICHER UNE ÉVALUATION SPÉCIFIQUE
     * Route : GET /api/ratings/{id}
     */
    public function show(Rating $rating)
    {
        return response()->json($rating->load(['user', 'content']));
    }

    /**
     * MODIFIER UNE ÉVALUATION
     * Route : PUT /api/ratings/{id}
     */
    public function update(Request $request, Rating $rating)
    {
        $request->validate([
            'comments' => 'required|integer|min:0',
            'stars' => 'required|integer|min:1|max:5',
        ]);

        $rating->update($request->only(['comments', 'stars']));
        return response()->json($rating->load(['user', 'content']));
    }

    /**
     * SUPPRIMER UNE ÉVALUATION
     * Route : DELETE /api/ratings/{id}
     */
    public function destroy(Rating $rating)
    {
        $rating->delete();
        return response()->json(['message' => 'Rating deleted successfully']);
    }
}
