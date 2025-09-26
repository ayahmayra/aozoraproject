<x-layouts.app :title="__('Admin Dashboard')">
    <div class="space-y-6">
        <!-- Welcome Header -->
        <flux:card >
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl" level="1" >Welcome {{ auth()->user()->name }}!</flux:heading>
                    <flux:text class="mb-6">School Management System Dashboard</flux:text>
                    <flux:text class="text-xs">{{ now()->format('l, d F Y') }}</flux:text>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-semibold text-white">{{ now()->format('H:i') }}</div>
                    <div class="text-blue-200 text-sm">{{ \App\Models\Organization::first()->short_name ?? 'Aozora Education' }}</div>
                </div>
            </div>
        </flux:card>

        <!-- Pending Enrollments Alert -->
        @php
            $pendingEnrollmentsCount = \App\Models\Student::whereHas('subjects', function($query) {
                $query->where('enrollment_status', 'pending');
            })->count();
        @endphp
        
        @if($pendingEnrollmentsCount > 0)
            <flux:callout variant="warning" icon="exclamation-circle" class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="md" class="mb-1">Pending Enrollments</flux:heading>
                        <flux:text class="text-sm">There are {{ $pendingEnrollmentsCount }} enrollment(s) waiting for your verification.</flux:text>
                    </div>
                    <flux:button variant="primary" href="{{ route('enrollment.index') }}" class="ml-4">
                        <flux:icon.eye class="h-4 w-4 mr-2" />
                        Review Enrollments
                    </flux:button>
                </div>
            </flux:callout>
        @endif

        <!-- Pending Parents Alert -->
        @php
            $pendingParentsCount = \App\Models\User::role('parent')->where('status', 'pending')->count();
        @endphp
        
        @if($pendingParentsCount > 0)
            <flux:callout variant="warning" icon="exclamation-circle" class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="md" class="mb-1">Pending Parent Verifications</flux:heading>
                        <flux:text class="text-sm">There are {{ $pendingParentsCount }} parent(s) waiting for account verification.</flux:text>
                    </div>
                    <flux:button variant="primary" href="{{ route('admin.parents') }}" class="ml-4">
                        <flux:icon.eye class="h-4 w-4 mr-2" />
                        Review Parents
                    </flux:button>
                </div>
            </flux:callout>
        @endif

        <!-- 1. STATISTIK UTAMA (Key Metrics) -->
        <div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                            <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ \App\Models\User::where('status', 'active')->count() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">All users</p>
                        </div>
                    </div>
                </div>

                <!-- Total Students -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                            <flux:icon.academic-cap class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ \App\Models\Student::whereHas('subjects', function($query) { $query->where('enrollment_status', 'active'); })->count() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Students enrolled in subjects</p>
                        </div>
                    </div>
                </div>

                <!-- Total Teachers -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                            <flux:icon.user-group class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Teachers</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ \App\Models\User::role('teacher')->where('status', 'active')->count() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Active teachers</p>
                        </div>
                    </div>
                </div>

                <!-- Total Parents -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900">
                            <flux:icon.user-plus class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Parents</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ \App\Models\User::role('parent')->where('status', 'active')->count() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Active parents</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 3. QUICK ACTIONS (Quick Actions) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <flux:heading size="lg" class="text-gray-900 dark:text-white">⚡ Quick Actions</flux:heading>
                </div>
                <div class="p-6">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('admin.users') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800 transition-colors">
                                        <flux:icon.users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        Manage Users
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">System user management</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.students') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800 transition-colors">
                                        <flux:icon.academic-cap class="w-5 h-5 text-green-600 dark:text-green-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                        Manage Students
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Student data management</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.teachers') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800 transition-colors">
                                        <flux:icon.user-group class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                        Manage Teachers
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Teacher data management</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.parents') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-800 transition-colors">
                                        <flux:icon.user class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                        Manage Parents
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Parent data management</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.organization') }}" 
                           class="group relative bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-800 transition-colors">
                                        <flux:icon.building-office class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                        Organization Settings
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">System configuration</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. AKTIVITAS TERBARU (Recent Activities) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <flux:heading size="lg" class="text-gray-900 dark:text-white">🕒 Recent Activities</flux:heading>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Sample Activity 1 -->
                        <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <flux:icon.user-plus class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">New user registered</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">New student has registered to the system</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">2 hours ago</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    New
                                </span>
                            </div>
                        </div>

                        <!-- Sample Activity 2 -->
                        <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <flux:icon.academic-cap class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Student data updated</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Student information has been updated</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">4 hours ago</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    Updated
                                </span>
                            </div>
                        </div>

                        <!-- Sample Activity 3 -->
                        <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                    <flux:icon.user-group class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">New teacher added</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">New teaching staff has joined</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">1 day ago</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                    Added
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
