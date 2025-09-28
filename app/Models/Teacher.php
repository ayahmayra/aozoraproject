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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($teacher) {
            if (empty($teacher->employee_number)) {
                $teacher->employee_number = self::generateEmployeeNumber();
            }
        });
    }

    /**
     * Generate employee number using document numbering system
     */
    public static function generateEmployeeNumber(): string
    {
        $config = \App\Models\DocumentNumberingConfig::where('entity_type', 'teacher')->first();
        
        if (!$config) {
            // Fallback if no config found
            $year = date('Y');
            $month = date('m');
            $lastNumber = self::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
            
            return "TCH{$year}{$month}" . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
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
        
        // Build the employee number
        $employeeNumber = $config->prefix;
        
        if ($config->include_year) {
            $employeeNumber .= $year;
        }
        
        if ($config->include_month) {
            $employeeNumber .= $month;
        }
        
        if ($config->include_day) {
            $employeeNumber .= $day;
        }
        
        if ($config->separator) {
            $employeeNumber .= $config->separator;
        }
        
        $employeeNumber .= $formattedNumber;
        
        if ($config->suffix) {
            $employeeNumber .= $config->suffix;
        }
        
        return $employeeNumber;
    }

    /**
     * Get the user that owns the teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Many-to-many relationship with Subject
    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }
}
