<?php

namespace App\Livewire\Parent;

use Livewire\Component;
use App\Models\User;

class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $user = auth()->user();
        $parentProfile = $user->parentProfile;
        
        $this->stats = [
            'children_count' => 0, // Will be implemented when student model is ready
            'upcoming_events' => 0,
            'recent_activities' => 0,
            'notifications' => 0,
            'parent_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $parentProfile?->phone ?? 'Not provided',
                'occupation' => $parentProfile?->occupation ?? 'Not provided',
                'workplace' => $parentProfile?->workplace ?? 'Not provided',
                'address' => $parentProfile?->address ?? 'Not provided',
            ]
        ];
    }

    public function render()
    {
        return view('livewire.parent.dashboard')
            ->title('Parent Dashboard')
            ->layout('components.layouts.app');
    }
}
