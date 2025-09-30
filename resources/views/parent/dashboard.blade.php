<x-layouts.app :title="__('Parent Dashboard')">
    <div class="space-y-6">
        <!-- Welcome Header -->
        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl" level="1">Welcome {{ auth()->user()->name }}!</flux:heading>
                    <flux:text class="mb-6">Parent Dashboard</flux:text>
                    <flux:text class="text-xs">{{ now()->format('l, d F Y') }}</flux:text>
                </div>
                <div class="text-right">
                    <div class="text-blue-200 text-sm">{{ $organization->short_name ?? 'Aozora Education' }}</div>
                </div>
            </div>
        </flux:card>

        <!-- Error Messages -->
        @if (session()->has('error'))
            <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
        @endif

        @if (session()->has('success'))
            <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
        @endif

        <!-- 1. STATISTIK UTAMA (Key Metrics) - Mobile Friendly Circle Design -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- My Children -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white dark:bg-blue-700 shadow-md flex items-center justify-center mb-3">
                        <flux:icon.academic-cap class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600 dark:text-blue-300" />
                    </div>
                    <p class="text-3xl sm:text-4xl font-bold text-blue-700 dark:text-blue-200 mb-1">{{ auth()->user()->children()->count() }}</p>
                    <p class="text-xs sm:text-sm font-medium text-blue-600 dark:text-blue-300">My Children</p>
                </div>
            </div>

            <!-- Today's Classes -->
            <a href="#schedule-section" class="block bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all hover:scale-105 cursor-pointer">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white dark:bg-green-700 shadow-md flex items-center justify-center mb-3 relative">
                        <flux:icon.calendar class="w-8 h-8 sm:w-10 sm:h-10 text-green-600 dark:text-green-300" />
                        @if($todaySchedules->count() > 0)
                            <span class="absolute -top-1 -right-1 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">{{ $todaySchedules->count() }}</span>
                        @endif
                    </div>
                    <p class="text-3xl sm:text-4xl font-bold text-green-700 dark:text-green-200 mb-1">{{ $todaySchedules->count() }}</p>
                    <p class="text-xs sm:text-sm font-medium text-green-600 dark:text-green-300">Today's Classes</p>
                </div>
            </a>

            <!-- Unpaid Invoices -->
            <a href="#invoices-section" class="block bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all hover:scale-105 cursor-pointer">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white dark:bg-purple-700 shadow-md flex items-center justify-center mb-3 relative">
                        <flux:icon.bell class="w-8 h-8 sm:w-10 sm:h-10 text-purple-600 dark:text-purple-300" />
                        @if($pendingInvoices->count() > 0)
                            <span class="absolute top-0 right-0 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold animate-pulse">!</span>
                        @endif
                    </div>
                    <p class="text-3xl sm:text-4xl font-bold text-purple-700 dark:text-purple-200 mb-1">{{ $pendingInvoices->count() }}</p>
                    <p class="text-xs sm:text-sm font-medium text-purple-600 dark:text-purple-300">Unpaid Invoices</p>
                </div>
            </a>

            <!-- Messages -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800 rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white dark:bg-orange-700 shadow-md flex items-center justify-center mb-3">
                        <flux:icon.chat-bubble-left-right class="w-8 h-8 sm:w-10 sm:h-10 text-orange-600 dark:text-orange-300" />
                    </div>
                    <p class="text-3xl sm:text-4xl font-bold text-orange-700 dark:text-orange-200 mb-1">0</p>
                    <p class="text-xs sm:text-sm font-medium text-orange-600 dark:text-orange-300">Messages</p>
                </div>
            </div>
        </div>

       


        <!-- 2. MY CHILDREN TABLE -->
        <flux:card>
            <div class="px-6 py-4 ">
                <div class="flex justify-between items-center">
                    <flux:heading size="lg">My Children ({{ $children->count() }})</flux:heading>
                    <flux:button variant="primary" href="{{ route('parent.children.create') }}">
                        <flux:icon.plus class="h-4 w-4 mr-2" />
                        Add Child
                    </flux:button>
                </div>
            </div>
            <!-- Mobile View: Card Layout -->
            <div class="block lg:hidden space-y-4">
                @forelse($children as $child)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200 dark:border-gray-700">
                        <!-- Card Header with Avatar -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <flux:avatar size="lg" name="{{ $child->user->name }}" class="ring-4 ring-white dark:ring-gray-800" />
                                    </div>
                                    <div class="text-white">
                                        <h3 class="font-bold text-lg">{{ $child->user->name }}</h3>
                                        <p class="text-sm text-blue-100">{{ $child->user->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 space-y-4">
                            <!-- Student Info -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Student ID</p>
                                    @if($child->student_id)
                                        <flux:badge variant="primary">{{ $child->student_id }}</flux:badge>
                                    @else
                                        <span class="text-xs text-gray-400">Not assigned</span>
                                    @endif
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Date of Birth</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $child->date_of_birth->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Subjects Section -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <flux:icon.academic-cap class="h-4 w-4 inline mr-1" />
                                        Enrolled Subjects
                                    </h4>
                                    <flux:button variant="primary" size="xs" href="{{ route('parent.enrollment.create', $child) }}">
                                        <flux:icon.plus class="h-3 w-3 mr-1" />
                                        Add
                                    </flux:button>
                                </div>
                                
                                @if($child->subjects->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($child->subjects->take(3) as $subject)
                                            @php
                                                $enrollmentStatus = $subject->pivot->enrollment_status ?? 'pending';
                                                $badgeColor = $enrollmentStatus === 'active' ? 'green' : ($enrollmentStatus === 'pending' ? 'amber' : 'red');
                                                $enrollmentNumber = $subject->pivot->enrollment_number ?? 'N/A';
                                            @endphp
                                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                    <flux:badge size="sm" color="{{ $badgeColor }}">
                                                        {{ $subject->name }}
                                                    </flux:badge>
                                                    @if($enrollmentStatus !== 'active')
                                                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                            ({{ $enrollmentNumber }})
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($enrollmentStatus !== 'active')
                                                    <form method="POST" action="{{ route('parent.enrollment.destroy', [$child, $subject]) }}" class="inline" onsubmit="return confirm('Cancel this enrollment?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="ml-2 p-1 hover:bg-red-500 hover:text-white rounded-full transition-colors" title="Cancel">
                                                            <flux:icon.x-mark class="h-4 w-4" />
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($child->subjects->count() > 3)
                                            <div class="text-center">
                                                <flux:badge size="sm" color="zinc">+{{ $child->subjects->count() - 3 }} more subjects</flux:badge>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center py-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <flux:icon.book-open class="h-8 w-8 mx-auto text-gray-400 mb-2" />
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No subjects enrolled yet</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                <flux:button variant="ghost" size="sm" href="{{ route('profile.show') }}?user={{ $child->user_id }}" class="flex-1">
                                    <flux:icon.user class="h-4 w-4 mr-2" />
                                    Profile
                                </flux:button>
                                <flux:button variant="ghost" size="sm" href="{{ route('parent.students.edit', $child) }}" class="flex-1">
                                    <flux:icon.pencil class="h-4 w-4 mr-2" />
                                    Edit
                                </flux:button>
                                <form method="POST" action="{{ route('parent.children.destroy', $child) }}" class="inline flex-1" onsubmit="return confirm('Delete this child?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button variant="ghost" size="sm" type="submit" class="w-full">
                                        <flux:icon.trash class="h-4 w-4 mr-2" />
                                        Delete
                                    </flux:button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-md">
                        <flux:icon.academic-cap class="h-16 w-16 mx-auto mb-4 text-gray-300" />
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">No children registered yet</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add your first child to get started</p>
                        <flux:button variant="primary" href="{{ route('parent.children.create') }}">
                            <flux:icon.plus class="h-4 w-4 mr-2" />
                            Add Your First Child
                        </flux:button>
                    </div>
                @endforelse
            </div>

            <!-- Desktop View: Table Layout -->
            <div class="hidden lg:block overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Student</flux:table.column>
                        <flux:table.column>Student ID</flux:table.column>
                        <flux:table.column>Date of Birth</flux:table.column>
                        <flux:table.column>Subjects</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($children as $child)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <flux:avatar name="{{ $child->user->name }}" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium">{{ $child->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $child->user->email }}</div>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($child->student_id)
                                        <flux:badge variant="primary">{{ $child->student_id }}</flux:badge>
                                    @else
                                        <span class="text-gray-400">Not assigned</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>{{ $child->date_of_birth->format('M d, Y') }}</flux:table.cell>
                                <flux:table.cell>
                                    @if($child->subjects->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($child->subjects->take(3) as $subject)
                                                @php
                                                    $enrollmentStatus = $subject->pivot->enrollment_status ?? 'pending';
                                                    $badgeColor = $enrollmentStatus === 'active' ? 'green' : ($enrollmentStatus === 'pending' ? 'amber' : 'red');
                                                    $enrollmentNumber = $subject->pivot->enrollment_number ?? 'N/A';
                                                    $statusText = $enrollmentStatus === 'active' ? $subject->name : $subject->name . ' (Waiting for verification)';
                                                @endphp
                                                <div class="flex items-center">
                                                    <flux:badge size="sm" color="{{ $badgeColor }}" class="flex items-center gap-1">
                                                        @if($enrollmentStatus === 'active')
                                                            {{ $subject->name }}
                                                        @else
                                                            <span class="font-mono text-xs">{{ $enrollmentNumber }}</span> - {{ $statusText }}
                                                            <form method="POST" action="{{ route('parent.enrollment.destroy', [$child, $subject]) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this enrollment?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="ml-1 hover:bg-red-500 hover:text-white rounded-full p-0.5 transition-colors" title="Cancel Enrollment">
                                                                    <flux:icon.x-mark class="h-3 w-3" />
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </flux:badge>
                                                </div>
                                            @endforeach
                                            @if($child->subjects->count() > 3)
                                                <div>
                                                    <flux:badge size="sm" color="zinc">+{{ $child->subjects->count() - 3 }} more</flux:badge>
                                                </div>
                                            @endif
                                        </div>
                                       
                                        <flux:button class="mt-2" variant="primary" size="xs" href="{{ route('parent.enrollment.create', $child) }}" title="Enroll in Subject">
                                                <flux:icon.plus class="h-3 w-3 mr-1" />
                                                Enroll
                                            </flux:button>
                                    @else
                                        <div class="flex items-center space-x-2">
                                            <span class="text-gray-400 text-sm">No subjects enrolled</span>
                                            <flux:button variant="primary" size="xs" href="{{ route('parent.enrollment.create', $child) }}" title="Enroll in Subject">
                                                <flux:icon.plus class="h-3 w-3 mr-1" />
                                                Enroll
                                            </flux:button>
                                        </div>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex space-x-2">
                                        <flux:button variant="ghost" size="sm" href="{{ route('profile.show') }}?user={{ $child->user_id }}" title="View Profile">
                                            <flux:icon.user class="h-4 w-4" />
                                        </flux:button>
                                        <flux:button variant="ghost" size="sm" href="{{ route('parent.students.edit', $child) }}" title="Edit Child">
                                            <flux:icon.pencil class="h-4 w-4" />
                                        </flux:button>
                                        <form method="POST" action="{{ route('parent.children.destroy', $child) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this child?')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button variant="ghost" size="sm" type="submit" title="Delete Child">
                                                <flux:icon.trash class="h-4 w-4" />
                                            </flux:button>
                                        </form>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="5" class="text-center py-8">
                                    <div class="text-gray-500">
                                        <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                        <p class="text-lg font-medium">No children registered yet</p>
                                        <p class="text-sm mb-4">Add your first child to get started</p>
                                        <flux:button variant="primary" href="{{ route('parent.children.create') }}">
                                            <flux:icon.plus class="h-4 w-4 mr-2" />
                                            Add Your First Child
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:card>

        <!-- 3. QUICK ACTIONS & RECENT ACTIVITIES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 5. TODAY'S SCHEDULE -->
            <div id="schedule-section" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 scroll-mt-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">
                            📅 Today's Schedule ({{ $today }})
                        </flux:heading>
                        <flux:badge color="blue" size="sm">{{ $todaySchedules->count() }} class(es)</flux:badge>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ now()->format('l, F d, Y') }}</p>
                </div>
                <div class="p-6">
                    @if($todaySchedules->count() > 0)
                        <!-- Mobile View: Card Layout -->
                        <div class="block lg:hidden space-y-3">
                            @foreach($todaySchedules as $schedule)
                                @php
                                    $startTime = \Carbon\Carbon::parse($schedule->start_time);
                                    $endTime = \Carbon\Carbon::parse($schedule->end_time);
                                    $now = \Carbon\Carbon::now();
                                    $isOngoing = $now->between($startTime, $endTime);
                                    $isPast = $now->greaterThan($endTime);
                                    $isUpcoming = $now->lessThan($startTime);
                                @endphp
                                <div class="bg-gradient-to-r {{ $isOngoing ? 'from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 border-green-300 dark:border-green-600' : ($isPast ? 'from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 border-gray-300 dark:border-gray-600' : 'from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 border-blue-300 dark:border-blue-600') }} border-l-4 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <flux:badge color="{{ $isOngoing ? 'green' : ($isPast ? 'zinc' : 'blue') }}" size="sm">
                                                    {{ $isOngoing ? '🔴 Live' : ($isPast ? 'Finished' : 'Upcoming') }}
                                                </flux:badge>
                                                <span class="text-xs font-mono text-gray-600 dark:text-gray-300">
                                                    {{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }}
                                                </span>
                                            </div>
                                            <h4 class="font-bold text-base {{ $isOngoing ? 'text-green-700 dark:text-green-300' : 'text-gray-900 dark:text-white' }} mb-1">
                                                {{ $schedule->subject->name }}
                                            </h4>
                                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 space-x-2 mb-2">
                                                <flux:icon.user class="h-4 w-4" />
                                                <span>{{ $schedule->teacher->user->name }}</span>
                                            </div>
                                            @if($schedule->location)
                                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 space-x-1 mb-2">
                                                    <flux:icon.map-pin class="h-3 w-3" />
                                                    <span>{{ $schedule->location }}</span>
                                                </div>
                                            @endif
                                            @if(count($schedule->enrolledChildren) > 0)
                                                <div class="flex items-center text-xs text-gray-600 dark:text-gray-400 space-x-1">
                                                    <flux:icon.academic-cap class="h-3 w-3" />
                                                    <span class="font-medium">
                                                        @foreach($schedule->enrolledChildren as $index => $child)
                                                            {{ $child->user->name }}@if($index < count($schedule->enrolledChildren) - 1), @endif
                                                        @endforeach
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop View: Timeline Layout -->
                        <div class="hidden lg:block space-y-4">
                            @foreach($todaySchedules as $schedule)
                                @php
                                    $startTime = \Carbon\Carbon::parse($schedule->start_time);
                                    $endTime = \Carbon\Carbon::parse($schedule->end_time);
                                    $now = \Carbon\Carbon::now();
                                    $isOngoing = $now->between($startTime, $endTime);
                                    $isPast = $now->greaterThan($endTime);
                                    $isUpcoming = $now->lessThan($startTime);
                                @endphp
                                <div class="flex items-start space-x-4 {{ $isPast ? 'opacity-60' : '' }}">
                                    <div class="flex-shrink-0 w-24 pt-1">
                                        <div class="text-right">
                                            <p class="text-sm font-semibold {{ $isOngoing ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $startTime->format('H:i') }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $endTime->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 pt-1.5">
                                        <div class="w-3 h-3 rounded-full {{ $isOngoing ? 'bg-green-500 ring-4 ring-green-200 dark:ring-green-800' : ($isPast ? 'bg-gray-400' : 'bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-800') }}"></div>
                                    </div>
                                    <div class="flex-1 pb-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <h4 class="font-bold text-base text-gray-900 dark:text-white">
                                                        {{ $schedule->subject->name }}
                                                    </h4>
                                                    @if($isOngoing)
                                                        <flux:badge color="green" size="sm">
                                                            <span class="flex items-center">
                                                                <span class="animate-pulse mr-1">🔴</span> Live Now
                                                            </span>
                                                        </flux:badge>
                                                    @elseif($isPast)
                                                        <flux:badge color="zinc" size="sm">Finished</flux:badge>
                                                    @else
                                                        <flux:badge color="blue" size="sm">Upcoming</flux:badge>
                                                    @endif
                                                </div>
                                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 space-x-4 mb-2">
                                                    <div class="flex items-center space-x-1">
                                                        <flux:icon.user class="h-4 w-4" />
                                                        <span>{{ $schedule->teacher->user->name }}</span>
                                                    </div>
                                                    @if($schedule->location)
                                                        <div class="flex items-center space-x-1">
                                                            <flux:icon.map-pin class="h-4 w-4" />
                                                            <span>{{ $schedule->location }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if(count($schedule->enrolledChildren) > 0)
                                                    <div class="flex items-center text-xs text-gray-600 dark:text-gray-400 space-x-1">
                                                        <flux:icon.academic-cap class="h-4 w-4" />
                                                        <span class="font-medium">
                                                            Students: 
                                                            @foreach($schedule->enrolledChildren as $index => $child)
                                                                {{ $child->user->name }}@if($index < count($schedule->enrolledChildren) - 1), @endif
                                                            @endforeach
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <flux:icon.calendar class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" />
                            <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">No classes scheduled today</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Enjoy your day off! 🎉</p>
                        </div>
                    @endif
                </div>
            </div>

            
            
            <!-- 4. QUICK ACTIONS (Quick Actions) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <flux:heading size="lg" class="text-gray-900 dark:text-white">⚡ Quick Actions</flux:heading>
                </div>
                <div class="p-6">
                    @if(auth()->user()->status === 'pending')
                        <flux:callout class="mb-4" variant="warning" icon="exclamation-triangle" heading="Your account is pending verification. Please wait for admin approval." />
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('profile.show') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800 transition-colors">
                                        <flux:icon.user class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        My Profile
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">View and edit profile</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('parent.children.create') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800 transition-colors">
                                        <flux:icon.plus class="w-5 h-5 text-green-600 dark:text-green-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                        Add Child
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Register a new child</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('parent.schedule') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800 transition-colors">
                                        <flux:icon.calendar class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                        Schedule
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">View class schedules</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('parent.grades') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-800 transition-colors">
                                        <flux:icon.chart-bar class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                        Grades
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">View children grades</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

           

           
        <!-- 7. RECENT INVOICES -->
        <flux:card id="invoices-section" class="scroll-mt-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg" class="text-gray-900 dark:text-white">💳 Recent Invoices</flux:heading>
                    
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Invoices from the last 3 months</p>
            </div>

            <!-- Mobile View: Card Layout -->
            <div class="block lg:hidden p-4 space-y-3">
                @forelse($recentInvoices->take(5) as $invoice)
                    @php
                        $isOverdue = $invoice->payment_status === 'pending' && $invoice->due_date < now();
                        $statusColor = match($invoice->payment_status) {
                            'pending' => $isOverdue ? 'red' : 'amber',
                            'paid' => 'blue',
                            'verified' => 'green',
                            default => 'zinc'
                        };
                        $statusText = match($invoice->payment_status) {
                            'pending' => $isOverdue ? 'Overdue' : 'Pending',
                            'paid' => 'Paid',
                            'verified' => 'Verified',
                            default => $invoice->payment_status
                        };
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <flux:badge color="{{ $statusColor }}" size="sm">{{ $statusText }}</flux:badge>
                                    @if($isOverdue)
                                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">⚠️ Overdue</span>
                                    @endif
                                </div>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->subject->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Student</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->student->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Due Date</p>
                                <p class="font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Period: {{ $invoice->billing_period_start->format('M Y') }}
                            </div>
                            <flux:button variant="ghost" size="xs" href="{{ route('parent.invoice.show', $invoice) }}">
                                View Details
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <flux:icon.document-text class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" />
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">No invoices yet</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Invoices will appear here once generated</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop View: Table Layout -->
            <div class="hidden lg:block overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Invoice #</flux:table.column>
                        <flux:table.column>Student</flux:table.column>
                        <flux:table.column>Subject</flux:table.column>
                        <flux:table.column>Period</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Due Date</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($recentInvoices->take(5) as $invoice)
                            @php
                                $isOverdue = $invoice->payment_status === 'pending' && $invoice->due_date < now();
                                $statusColor = match($invoice->payment_status) {
                                    'pending' => $isOverdue ? 'red' : 'amber',
                                    'paid' => 'blue',
                                    'verified' => 'green',
                                    default => 'zinc'
                                };
                                $statusText = match($invoice->payment_status) {
                                    'pending' => $isOverdue ? 'Overdue' : 'Pending',
                                    'paid' => 'Paid',
                                    'verified' => 'Verified',
                                    default => $invoice->payment_status
                                };
                            @endphp
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="font-mono text-sm font-medium">{{ $invoice->invoice_number }}</div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center space-x-2">
                                        <flux:avatar size="sm" name="{{ $invoice->student->user->name }}" />
                                        <span class="text-sm">{{ $invoice->student->user->name }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-sm">{{ $invoice->subject->name }}</span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-sm">{{ $invoice->billing_period_start->format('M Y') }}</span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-sm font-semibold">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="{{ $isOverdue ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">
                                        {{ $invoice->due_date->format('M d, Y') }}
                                        @if($isOverdue)
                                            <div class="text-xs text-red-600 dark:text-red-400">⚠️ Overdue</div>
                                        @endif
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ $statusColor }}" size="sm">{{ $statusText }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button variant="ghost" size="sm" href="{{ route('parent.invoice.show', $invoice) }}">
                                        <flux:icon.eye class="h-4 w-4" />
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="8" class="text-center py-12">
                                    <div class="text-gray-500">
                                        <flux:icon.document-text class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                        <p class="text-lg font-medium">No invoices yet</p>
                                        <p class="text-sm">Invoices will appear here once generated</p>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:card>

        <!-- 8. INFORMASI ORGANISASI (Organization Info) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">🏫 School Information</flux:heading>
            </div>
            <div class="p-6">
                @if($organization)
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
                @else
                    <flux:text class="text-gray-500 dark:text-gray-400">No organization information available.</flux:text>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Smooth scroll to anchor links
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all anchor links with smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Add highlight effect based on target
                        const ringColor = targetId === '#schedule-section' ? 'ring-green-500' : 'ring-purple-500';
                        targetElement.classList.add('ring-4', ringColor, 'ring-opacity-50');
                        setTimeout(() => {
                            targetElement.classList.remove('ring-4', ringColor, 'ring-opacity-50');
                        }, 2000);
                    }
                });
            });
        });
    </script>
    @endpush
</x-layouts.app>

