<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use Illuminate\Support\Facades\Validator;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::orderBy('name')->get();
        return view('super_admin.provinces', compact('provinces'));
    }

    public function show($id)
    {
        $province = Province::find($id);
        if (!$province) {
            return response()->json(['success' => false, 'message' => 'Province not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $province]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only('name'), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $province = Province::create(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Province created successfully',
            'data' => $province
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $province = Province::find($id);
        if (!$province) {
            return response()->json(['success' => false, 'message' => 'Province not found'], 404);
        }

        $validator = Validator::make($request->only('name'), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $province->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Province updated successfully',
            'data' => $province
        ]);
    }

    public function destroy($id)
    {
        $province = Province::find($id);
        if (!$province) {
            return response()->json(['success' => false, 'message' => 'Province not found'], 404);
        }

        $province->delete();
        return response()->json(['success' => true, 'message' => 'Province deleted successfully'], 200);
    }
}
