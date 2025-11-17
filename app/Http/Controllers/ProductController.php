<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'id_category' => 'required|exists:categories,id',
            'id_vendor' => 'required|exists:vendor,id',
            'pax' => 'required|integer|min:1',
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
        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('images', $imageName, 'public');
                $imagePaths[] = 'storage/images/' . $imageName;
            }
        }

        $data['image'] = json_encode($imagePaths);

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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'id_category' => 'required|exists:categories,id',
            'id_vendor' => 'required|exists:vendor,id',
            'pax' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:0',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'status' => 'required|in:available,unavailable,draft',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $oldImages = $product->image ? json_decode($product->image, true) : [];
        $imagePaths = $oldImages ?: [];

        // Handle image removal
        if ($request->has('remove_images') && is_array($request->remove_images)) {
            foreach ($request->remove_images as $index) {
                if (isset($imagePaths[$index])) {
                    $imageToRemove = $imagePaths[$index];
                    $path = str_replace('storage/', '', $imageToRemove);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                    unset($imagePaths[$index]);
                }
            }
            // Reindex array
            $imagePaths = array_values($imagePaths);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('images', $imageName, 'public');
                $imagePaths[] = 'storage/images/' . $imageName;
            }
        }

        // Update image field only if there are changes
        if ($request->hasFile('images') || $request->has('remove_images')) {
            $data['image'] = !empty($imagePaths) ? json_encode($imagePaths) : null;
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

        if ($product->image) {
            $images = json_decode($product->image, true);
            if (is_array($images)) {
                foreach ($images as $image) {
                    $path = str_replace('storage/', '', $image);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        }

        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted successfully'], 204);
    }

    public function showDetail($id)
    {
        $product = Product::with(['category', 'vendor.vendorInfo', 'reviews.user'])->find($id);
        if (!$product) {
            abort(404, 'Product not found');
        }

        $isInWishlist = false;
        if (Auth::check()) {
            $isInWishlist = Auth::user()->wishlists()
                ->where('wishable_type', Product::class)
                ->where('wishable_id', $id)
                ->exists();
        }

        return view('user.product_detail', compact('product', 'isInWishlist'));
    }
}
