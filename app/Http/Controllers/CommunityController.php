<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;

/**
 * CONTRÔLEUR COMMUNITY (GESTION DES COMMUNAUTÉS)
 */
class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Community::with(['subcategory', 'members'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        $community = Community::create($request->all());
        return response()->json($community->load('subcategory'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community)
    {
        return response()->json($community->load(['subcategory', 'members']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Community $community)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        $community->update($request->all());
        return response()->json($community->load('subcategory'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        $community->delete();
        return response()->json(['message' => 'Community deleted successfully']);
    }
}
