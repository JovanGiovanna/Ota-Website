<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        'pax' => 'sometimes|integer|min:1',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $request->only(['id_vendor','addons','desc','status','price','publish','pax']);

    DB::beginTransaction();
    try {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('addons', 'public'); // -> "addons/xxx.jpg"
            $data['image'] = $path;
        }

        $addon = Addon::create($data);

        DB::commit();

        $addon->image_url = $addon->image ? Storage::url($addon->image) : null;

        return response()->json(['message' => 'Addon berhasil ditambahkan', 'data' => $addon], 201);
    } catch (\Exception $e) {
        DB::rollBack();

        if (isset($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return response()->json(['message' => 'Gagal menyimpan Addon','error' => $e->getMessage()], 500);
    }
}

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['id_vendor', 'addons', 'desc', 'status', 'price', 'publish']);

            // Jika meng-upload gambar baru, simpan dan hapus gambar lama
            if ($request->hasFile('image')) {
                $newPath = $request->file('image')->store('addons', 'public');

                // hapus file lama bila ada
                if ($addon->image && Storage::disk('public')->exists($addon->image)) {
                    Storage::disk('public')->delete($addon->image);
                }

                $data['image'] = $newPath;
            }

            $addon->update($data);
            DB::commit();

            $addon->image_url = $addon->image ? Storage::url($addon->image) : null;

            return response()->json([
                'message' => 'Addon berhasil diperbarui',
                'data' => $addon
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            // jika file baru telah dibuat namun update gagal, hapus file baru
            if (isset($newPath) && Storage::disk('public')->exists($newPath)) {
                Storage::disk('public')->delete($newPath);
            }

            return response()->json([
                'message' => 'Gagal memperbarui Addon',
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

    public function showDetail($id)
    {
        $addon = Addon::with('vendor.vendorInfo')->find($id);
        if (!$addon) {
            abort(404, 'Addon not found');
        }
        return view('user.addon_detail', compact('addon'));
    }
}
