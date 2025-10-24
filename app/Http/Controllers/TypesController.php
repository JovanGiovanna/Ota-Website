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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Type::create($validator->validated());
            return redirect()->route('super_admin.types_categories')->with('success', 'Jenis berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan jenis: ' . $e->getMessage())->withInput();
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $type->update($validator->validated());
            return redirect()->route('super_admin.types_categories')->with('success', 'Jenis berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui jenis: ' . $e->getMessage())->withInput();
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
            return redirect()->route('super_admin.types_categories')->with('success', 'Jenis berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus jenis: ' . $e->getMessage());
        }
    }
}