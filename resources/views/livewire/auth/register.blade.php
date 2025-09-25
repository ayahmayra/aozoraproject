<div class="flex flex-col gap-6">
    <div class="text-center">
        <flux:heading size="xl">{{ __('Create Parent Account') }}</flux:heading>
        <flux:subheading>{{ __('Enter your details below to create your parent account') }}</flux:subheading>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Basic Information -->
        <div class="space-y-4">
            <flux:heading size="sm" class="text-gray-700 dark:text-gray-300">Basic Information</flux:heading>
            
            <!-- Name -->
            <flux:input
                wire:model="name"
                :label="__('Full Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Enter your full name')"
            />

            <!-- Email Address -->
            <flux:input
                wire:model="email"
                :label="__('Email Address')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Enter your password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm your password')"
                viewable
            />
        </div>

        <!-- Personal Information -->
        <div class="space-y-4">
            <flux:heading size="sm" class="text-gray-700 dark:text-gray-300">Personal Information</flux:heading>
            
            <!-- Phone -->
            <flux:input
                wire:model="phone"
                :label="__('Phone Number')"
                type="tel"
                autocomplete="tel"
                placeholder="+6281234567890"
            />

            <!-- Address -->
            <flux:textarea
                wire:model="address"
                :label="__('Address')"
                :placeholder="__('Enter your full address')"
                rows="3"
            />

            <!-- Date of Birth -->
            <flux:input
                wire:model="date_of_birth"
                :label="__('Date of Birth')"
                type="date"
            />

            <!-- Gender -->
            <flux:field>
                <flux:label>{{ __('Gender') }}</flux:label>
                <flux:select wire:model="gender">
                    <option value="">{{ __('Select Gender') }}</option>
                    <option value="male">{{ __('Male') }}</option>
                    <option value="female">{{ __('Female') }}</option>
                </flux:select>
            </flux:field>
        </div>

        <!-- Professional Information -->
        <div class="space-y-4">
            <flux:heading size="sm" class="text-gray-700 dark:text-gray-300">Professional Information</flux:heading>
            
            <!-- Occupation -->
            <flux:input
                wire:model="occupation"
                :label="__('Occupation')"
                type="text"
                placeholder="Engineer, Teacher, etc."
            />

            <!-- Workplace -->
            <flux:input
                wire:model="workplace"
                :label="__('Workplace')"
                type="text"
                placeholder="Company or Organization Name"
            />
        </div>

        <!-- Emergency Contact -->
        <div class="space-y-4">
            <flux:heading size="sm" class="text-gray-700 dark:text-gray-300">Emergency Contact</flux:heading>
            
            <!-- Emergency Contact Name -->
            <flux:input
                wire:model="emergency_contact_name"
                :label="__('Emergency Contact Name')"
                type="text"
                placeholder="Contact person name"
            />

            <!-- Emergency Contact Phone -->
            <flux:input
                wire:model="emergency_contact_phone"
                :label="__('Emergency Contact Phone')"
                type="tel"
                placeholder="+6281234567890"
            />
        </div>

        <!-- Additional Information -->
        <div class="space-y-4">
            <flux:heading size="sm" class="text-gray-700 dark:text-gray-300">Additional Information</flux:heading>
            
            <!-- Notes -->
            <flux:textarea
                wire:model="notes"
                :label="__('Notes')"
                :placeholder="__('Any additional information or notes')"
                rows="3"
            />
        </div>

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                {{ __('Create Parent Account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
