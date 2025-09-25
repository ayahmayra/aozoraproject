<x-layouts.app :title="__('My Profile')">
    <div class="mb-8">
        <flux:heading size="xl" level="1">{{ $user->name }}</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">View and manage profile information</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
    @endif

    @if (session()->has('error'))
        <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Profile Information -->
        <div class="lg:col-span-2">
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Profile Information</flux:heading>
                        @if(auth()->user()->hasRole('admin') && request()->has('user'))
                            @if($user->hasRole('admin'))
                                <flux:button variant="ghost" href="{{ route('admin.users.edit', $user) }}">
                                    <flux:icon.pencil class="h-4 w-4 mr-2" />
                                    Edit User
                                </flux:button>
                            @elseif($user->hasRole('teacher'))
                                <flux:button variant="ghost" href="{{ route('admin.teachers.edit', $user) }}">
                                    <flux:icon.pencil class="h-4 w-4 mr-2" />
                                    Edit Teacher
                                </flux:button>
                            @elseif($user->hasRole('student'))
                                <flux:button variant="ghost" href="{{ route('admin.students.edit', $user->studentProfile) }}">
                                    <flux:icon.pencil class="h-4 w-4 mr-2" />
                                    Edit Student
                                </flux:button>
                            @elseif($user->hasRole('parent'))
                                <flux:button variant="ghost" href="{{ route('admin.parents.edit', $user) }}">
                                    <flux:icon.pencil class="h-4 w-4 mr-2" />
                                    Edit Parent
                                </flux:button>
                            @endif
                        @elseif(auth()->user()->id === $user->id && !$user->hasRole('student'))
                            <flux:button variant="ghost" href="{{ route('profile.edit') }}">
                                <flux:icon.pencil class="h-4 w-4 mr-2" />
                                Edit Profile
                            </flux:button>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <flux:field>
                                <flux:label>Full Name</flux:label>
                                <flux:input value="{{ $user->name }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Email Address</flux:label>
                                <flux:input value="{{ $user->email }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>User Role</flux:label>
                                <flux:input value="{{ ucfirst($user->getRoleNames()->first() ?? 'No role assigned') }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Account Status</flux:label>
                                <flux:input value="{{ ucfirst($user->status) }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Member Since</flux:label>
                                <flux:input value="{{ $user->created_at->format('M d, Y') }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Last Updated</flux:label>
                                <flux:input value="{{ $user->updated_at->format('M d, Y') }}" readonly />
                            </flux:field>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Role-Specific Information -->
            @if($user->hasRole('teacher') && $user->teacherProfile)
                <flux:card class="mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Teacher Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:field>
                                    <flux:label>Employee Number</flux:label>
                                    <flux:input value="{{ $user->teacherProfile->employee_number ?? 'Not assigned' }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Education Level</flux:label>
                                    <flux:input value="{{ $user->teacherProfile->education_level ?? 'Not specified' }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Institution</flux:label>
                                    <flux:input value="{{ $user->teacherProfile->institution ?? 'Not specified' }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Employment Status</flux:label>
                                    <flux:input value="{{ ucfirst($user->teacherProfile->employment_status ?? 'Not specified') }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Phone Number</flux:field>
                                    <flux:input value="{{ $user->teacherProfile->phone ?? 'Not provided' }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Address</flux:field>
                                    <flux:textarea value="{{ $user->teacherProfile->address ?? 'Not provided' }}" readonly rows="3" />
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </flux:card>
            @elseif($user->hasRole('student') && $user->studentProfile)
                <flux:card class="mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Student Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:field>
                                    <flux:label>Student ID</flux:label>
                                    <flux:input value="{{ $user->studentProfile->student_id ?? 'Not assigned' }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Date of Birth</flux:label>
                                    <flux:input value="{{ $user->studentProfile->date_of_birth ? $user->studentProfile->date_of_birth->format('M d, Y') : 'Not specified' }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Gender</flux:label>
                                    <flux:input value="{{ ucfirst($user->studentProfile->gender ?? 'Not specified') }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Phone Number</flux:field>
                                    <flux:input value="{{ $user->studentProfile->phone ?? 'Not provided' }}" readonly />
                                </flux:field>
                            </div>
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Address</flux:field>
                                    <flux:textarea value="{{ $user->studentProfile->address ?? 'Not provided' }}" readonly rows="3" />
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </flux:card>
            @elseif($user->hasRole('parent') && $user->parentProfile)
                <flux:card class="mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Parent Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:field>
                                    <flux:label>Phone Number</flux:field>
                                    <flux:input value="{{ $user->parentProfile->phone ?? 'Not provided' }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Address</flux:field>
                                    <flux:input value="{{ $user->parentProfile->address ?? 'Not provided' }}" readonly />
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </flux:card>
            @endif
            
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Profile Avatar -->
            <flux:card>
                <div class="p-6 text-center">
                    <flux:avatar name="{{ $user->name }}" size="xl" class="mx-auto mb-4" />
                    <flux:heading size="lg">{{ $user->name }}</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400">{{ ucfirst($user->getRoleNames()->first() ?? 'No role') }}</flux:text>
                </div>
            </flux:card>

            <!-- Quick Actions -->
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Quick Actions</flux:heading>
                </div>
                <div class="p-6 space-y-3">
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
                    
                    
                </div>
            </flux:card>

            <!-- Subjects Information for Students -->
            @if($user->hasRole('student') && $user->studentProfile)
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Enrolled Subjects</flux:heading>
                    </div>
                    <div class="p-6">
                        @if($user->studentProfile->subjects->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->studentProfile->subjects as $subject)
                                    <flux:badge size="sm" variant="blue">{{ $subject->name }}</flux:badge>
                                @endforeach
                            </div>
                        @else
                            <div class="text-gray-500 text-sm">No subjects enrolled yet</div>
                        @endif
                    </div>
                </flux:card>
            @endif
        </div>
    </div>
</x-layouts.app>
