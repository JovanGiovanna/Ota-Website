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

        $cities = $query->orderBy('name')->get();
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
        $validator = Validator::make($request->all(), [
            'id_province' => 'required|exists:province,id',
            'name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            if (function_exists('alert')) {
                alert()->error('Validation Failed', 'Please check the form and try again');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            City::create($request->only(['id_province', 'name']));
            if (function_exists('alert')) {
                alert()->success('Success', 'City created successfully');
            }
            return redirect()->route('super_admin.cities');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'City creation failed: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $city = City::find($id);
        if (!$city) {
            if (function_exists('alert')) {
                alert()->error('Error', 'City not found');
            }
            return redirect()->route('super_admin.cities');
        }

        $validator = Validator::make($request->all(), [
            'id_province' => 'required|exists:province,id',
            'name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            if (function_exists('alert')) {
                alert()->error('Validation Failed', 'Please check the form and try again');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $city->update($request->only(['id_province', 'name']));
            if (function_exists('alert')) {
                alert()->success('Success', 'City updated successfully');
            }
            return redirect()->route('super_admin.cities');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'City update failed: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
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
        try {
            $city = City::find($id);
            if (!$city) {
                if (function_exists('alert')) {
                    alert()->error('Error', 'City not found');
                }
                return redirect()->route('super_admin.cities');
            }

            $city->delete();
            if (function_exists('alert')) {
                alert()->success('Success', 'City deleted successfully');
            }
            return redirect()->route('super_admin.cities');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'City deletion failed: ' . $e->getMessage());
            }
            return redirect()->back();
        }
    }
}
