<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypesController extends Controller
{
    /**
     * Menampilkan daftar semua Type dalam format JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $types = Type::orderBy('created_at', 'desc')->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Daftar semua jenis berhasil diambil.',
            'data' => $types
        ], 200);
    }

    /**
     * Menyimpan Type baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255|unique:types,type',
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
            $type = Type::create($validator->validated());
            return response()->json([
                'success' => true,
                'message' => 'Jenis berhasil ditambahkan.',
                'data' => $type
            ], 201); // HTTP 201 Created
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jenis: ' . $e->getMessage(),
            ], 500);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Type $type)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255|unique:types,type,' . $type->id,
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
            $type->update($validator->validated());
            return response()->json([
                'success' => true,
                'message' => 'Jenis berhasil diperbarui.',
                'data' => $type
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jenis.',
            ], 500);
        }
    }

    /**
     * Menghapus Type tertentu.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Type $type)
    {
        try {
            $type->delete();
            return response()->json([
                'success' => true,
                'message' => 'Jenis berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jenis: ' . $e->getMessage(),
            ], 500);
        }
    }
}