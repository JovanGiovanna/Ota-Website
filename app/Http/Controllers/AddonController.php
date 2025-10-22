<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class AddonController extends Controller
{
    /**
     * Menampilkan daftar semua addons.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // 
        $addons = Addon::with('vendor') // Memuat relasi vendor
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'message' => 'Daftar Addons berhasil diambil',
            'data' => $addons,
        ]);
    }

    /**
     * Menyimpan addon baru.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_vendor' => 'nullable|uuid|exists:vendor,id',
            'addons' => 'required|string|max:255',
            'desc' => 'nullable|string|max:500',
            'status' => 'sometimes|string|in:available,unavailable,draft',
            'price' => 'required|numeric|min:0',
            'publish' => 'sometimes|boolean',
            'image_url' => 'nullable|url|max:2048',
        ]);

        if ($validator->fails()) {
            // 
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $addon = Addon::create($request->all());

            return response()->json([
                'message' => 'Addon berhasil ditambahkan',
                'data' => $addon
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan Addon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail addon tertentu.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $addon = Addon::with('vendor')->find($id);

        if (!$addon) {
            return response()->json(['message' => 'Addon tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail Addon berhasil diambil',
            'data' => $addon,
        ]);
    }

    /**
     * Memperbarui addon tertentu.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $addon = Addon::find($id);

        if (!$addon) {
            return response()->json(['message' => 'Addon tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_vendor' => 'nullable|uuid|exists:vendor,id',
            'addons' => 'sometimes|string|max:255',
            'desc' => 'nullable|string|max:500',
            'status' => 'sometimes|string|in:available,unavailable,draft',
            'price' => 'sometimes|numeric|min:0',
            'publish' => 'sometimes|boolean',
            'image_url' => 'nullable|url|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            $addon->update($request->all());

            return response()->json([
                'message' => 'Addon berhasil diperbarui',
                'data' => $addon
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui Addon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus addon tertentu (Soft Delete).
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $addon = Addon::find($id);

        if (!$addon) {
            return response()->json(['message' => 'Addon tidak ditemukan'], 404);
        }

        try {
            $addon->delete(); // Soft delete
            // 
            return response()->json(['message' => 'Addon berhasil dihapus (soft deleted)']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus Addon',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}