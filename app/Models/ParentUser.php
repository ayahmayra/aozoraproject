<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentUser extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'occupation',
        'workplace',
        'date_of_birth',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user that owns the parent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
