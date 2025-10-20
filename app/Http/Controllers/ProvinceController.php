<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProvinceController extends Controller
{
    // ===================== API JSON =====================
    public function index()
    {
        $provinces = Province::all();
        return response()->json([
            'success' => true,
            'data' => $provinces
        ]);
    }

    public function show($id)
    {
        $province = Province::find($id);
        if (!$province) {
            return response()->json(['success' => false, 'message' => 'Province not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $province]);
    }

    public function searchApi(Request $request)
    {
        $name = $request->query('q');
        $provinces = Province::where('name', 'like', "%{$name}%")->get();
        return $provinces->isEmpty()
            ? response()->json(['success' => false, 'message' => 'Province not found'], 404)
            : response()->json(['success' => true, 'data' => $provinces]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only('name'), [
            'name' => 'required|string|max:100',
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
            'name' => 'required|string|max:100',
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

    public function welcome(Request $request)
    {
        Log::info("Request received: {$request->method()} {$request->path()}");
        return response()->json(['message' => 'Welcome to the Province API!']);
    }


}
