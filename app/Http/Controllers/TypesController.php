<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypesController extends Controller
{
    /**
     * Menampilkan daftar semua Type dan Categories dalam view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $types = Type::where('status', true)->orderBy('created_at', 'desc')->get();
        $categories = \App\Models\Category::with('type')->where('status', true)->orderBy('created_at', 'desc')->get();

        return view('super_admin.types_categories', compact('types', 'categories'));
    }

    /**
     * Menampilkan form untuk membuat Type baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('super_admin.types.create');
    }

    /**
     * Menampilkan form untuk mengedit Type tertentu.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\View\View
     */
    public function edit(Type $type)
    {
        return view('super_admin.types.edit', compact('type'));
    }

    /**
     * Menyimpan Type baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255|unique:types,type',
            'status' => 'required|boolean',
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
            Type::create($validator->validated());
            if (function_exists('alert')) {
                alert()->success('Success', 'Type created successfully');
            }
            return redirect()->route('super_admin.types_categories');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Type creation failed: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    /**
     * Menampilkan Type tertentu.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Type $type)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail jenis berhasil diambil.',
            'data' => $type
        ], 200);
    }

    /**
     * Memperbarui Type tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Type $type)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255|unique:types,type,' . $type->id,
            'status' => 'required|boolean',
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
            $type->update($validator->validated());
            if (function_exists('alert')) {
                alert()->success('Success', 'Type updated successfully');
            }
            return redirect()->route('super_admin.types_categories');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Type update failed: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    /**
     * Menghapus Type tertentu.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Type $type)
    {
        try {
            $type->delete();
            if (function_exists('alert')) {
                alert()->success('Success', 'Type deleted successfully');
            }
            return redirect()->route('super_admin.types_categories');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Type deletion failed: ' . $e->getMessage());
            }
            return redirect()->back();
        }
    }
}