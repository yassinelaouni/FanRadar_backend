<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderProducts = OrderProduct::with(['order', 'product'])->get();
        return response()->json($orderProducts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $orderProduct = OrderProduct::create($request->all());
        return response()->json($orderProduct->load(['order', 'product']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderProduct $orderProduct)
    {
        return response()->json($orderProduct->load(['order', 'product']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderProduct $orderProduct)
    {
        $request->validate([
            'order_id' => 'exists:orders,id',
            'product_id' => 'exists:products,id',
            'quantity' => 'integer|min:1',
        ]);

        $orderProduct->update($request->all());
        return response()->json($orderProduct);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderProduct $orderProduct)
    {
        $orderProduct->delete();
        return response()->json(['message' => 'OrderProduct deleted successfully']);
    }
}
