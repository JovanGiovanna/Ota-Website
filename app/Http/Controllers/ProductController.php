<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'vendor']);

        if ($request->has('city_id') && $request->city_id) {
            $query->whereHas('vendor.vendorInfo', function ($q) use ($request) {
                $q->where('id_city', $request->city_id);
            });
        }

        if ($request->has('vendor_id') && $request->vendor_id) {
            $query->where('id_vendor', $request->vendor_id);
        }

        $products = $query->get();
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'id_category' => 'required|exists:categories,id',
            'id_vendor' => 'required|exists:vendor,id',
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

        $data = $request->all();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images', $imageName, 'public');
            $data['image'] = 'storage/images/' . $imageName;
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
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
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'id_category' => 'required|exists:categories,id',
            'id_vendor' => 'required|exists:vendor,id',
            'jumlah' => 'required|integer|min:0',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'status' => 'required|in:available,unavailable,draft',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
            }
            $file = $request->file('image');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images', $imageName, 'public');
            $data['image'] = 'storage/images/' . $imageName;
        } else {
            unset($data['image']);
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
        }

        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted successfully'], 204);
    }
}
