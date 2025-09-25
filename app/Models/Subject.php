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
                    ->withPivot(['status', 'enrolled_at', 'completed_at', 'notes'])
                    ->withTimestamps()
                    ->withCasts([
                        'enrolled_at' => 'date',
                        'completed_at' => 'date',
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
