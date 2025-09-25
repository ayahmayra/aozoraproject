<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'description',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'accent_color',
        'mission',
        'vision',
        'values',
        'founded_year',
        'license_number',
        'tax_id',
        'social_media',
        'contact_persons',
        'is_active',
    ];

    protected $casts = [
        'social_media' => 'array',
        'contact_persons' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the organisation's full address
     */
    public function getFullAddressAttribute(): string
    {
        $address = collect([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ])->filter()->implode(', ');

        return $address ?: 'Address not provided';
    }

    /**
     * Get the organisation's display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->short_name ?: $this->name;
    }

    /**
     * Scope for active organisations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
