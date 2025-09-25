<?php

namespace App\Livewire\Teacher;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.teacher.dashboard')
            ->title('Teacher Dashboard')
            ->layout('components.layouts.app');
    }
}
