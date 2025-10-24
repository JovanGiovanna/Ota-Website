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
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Province::create(['name' => $request->name]);

        return redirect()->route('super_admin.provinces')->with('success', 'Province created successfully');
    }

    public function edit($id)
    {
        $province = Province::find($id);
        if (!$province) {
            return redirect()->route('super_admin.provinces')->with('error', 'Province not found');
        }

        return view('super_admin.provinces.edit', compact('province'));
    }

    public function update(Request $request, $id)
    {
        $province = Province::find($id);
        if (!$province) {
            return redirect()->route('super_admin.provinces')->with('error', 'Province not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $province->update($request->only('name'));

        return redirect()->route('super_admin.provinces')->with('success', 'Province updated successfully');
    }

    public function destroy($id)
    {
        $province = Province::find($id);
        if (!$province) {
            return redirect()->route('super_admin.provinces')->with('error', 'Province not found');
        }

        $province->delete();
        return redirect()->route('super_admin.provinces')->with('success', 'Province deleted successfully');
    }
}
