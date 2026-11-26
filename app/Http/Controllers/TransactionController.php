<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $transactions = Transaction::query()
                ->with('user:id,name,email')
                ->when($request->query('status'), function ($query, $status) {
                    $query->where('status', $status);
                })
                ->latest('id')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar transaksi penyewaan alat camping',
                'data'    => $transactions,
            ], 200);
        } catch (QueryException $e) {
            Log::error('Gagal membaca transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database',
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $transaction = Transaction::with('user:id,name,email')->find($id);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail transaksi penyewaan',
                'data'    => $transaction,
            ], 200);
        } catch (QueryException $e) {
            Log::error('Gagal membaca detail transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_alat'       => 'required|string|max:100',
                'jumlah'          => 'required|integer|min:1',
                'tanggal_sewa'    => 'required|date',
                'tanggal_kembali' => 'required|date|after_or_equal:tanggal_sewa',
                'total_harga'     => 'required|integer|min:0',
                'status'          => 'sometimes|in:disewa,selesai,batal',
                'catatan'         => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = auth('api')->id() ?? $request->input('user_id');

            $transaction = Transaction::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi penyewaan berhasil dibuat',
                'data'    => $transaction->load('user:id,name,email'),
            ], 201);
        } catch (QueryException $e) {
            Log::error('Gagal menyimpan transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database',
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_alat'       => 'sometimes|required|string|max:100',
                'jumlah'          => 'sometimes|required|integer|min:1',
                'tanggal_sewa'    => 'sometimes|required|date',
                'tanggal_kembali' => 'sometimes|required|date|after_or_equal:tanggal_sewa',
                'total_harga'     => 'sometimes|required|integer|min:0',
                'status'          => 'sometimes|in:disewa,selesai,batal',
                'catatan'         => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $transaction->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Transaksi penyewaan berhasil diperbarui',
                'data'    => $transaction->load('user:id,name,email'),
            ], 200);
        } catch (QueryException $e) {
            Log::error('Gagal mengubah transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database',
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = Transaction::whereKey($id)->delete();

            if ($deleted === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi penyewaan berhasil dihapus',
                'data'    => null,
            ], 200);
        } catch (QueryException $e) {
            Log::error('Gagal menghapus transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database',
            ], 500);
        }
    }
}