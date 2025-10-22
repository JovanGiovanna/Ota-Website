<?php

namespace App\Http\Controllers;

use App\Models\PackageProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\UniqueConstraintViolationException;

class PackageProductController extends Controller
{
    /**
     * Menampilkan daftar semua relasi PackageProduct.
     */
    public function index()
    {
        $packageProducts = PackageProduct::with(['package', 'product'])->get();
        return response()->json([
            'message' => 'Daftar semua produk dalam paket',
            'data' => $packageProducts
        ]);
    }

    /**
     * Menyimpan relasi PackageProduct baru (menambahkan produk ke paket).
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_package' => ['required', 'uuid', 'exists:packages,id'],
            'id_product' => ['required', 'uuid', 'exists:products,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Membuat entri baru di tabel pivot
            $packageProduct = PackageProduct::create($validator->validated());

            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke paket.',
                'data' => $packageProduct
            ], 201);

        } catch (UniqueConstraintViolationException $e) {
            // Menangkap jika kombinasi id_package dan id_product sudah ada
            return response()->json(['message' => 'Produk sudah ada di paket ini.'], 409);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data.'], 500);
        }
    }

    /**
     * Menampilkan relasi PackageProduct tertentu berdasarkan ID.
     */
    public function show(string $id)
    {
        $packageProduct = PackageProduct::with(['package', 'product'])->find($id);

        if (!$packageProduct) {
            return response()->json(['message' => 'Relasi tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $packageProduct]);
    }

    // Metode 'update' biasanya tidak digunakan untuk model pivot seperti ini
    // karena Anda tidak perlu mengubah kolom 'id_package' atau 'id_product' setelah dibuat.

    /**
     * Menghapus relasi PackageProduct (menghapus produk dari paket).
     */
    public function destroy(string $id)
    {
        $packageProduct = PackageProduct::find($id);

        if (!$packageProduct) {
            return response()->json(['message' => 'Relasi tidak ditemukan.'], 404);
        }

        $packageProduct->delete();

        return response()->json(['message' => 'Produk berhasil dihapus dari paket.'], 200);
    }
}