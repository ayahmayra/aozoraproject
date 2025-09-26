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
}
