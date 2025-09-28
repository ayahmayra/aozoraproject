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

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subject) {
            if (empty($subject->code)) {
                $subject->code = self::generateSubjectCode();
            }
        });
    }

    /**
     * Generate subject code using document numbering system
     */
    public static function generateSubjectCode(): string
    {
        $config = \App\Models\DocumentNumberingConfig::where('entity_type', 'subject')->first();
        
        if (!$config) {
            // Fallback if no config found
            $year = date('Y');
            $month = date('m');
            $lastNumber = self::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
            
            return "SUB{$year}{$month}" . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        // Get the last number for this period
        $lastNumber = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $nextNumber = $lastNumber + 1;
        
        // Format the number based on config
        $formattedNumber = str_pad($nextNumber, $config->number_length, '0', STR_PAD_LEFT);
        
        // Build the subject code
        $subjectCode = $config->prefix;
        
        if ($config->include_year) {
            $subjectCode .= $year;
        }
        
        if ($config->include_month) {
            $subjectCode .= $month;
        }
        
        if ($config->include_day) {
            $subjectCode .= $day;
        }
        
        if ($config->separator) {
            $subjectCode .= $config->separator;
        }
        
        $subjectCode .= $formattedNumber;
        
        if ($config->suffix) {
            $subjectCode .= $config->suffix;
        }
        
        return $subjectCode;
    }

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
