<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'student_id',
        'subject_id',
        'enrollment_id',
        'invoice_date',
        'due_date',
        'billing_period_start',
        'billing_period_end',
        'amount',
        'tax_amount',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        'paid_at',
        'paid_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Get the student that owns the invoice.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the subject for this invoice.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the enrollment for this invoice.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentSubject::class, 'enrollment_id');
    }

    /**
     * Get the admin who created this invoice.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the invoice items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the payments for this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now() && $this->payment_status !== 'paid';
    }

    /**
     * Check if invoice is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if invoice is pending.
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Get remaining amount to pay.
     */
    public function getRemainingAmount(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Check if invoice is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    /**
     * Scope for overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('payment_status', '!=', 'paid');
    }

    /**
     * Scope for pending invoices.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope for paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope for invoices by student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope for invoices by subject.
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

}