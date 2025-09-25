<x-layouts.app :title="__('Organization Management')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl">Organization Management</flux:heading>
                <flux:text class="mt-2">Manage your organization's information and settings</flux:text>
            </div>

            @if (session()->has('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <flux:icon.check class="h-5 w-5 text-green-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($organization)
                <!-- Organization Overview -->
                <flux:card>
                    <div class="px-6 py-4 border-b">
                        <div class="flex justify-between items-center">
                            <div>
                                <flux:heading size="lg">{{ $organization->name }}</flux:heading>
                                <flux:subheading>{{ $organization->short_name ?: 'No short name' }}</flux:subheading>
                            </div>
                            <flux:button variant="primary" href="{{ route('admin.organization.edit') }}">
                                <flux:icon.pencil-square class="h-4 w-4 mr-2" />
                                Edit Organization
                            </flux:button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <flux:heading size="sm">Basic Information</flux:heading>
                                <div class="space-y-3">
                                    <div>
                                        <flux:heading size="lg">Description</flux:heading>
                                        <flux:text class="mt-2">{{ $organization->description ?: 'No description provided' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:heading size="lg">Email</flux:heading>
                                        <flux:text class="mt-2">{{ $organization->email ?: 'No email provided' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:heading size="lg">Phone</flux:heading>
                                        <flux:text class="mt-2">{{ $organization->phone ?: 'No phone provided' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:heading size="lg">Website</flux:heading>
                                        <flux:text class="mt-2">
                                            @if($organization->website)
                                                <a href="{{ $organization->website }}" target="_blank" class="hover:underline">{{ $organization->website }}</a>
                                            @else
                                                No website provided
                                            @endif
                                        </flux:text>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="space-y-4">
                                <flux:heading size="sm">Address Information</flux:heading>
                                <div class="space-y-3">
                                    <div>
                                        <flux:heading size="lg">Full Address</flux:heading>
                                        <flux:text class="mt-2">{{ $organization->full_address }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:heading size="lg">Founded Year</flux:heading>
                                        <flux:text class="mt-2">{{ $organization->founded_year ?: 'Not specified' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:heading size="lg">License Number</flux:heading>
                                        <flux:text class="mt-2">{{ $organization->license_number ?: 'Not provided' }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:heading size="lg">Tax ID</flux:heading>
                                        <flux:text class="mt-2">{{ $organization->tax_id ?: 'Not provided' }}</flux:text>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <div>
                                <flux:heading size="sm" class="mb-3">Mission</flux:heading>
                                <p>{{ $organization->mission ?: 'No mission statement provided' }}</p>
                            </div>
                            <div>
                                <flux:heading size="sm" class="mb-3">Vision</flux:heading>
                                <p>{{ $organization->vision ?: 'No vision statement provided' }}</p>
                            </div>
                            <div>
                                <flux:heading size="sm" class="mb-3">Values</flux:heading>
                                <p>{{ $organization->values ?: 'No values statement provided' }}</p>
                            </div>
                        </div>

                        <!-- Brand Colors -->
                        <div class="mt-8">
                            <flux:heading size="sm" class="mb-4">Brand Colors</flux:heading>
                            <div class="flex space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded" style="background-color: {{ $organization->primary_color }}"></div>
                                    <span class="text-sm">Primary</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded" style="background-color: {{ $organization->secondary_color }}"></div>
                                    <span class="text-sm">Secondary</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded" style="background-color: {{ $organization->accent_color }}"></div>
                                    <span class="text-sm">Accent</span>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        @if(!empty($organization->social_media))
                            <div class="mt-8">
                                <flux:heading size="sm" class="mb-4">Social Media</flux:heading>
                                <div class="flex flex-wrap gap-4">
                                    @foreach($organization->social_media as $platform => $link)
                                        <a href="{{ $link }}" target="_blank" class="flex items-center space-x-2 text-blue-600 hover:underline">
                                            <flux:icon.link class="h-4 w-4" />
                                            <span>{{ ucfirst($platform) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Contact Persons -->
                        @if(!empty($organization->contact_persons))
                            <div class="mt-8">
                                <flux:heading size="sm" class="mb-4">Contact Persons</flux:heading>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    @foreach($organization->contact_persons as $person)
                                        <flux:card>
                                            <div class="p-4">
                                                <flux:heading size="base">{{ $person['name'] }}</flux:heading>
                                                <flux:subheading class="text-gray-600">{{ $person['position'] }}</flux:subheading>
                                                <p class="text-sm text-gray-700 mt-2">Email: {{ $person['email'] }}</p>
                                                <p class="text-sm text-gray-700">Phone: {{ $person['phone'] }}</p>
                                            </div>
                                        </flux:card>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </flux:card>
            @else
                <!-- No Organization -->
                <flux:card>
                    <div class="p-6 text-center">
                        <flux:icon.home class="h-16 w-16 text-gray-400 mx-auto mb-4" />
                        <flux:heading size="lg" class="text-gray-900 mb-2">No Organization Found</flux:heading>
                        <flux:subheading class="text-gray-500 mb-6">Create your organization profile to get started</flux:subheading>
                        <flux:button variant="primary" href="{{ route('admin.organization.edit') }}">
                            <flux:icon.plus class="h-4 w-4 mr-2" />
                            Create Organization
                        </flux:button>
                    </div>
                </flux:card>
            @endif
        </div>
    </div>
</x-layouts.app>
