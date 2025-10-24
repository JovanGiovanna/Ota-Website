<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::with('province');

        if ($request->has('province_id') && $request->province_id) {
            $query->where('id_province', $request->province_id);
        }

        $cities = $query->orderBy('name')->paginate(10);
        $provinces = \App\Models\Province::orderBy('name')->get();

        return view('super_admin.cities', compact('cities', 'provinces'));
    }

    public function create()
    {
        $provinces = \App\Models\Province::orderBy('name')->get();
        return view('super_admin.cities.create', compact('provinces'));
    }

    public function show($id)
    {
        $city = City::with('province')->find($id);
        if (!$city) {
            return response()->json(['success' => false, 'message' => 'City not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $city]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_province' => 'required|exists:province,id',
            'name' => 'required|string|max:100',
        ]);

        City::create($request->only(['id_province', 'name']));

        return redirect()->route('super_admin.cities')->with('success', 'City created successfully');
    }

    public function update(Request $request, $id)
    {
        $city = City::find($id);
        if (!$city) {
            return redirect()->route('super_admin.cities')->with('error', 'City not found');
        }

        $request->validate([
            'id_province' => 'required|exists:province,id',
            'name' => 'required|string|max:100',
        ]);

        $city->update($request->only(['id_province', 'name']));

        return redirect()->route('super_admin.cities')->with('success', 'City updated successfully');
    }

    public function edit($id)
    {
        $city = City::find($id);
        if (!$city) {
            return redirect()->route('super_admin.cities')->with('error', 'City not found');
        }

        $provinces = \App\Models\Province::orderBy('name')->get();
        return view('super_admin.cities.edit', compact('city', 'provinces'));
    }

    public function destroy($id)
    {
        $city = City::find($id);
        if (!$city) {
            return redirect()->route('super_admin.cities')->with('error', 'City not found');
        }

        $city->delete();
        return redirect()->route('super_admin.cities')->with('success', 'City deleted successfully');
    }
}
