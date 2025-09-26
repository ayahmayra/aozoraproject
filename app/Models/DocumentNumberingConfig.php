<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentNumberingConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'prefix',
        'suffix',
        'current_number',
        'number_length',
        'separator',
        'include_year',
        'include_month',
        'include_day',
        'year_format',
        'month_format',
        'day_format',
        'reset_yearly',
        'reset_monthly',
        'reset_daily',
        'description',
        'is_active',
    ];

    protected $casts = [
        'include_year' => 'boolean',
        'include_month' => 'boolean',
        'include_day' => 'boolean',
        'reset_yearly' => 'boolean',
        'reset_monthly' => 'boolean',
        'reset_daily' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Set the separator attribute.
     */
    public function setSeparatorAttribute($value)
    {
        $this->attributes['separator'] = empty($value) ? null : $value;
    }

    /**
     * Set the prefix attribute.
     */
    public function setPrefixAttribute($value)
    {
        $this->attributes['prefix'] = empty($value) ? null : $value;
    }

    /**
     * Set the suffix attribute.
     */
    public function setSuffixAttribute($value)
    {
        $this->attributes['suffix'] = empty($value) ? null : $value;
    }

    /**
     * Set the description attribute.
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = empty($value) ? null : $value;
    }

    /**
     * Generate the next number for this entity type
     */
    public function generateNextNumber(): string
    {
        $number = $this->current_number;
        $formattedNumber = str_pad($number, $this->number_length, '0', STR_PAD_LEFT);
        
        $parts = [];
        
        // Add prefix
        if ($this->prefix) {
            $parts[] = $this->prefix;
        }
        
        // Add date parts if enabled
        if ($this->include_year) {
            $parts[] = now()->format($this->year_format);
        }
        if ($this->include_month) {
            $parts[] = now()->format($this->month_format);
        }
        if ($this->include_day) {
            $parts[] = now()->format($this->day_format);
        }
        
        // Add separator and number
        $parts[] = $this->separator . $formattedNumber;
        
        // Add suffix
        if ($this->suffix) {
            $parts[] = $this->suffix;
        }
        
        return implode('', $parts);
    }

    /**
     * Increment the current number
     */
    public function incrementNumber(): void
    {
        $this->current_number++;
        $this->save();
    }

    /**
     * Check if number should be reset based on reset settings
     */
    public function shouldResetNumber(): bool
    {
        if ($this->reset_daily) {
            return true; // Reset every day
        }
        
        if ($this->reset_monthly && now()->day === 1) {
            return true; // Reset on first day of month
        }
        
        if ($this->reset_yearly && now()->month === 1 && now()->day === 1) {
            return true; // Reset on first day of year
        }
        
        return false;
    }

    /**
     * Reset the current number to 1
     */
    public function resetNumber(): void
    {
        $this->current_number = 1;
        $this->save();
    }

    /**
     * Get active config for entity type
     */
    public static function getActiveConfig(string $entityType): ?self
    {
        return self::where('entity_type', $entityType)
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Generate preview of the numbering format
     */
    public function getPreview(): string
    {
        $number = 123; // Sample number for preview
        $formattedNumber = str_pad($number, $this->number_length, '0', STR_PAD_LEFT);
        
        $parts = [];
        
        if ($this->prefix) {
            $parts[] = $this->prefix;
        }
        
        if ($this->include_year) {
            $parts[] = now()->format($this->year_format);
        }
        if ($this->include_month) {
            $parts[] = now()->format($this->month_format);
        }
        if ($this->include_day) {
            $parts[] = now()->format($this->day_format);
        }
        
        $parts[] = $this->separator . $formattedNumber;
        
        if ($this->suffix) {
            $parts[] = $this->suffix;
        }
        
        return implode('', $parts);
    }
}