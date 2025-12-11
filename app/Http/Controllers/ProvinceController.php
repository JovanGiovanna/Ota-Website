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

    public function create()
    {
        return view('super_admin.provinces.create');
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
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
            Province::create(['name' => $request->name]);
            if (function_exists('alert')) {
                alert()->success('Success', 'Province created successfully');
            }
            return redirect()->route('super_admin.provinces');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Province creation failed: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    public function edit($id)
    {
        $province = Province::find($id);
        if (!$province) {
            alert()->error('Error', 'Province not found');
            return redirect()->route('super_admin.provinces');
        }

        return view('super_admin.provinces.edit', compact('province'));
    }

    public function update(Request $request, $id)
    {
        $province = Province::find($id);
        if (!$province) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Province not found');
            }
            return redirect()->route('super_admin.provinces');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
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
            $province->update($request->only('name'));
            if (function_exists('alert')) {
                alert()->success('Success', 'Province updated successfully');
            }
            return redirect()->route('super_admin.provinces');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Province update failed: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $province = Province::find($id);
            if (!$province) {
                if (function_exists('alert')) {
                    alert()->error('Error', 'Province not found');
                }
                return redirect()->route('super_admin.provinces');
            }

            $province->delete();
            if (function_exists('alert')) {
                alert()->success('Success', 'Province deleted successfully');
            }
            return redirect()->route('super_admin.provinces');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Province deletion failed: ' . $e->getMessage());
            }
            return redirect()->back();
        }
    }
}
