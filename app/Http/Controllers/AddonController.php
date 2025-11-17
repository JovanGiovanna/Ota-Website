<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
        $addons = Addon::with('vendor') 
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
        'images' => 'nullable|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $request->only(['id_vendor','addons','desc','status','price','publish','pax']);
    $imagePaths = [];

    DB::beginTransaction();
    try {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('addons', $imageName, 'public');
                $imagePaths[] = 'addons/' . $imageName;
            }
        }

        $data['image'] = json_encode($imagePaths);

        $addon = Addon::create($data);

        DB::commit();

        $addon->image_url = $addon->image ? Storage::url($addon->image) : null;

        return response()->json(['message' => 'Addon berhasil ditambahkan', 'data' => $addon], 201);
    } catch (\Exception $e) {
        DB::rollBack();

        foreach ($imagePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
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
            'pax' => 'sometimes|integer|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['id_vendor', 'addons', 'desc', 'status', 'price', 'publish', 'pax']);
            $oldImages = $addon->image ? json_decode($addon->image, true) : [];
            $imagePaths = $oldImages ?: [];

            // Handle image removal
            if ($request->has('remove_images') && is_array($request->remove_images)) {
                foreach ($request->remove_images as $index) {
                    if (isset($imagePaths[$index])) {
                        $imageToRemove = $imagePaths[$index];
                        if (Storage::disk('public')->exists($imageToRemove)) {
                            Storage::disk('public')->delete($imageToRemove);
                        }
                        unset($imagePaths[$index]);
                    }
                }
                // Reindex array
                $imagePaths = array_values($imagePaths);
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('addons', $imageName, 'public');
                    $imagePaths[] = 'addons/' . $imageName;
                }
            }

            // Update image field only if there are changes
            if ($request->hasFile('images') || $request->has('remove_images')) {
                $data['image'] = !empty($imagePaths) ? json_encode($imagePaths) : null;
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
            foreach ($imagePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
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
            // Delete associated images
            if ($addon->image) {
                $images = json_decode($addon->image, true);
                if (is_array($images)) {
                    foreach ($images as $image) {
                        if (Storage::disk('public')->exists($image)) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }
            }

            $addon->delete(); // Soft delete
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
        $addon = Addon::with('vendor.vendorInfo', 'reviews.user')->find($id);
        if (!$addon) {
            abort(404, 'Addon not found');
        }

        $isInWishlist = false;
        if (Auth::check()) {
            $isInWishlist = Auth::user()->wishlists()
                ->where('wishable_type', Addon::class)
                ->where('wishable_id', $id)
                ->exists();
        }

        return view('user.addon_detail', compact('addon', 'isInWishlist'));
    }
}
