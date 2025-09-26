<x-layouts.app :title="__('Profile')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <flux:avatar size="xl" :src="$user->profile_photo_url" :alt="$user->name">
                                {{ substr($user->name, 0, 2) }}
                            </flux:avatar>
                            <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-green-500 border-2 border-white rounded-full flex items-center justify-center">
                                <flux:icon.check class="w-3 h-3 text-white" />
                            </div>
                        </div>
                        <div>
                            <flux:heading size="2xl" class="mb-2">{{ $user->name }}</flux:heading>
                            <div class="flex items-center space-x-4">
                                <flux:badge variant="blue" size="lg">
                                    <flux:icon.user class="w-4 h-4 mr-2" />
                                    {{ ucfirst($user->getRoleNames()->first() ?? 'User') }}
                                </flux:badge>
                                <flux:text class="text-gray-600">{{ $user->email }}</flux:text>
                            </div>
                            <div class="mt-2 text-sm text-gray-500">
                                Member since {{ $user->created_at->format('F Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        @if($user->hasRole('student'))
                            <!-- Students cannot edit their own profile -->
                        @elseif($request->has('user') && $request->user()->hasRole('admin'))
                            <!-- Admin viewing another user's profile -->
                            @if($user->hasRole('admin'))
                                <flux:button variant="primary" href="{{ route('admin.users.edit', $user) }}">
                                    <flux:icon.pencil class="w-4 h-4 mr-2" />
                                    Edit Admin Profile
                                </flux:button>
                            @elseif($user->hasRole('teacher'))
                                <flux:button variant="primary" href="{{ route('admin.teachers.edit', $user->teacherProfile) }}">
                                    <flux:icon.pencil class="w-4 h-4 mr-2" />
                                    Edit Teacher Profile
                                </flux:button>
                            @elseif($user->hasRole('student'))
                                <flux:button variant="primary" href="{{ route('admin.students.edit', $user->studentProfile) }}">
                                    <flux:icon.pencil class="w-4 h-4 mr-2" />
                                    Edit Student Profile
                                </flux:button>
                            @elseif($user->hasRole('parent'))
                                <flux:button variant="primary" href="{{ route('admin.parents.edit', $user->parentProfile) }}">
                                    <flux:icon.pencil class="w-4 h-4 mr-2" />
                                    Edit Parent Profile
                                </flux:button>
                            @endif
                        @else
                            <!-- User viewing their own profile -->
                            <flux:button variant="primary" href="{{ route('profile.edit') }}">
                                <flux:icon.pencil class="w-4 h-4 mr-2" />
                                Edit Profile
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <flux:card>
                        <div class="px-6 py-4 border-b border-gray-200">
                            <flux:heading size="lg" class="flex items-center">
                                <flux:icon.user class="w-5 h-5 mr-2 text-blue-600" />
                                Basic Information
                            </flux:heading>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <flux:text class="text-sm font-medium text-gray-500 mb-1">Full Name</flux:text>
                                    <flux:text class="text-lg">{{ $user->name }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-sm font-medium text-gray-500 mb-1">Email Address</flux:text>
                                    <flux:text class="text-lg">{{ $user->email }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-sm font-medium text-gray-500 mb-1">Role</flux:text>
                                    <flux:badge variant="blue" size="sm">
                                        {{ ucfirst($user->getRoleNames()->first() ?? 'User') }}
                                    </flux:badge>
                                </div>
                                <div>
                                    <flux:text class="text-sm font-medium text-gray-500 mb-1">Account Status</flux:text>
                                    <flux:badge variant="{{ $user->status === 'active' ? 'green' : 'red' }}" size="sm">
                                        {{ ucfirst($user->status ?? 'Active') }}
                                    </flux:badge>
                                </div>
                            </div>
                        </div>
                    </flux:card>

                    <!-- Role-Specific Information -->
                    @if($user->hasRole('student') && $user->studentProfile)
                        <flux:card>
                            <div class="px-6 py-4 border-b border-gray-200">
                                <flux:heading size="lg" class="flex items-center">
                                    <flux:icon.user class="w-5 h-5 mr-2 text-green-600" />
                                    Student Information
                                </flux:heading>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Student ID</flux:text>
                                        <flux:text class="text-lg">{{ $user->studentProfile->student_id ?? 'Not assigned' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Status</flux:text>
                                        <flux:badge variant="{{ $user->studentProfile->status === 'active' ? 'green' : 'red' }}" size="sm">
                                            {{ ucfirst($user->studentProfile->status ?? 'Active') }}
                                        </flux:badge>
                                    </div>
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Date of Birth</flux:text>
                                        <flux:text class="text-lg">{{ $user->studentProfile->date_of_birth ? $user->studentProfile->date_of_birth->format('F j, Y') : 'Not specified' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Gender</flux:text>
                                        <flux:text class="text-lg">{{ ucfirst($user->studentProfile->gender ?? 'Not specified') }}</flux:text>
                                    </div>
                                    @if($user->studentProfile->parent)
                                        <div>
                                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Parent</flux:text>
                                            <flux:text class="text-lg">{{ $user->studentProfile->parent->name }}</flux:text>
                                        </div>
                                    @endif
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Phone</flux:text>
                                        <flux:text class="text-lg">{{ $user->studentProfile->phone ?? 'Not provided' }}</flux:text>
                                    </div>
                                    <div class="md:col-span-2">
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Address</flux:text>
                                        <flux:text class="text-lg">{{ $user->studentProfile->address ?? 'Not provided' }}</flux:text>
                                    </div>
                                    <div class="md:col-span-2">
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Previous School</flux:text>
                                        <flux:text class="text-lg">{{ $user->studentProfile->school_origin ?? 'Not specified' }}</flux:text>
                                    </div>
                                    @if($user->studentProfile->medical_notes)
                                        <div class="md:col-span-2">
                                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Medical Notes</flux:text>
                                            <flux:text class="text-lg">{{ $user->studentProfile->medical_notes }}</flux:text>
                                        </div>
                                    @endif
                                    @if($user->studentProfile->notes)
                                        <div class="md:col-span-2">
                                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Additional Notes</flux:text>
                                            <flux:text class="text-lg">{{ $user->studentProfile->notes }}</flux:text>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </flux:card>
                    @endif

                    @if($user->hasRole('teacher') && $user->teacherProfile)
                        <flux:card>
                            <div class="px-6 py-4 border-b border-gray-200">
                                <flux:heading size="lg" class="flex items-center">
                                    <flux:icon.user class="w-5 h-5 mr-2 text-purple-600" />
                                    Teacher Information
                                </flux:heading>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Employee ID</flux:text>
                                        <flux:text class="text-lg">{{ $user->teacherProfile->employee_id ?? 'Not assigned' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Phone</flux:text>
                                        <flux:text class="text-lg">{{ $user->teacherProfile->phone ?? 'Not provided' }}</flux:text>
                                    </div>
                                    @if($user->teacherProfile->address)
                                        <div class="md:col-span-2">
                                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Address</flux:text>
                                            <flux:text class="text-lg">{{ $user->teacherProfile->address }}</flux:text>
                                        </div>
                                    @endif
                                    @if($user->teacherProfile->subjects && $user->teacherProfile->subjects && $user->teacherProfile->subjects->count() > 0)
                                        <div class="md:col-span-2">
                                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Teaching Subjects</flux:text>
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                @foreach($user->teacherProfile->subjects as $subject)
                                                    <flux:badge variant="blue" size="sm">{{ $subject->name }}</flux:badge>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </flux:card>
                    @endif

                    @if($user->hasRole('parent') && $user->parentProfile)
                        <flux:card>
                            <div class="px-6 py-4 border-b border-gray-200">
                                <flux:heading size="lg" class="flex items-center">
                                    <flux:icon.user class="w-5 h-5 mr-2 text-orange-600" />
                                    Parent Information
                                </flux:heading>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Phone</flux:text>
                                        <flux:text class="text-lg">{{ $user->parentProfile->phone ?? 'Not provided' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:text class="text-sm font-medium text-gray-500 mb-1">Children Count</flux:text>
                                        <flux:text class="text-lg">{{ $user->parentProfile->children ? $user->parentProfile->children->count() : 0 }} children</flux:text>
                                    </div>
                                    @if($user->parentProfile->address)
                                        <div class="md:col-span-2">
                                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Address</flux:text>
                                            <flux:text class="text-lg">{{ $user->parentProfile->address }}</flux:text>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </flux:card>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <flux:card>
                        <div class="px-6 py-4 border-b border-gray-200">
                            <flux:heading size="lg" class="flex items-center">
                                <flux:icon.cog class="w-5 h-5 mr-2 text-yellow-600" />
                                Quick Actions
                            </flux:heading>
                        </div>
                        <div class="p-6 space-y-3">
                            @if(!$user->hasRole('student'))
                                @if($request->has('user') && $request->user()->hasRole('admin'))
                                    <!-- Admin viewing another user -->
                                @else
                                    <!-- User viewing their own profile -->
                                    <flux:button variant="ghost" href="{{ route('profile.edit') }}" class="w-full justify-start">
                                        <flux:icon.pencil class="w-4 h-4 mr-3" />
                                        Edit Profile
                                    </flux:button>
                                @endif
                                
                                <flux:button variant="ghost" href="{{ route('profile.edit') }}" class="w-full justify-start">
                                    <flux:icon.cog class="w-4 h-4 mr-3" />
                                    Account Settings
                                </flux:button>
                            @endif

                            @if($user->hasRole('admin'))
                                <flux:button variant="ghost" href="{{ route('admin.dashboard') }}" class="w-full justify-start">
                                    <flux:icon.chart-bar class="w-4 h-4 mr-3" />
                                    Admin Dashboard
                                </flux:button>
                            @elseif($user->hasRole('teacher'))
                                <flux:button variant="ghost" href="{{ route('teacher.dashboard') }}" class="w-full justify-start">
                                    <flux:icon.chart-bar class="w-4 h-4 mr-3" />
                                    Teacher Dashboard
                                </flux:button>
                            @elseif($user->hasRole('parent'))
                                <flux:button variant="ghost" href="{{ route('parent.dashboard') }}" class="w-full justify-start">
                                    <flux:icon.chart-bar class="w-4 h-4 mr-3" />
                                    Parent Dashboard
                                </flux:button>
                            @endif
                        </div>
                    </flux:card>

                    <!-- Enrolled Subjects (for Students) -->
                    @if($user->hasRole('student') && $user->studentProfile && $user->studentProfile->subjects)
                        <flux:card>
                            <div class="px-6 py-4 border-b border-gray-200">
                                <flux:heading size="lg" class="flex items-center">
                                    <flux:icon.document-text class="w-5 h-5 mr-2 text-green-600" />
                                    Enrolled Subjects
                                </flux:heading>
                            </div>
                            <div class="p-6">
                                @if($user->studentProfile->subjects && $user->studentProfile->subjects->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($user->studentProfile->subjects as $subject)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                <div>
                                                    <flux:text class="font-medium">{{ $subject->name }}</flux:text>
                                                    <flux:text class="text-sm text-gray-500">{{ $subject->code }}</flux:text>
                                                </div>
                                                <flux:badge variant="green" size="sm">Enrolled</flux:badge>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-6">
                                        <flux:icon.document-text class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                        <flux:text class="text-gray-500">No subjects enrolled yet</flux:text>
                                    </div>
                                @endif
                            </div>
                        </flux:card>
                    @endif

                    <!-- Account Information -->
                    <flux:card>
                        <div class="px-6 py-4 border-b border-gray-200">
                            <flux:heading size="lg" class="flex items-center">
                                <flux:icon.user class="w-5 h-5 mr-2 text-gray-600" />
                                Account Information
                            </flux:heading>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Account Created</flux:text>
                                <flux:text class="text-sm font-medium">{{ $user->created_at->format('M j, Y') }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Last Updated</flux:text>
                                <flux:text class="text-sm font-medium">{{ $user->updated_at->format('M j, Y') }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Email Verified</flux:text>
                                <flux:badge variant="{{ $user->hasVerifiedEmail() ? 'green' : 'red' }}" size="sm">
                                    {{ $user->hasVerifiedEmail() ? 'Verified' : 'Pending' }}
                                </flux:badge>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>