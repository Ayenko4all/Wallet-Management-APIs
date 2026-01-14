<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'debit_id',
        'credit_id',
        'reversible',
        'reversed_at',
        'metadata',
    ];

    protected $casts = [
        'reversible' => 'boolean',
        'reversed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the debit transaction entry
     */
    public function debitEntry(): BelongsTo
    {
        return $this->belongsTo(TransactionEntry::class, 'debit_id');
    }

    /**
     * Get the credit transaction entry
     */
    public function creditEntry(): BelongsTo
    {
        return $this->belongsTo(TransactionEntry::class, 'credit_id');
    }

    /**
     * Check if transaction is reversed
     */
    public function isReversed(): bool
    {
        return !is_null($this->reversed_at);
    }

    /**
     * Check if transaction can be reversed
     */
    public function canBeReversed(): bool
    {
        return $this->reversable && !$this->isReversed();
    }
}
