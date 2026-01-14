<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TransactionEntry extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
        'status',
        'reference',
        'currency',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($transfer) {
            if (empty($transfer->reference)) {
                $transfer->reference = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the wallet associated with this entry
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, $this->type === 'debit' ? 'debit_id' : 'credit_id');
    }
}
