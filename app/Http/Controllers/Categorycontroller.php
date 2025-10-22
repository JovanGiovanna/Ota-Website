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
     * Metode create dan edit biasanya dihilangkan untuk API.
     * Saya akan menghapusnya atau membiarkannya saja jika Anda ingin menyimpannya untuk keperluan lain.
     * Jika Anda hanya menggunakan ini sebagai API, metode ini tidak diperlukan.
     */
    public function create()
    {
        // Mengembalikan daftar types yang aktif untuk referensi (opsional)
        $types = Type::where('status', true)->pluck('type', 'id');
        
        return response()->json([
            'success' => true,
            'message' => 'Data pendukung untuk form buat kategori.',
            'types' => $types
        ], 200);
    }
    
    public function edit(Category $category)
    {
        // Mengembalikan detail kategori dan daftar types (opsional)
        $types = Type::where('status', true)->pluck('type', 'id');
        $category->load('type'); // Muat data type

        return response()->json([
            'success' => true,
            'message' => 'Data kategori untuk pengeditan.',
            'category' => $category,
            'types' => $types
        ], 200);
    }


    /**
     * Menyimpan Category yang baru dibuat di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_type' => 'required|uuid|exists:types,id',
            'categories' => 'required|string|max:255|unique:categories,categories',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422); // HTTP 422 Unprocessable Entity
        }

        try {
            $category = Category::create($validator->validated());
            $category->load('type'); // Muat relasi setelah dibuat
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan.',
                'data' => $category
            ], 201); // HTTP 201 Created
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kategori.',
                'error_detail' => $e->getMessage()
            ], 500);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'id_type' => 'required|uuid|exists:types,id',
            'categories' => 'required|string|max:255|unique:categories,categories,' . $category->id,
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category->update($validator->validated());
            $category->load('type'); // Muat relasi setelah diupdate

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui!',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kategori.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus Category tertentu dari database.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori. Pastikan tidak ada data lain yang terkait.',
                'error_detail' => $e->getMessage()
            ], 409); // HTTP 409 Conflict (umumnya digunakan untuk foreign key constraint violations)
        }
    }
}