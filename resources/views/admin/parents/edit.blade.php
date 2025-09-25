<x-layouts.app :title="__('Edit Parent User')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl">Edit Parent User</flux:heading>
                <flux:text class="mt-2">Update parent user information and parent details</flux:text>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">User Information</flux:heading>
                        <flux:button variant="ghost" href="{{ route('admin.parents') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Parent Users
                        </flux:button>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.parents.update', $parent) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- User Basic Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Basic Information</flux:heading>
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <flux:input
                                    name="name"
                                    label="Full Name"
                                    placeholder="Enter full name"
                                    value="{{ old('name', $parent->name) }}"
                                    required
                                />
                                <flux:input
                                    name="email"
                                    label="Email Address"
                                    type="email"
                                    placeholder="Enter email address"
                                    value="{{ old('email', $parent->email) }}"
                                    required
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
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

                            <div class="mt-6">
                                <flux:field>
                                    <flux:label>Status</flux:label>
                                    <flux:select name="status" required>
                                        <option value="">Select status</option>
                                        <option value="active" {{ old('status', $parent->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="pending" {{ old('status', $parent->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="inactive" {{ old('status', $parent->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </flux:select>
                                    <flux:subheading class="mt-1">Select the status for this user</flux:subheading>
                                </flux:field>
                            </div>

                            <div class="mt-6">
                                <flux:field>
                                    <flux:label>Role</flux:label>
                                    <flux:input 
                                        name="role_display" 
                                        value="Parent" 
                                        readonly 
                                        class="bg-gray-50"
                                    />
                                    <flux:subheading class="mt-1">This user has the Parent role</flux:subheading>
                                </flux:field>
                            </div>
                        </div>

                        <!-- Parent Specific Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Parent Information</flux:heading>
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <flux:input
                                    name="phone"
                                    label="Phone Number"
                                    placeholder="Enter phone number"
                                    value="{{ old('phone', $parentData?->phone) }}"
                                />
                                <flux:input
                                    name="occupation"
                                    label="Occupation"
                                    placeholder="Enter occupation"
                                    value="{{ old('occupation', $parentData?->occupation) }}"
                                />
                            </div>

                            <div class="mt-6">
                                <flux:field>
                                    <flux:label>Address</flux:label>
                                    <flux:textarea
                                        name="address"
                                        placeholder="Enter full address"
                                        rows="3"
                                    >{{ old('address', $parentData?->address) }}</flux:textarea>
                                </flux:field>
                            </div>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
                                <flux:input
                                    name="emergency_contact"
                                    label="Emergency Contact"
                                    placeholder="Enter emergency contact number"
                                    value="{{ old('emergency_contact', $parentData?->emergency_contact_phone) }}"
                                />
                                <flux:input
                                    name="emergency_contact_name"
                                    label="Emergency Contact Name"
                                    placeholder="Enter emergency contact name"
                                    value="{{ old('emergency_contact_name', $parentData?->emergency_contact_name) }}"
                                />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('admin.parents') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Update Parent User
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
