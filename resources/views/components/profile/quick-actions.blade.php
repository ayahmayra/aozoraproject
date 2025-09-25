@props(['user'])

<div class="space-y-3">
    <flux:button variant="ghost" href="{{ route('profile.edit') }}" class="w-full justify-start">
        <flux:icon.pencil class="h-4 w-4 mr-3" />
        Edit Profile
    </flux:button>
    
    @if($user->hasRole('admin'))
        <flux:button variant="ghost" href="{{ route('admin.dashboard') }}" class="w-full justify-start">
            <flux:icon.cog-6-tooth class="h-4 w-4 mr-3" />
            Admin Dashboard
        </flux:button>
    @elseif($user->hasRole('teacher'))
        <flux:button variant="ghost" href="{{ route('teacher.dashboard') }}" class="w-full justify-start">
            <flux:icon.academic-cap class="h-4 w-4 mr-3" />
            Teacher Dashboard
        </flux:button>
    @elseif($user->hasRole('student'))
        <flux:button variant="ghost" href="{{ route('student.dashboard') }}" class="w-full justify-start">
            <flux:icon.academic-cap class="h-4 w-4 mr-3" />
            Student Dashboard
        </flux:button>
    @elseif($user->hasRole('parent'))
        <flux:button variant="ghost" href="{{ route('parent.dashboard') }}" class="w-full justify-start">
            <flux:icon.user-group class="h-4 w-4 mr-3" />
            Parent Dashboard
        </flux:button>
    @endif
    
    <flux:button variant="ghost" href="{{ route('settings.profile') }}" class="w-full justify-start">
        <flux:icon.cog-6-tooth class="h-4 w-4 mr-3" />
        Account Settings
    </flux:button>
</div>
