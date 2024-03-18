<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest('id')->get();
        return response()->json([
            'status' => 'success',
            'products' => $products,
            'message' => 'All products fetched successfully.'
        ], 200);
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
        // Validate the request
        $validated = $request->validate([
            'product_name'        => 'required|string|max:255',
            'product_description' => 'required|string|max:255',
            'product_price'       => 'required|numeric|min:0',
            'product_tag'         => 'required|array|min:1'
        ]);

        // Check if the user is authenticated
        if (auth()->check()) {
            // If the user is authenticated, set the user_id from the authenticated user
            $validated['user_id'] = auth()->id();

            $product = Product::create($validated);

            if ($product) {
                return response()->json([
                    'status'  => 'success',
                    'product' => $product,
                    'message' => 'Product created successfully.'
                ], 201);
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Product could not be created.'
                ], 400);
            }
        } else {
            // If the user is not authenticated, return an error response
            return response()->json([
                'status'  => 'error',
                'message' => 'User is not authenticated.'
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return response()->json([
            'status'  => 'success',
            'product' => $product,
            'message' => 'Product fetched successfully.'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        // Validate the request
        $validated = $request->validate([
            'product_name'        => 'string|max:255',
            'product_description' => 'string|max:255',
            'product_price'       => 'numeric|min:0',
            'product_tag'         => 'array|min:1'
        ]);

        if (!$product) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }
        // Check if the user is the owner of the product
        if ($request->user()->id != $product->user_id) {
            return response()->json([
                'error' => 'You do not have permission to update this product'
            ], 403);
        }

        // Update the product
        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'product' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }

        // Check if the user is the owner of the product then delete the product
        if ($request->user()->id != $product->user_id) {
            return response()->json([
                'error' => 'You do not have permission to delete this product'
            ], 403);
        } else {
            $product->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ], 200);
        }
    }
}
