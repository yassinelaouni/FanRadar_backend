<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

/**
 * CONTRÔLEUR CONTENT (GESTION DU CONTENU)
 */
class ContentController extends Controller
{
    /**
     * LISTER TOUT LE CONTENU
     * Route : GET /api/content
     */
    public function index()
    {
        $content = Content::with(['user', 'media', 'favorites', 'ratings', 'tags'])->get();
        return response()->json($content);
    }

    /**
     * CRÉER DU NOUVEAU CONTENU
     * Route : POST /api/content
     */
    public function store(Request $request)
    {
        $request->validate([
            'content_type' => 'required|in:image,video,text',
            'content_status' => 'in:pending,approved,rejected',
            'feedback' => 'integer|min:0',
            'should_at' => 'integer|min:0',
            'user_id' => 'required|exists:users,id',
        ]);

        $content = Content::create($request->all());
        return response()->json($content->load('user'), 201);
    }

    /**
     * AFFICHER UN CONTENU SPÉCIFIQUE
     * Route : GET /api/content/{id}
     */
    public function show(Content $content)
    {
        return response()->json($content->load(['user', 'media', 'favorites', 'ratings', 'tags']));
    }

    /**
     * MODIFIER UN CONTENU
     * Route : PUT /api/content/{id}
     */
    public function update(Request $request, Content $content)
    {
        $request->validate([
            'content_type' => 'required|in:image,video,text',
            'content_status' => 'in:pending,approved,rejected',
            'feedback' => 'integer|min:0',
            'should_at' => 'integer|min:0',
        ]);

        $content->update($request->all());
        return response()->json($content->load('user'));
    }

    /**
     * SUPPRIMER UN CONTENU
     * Route : DELETE /api/content/{id}
     */
    public function destroy(Content $content)
    {
        $content->delete();
        return response()->json(['message' => 'Content deleted successfully']);
    }
}
