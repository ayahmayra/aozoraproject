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
}
