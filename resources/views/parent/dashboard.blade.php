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

        <!-- 1. STATISTIK UTAMA (Key Metrics) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- My Children -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                        <flux:icon.academic-cap class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">My Children</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->children()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Profile Status -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                        <flux:icon.user class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Profile Status</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            @if(auth()->user()->status === 'active')
                                <span class="text-green-600">Active</span>
                            @elseif(auth()->user()->status === 'pending')
                                <span class="text-yellow-600">Pending</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                        <flux:icon.bell class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unpaid Invoices</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingInvoices->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900">
                        <flux:icon.chat-bubble-left-right class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Messages</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- INVOICE NOTIFICATION -->
        @if($pendingInvoices->count() > 0)
            <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="md" class="mb-2">Unpaid Invoices</flux:heading>
                        <flux:text class="mb-2">
                            You have <strong>{{ $pendingInvoices->count() }}</strong> pending invoice(s) that need payment:
                        </flux:text>
                        <div class="space-y-2">
                            @foreach($pendingInvoices->take(3) as $invoice)
                                <div class=" rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-medium text-sm">
                                            <strong>{{ $invoice->student->user->name }}</strong> - {{ $invoice->subject->name }}
                                        </span>
                                        <flux:badge size="sm" color="red">Pending</flux:badge>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-500">
                                            Due: {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'No due date' }}
                                        </span>
                                        <span class="font-bold text-red-600">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                            @if($pendingInvoices->count() > 3)
                                <div class="text-sm text-gray-600">
                                    +{{ $pendingInvoices->count() - 3 }} more invoice(s)
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4">
                        <flux:button variant="primary" href="{{ route('parent.invoice') }}">
                            <flux:icon.document-text class="h-4 w-4 mr-2" />
                            View All Invoices
                        </flux:button>
                    </div>
                </div>
            </flux:callout>
        @endif


        <!-- 2. MY CHILDREN TABLE -->
        <flux:card>
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <flux:heading size="lg">My Children ({{ $children->count() }})</flux:heading>
                    <flux:button variant="primary" href="{{ route('parent.children.create') }}">
                        <flux:icon.plus class="h-4 w-4 mr-2" />
                        Add Child
                    </flux:button>
                </div>
            </div>
            <div class="overflow-x-auto">
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

            <!-- 5. AKTIVITAS TERBARU (Recent Activities) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <flux:heading size="lg" class="text-gray-900 dark:text-white">🕒 Recent Activities</flux:heading>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <flux:icon.information-circle class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">Welcome to the parent portal!</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">You can now access your children's information and school updates.</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ now()->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if(auth()->user()->status === 'pending')
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                    <flux:icon.clock class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">Account verification pending</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Your account is waiting for admin approval.</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ now()->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. INFORMASI ORGANISASI (Organization Info) -->
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
</x-layouts.app>
