<x-layouts.app :title="__('Edit User')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl">Edit User</flux:heading>
                <flux:text class="mt-2">Update user information</flux:text>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">User Information</flux:heading>
                        <flux:button variant="ghost" href="{{ route('admin.users') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Users
                        </flux:button>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="name"
                                label="Full Name"
                                placeholder="Enter full name"
                                value="{{ old('name', $user->name) }}"
                                required
                            />
                            <flux:input
                                name="email"
                                label="Email"
                                type="email"
                                placeholder="Enter email address"
                                value="{{ old('email', $user->email) }}"
                                required
                            />
                        </div>

                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="password"
                                label="New Password (leave blank to keep current)"
                                type="password"
                                placeholder="Enter new password"
                            />
                            <flux:input
                                name="password_confirmation"
                                label="Confirm New Password"
                                type="password"
                                placeholder="Confirm new password"
                            />
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Role</flux:label>
                                <flux:select name="role" required>
                                    <option value="">Select a role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Status</flux:label>
                                <flux:select name="status" required>
                                    <option value="">Select status</option>
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </flux:select>
                                <flux:subheading class="mt-1">Select the status for this user</flux:subheading>
                            </flux:field>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('admin.users') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Update User
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
