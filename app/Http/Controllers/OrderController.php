<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'products'])->get();
        return response()->json($orders);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'total_amount' => 'required|numeric|min:0',
        'status' => 'in:' . implode(',', Order::STATUSES),
        'order_date' => 'required|date',
        'products' => 'required|array',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
    ]);

    // 1. Vérification du stock de chaque produit
    foreach ($request->products as $productData) {
        $product = Product::find($productData['product_id']);
        if (!$product) {
            return response()->json(['error' => "Produit ID {$productData['product_id']} introuvable."], 404);
        }

        if ($product->stock < $productData['quantity']) {
            return response()->json([
                'error' => "Stock insuffisant pour le produit '{$product->product_name}'. Stock disponible : {$product->stock}, demandé : {$productData['quantity']}."
            ], 422);
        }
    }

    // 2. Création de la commande
    $order = Order::create([
        'user_id' => $request->user_id,
        'total_amount' => $request->total_amount,
        'status' => $request->status ?? 'pending',
        'order_date' => $request->order_date,
    ]);

    // 3. Attachement des produits + décrémentation du stock
    foreach ($request->products as $productData) {
        $product = Product::find($productData['product_id']);

        // Attachement à la commande
        $order->products()->attach($product->id, [
            'quantity' => $productData['quantity'],
        ]);

        // Mise à jour du stock
        $product->decrement('stock', $productData['quantity']);
    }

    return response()->json($order->load('products'), 201);
}


    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json($order->load(['user', 'products']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'user_id' => 'exists:users,id',
            'total_amount' => 'numeric|min:0',
            'status' => 'in:' . implode(',', Order::STATUSES),
            'order_date' => 'date',
        ]);

        $order->update($request->only(['user_id', 'total_amount', 'status', 'order_date']));
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
