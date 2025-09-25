<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'student_id',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'school_origin',
        'medical_notes',
        'notes',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent that owns the student.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get the subjects that the student is enrolled in.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class)
                    ->withPivot(['status', 'enrolled_at', 'completed_at', 'notes'])
                    ->withTimestamps()
                    ->withCasts([
                        'enrolled_at' => 'date',
                        'completed_at' => 'date',
                    ]);
    }

    /**
     * Get only enrolled subjects.
     */
    public function enrolledSubjects()
    {
        return $this->subjects()->wherePivot('status', 'enrolled');
    }

    /**
     * Get completed subjects.
     */
    public function completedSubjects()
    {
        return $this->subjects()->wherePivot('status', 'completed');
    }
}
