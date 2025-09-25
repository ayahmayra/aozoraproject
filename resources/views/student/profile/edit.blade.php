<x-layouts.app :title="__('Edit Student Profile')">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Edit Student Profile</flux:heading>
                        <flux:text class="mt-2">Update your personal information</flux:text>
                    </div>
                    <div class="flex space-x-3">
                        @if(auth()->user()->hasRole('parent'))
                            <flux:button variant="ghost" href="{{ route('student.profile.view') }}?student_id={{ $student->id }}">
                                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                Back to Profile
                            </flux:button>
                        @elseif(auth()->user()->hasRole('admin'))
                            <flux:button variant="ghost" href="{{ route('student.profile.view') }}?student_id={{ $student->id }}">
                                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                Back to Profile
                            </flux:button>
                        @else
                            <flux:button variant="ghost" href="{{ route('student.profile') }}">
                                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                Back to Profile
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            @if (session()->has('success'))
                <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
            @endif

            @if (session()->has('error'))
                <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
            @endif

            <form method="POST" action="{{ auth()->user()->hasRole('student') ? route('student.profile.update') : route('student.profile.update.view') }}">
                @csrf
                @method('PUT')
                @if(request()->has('student_id'))
                    <input type="hidden" name="student_id" value="{{ request('student_id') }}">
                @endif

                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Personal Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <flux:input
                                name="name"
                                label="Full Name"
                                placeholder="Enter your full name"
                                value="{{ old('name', $student->user->name) }}"
                                required
                            />
                            <flux:input
                                name="email"
                                label="Email Address"
                                type="email"
                                placeholder="Enter your email address"
                                value="{{ old('email', $student->user->email) }}"
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
                                placeholder="Confirm your new password"
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
                                placeholder="Enter your phone number"
                                value="{{ old('phone', $student->phone) }}"
                            />
                            <flux:input
                                name="address"
                                label="Address"
                                placeholder="Enter your address"
                                value="{{ old('address', $student->address) }}"
                            />
                        </div>
                    </div>
                </flux:card>

                <div class="mt-6 flex justify-end space-x-3">
                    <flux:button variant="ghost" href="{{ route('student.profile') }}">
                        Cancel
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        <flux:icon.check class="h-4 w-4 mr-2" />
                        Update Profile
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
