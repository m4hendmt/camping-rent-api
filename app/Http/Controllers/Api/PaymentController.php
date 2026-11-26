<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user:id,name,email', 'transaction'])->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Daftar data pembayaran berhasil diambil',
            'data' => $payments
        ], 200);
    }

    public function show($id)
    {
        $payment = Payment::with(['user:id,name,email', 'transaction'])->find($id);

        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail pembayaran berhasil diambil',
            'data' => $payment
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:transfer,cash,qris,ewallet',
            'status_pembayaran' => 'nullable|in:pending,lunas,gagal',
            'tanggal_bayar' => 'nullable|date',
        ]);

        $payment = Payment::create([
            'user_id' => $request->user()->id, // Otomatis ID user yang login
            'transaction_id' => $validated['transaction_id'],
            'kode_pembayaran' => 'PAY-' . strtoupper(Str::random(8)),
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'status_pembayaran' => $validated['status_pembayaran'] ?? 'pending',
            'tanggal_bayar' => $validated['tanggal_bayar'] ?? now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pembayaran berhasil dibuat',
            'data' => $payment
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        if ($request->user()->role !== 'admin' && $payment->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden: Anda hanya dapat mengubah data pembayaran milik Anda sendiri.'
            ], 403);
        }

        $validated = $request->validate([
            'jumlah_bayar' => 'sometimes|numeric|min:0',
            'metode_pembayaran' => 'sometimes|in:transfer,cash,qris,ewallet',
            'status_pembayaran' => 'sometimes|in:pending,lunas,gagal',
            'tanggal_bayar' => 'nullable|date',
        ]);

        $payment->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil diperbarui',
            'data' => $payment
        ], 200);
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        $payment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil dihapus oleh Admin'
        ], 200);
    }
}
