<x-layouts.app :title="__('Edit Teacher')">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Edit Teacher</flux:heading>
                        <flux:text class="mt-2">Update teacher information</flux:text>
                    </div>
                    <div class="flex space-x-3">
                        <flux:button variant="ghost" href="{{ route('admin.teachers') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Teachers
                        </flux:button>
                    </div>
                </div>
            </div>

            @if (session()->has('success'))
                <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
            @endif

            @if (session()->has('error'))
                <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
            @endif

            <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
                @csrf
                @method('PUT')

                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Basic Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="name"
                                label="Full Name"
                                placeholder="Enter teacher's full name"
                                value="{{ old('name', $teacher->name) }}"
                                required
                            />
                            <flux:input
                                name="email"
                                label="Email Address"
                                type="email"
                                placeholder="Enter teacher's email"
                                value="{{ old('email', $teacher->email) }}"
                                required
                            />
                            <flux:input
                                name="password"
                                label="New Password"
                                type="password"
                                placeholder="Leave blank to keep current password"
                            />
                            <flux:input
                                name="password_confirmation"
                                label="Confirm New Password"
                                type="password"
                                placeholder="Confirm new password"
                            />
                        </div>
                    </div>
                </flux:card>

                <flux:card class="mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Contact Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="phone"
                                label="Phone Number"
                                placeholder="Enter phone number"
                                value="{{ old('phone', $teacher->teacherProfile->phone ?? '') }}"
                            />
                            <flux:input
                                name="address"
                                label="Address"
                                placeholder="Enter address"
                                value="{{ old('address', $teacher->teacherProfile->address ?? '') }}"
                            />
                        </div>
                    </div>
                </flux:card>

                <flux:card class="mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Professional Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="education_level"
                                label="Education Level"
                                placeholder="e.g., Bachelor's, Master's, PhD"
                                value="{{ old('education_level', $teacher->teacherProfile->education_level ?? '') }}"
                            />
                            <flux:input
                                name="institution"
                                label="Institution"
                                placeholder="University or college name"
                                value="{{ old('institution', $teacher->teacherProfile->institution ?? '') }}"
                            />
                            <flux:input
                                name="graduation_year"
                                label="Graduation Year"
                                type="number"
                                placeholder="Year of graduation"
                                value="{{ old('graduation_year', $teacher->teacherProfile->graduation_year ?? '') }}"
                            />
                            <flux:input
                                name="hire_date"
                                label="Hire Date"
                                type="date"
                                value="{{ old('hire_date', $teacher->teacherProfile->hire_date ? $teacher->teacherProfile->hire_date->format('Y-m-d') : '') }}"
                            />
                            <flux:input
                                name="employment_status"
                                label="Employment Status"
                                placeholder="e.g., Full-time, Part-time, Contract"
                                value="{{ old('employment_status', $teacher->teacherProfile->employment_status ?? '') }}"
                            />
                        </div>
                    </div>
                </flux:card>

                <flux:card class="mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Personal Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="date_of_birth"
                                label="Date of Birth"
                                type="date"
                                value="{{ old('date_of_birth', $teacher->teacherProfile->date_of_birth ? $teacher->teacherProfile->date_of_birth->format('Y-m-d') : '') }}"
                            />
                            <flux:field>
                                <flux:label>Gender</flux:label>
                                <flux:select name="gender">
                                    <option value="">Select gender</option>
                                    <option value="male" {{ old('gender', $teacher->teacherProfile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $teacher->teacherProfile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                </flux:select>
                            </flux:field>
                            <flux:input
                                name="certifications"
                                label="Certifications"
                                placeholder="List any certifications"
                                value="{{ old('certifications', $teacher->teacherProfile->certifications ?? '') }}"
                            />
                            <flux:input
                                name="notes"
                                label="Notes"
                                placeholder="Additional notes"
                                value="{{ old('notes', $teacher->teacherProfile->notes ?? '') }}"
                            />
                        </div>
                    </div>
                </flux:card>

                <div class="mt-6 flex justify-end space-x-3">
                    <flux:button variant="ghost" href="{{ route('admin.teachers') }}">
                        Cancel
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        
                        Update Teacher
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
