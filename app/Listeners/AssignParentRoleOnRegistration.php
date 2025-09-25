<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Registered;

class AssignParentRoleOnRegistration
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // Only assign parent role if user doesn't already have a role
        // (This prevents overriding roles for admin/teacher/student users)
        if (!$event->user->hasAnyRole(['admin', 'teacher', 'student', 'parent'])) {
            $event->user->assignRole('parent');
        }
    }
}
