<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // ===================== API JSON =====================
    public function index()
    {
        $products = Product::with(['category', 'vendor'])->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'vendor'])->find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $product]);
    }

    public function searchApi(Request $request)
    {
        $name = $request->query('q');
        $products = Product::with(['category', 'vendor'])->where('name', 'like', "%{$name}%")->get();
        return $products->isEmpty()
            ? response()->json(['success' => false, 'message' => 'Product not found'], 404)
            : response()->json(['success' => true, 'data' => $products]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'id_category' => 'required|uuid|exists:categories,id',
            'id_vendor' => 'required|uuid|exists:vendor,id',
            'jumlah' => 'required|integer|min:0',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'status' => 'required|in:available,unavailable,draft',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $product = Product::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load(['category', 'vendor'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'id_category' => 'required|uuid|exists:categories,id',
            'id_vendor' => 'required|uuid|exists:vendor,id',
            'jumlah' => 'required|integer|min:0',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'status' => 'required|in:available,unavailable,draft',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load(['category', 'vendor'])
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted successfully'], 200);
    }


}
