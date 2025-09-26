<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is pending verification
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the parent profile associated with the user.
     */
    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentUser::class);
    }

    /**
     * Get the student profile associated with the user.
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the student profile associated with the user (alias).
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the teacher profile associated with the user.
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get the children (students) associated with the user as parent.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    /**
     * Check if parent has active students
     */
    public function hasActiveStudents(): bool
    {
        if (!$this->parentProfile) {
            return false;
        }
        
        return $this->parentProfile->children()
            ->whereHas('user', function($query) {
                $query->where('status', 'active');
            })
            ->exists();
    }

    /**
     * Check if parent has any students (regardless of status)
     */
    public function hasStudents(): bool
    {
        if (!$this->parentProfile) {
            return false;
        }
        
        return $this->parentProfile->children()->exists();
    }

    /**
     * Check if student has active enrollments
     */
    public function hasActiveEnrollments(): bool
    {
        if (!$this->studentProfile) {
            return false;
        }
        
        return $this->studentProfile->subjects()
            ->wherePivot('enrollment_status', 'active')
            ->exists();
    }

    /**
     * Check if teacher is assigned to subjects
     */
    public function isAssignedToSubjects(): bool
    {
        if (!$this->teacherProfile) {
            return false;
        }
        
        return $this->teacherProfile->subjects()->exists();
    }

    /**
     * Check if user can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Check if user has students (for parents) - any students, not just active ones
        if ($this->hasRole('parent') && $this->hasStudents()) {
            return false;
        }
        
        // Check if user has active enrollments (for students)
        if ($this->hasRole('student') && $this->hasActiveEnrollments()) {
            return false;
        }
        
        // Check if teacher is assigned to subjects
        if ($this->hasRole('teacher') && $this->isAssignedToSubjects()) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if user status can be changed to pending/inactive
     */
    public function canChangeStatusToPendingOrInactive(): bool
    {
        // Parent with active students cannot be deactivated
        if ($this->hasRole('parent') && $this->hasActiveStudents()) {
            return false;
        }
        
        return true;
    }
}
