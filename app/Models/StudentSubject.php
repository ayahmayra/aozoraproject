<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentSubject extends Model
{
    protected $table = 'student_subject';

    protected $fillable = [
        'student_id',
        'subject_id',
        'status',
        'enrolled_at',
        'completed_at',
        'notes',
        'enrollment_date',
        'start_date',
        'end_date',
        'payment_method',
        'payment_amount',
        'enrollment_status',
        'parent_id',
        'enrollment_number',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
        'completed_at' => 'date',
        'enrollment_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * Get the student that owns this enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the subject for this enrollment.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the parent who enrolled the student.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get the invoices for this enrollment.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'enrollment_id');
    }

    /**
     * Check if enrollment is active.
     */
    public function isActive(): bool
    {
        return $this->enrollment_status === 'active';
    }

    /**
     * Check if enrollment is pending.
     */
    public function isPending(): bool
    {
        return $this->enrollment_status === 'pending';
    }

    /**
     * Check if enrollment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->enrollment_status === 'completed';
    }

    /**
     * Check if enrollment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->enrollment_status === 'cancelled';
    }

    /**
     * Scope for active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('enrollment_status', 'active');
    }

    /**
     * Scope for pending enrollments.
     */
    public function scopePending($query)
    {
        return $query->where('enrollment_status', 'pending');
    }

    /**
     * Scope for completed enrollments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('enrollment_status', 'completed');
    }

    /**
     * Scope for cancelled enrollments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('enrollment_status', 'cancelled');
    }

    /**
     * Scope for enrollments by payment method.
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }
}