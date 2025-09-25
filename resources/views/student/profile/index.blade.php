<x-layouts.app :title="__('Student Profile')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Student Profile</flux:heading>
                        @if(auth()->user()->hasRole('parent'))
                            <flux:text class="mt-2">View your child's information</flux:text>
                        @else
                            <flux:text class="mt-2">View and manage your student information</flux:text>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        @if(auth()->user()->hasRole('parent'))
                            <flux:button variant="ghost" href="{{ route('parent.children') }}">
                                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                Back to Children
                            </flux:button>
                            
                        @elseif(auth()->user()->hasRole('admin'))
                            <flux:button variant="ghost" href="{{ route('admin.students') }}">
                                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                Back to Students
                            </flux:button>
                            
                        @else
                            <flux:button variant="ghost" href="{{ route('student.dashboard') }}">
                                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                Back to Dashboard
                            </flux:button>
                        @endif
                    </div>
                </div>
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
                            <flux:heading size="lg">Personal Information</flux:heading>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Full Name</flux:label>
                                    <flux:text class="mt-1">{{ $student->user->name }}</flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Email Address</flux:label>
                                    <flux:text class="mt-1">{{ $student->user->email }}</flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Student ID</flux:label>
                                    <flux:text class="mt-1">
                                        @if($student->student_id)
                                            <flux:badge variant="primary">{{ $student->student_id }}</flux:badge>
                                        @else
                                            <span class="text-gray-400">Not assigned</span>
                                        @endif
                                    </flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Status</flux:label>
                                    <flux:text class="mt-1">
                                        @if($student->user->status === 'active')
                                            <flux:badge variant="success">Active</flux:badge>
                                        @elseif($student->user->status === 'pending')
                                            <flux:badge variant="blue">Pending</flux:badge>
                                        @else
                                            <flux:badge variant="danger">Inactive</flux:badge>
                                        @endif
                                    </flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Date of Birth</flux:label>
                                    <flux:text class="mt-1">{{ $student->date_of_birth->format('M d, Y') }}</flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Gender</flux:label>
                                    <flux:text class="mt-1">{{ ucfirst($student->gender) }}</flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Phone Number</flux:label>
                                    <flux:text class="mt-1">{{ $student->phone ?? 'Not provided' }}</flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Address</flux:label>
                                    <flux:text class="mt-1">{{ $student->address ?? 'Not provided' }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:card>

                    <!-- Academic Information -->
                    <flux:card class="mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <flux:heading size="lg">Academic Information</flux:heading>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">School Origin</flux:label>
                                    <flux:text class="mt-1">{{ $student->school_origin ?? 'Not provided' }}</flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-500">Registration Date</flux:label>
                                    <flux:text class="mt-1">{{ $student->created_at->format('M d, Y') }}</flux:text>
                                </div>
                                @if($student->medical_notes)
                                <div class="lg:col-span-2">
                                    <flux:label class="text-sm font-medium text-gray-500">Medical Notes</flux:label>
                                    <flux:text class="mt-1">{{ $student->medical_notes }}</flux:text>
                                </div>
                                @endif
                                @if($student->notes)
                                <div class="lg:col-span-2">
                                    <flux:label class="text-sm font-medium text-gray-500">Additional Notes</flux:label>
                                    <flux:text class="mt-1">{{ $student->notes }}</flux:text>
                                </div>
                                @endif
                            </div>
                        </div>
                    </flux:card>
                </div>

                <!-- Parent Information -->
                <div class="lg:col-span-1">
                    <flux:card>
                        <div class="px-6 py-4 border-b border-gray-200">
                            <flux:heading size="lg">Parent Information</flux:heading>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <flux:avatar name="{{ $student->parent->name }}" />
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium">{{ $student->parent->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->parent->email }}</div>
                                </div>
                            </div>
                            
                            @if($student->parent->parentUser)
                                <div class="space-y-3">
                                    @if($student->parent->parentUser->phone)
                                    <div>
                                        <flux:label class="text-sm font-medium text-gray-500">Phone</flux:label>
                                        <flux:text class="mt-1">{{ $student->parent->parentUser->phone }}</flux:text>
                                    </div>
                                    @endif
                                    
                                    @if($student->parent->parentUser->address)
                                    <div>
                                        <flux:label class="text-sm font-medium text-gray-500">Address</flux:label>
                                        <flux:text class="mt-1">{{ $student->parent->parentUser->address }}</flux:text>
                                    </div>
                                    @endif
                                    
                                    @if($student->parent->parentUser->occupation)
                                    <div>
                                        <flux:label class="text-sm font-medium text-gray-500">Occupation</flux:label>
                                        <flux:text class="mt-1">{{ $student->parent->parentUser->occupation }}</flux:text>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <flux:text class="text-gray-500">No parent information available</flux:text>
                            @endif
                        </div>
                    </flux:card>

                    <!-- Quick Actions -->
                    <flux:card class="mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <flux:heading size="lg">Quick Actions</flux:heading>
                        </div>
                        <div class="p-6 space-y-3">
                            @if(auth()->user()->hasRole('parent'))
                                <flux:button variant="primary" href="{{ route('parent.children.edit', $student) }}" class="w-full">
                                    <flux:icon.pencil class="h-4 w-4 mr-2" />
                                    Edit Profile
                                </flux:button>
                                <flux:button variant="ghost" href="{{ route('parent.children') }}" class="w-full">
                                    <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                    Back to Children
                                </flux:button>
                            @elseif(auth()->user()->hasRole('admin'))
                                <flux:button variant="primary" href="{{ route('student.profile.edit.view') }}?student_id={{ $student->id }}" class="w-full">
                                    <flux:icon.pencil class="h-4 w-4 mr-2" />
                                    Edit Profile
                                </flux:button>
                                <flux:button variant="ghost" href="{{ route('admin.students') }}" class="w-full">
                                    <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                    Back to Students
                                </flux:button>
                            @else
                                <flux:button variant="ghost" href="{{ route('student.dashboard') }}" class="w-full">
                                    <flux:icon.home class="h-4 w-4 mr-2" />
                                    Back to Dashboard
                                </flux:button>
                                <div class="text-center text-sm text-gray-500 mt-2">
                                    <flux:icon.information-circle class="h-4 w-4 inline mr-1" />
                                    Profile can only be edited by your parent
                                </div>
                            @endif
                        </div>
                    </flux:card>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
