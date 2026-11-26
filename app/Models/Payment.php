<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'kode_pembayaran',
        'jumlah_bayar',
        'metode_pembayaran',
        'status_pembayaran',
        'tanggal_bayar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
