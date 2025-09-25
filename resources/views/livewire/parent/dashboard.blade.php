<div>
    <flux:header class="mb-8">
        <flux:heading size="xl">Parent Dashboard</flux:heading>
        <flux:text>Welcome back, {{ auth()->user()->name }}! Manage your children's education</flux:text>
    </flux:header>

    <div class="space-y-8">
        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
            <flux:card>
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <flux:icon.user class="h-8 w-8 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <flux:heading size="sm" class="text-gray-500">My Children</flux:heading>
                            <flux:heading size="lg" class="text-gray-900">{{ $stats['children_count'] ?? 0 }}</flux:heading>
                        </div>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <flux:icon.calendar class="h-8 w-8 text-green-600" />
                        </div>
                        <div class="ml-4">
                            <flux:heading size="sm" class="text-gray-500">Upcoming Events</flux:heading>
                            <flux:heading size="lg" class="text-gray-900">{{ $stats['upcoming_events'] ?? 0 }}</flux:heading>
                        </div>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <flux:icon.chart-bar class="h-8 w-8 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <flux:heading size="sm" class="text-gray-500">Recent Activities</flux:heading>
                            <flux:heading size="lg" class="text-gray-900">{{ $stats['recent_activities'] ?? 0 }}</flux:heading>
                        </div>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <flux:icon.bell class="h-8 w-8 text-orange-600" />
                        </div>
                        <div class="ml-4">
                            <flux:heading size="sm" class="text-gray-500">Notifications</flux:heading>
                            <flux:heading size="lg" class="text-gray-900">{{ $stats['notifications'] ?? 0 }}</flux:heading>
                        </div>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Quick Actions -->
        <flux:card>
            <div class="px-6 py-4 border-b border-gray-200">
                <flux:heading size="lg">Quick Actions</flux:heading>
                <flux:subheading>Common tasks for parents</flux:subheading>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <flux:card class="cursor-pointer hover:shadow-md transition-shadow">
                        <div class="p-6 text-center">
                            <flux:icon.user-plus class="h-8 w-8 mb-2 mx-auto text-blue-600" />
                            <div class="font-medium">Add Child</div>
                            <div class="text-sm text-gray-500">Register new child</div>
                        </div>
                    </flux:card>

                    <flux:card class="cursor-pointer hover:shadow-md transition-shadow">
                        <div class="p-6 text-center">
                            <flux:icon.calendar class="h-8 w-8 mb-2 mx-auto text-green-600" />
                            <div class="font-medium">View Schedule</div>
                            <div class="text-sm text-gray-500">Check class schedule</div>
                        </div>
                    </flux:card>

                    <flux:card class="cursor-pointer hover:shadow-md transition-shadow">
                        <div class="p-6 text-center">
                            <flux:icon.chart-bar class="h-8 w-8 mb-2 mx-auto text-purple-600" />
                            <div class="font-medium">View Grades</div>
                            <div class="text-sm text-gray-500">Check academic progress</div>
                        </div>
                    </flux:card>

                    <flux:card class="cursor-pointer hover:shadow-md transition-shadow">
                        <div class="p-6 text-center">
                            <flux:icon.document class="h-8 w-8 mb-2 mx-auto text-orange-600" />
                            <div class="font-medium">Reports</div>
                            <div class="text-sm text-gray-500">Download reports</div>
                        </div>
                    </flux:card>
                </div>
            </div>
        </flux:card>

        <!-- Parent Information -->
        <flux:card>
            <div class="px-6 py-4 border-b border-gray-200">
                <flux:heading size="lg">Your Information</flux:heading>
                <flux:subheading>Your parent profile details</flux:subheading>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <flux:heading size="sm" class="text-gray-500 mb-2">Full Name</flux:heading>
                        <flux:heading size="base" class="text-gray-900">{{ $stats['parent_info']['name'] }}</flux:heading>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 mb-2">Email</flux:heading>
                        <flux:heading size="base" class="text-gray-900">{{ $stats['parent_info']['email'] }}</flux:heading>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 mb-2">Phone</flux:heading>
                        <flux:heading size="base" class="text-gray-900">{{ $stats['parent_info']['phone'] }}</flux:heading>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 mb-2">Occupation</flux:heading>
                        <flux:heading size="base" class="text-gray-900">{{ $stats['parent_info']['occupation'] }}</flux:heading>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 mb-2">Workplace</flux:heading>
                        <flux:heading size="base" class="text-gray-900">{{ $stats['parent_info']['workplace'] }}</flux:heading>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 mb-2">Address</flux:heading>
                        <flux:heading size="base" class="text-gray-900">{{ $stats['parent_info']['address'] }}</flux:heading>
                    </div>
                </div>
            </div>
        </flux:card>

        <!-- Recent Activities -->
        <flux:card>
            <div class="px-6 py-4 border-b border-gray-200">
                <flux:heading size="lg">Recent Activities</flux:heading>
                <flux:subheading>Latest updates about your children</flux:subheading>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <flux:icon.clock class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                    <flux:heading size="sm" class="text-gray-500">No recent activities</flux:heading>
                    <flux:subheading class="text-gray-400">Activities will appear here once your children are enrolled</flux:subheading>
                </div>
            </div>
        </flux:card>
    </div>
</div>
