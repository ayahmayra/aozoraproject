<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'employee_number',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'education_level',
        'institution',
        'graduation_year',
        'hire_date',
        'employment_status',
        'certifications',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
