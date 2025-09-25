<x-layouts.app :title="__('Edit Organization')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl">Edit Organization</flux:heading>
                <flux:text class="mt-2">Update your organization's information and settings</flux:text>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Organization Information</flux:heading>
                        <flux:button variant="ghost" href="{{ route('admin.organization') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Organization
                        </flux:button>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.organization.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <flux:heading size="sm" class="text-gray-700">Basic Information</flux:heading>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <flux:input
                                    name="name"
                                    label="Organization Name"
                                    placeholder="Enter organisation name"
                                    value="{{ old('name', $organization->name) }}"
                                    required
                                />
                                <flux:input
                                    name="short_name"
                                    label="Short Name"
                                    placeholder="Enter short name"
                                    value="{{ old('short_name', $organization->short_name) }}"
                                />
                            </div>

                            <flux:textarea
                                name="description"
                                label="Description"
                                placeholder="Enter organisation description"
                                rows="3"
                            >{{ old('description', $organization->description) }}</flux:textarea>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <flux:input
                                    name="email"
                                    label="Email"
                                    type="email"
                                    placeholder="contact@organisation.com"
                                    value="{{ old('email', $organization->email) }}"
                                />
                                <flux:input
                                    name="phone"
                                    label="Phone"
                                    placeholder="+6281234567890"
                                    value="{{ old('phone', $organization->phone) }}"
                                />
                            </div>

                            <flux:input
                                name="website"
                                label="Website"
                                placeholder="https://www.organisation.com"
                                value="{{ old('website', $organization->website) }}"
                            />
                        </div>

                        <!-- Address Information -->
                        <div class="space-y-6">
                            <flux:heading size="sm" class="text-gray-700">Address Information</flux:heading>

                            <flux:textarea
                                name="address"
                                label="Address"
                                placeholder="Enter full address"
                                rows="2"
                            >{{ old('address', $organization->address) }}</flux:textarea>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                                <flux:input
                                    name="city"
                                    label="City"
                                    placeholder="Enter city"
                                    value="{{ old('city', $organization->city) }}"
                                />
                                <flux:input
                                    name="state"
                                    label="State/Province"
                                    placeholder="Enter state"
                                    value="{{ old('state', $organization->state) }}"
                                />
                                <flux:input
                                    name="postal_code"
                                    label="Postal Code"
                                    placeholder="Enter postal code"
                                    value="{{ old('postal_code', $organization->postal_code) }}"
                                />
                            </div>

                            <flux:input
                                name="country"
                                label="Country"
                                placeholder="Enter country"
                                value="{{ old('country', $organization->country) }}"
                            />
                        </div>

                        <!-- Branding -->
                        <div class="space-y-6">
                            <flux:heading size="sm" class="text-gray-700">Branding</flux:heading>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <div>
                                    <flux:label>Logo</flux:label>
                                    <flux:input type="file" name="logo" accept="image/*" />
                                    @if($organization->logo)
                                        <p class="text-sm text-gray-500 mt-1">Current: {{ $organization->logo }}</p>
                                    @endif
                                </div>
                                <div>
                                    <flux:label>Favicon</flux:label>
                                    <flux:input type="file" name="favicon" accept="image/*" />
                                    @if($organization->favicon)
                                        <p class="text-sm text-gray-500 mt-1">Current: {{ $organization->favicon }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                                <flux:input
                                    name="primary_color"
                                    label="Primary Color"
                                    type="color"
                                    value="{{ old('primary_color', $organization->primary_color) }}"
                                />
                                <flux:input
                                    name="secondary_color"
                                    label="Secondary Color"
                                    type="color"
                                    value="{{ old('secondary_color', $organization->secondary_color) }}"
                                />
                                <flux:input
                                    name="accent_color"
                                    label="Accent Color"
                                    type="color"
                                    value="{{ old('accent_color', $organization->accent_color) }}"
                                />
                            </div>
                        </div>

                        <!-- Mission, Vision, Values -->
                        <div class="space-y-6">
                            <flux:heading size="sm" class="text-gray-700">Mission, Vision & Values</flux:heading>

                            <flux:textarea
                                name="mission"
                                label="Mission Statement"
                                placeholder="Enter mission statement"
                                rows="3"
                            >{{ old('mission', $organization->mission) }}</flux:textarea>

                            <flux:textarea
                                name="vision"
                                label="Vision Statement"
                                placeholder="Enter vision statement"
                                rows="3"
                            >{{ old('vision', $organization->vision) }}</flux:textarea>

                            <flux:textarea
                                name="values"
                                label="Values"
                                placeholder="Enter organisation values"
                                rows="3"
                            >{{ old('values', $organization->values) }}</flux:textarea>
                        </div>

                        <!-- Legal Information -->
                        <div class="space-y-6">
                            <flux:heading size="sm" class="text-gray-700">Legal Information</flux:heading>

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                                <flux:input
                                    name="founded_year"
                                    label="Founded Year"
                                    placeholder="2024"
                                    value="{{ old('founded_year', $organization->founded_year) }}"
                                />
                                <flux:input
                                    name="license_number"
                                    label="License Number"
                                    placeholder="Enter license number"
                                    value="{{ old('license_number', $organization->license_number) }}"
                                />
                                <flux:input
                                    name="tax_id"
                                    label="Tax ID"
                                    placeholder="Enter tax ID"
                                    value="{{ old('tax_id', $organization->tax_id) }}"
                                />
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <flux:checkbox name="is_active" value="1" {{ old('is_active', $organization->is_active) ? 'checked' : '' }} />
                                <flux:label class="ml-2">Active Organization</flux:label>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('admin.organization') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Update Organization
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
