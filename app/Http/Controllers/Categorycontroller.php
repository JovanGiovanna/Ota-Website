<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua Category dalam format JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Muat relasi 'type' untuk menghindari N+1 problem
        $categories = Category::with('type')->orderBy('created_at', 'desc')->paginate(10);
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil.',
            'data' => $categories
        ], 200);
    }

    /**
     * Menampilkan form untuk membuat Category baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $types = Type::where('status', true)->get();
        return view('super_admin.categories.create', compact('types'));
    }
    
    public function edit(Category $category)
    {
        $types = Type::where('status', true)->get();
        return view('super_admin.categories.edit', compact('category', 'types'));
    }


    /**
     * Menyimpan Category yang baru dibuat di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_type' => 'required|uuid|exists:types,id',
            'categories' => 'required|string|max:255|unique:categories,categories',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            // Use realrashid/sweet-alert facade helper if available
            if (function_exists('alert')) {
                alert()->error('Validation Failed', 'Please check the form and try again');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Category::create($validator->validated());
            if (function_exists('alert')) {
                alert()->success('Success', 'Category created successfully');
            }
            return redirect()->route('super_admin.types_categories');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Category creation failed: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    /**
     * Menampilkan Category tertentu.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        // Muat relasi 'type'
        $category->load('type');
        
        return response()->json([
            'success' => true,
            'message' => 'Detail kategori berhasil diambil.',
            'data' => $category
        ], 200);
    }

    /**
     * Memperbarui Category tertentu di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'id_type' => 'required|uuid|exists:types,id',
            'categories' => 'required|string|max:255|unique:categories,categories,' . $category->id,
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
            $category->update($validator->validated());
            if (function_exists('alert')) {
                alert()->success('Success', 'Category updated successfully');
            }
            return redirect()->route('super_admin.types_categories');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Category update failed: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    /**
     * Menghapus Category tertentu dari database.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            if (function_exists('alert')) {
                alert()->success('Success', 'Category deleted successfully');
            }
            return redirect()->route('super_admin.types_categories');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Category deletion failed: ' . $e->getMessage());
            }
            return redirect()->back();
        }
    }
}