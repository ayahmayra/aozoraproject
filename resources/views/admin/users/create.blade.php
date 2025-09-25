<x-layouts.app :title="__('Create Admin User')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl">Create New Admin User</flux:heading>
                <flux:text class="mt-2">Add a new admin user to the system</flux:text>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Admin User Information</flux:heading>
                        <flux:button variant="ghost" href="{{ route('admin.users') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Admin Users
                        </flux:button>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
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
                                label="Email"
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
                                    value="Admin" 
                                    readonly 
                                    class="bg-gray-50"
                                />
                                <flux:subheading class="mt-1">This user will automatically be assigned the Admin role</flux:subheading>
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Status</flux:label>
                                <flux:select name="status" variant="listbox" placeholder="Choose status..." required>
                                    <flux:select.option value="active">Active</flux:select.option>
                                    <flux:select.option value="pending">Pending</flux:select.option>
                                    <flux:select.option value="inactive">Inactive</flux:select.option>
                                </flux:select>
                                <flux:subheading class="mt-1">Select the initial status for this user</flux:subheading>
                            </flux:field>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('admin.users') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Create Admin User
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
