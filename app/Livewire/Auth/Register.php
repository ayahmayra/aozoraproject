<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\ParentUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class Register extends Component
{
    // User fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    // Parent fields
    public $phone = '';
    public $address = '';
    public $occupation = '';
    public $workplace = '';
    public $date_of_birth = '';
    public $gender = '';
    public $emergency_contact_name = '';
    public $emergency_contact_phone = '';
    public $notes = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'occupation' => 'nullable|string|max:100',
        'workplace' => 'nullable|string|max:100',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female',
        'emergency_contact_name' => 'nullable|string|max:100',
        'emergency_contact_phone' => 'nullable|string|max:20',
        'notes' => 'nullable|string|max:1000',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'email_verified_at' => now(),
            'status' => 'pending', // Set user as pending verification
        ]);

        // Assign parent role
        $user->assignRole('parent');

        // Create parent profile
        ParentUser::create([
            'user_id' => $user->id,
            'phone' => $this->phone,
            'address' => $this->address,
            'occupation' => $this->occupation,
            'workplace' => $this->workplace,
            'date_of_birth' => $this->date_of_birth ?: null,
            'gender' => $this->gender ?: null,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'notes' => $this->notes,
        ]);

        // Fire registered event
        event(new Registered($user));

        // Login user
        auth()->login($user);

        // Redirect to verification pending page
        return redirect()->route('verification.pending');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.guest');
    }
}
