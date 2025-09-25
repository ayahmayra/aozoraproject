<x-layouts.app :title="__('Add Child')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Add New Child</flux:heading>
                        <flux:text class="mt-2">Add a new student to your account</flux:text>
                    </div>
                    <flux:button variant="ghost" href="{{ route('parent.children') }}">
                        <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                        Back to Children
                    </flux:button>
                </div>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Student Information</flux:heading>
                </div>
                <div class="p-6">
                    <form action="{{ route('parent.children.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Basic Information</flux:heading>
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <flux:input
                                    name="name"
                                    label="Full Name"
                                    placeholder="Enter student's full name"
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

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
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
                        </div>

                        <!-- Student Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Student Information</flux:heading>
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <flux:input
                                    name="date_of_birth"
                                    label="Date of Birth"
                                    type="date"
                                    value="{{ old('date_of_birth') }}"
                                    required
                                />
                                <flux:field>
                                    <flux:label>Gender</flux:label>
                                    <flux:select name="gender" required>
                                        <option value="">Select gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </flux:select>
                                </flux:field>
                            </div>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
                                <flux:input
                                    name="phone"
                                    label="Phone Number"
                                    placeholder="Enter phone number"
                                    value="{{ old('phone') }}"
                                />
                                <flux:input
                                    name="address"
                                    label="Address"
                                    placeholder="Enter address"
                                    value="{{ old('address') }}"
                                />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('parent.children') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Add Child
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
