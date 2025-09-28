<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    // Many-to-many relationship with Teacher
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }

    // Many-to-many relationship with Student
    public function students()
    {
        return $this->belongsToMany(Student::class)
                    ->withPivot([
                        'status', 'enrolled_at', 'completed_at', 'notes',
                        'enrollment_date', 'start_date', 'end_date',
                        'payment_method', 'payment_amount',
                        'enrollment_status', 'parent_id', 'enrollment_number'
                    ])
                    ->withTimestamps()
                    ->withCasts([
                        'enrolled_at' => 'date',
                        'completed_at' => 'date',
                        'enrollment_date' => 'date',
                        'start_date' => 'date',
                        'end_date' => 'date',
                    ]);
    }

    // Get only enrolled students
    public function enrolledStudents()
    {
        return $this->students()->wherePivot('status', 'enrolled');
    }

    // Get completed students
    public function completedStudents()
    {
        return $this->students()->wherePivot('status', 'completed');
    }

    /**
     * Check if subject has active enrollments
     */
    public function hasActiveEnrollments(): bool
    {
        return $this->students()
            ->wherePivot('enrollment_status', 'active')
            ->exists();
    }

    /**
     * Check if subject has assigned teachers
     */
    public function hasAssignedTeachers(): bool
    {
        return $this->teachers()->exists();
    }

    /**
     * Check if subject has any enrollments (active or not)
     */
    public function hasAnyEnrollments(): bool
    {
        return $this->students()->exists();
    }

    /**
     * Check if subject can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->hasActiveEnrollments() && !$this->hasAssignedTeachers() && !$this->hasAnyEnrollments();
    }
}
