<x-layouts.app :title="__('Create Parent User')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl">Create New Parent User</flux:heading>
                <flux:text class="mt-2">Add a new parent user to the system</flux:text>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Parent User Information</flux:heading>
                        <flux:button variant="ghost" href="{{ route('admin.parents') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Parent Users
                        </flux:button>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.parents.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="name"
                                label="Full Name"
                                placeholder="Enter full name"
                                value="{{ old('name') }}"
                                required
                            />
                            <flux:input
                                name="email"
                                label="Email Address"
                                type="email"
                                placeholder="Enter email address"
                                value="{{ old('email') }}"
                                required
                            />
                        </div>

                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="password"
                                label="Password"
                                type="password"
                                placeholder="Enter password"
                                required
                            />
                            <flux:input
                                name="password_confirmation"
                                label="Confirm Password"
                                type="password"
                                placeholder="Confirm password"
                                required
                            />
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Role</flux:label>
                                <flux:input 
                                    name="role_display" 
                                    value="Parent" 
                                    readonly 
                                    class="bg-gray-50"
                                />
                                <flux:subheading class="mt-1">This user will automatically be assigned the Parent role</flux:subheading>
                            </flux:field>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('admin.parents') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Create Parent User
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
