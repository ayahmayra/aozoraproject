<x-layouts.app :title="__('Edit Student')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Edit Student</flux:heading>
                        <flux:text class="mt-2">Update student information</flux:text>
                    </div>
                    <flux:button variant="ghost" href="{{ route('admin.students') }}">
                        <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                        Back to Students
                    </flux:button>
                </div>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Student Information</flux:heading>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.students.update', $student) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Basic Information</flux:heading>
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <flux:input
                                    name="name"
                                    label="Full Name"
                                    placeholder="Enter student's full name"
                                    value="{{ old('name', $student->user->name) }}"
                                    required
                                />
                                <flux:input
                                    name="email"
                                    label="Email Address"
                                    type="email"
                                    placeholder="Enter email address"
                                    value="{{ old('email', $student->user->email) }}"
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
                        </div>

                        <!-- Student Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Student Information</flux:heading>
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <flux:field>
                                    <flux:label>Parent</flux:label>
                                    <flux:select name="parent_id" required>
                                        <option value="">Select parent</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id', $student->parent_id) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }} ({{ $parent->email }})
                                            </option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                                <flux:input
                                    name="student_id"
                                    label="Student ID"
                                    placeholder="Enter student ID (optional)"
                                    value="{{ old('student_id', $student->student_id) }}"
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
                                <flux:input
                                    name="date_of_birth"
                                    label="Date of Birth"
                                    type="date"
                                    value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}"
                                    required
                                />
                                <flux:field>
                                    <flux:label>Gender</flux:label>
                                    <flux:select name="gender" required>
                                        <option value="">Select gender</option>
                                        <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </flux:select>
                                </flux:field>
                            </div>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
                                <flux:input
                                    name="phone"
                                    label="Phone Number"
                                    placeholder="Enter phone number"
                                    value="{{ old('phone', $student->phone) }}"
                                />
                                <flux:input
                                    name="address"
                                    label="Address"
                                    placeholder="Enter address"
                                    value="{{ old('address', $student->address) }}"
                                />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('admin.students') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Update Student
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
