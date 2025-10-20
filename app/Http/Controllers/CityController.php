<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Province;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    // ===================== API JSON =====================
    public function index()
    {
        $cities = City::with('province')->get();
        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    public function show($id)
    {
        $city = City::with('province')->find($id);
        if (!$city) {
            return response()->json(['success' => false, 'message' => 'City not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $city]);
    }

    public function searchApi(Request $request)
    {
        $name = $request->query('q');
        $cities = City::with('province')->where('name', 'like', "%{$name}%")->get();
        return $cities->isEmpty()
            ? response()->json(['success' => false, 'message' => 'City not found'], 404)
            : response()->json(['success' => true, 'data' => $cities]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_province' => 'required|uuid|exists:province,id',
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

        $city = City::create($request->only(['id_province', 'name']));

        return response()->json([
            'success' => true,
            'message' => 'City created successfully',
            'data' => $city->load('province')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json(['success' => false, 'message' => 'City not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_province' => 'required|uuid|exists:province,id',
            'name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $city->update($request->only(['id_province', 'name']));

        return response()->json([
            'success' => true,
            'message' => 'City updated successfully',
            'data' => $city->load('province')
        ]);
    }

    public function destroy($id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json(['success' => false, 'message' => 'City not found'], 404);
        }

        $city->delete();
        return response()->json(['success' => true, 'message' => 'City deleted successfully'], 200);
    }


}
