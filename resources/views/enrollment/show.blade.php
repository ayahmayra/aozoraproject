<x-layouts.app :title="__('Student Enrollments')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('enrollment.index') }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Enrollments
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">{{ $student->user->name }}'s Enrollments</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Manage student enrollments and status</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
    @endif

    @if (session()->has('error'))
        <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Enrollments List -->
        <div class="lg:col-span-2">
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Enrolled Subjects</flux:heading>
                        <flux:button variant="primary" href="{{ route('enrollment.create', $student) }}">
                            <flux:icon.plus class="h-4 w-4 mr-2" />
                            Enroll in More Subjects
                        </flux:button>
                    </div>
                </div>
                <div class="p-6">
                    @if($student->subjects->count() > 0)
                        <div class="space-y-4">
                            @foreach($student->subjects as $subject)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <div class="font-medium">{{ $subject->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $subject->code }}</div>
                                            @if($subject->description)
                                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ Str::limit($subject->description, 100) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <flux:badge 
                                                size="sm" 
                                                variant="{{ $subject->pivot->status === 'enrolled' ? 'blue' : ($subject->pivot->status === 'completed' ? 'green' : 'red') }}"
                                            >
                                                {{ ucfirst($subject->pivot->status) }}
                                            </flux:badge>
                                            <div class="flex space-x-1">
                                                <flux:button 
                                                    variant="ghost" 
                                                    size="sm" 
                                                    href="{{ route('enrollment.edit', [$student, $subject]) }}"
                                                    title="Edit Enrollment"
                                                >
                                                    <flux:icon.pencil class="h-4 w-4" />
                                                </flux:button>
                                                <form method="POST" action="{{ route('enrollment.destroy', [$student, $subject]) }}" class="inline" onsubmit="return confirm('Are you sure you want to remove this enrollment?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <flux:button variant="ghost" size="sm" type="submit" title="Remove Enrollment">
                                                        <flux:icon.trash class="h-4 w-4" />
                                                    </flux:button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Enrollment Details -->
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mt-4">
                                        <flux:heading size="sm" class="mb-3">Enrollment Details</flux:heading>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                            <!-- Basic Info -->
                                            <div>
                                                <flux:text class="text-xs font-medium text-gray-500">Enrollment Number</flux:text>
                                                <flux:text class="text-sm font-mono">{{ $subject->pivot->enrollment_number ?? 'Not assigned' }}</flux:text>
                                            </div>
                                            <div>
                                                <flux:text class="text-xs font-medium text-gray-500">Enrollment Status</flux:text>
                                                <flux:badge size="sm" color="{{ $subject->pivot->enrollment_status === 'active' ? 'green' : ($subject->pivot->enrollment_status === 'pending' ? 'amber' : 'red') }}">
                                                    {{ ucfirst($subject->pivot->enrollment_status ?? 'pending') }}
                                                </flux:badge>
                                            </div>
                                            
                                            <!-- Dates -->
                                            <div>
                                                <flux:text class="text-xs font-medium text-gray-500">Enrollment Date</flux:text>
                                                <flux:text class="text-sm">
                                                    @if($subject->pivot->enrollment_date)
                                                        @if(is_string($subject->pivot->enrollment_date))
                                                            {{ \Carbon\Carbon::parse($subject->pivot->enrollment_date)->format('M d, Y') }}
                                                        @else
                                                            {{ $subject->pivot->enrollment_date->format('M d, Y') }}
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </flux:text>
                                            </div>
                                            <div>
                                                <flux:text class="text-xs font-medium text-gray-500">Start Date</flux:text>
                                                <flux:text class="text-sm">
                                                    @if($subject->pivot->start_date)
                                                        @if(is_string($subject->pivot->start_date))
                                                            {{ \Carbon\Carbon::parse($subject->pivot->start_date)->format('M d, Y') }}
                                                        @else
                                                            {{ $subject->pivot->start_date->format('M d, Y') }}
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </flux:text>
                                            </div>
                                            <div>
                                                <flux:text class="text-xs font-medium text-gray-500">End Date</flux:text>
                                                <flux:text class="text-sm">
                                                    @if($subject->pivot->end_date)
                                                        @if(is_string($subject->pivot->end_date))
                                                            {{ \Carbon\Carbon::parse($subject->pivot->end_date)->format('M d, Y') }}
                                                        @else
                                                            {{ $subject->pivot->end_date->format('M d, Y') }}
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </flux:text>
                                            </div>
                                            
                                            <!-- Payment Info -->
                                            <div>
                                                <flux:text class="text-xs font-medium text-gray-500">Payment Method</flux:text>
                                                <flux:text class="text-sm">{{ ucfirst($subject->pivot->payment_method ?? 'Not set') }}</flux:text>
                                            </div>
                                            <div>
                                                <flux:text class="text-xs font-medium text-gray-500">Payment Amount</flux:text>
                                                <flux:text class="text-sm">
                                                    @if($subject->pivot->payment_amount)
                                                        Rp.{{ number_format($subject->pivot->payment_amount, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </flux:text>
                                            </div>
                                            <div>
                                                <flux:text class="text-xs font-medium text-gray-500">Parent ID</flux:text>
                                                <flux:text class="text-sm">{{ $subject->pivot->parent_id ?? 'N/A' }}</flux:text>
                                            </div>
                                            
                                            <!-- Legacy Fields -->
                                            @if($subject->pivot->enrolled_at)
                                                <div>
                                                    <flux:text class="text-xs font-medium text-gray-500">Legacy Enrolled Date</flux:text>
                                                    <flux:text class="text-sm">
                                                        @if(is_string($subject->pivot->enrolled_at))
                                                            {{ \Carbon\Carbon::parse($subject->pivot->enrolled_at)->format('M d, Y') }}
                                                        @else
                                                            {{ $subject->pivot->enrolled_at->format('M d, Y') }}
                                                        @endif
                                                    </flux:text>
                                                </div>
                                            @endif
                                            @if($subject->pivot->completed_at)
                                                <div>
                                                    <flux:text class="text-xs font-medium text-gray-500">Legacy Completed Date</flux:text>
                                                    <flux:text class="text-sm">
                                                        @if(is_string($subject->pivot->completed_at))
                                                            {{ \Carbon\Carbon::parse($subject->pivot->completed_at)->format('M d, Y') }}
                                                        @else
                                                            {{ $subject->pivot->completed_at->format('M d, Y') }}
                                                        @endif
                                                    </flux:text>
                                                </div>
                                            @endif
                                            @if($subject->pivot->status)
                                                <div>
                                                    <flux:text class="text-xs font-medium text-gray-500">Legacy Status</flux:text>
                                                    <flux:badge size="sm" color="{{ $subject->pivot->status === 'enrolled' ? 'blue' : ($subject->pivot->status === 'completed' ? 'green' : 'red') }}">
                                                        {{ ucfirst($subject->pivot->status) }}
                                                    </flux:badge>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Notes -->
                                        @if($subject->pivot->notes)
                                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                                <flux:text class="text-xs font-medium text-gray-500">Notes</flux:text>
                                                <flux:text class="text-sm mt-1">{{ $subject->pivot->notes }}</flux:text>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                            <p class="text-lg font-medium text-gray-500">No enrollments yet</p>
                            <p class="text-sm text-gray-400">This student hasn't been enrolled in any subjects</p>
                            <flux:button variant="primary" href="{{ route('enrollment.create', $student) }}" class="mt-4">
                                <flux:icon.plus class="h-4 w-4 mr-2" />
                                Enroll in Subjects
                            </flux:button>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>

        <!-- Student Info Sidebar -->
        <div>
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Student Information</flux:heading>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        <flux:avatar name="{{ $student->user->name }}" size="xl" class="mx-auto mb-3" />
                        <flux:heading size="lg">{{ $student->user->name }}</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400">{{ $student->user->email }}</flux:text>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <flux:text class="text-sm font-medium">Student ID</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $student->student_id ?? 'Not assigned' }}
                            </flux:text>
                        </div>
                        
                        <div>
                            <flux:text class="text-sm font-medium">Parent</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $student->parent->name ?? 'Not assigned' }}
                            </flux:text>
                        </div>
                        
                        <div>
                            <flux:text class="text-sm font-medium">Total Enrollments</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $student->subjects->count() }} subjects
                            </flux:text>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Enrollment Statistics -->
            <flux:card class="mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Enrollment Statistics</flux:heading>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <flux:text class="text-sm">Enrolled</flux:text>
                            <flux:badge size="sm" variant="blue">{{ $student->enrolledSubjects()->count() }}</flux:badge>
                        </div>
                        <div class="flex justify-between">
                            <flux:text class="text-sm">Completed</flux:text>
                            <flux:badge size="sm" variant="green">{{ $student->completedSubjects()->count() }}</flux:badge>
                        </div>
                        <div class="flex justify-between">
                            <flux:text class="text-sm">Dropped</flux:text>
                            <flux:badge size="sm" variant="red">{{ $student->subjects()->wherePivot('status', 'dropped')->count() }}</flux:badge>
                        </div>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
