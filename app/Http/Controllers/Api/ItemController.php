<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // PUBLIC: Lihat semua alat camping
    public function index()
    {
        $items = Item::with(['category:id,name', 'user:id,name'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Daftar alat camping berhasil diambil',
            'data' => $items
        ], 200);
    }

    // PUBLIC: Lihat detail alat camping
    public function show($id)
    {
        $item = Item::with(['category:id,name', 'user:id,name'])->find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Data alat camping tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail alat camping berhasil diambil',
            'data' => $item
        ], 200);
    }

    // ADMIN & USER: Tambah alat camping baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'stock'         => 'required|integer|min:0',
            'price_per_day' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = $request->user()->id;

        $item = Item::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Alat camping berhasil ditambahkan',
            'data' => $item
        ], 201);
    }

    // ADMIN (Semua data) & USER (Hanya data miliknya sendiri / Ownership)
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Data alat camping tidak ditemukan'
            ], 404);
        }

        $user = $request->user();

        if ($user->role !== 'admin' && $item->user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden: Anda hanya dapat mengubah data alat yang Anda buat sendiri'
            ], 403);
        }

        $validated = $request->validate([
            'category_id'   => 'sometimes|required|exists:categories,id',
            'name'          => 'sometimes|required|string|max:255',
            'description'   => 'nullable|string',
            'stock'         => 'sometimes|required|integer|min:0',
            'price_per_day' => 'sometimes|required|numeric|min:0',
        ]);

        $item->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Data alat camping berhasil diperbarui',
            'data' => $item
        ], 200);
    }

    // HANYA ADMIN: Hapus data alat camping
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden: Hanya admin yang diizinkan menghapus data'
            ], 403);
        }

        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Data alat camping tidak ditemukan'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data alat camping berhasil dihapus'
        ], 200);
    }
}