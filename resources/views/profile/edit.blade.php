<x-layouts.app :title="__('Edit Profile')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('profile.show') }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Profile
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">Edit Profile</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Update your profile information</flux:text>
    </div>

    @if ($errors->any())
        <flux:callout class="mb-6" variant="danger" icon="x-mark" heading="Please correct the following errors:">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <flux:card>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div>
                            <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <flux:input
                                    name="name"
                                    label="Full Name"
                                    placeholder="Enter your full name"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                />
                                
                                <flux:input
                                    name="email"
                                    label="Email Address"
                                    type="email"
                                    placeholder="Enter your email address"
                                    value="{{ old('email', $user->email) }}"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div>
                            <flux:heading size="lg" class="mb-4">Change Password</flux:heading>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-4">Leave blank if you don't want to change your password</flux:text>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <flux:input
                                    name="password"
                                    label="New Password"
                                    type="password"
                                    placeholder="Enter new password"
                                />
                                
                                <flux:input
                                    name="password_confirmation"
                                    label="Confirm New Password"
                                    type="password"
                                    placeholder="Confirm new password"
                                />
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div>
                            <flux:heading size="lg" class="mb-4">Contact Information</flux:heading>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <flux:input
                                    name="phone"
                                    label="Phone Number"
                                    placeholder="Enter your phone number"
                                    value="{{ old('phone', $user->teacherProfile->phone ?? $user->studentProfile->phone ?? $user->parentProfile->phone ?? '') }}"
                                />
                                
                                <flux:field>
                                    <flux:label>Address</flux:label>
                                    <flux:textarea
                                        name="address"
                                        placeholder="Enter your address"
                                        rows="3"
                                    >{{ old('address', $user->teacherProfile->address ?? $user->studentProfile->address ?? $user->parentProfile->address ?? '') }}</flux:textarea>
                                </flux:field>
                            </div>
                        </div>

                        <!-- Role-Specific Information -->
                        @if($user->hasRole('teacher') && $user->teacherProfile)
                            <div>
                                <flux:heading size="lg" class="mb-4">Teacher Information</flux:heading>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <flux:input
                                        label="Employee Number"
                                        value="{{ $user->teacherProfile->employee_number }}"
                                        readonly
                                    />
                                    
                                    <flux:input
                                        label="Education Level"
                                        value="{{ $user->teacherProfile->education_level }}"
                                        readonly
                                    />
                                    
                                    <flux:input
                                        label="Institution"
                                        value="{{ $user->teacherProfile->institution }}"
                                        readonly
                                    />
                                    
                                    <flux:input
                                        label="Employment Status"
                                        value="{{ ucfirst($user->teacherProfile->employment_status) }}"
                                        readonly
                                    />
                                </div>
                                <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-2">Teacher-specific information can only be updated by administrators.</flux:text>
                            </div>
                        @elseif($user->hasRole('student') && $user->studentProfile)
                            <div>
                                <flux:heading size="lg" class="mb-4">Student Information</flux:heading>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <flux:input
                                        label="Student ID"
                                        value="{{ $user->studentProfile->student_id }}"
                                        readonly
                                    />
                                    
                                    <flux:input
                                        label="Date of Birth"
                                        value="{{ $user->studentProfile->date_of_birth ? $user->studentProfile->date_of_birth->format('M d, Y') : 'Not specified' }}"
                                        readonly
                                    />
                                    
                                    <flux:input
                                        label="Gender"
                                        value="{{ ucfirst($user->studentProfile->gender) }}"
                                        readonly
                                    />
                                </div>
                                <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-2">Student-specific information can only be updated by administrators or parents.</flux:text>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3">
                            <flux:button variant="ghost" href="{{ route('profile.show') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                <flux:icon.check class="h-4 w-4 mr-2" />
                                Update Profile
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>

        <!-- Sidebar -->
        <div>
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Profile Tips</flux:heading>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <flux:icon.information-circle class="h-5 w-5 text-blue-500 mt-0.5" />
                            <div>
                                <flux:text class="text-sm font-medium">Keep Information Updated</flux:text>
                                <flux:text class="text-sm text-gray-600 dark:text-gray-400">Make sure your contact information is current so we can reach you when needed.</flux:text>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <flux:icon.shield-check class="h-5 w-5 text-green-500 mt-0.5" />
                            <div>
                                <flux:text class="text-sm font-medium">Secure Password</flux:text>
                                <flux:text class="text-sm text-gray-600 dark:text-gray-400">Use a strong password with at least 8 characters for better security.</flux:text>
                            </div>
                        </div>
                        
                        @if($user->hasRole('teacher'))
                            <div class="flex items-start space-x-3">
                                <flux:icon.academic-cap class="h-5 w-5 text-purple-500 mt-0.5" />
                                <div>
                                    <flux:text class="text-sm font-medium">Teacher Information</flux:text>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Professional information can only be updated by administrators.</flux:text>
                                </div>
                            </div>
                        @elseif($user->hasRole('student'))
                            <div class="flex items-start space-x-3">
                                <flux:icon.user-group class="h-5 w-5 text-orange-500 mt-0.5" />
                                <div>
                                    <flux:text class="text-sm font-medium">Student Information</flux:text>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Academic information can be updated by your parents or administrators.</flux:text>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
