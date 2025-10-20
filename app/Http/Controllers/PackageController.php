<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    // ===================== API JSON =====================
    public function index()
    {
        $packages = Package::with('products')->get();
        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    public function show($id)
    {
        $package = Package::with('products')->find($id);
        if (!$package) {
            return response()->json(['success' => false, 'message' => 'Package not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $package]);
    }

    public function searchApi(Request $request)
    {
        $name = $request->query('q');
        $packages = Package::with('products')->where('name_package', 'like', "%{$name}%")->get();
        return $packages->isEmpty()
            ? response()->json(['success' => false, 'message' => 'Package not found'], 404)
            : response()->json(['success' => true, 'data' => $packages]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:packages,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price_publish' => 'required|numeric|min:0',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after:start_publish',
            'is_active' => 'boolean',
            'products' => 'array',
            'products.*' => 'uuid|exists:products,id',
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
        if (!$request->slug) {
            $data['slug'] = Str::slug($request->name_package);
        }

        $package = Package::create($data);

        if ($request->products) {
            $package->products()->attach($request->products);
        }

        return response()->json([
            'success' => true,
            'message' => 'Package created successfully',
            'data' => $package->load('products')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $package = Package::find($id);
        if (!$package) {
            return response()->json(['success' => false, 'message' => 'Package not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:packages,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price_publish' => 'required|numeric|min:0',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after:start_publish',
            'is_active' => 'boolean',
            'products' => 'array',
            'products.*' => 'uuid|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (!$request->slug) {
            $data['slug'] = Str::slug($request->name_package);
        }

        $package->update($data);

        if ($request->products) {
            $package->products()->sync($request->products);
        }

        return response()->json([
            'success' => true,
            'message' => 'Package updated successfully',
            'data' => $package->load('products')
        ]);
    }

    public function destroy($id)
    {
        $package = Package::find($id);
        if (!$package) {
            return response()->json(['success' => false, 'message' => 'Package not found'], 404);
        }

        $package->delete();
        return response()->json(['success' => true, 'message' => 'Package deleted successfully'], 200);
    }


}
