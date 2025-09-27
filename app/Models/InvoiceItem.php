<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_name',
        'item_description',
        'item_type',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the invoice that owns this item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Calculate total price based on quantity and unit price.
     */
    public function calculateTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Scope for tuition items.
     */
    public function scopeTuition($query)
    {
        return $query->where('item_type', 'tuition');
    }

    /**
     * Scope for fee items.
     */
    public function scopeFee($query)
    {
        return $query->where('item_type', 'fee');
    }

    /**
     * Scope for penalty items.
     */
    public function scopePenalty($query)
    {
        return $query->where('item_type', 'penalty');
    }

    /**
     * Scope for discount items.
     */
    public function scopeDiscount($query)
    {
        return $query->where('item_type', 'discount');
    }
}