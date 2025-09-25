<x-layouts.app :title="__('Edit Child')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Edit Child Information</flux:heading>
                        <flux:text class="mt-2">Update {{ $student->user->name }}'s information</flux:text>
                    </div>
                    <div class="flex space-x-3">
                        <flux:button variant="ghost" href="{{ route('parent.dashboard') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Dashboard
                        </flux:button>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <flux:callout class="mb-6" variant="danger" icon="x-mark" heading="Please correct the following errors:">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </flux:callout>
            @endif

            <div class="max-w-2xl">
                <flux:card>
                    <div class="p-6">
                        <form method="POST" action="{{ route('parent.children.update', $student) }}" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <flux:field>
                                    <flux:label>Full Name *</flux:label>
                                    <flux:input 
                                        name="name" 
                                        value="{{ old('name', $student->user->name) }}" 
                                        placeholder="Enter child's full name"
                                        required 
                                    />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Email Address *</flux:label>
                                    <flux:input 
                                        type="email" 
                                        name="email" 
                                        value="{{ old('email', $student->user->email) }}" 
                                        placeholder="Enter child's email"
                                        required 
                                    />
                                </flux:field>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <flux:field>
                                    <flux:label>New Password</flux:label>
                                    <flux:input 
                                        type="password" 
                                        name="password" 
                                        placeholder="Leave blank to keep current password"
                                    />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Confirm New Password</flux:label>
                                    <flux:input 
                                        type="password" 
                                        name="password_confirmation" 
                                        placeholder="Confirm new password"
                                    />
                                </flux:field>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <flux:field>
                                    <flux:label>Date of Birth *</flux:label>
                                    <flux:input 
                                        type="date" 
                                        name="date_of_birth" 
                                        value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" 
                                        required 
                                    />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Gender *</flux:label>
                                    <flux:select name="gender" required>
                                        <option value="">Select gender</option>
                                        <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    </flux:select>
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label>Address</flux:label>
                                <flux:textarea 
                                    name="address" 
                                    placeholder="Enter child's address"
                                    rows="3"
                                >{{ old('address', $student->address) }}</flux:textarea>
                            </flux:field>

                            <flux:field>
                                <flux:label>Phone Number</flux:label>
                                <flux:input 
                                    type="tel" 
                                    name="phone" 
                                    value="{{ old('phone', $student->phone) }}" 
                                    placeholder="Enter phone number"
                                />
                            </flux:field>

                            <div class="flex justify-end space-x-3">
                                <flux:button variant="ghost" href="{{ route('parent.dashboard') }}">
                                    Cancel
                                </flux:button>
                                <flux:button variant="primary" type="submit">
                                    <flux:icon.check class="h-4 w-4 mr-2" />
                                    Update Child
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</x-layouts.app>
