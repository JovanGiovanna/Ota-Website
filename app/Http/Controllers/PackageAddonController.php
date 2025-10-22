<?php

namespace App\Http\Controllers;

use App\Models\PackageAddon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PackageAddonController extends Controller
{
    /**
     * Menampilkan daftar semua relasi PackageAddon.
     */
    public function index()
    {
        $packageAddons = PackageAddon::with(['package', 'addon'])->latest()->get();
        // Return response dengan data relasi
        return response()->json([
            'message' => 'Daftar relasi Package Addon berhasil diambil.',
            'data' => $packageAddons
        ]);
    }

    /**
     * Menyimpan relasi PackageAddon baru.
     */
    public function store(Request $request)
    {
        try {
            // Validasi data input
            $validatedData = $request->validate([
                'id_package' => 'required|uuid|exists:packages,id',
                'id_addons' => 'required|uuid|exists:addons,id',
                'note' => 'nullable|string|max:255',
            ]);

            // Cek apakah kombinasi sudah ada (sesuai unique key di migrasi)
            $existing = PackageAddon::where('id_package', $validatedData['id_package'])
                                     ->where('id_addons', $validatedData['id_addons'])
                                     ->exists();

            if ($existing) {
                return response()->json([
                    'message' => 'Relasi ini sudah ada.',
                ], 409); // Conflict
            }

            // Buat entri baru
            $packageAddon = PackageAddon::create($validatedData);

            // Return response
            return response()->json([
                'message' => 'Relasi Package Addon berhasil ditambahkan.',
                'data' => $packageAddon
            ], 201); // Created

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        }
    }

    /**
     * Menampilkan detail relasi PackageAddon tertentu.
     */
    public function show(string $id)
    {
        // Cari berdasarkan kolom 'id' (UUID)
        $packageAddon = PackageAddon::with(['package', 'addon'])->find($id);

        if (!$packageAddon) {
            return response()->json([
                'message' => 'Relasi Package Addon tidak ditemukan.',
            ], 404); // Not Found
        }

        return response()->json([
            'message' => 'Detail relasi Package Addon berhasil diambil.',
            'data' => $packageAddon
        ]);
    }

    /**
     * Memperbarui relasi PackageAddon yang ada.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Cari entri berdasarkan 'id' (UUID)
            $packageAddon = PackageAddon::find($id);

            if (!$packageAddon) {
                return response()->json([
                    'message' => 'Relasi Package Addon tidak ditemukan.',
                ], 404);
            }

            // Validasi data input
            $validatedData = $request->validate([
                'id_package' => 'required|uuid|exists:packages,id',
                'id_addons' => 'required|uuid|exists:addons,id',
                'note' => 'nullable|string|max:255',
            ]);

            // Cek duplikasi: pastikan kombinasi baru tidak ada pada entri lain
            $existing = PackageAddon::where('id_package', $validatedData['id_package'])
                                     ->where('id_addons', $validatedData['id_addons'])
                                     ->where('id', '!=', $id) // Kecuali entri saat ini
                                     ->exists();

            if ($existing) {
                return response()->json([
                    'message' => 'Kombinasi Package dan Addon ini sudah digunakan oleh entri lain.',
                ], 409);
            }

            // Perbarui data
            $packageAddon->update($validatedData);

            // Return response
            return response()->json([
                'message' => 'Relasi Package Addon berhasil diperbarui.',
                'data' => $packageAddon
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Menghapus relasi PackageAddon.
     */
    public function destroy(string $id)
    {
        // Cari dan hapus entri
        $packageAddon = PackageAddon::find($id);

        if (!$packageAddon) {
            return response()->json([
                'message' => 'Relasi Package Addon tidak ditemukan.',
            ], 404);
        }

        $packageAddon->delete();

        // Return response
        return response()->json([
            'message' => 'Relasi Package Addon berhasil dihapus.'
        ], 200); // OK
    }
}