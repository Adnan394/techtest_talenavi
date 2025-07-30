<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'points',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
