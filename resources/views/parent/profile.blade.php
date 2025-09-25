<x-layouts.app :title="__('My Profile')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">My Profile</flux:heading>
                        <flux:text class="mt-2">View your complete profile information</flux:text>
                    </div>
                    <flux:button variant="ghost" href="{{ route('parent.dashboard') }}">
                        <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                        Back to Dashboard
                    </flux:button>
                </div>
            </div>

            <div class="space-y-6">
                <!-- User Basic Information -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Basic Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Full Name</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $user->name }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Email Address</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $user->email }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Account Status</flux:heading>
                                <flux:badge variant="{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($user->status) }}
                                </flux:badge>
                            </div>
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Role</flux:heading>
                                <flux:text class="text-lg font-medium">Parent</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Member Since</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $user->created_at->format('M d, Y') }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Last Updated</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $user->updated_at->format('M d, Y H:i') }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Parent Specific Information -->
                @if($parentProfile)
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Parent Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Phone Number</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $parentProfile->phone ?? 'Not provided' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Occupation</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $parentProfile->occupation ?? 'Not provided' }}</flux:text>
                            </div>
                            <div class="lg:col-span-2">
                                <flux:heading size="sm" class="text-gray-500 mb-2">Address</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $parentProfile->address ?? 'Not provided' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Emergency Contact</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $parentProfile->emergency_contact_phone ?? 'Not provided' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Emergency Contact Name</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $parentProfile->emergency_contact_name ?? 'Not provided' }}</flux:text>
                            </div>
                            @if($parentProfile->workplace)
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Workplace</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $parentProfile->workplace }}</flux:text>
                            </div>
                            @endif
                            @if($parentProfile->date_of_birth)
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Date of Birth</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $parentProfile->date_of_birth->format('M d, Y') }}</flux:text>
                            </div>
                            @endif
                            @if($parentProfile->gender)
                            <div>
                                <flux:heading size="sm" class="text-gray-500 mb-2">Gender</flux:heading>
                                <flux:text class="text-lg font-medium">{{ ucfirst($parentProfile->gender) }}</flux:text>
                            </div>
                            @endif
                            @if($parentProfile->notes)
                            <div class="lg:col-span-2">
                                <flux:heading size="sm" class="text-gray-500 mb-2">Notes</flux:heading>
                                <flux:text class="text-lg font-medium">{{ $parentProfile->notes }}</flux:text>
                            </div>
                            @endif
                        </div>
                    </div>
                </flux:card>
                @else
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Parent Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <flux:callout variant="warning" icon="exclamation-triangle" heading="No parent information available">
                            Your parent profile has not been set up yet. Please contact the school administration to complete your profile.
                        </flux:callout>
                    </div>
                </flux:card>
                @endif

                <!-- School Information -->
                @if($organization)
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">School Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <flux:heading size="md" class="text-gray-900 dark:text-white mb-2">{{ $organization->name }}</flux:heading>
                                <flux:text class="text-gray-600 dark:text-gray-300 mb-4">{{ $organization->description }}</flux:text>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon.map-pin class="w-4 h-4 text-gray-400" />
                                        <flux:text class="text-sm text-gray-600 dark:text-gray-300">{{ $organization->address }}</flux:text>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <flux:icon.phone class="w-4 h-4 text-gray-400" />
                                        <flux:text class="text-sm text-gray-600 dark:text-gray-300">{{ $organization->phone }}</flux:text>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <flux:icon.envelope class="w-4 h-4 text-gray-400" />
                                        <flux:text class="text-sm text-gray-600 dark:text-gray-300">{{ $organization->email }}</flux:text>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center">
                                <div class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <flux:icon.building-office-2 class="w-16 h-16 text-gray-400" />
                                </div>
                            </div>
                        </div>
                    </div>
                </flux:card>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
