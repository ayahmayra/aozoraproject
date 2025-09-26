<x-layouts.app :title="__('Create Document Numbering Configuration')">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Create Document Numbering Configuration</flux:heading>
                        <flux:text class="mt-2">Configure automatic numbering for entities</flux:text>
                    </div>
                    <flux:button variant="ghost" href="{{ route('admin.document-numbering.index') }}">
                        <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                        Back to Configurations
                    </flux:button>
                </div>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Configuration Details</flux:heading>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.document-numbering.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Basic Information</flux:heading>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:field>
                                    <flux:label>Entity Type</flux:label>
                                    <flux:select name="entity_type" required>
                                        <option value="">Select entity type</option>
                                        @foreach($entityTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('entity_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                    @error('entity_type')
                                        <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                    @enderror
                                </flux:field>
                                
                                <flux:input
                                    name="description"
                                    label="Description"
                                    placeholder="Enter description for this configuration"
                                    value="{{ old('description') }}"
                                />
                                @error('description')
                                    <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                @enderror
                            </div>
                        </div>

                        <!-- Numbering Format -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Numbering Format</flux:heading>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <flux:input
                                    name="prefix"
                                    label="Prefix"
                                    placeholder="e.g., STU, TCH, PRT"
                                    value="{{ old('prefix') }}"
                                />
                                @error('prefix')
                                    <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                @enderror
                                
                                <flux:input
                                    name="separator"
                                    label="Separator"
                                    placeholder="e.g., -, /, _"
                                    value="{{ old('separator', '') }}"
                                />
                                @error('separator')
                                    <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                @enderror
                                
                                <flux:input
                                    name="suffix"
                                    label="Suffix"
                                    placeholder="e.g., -2024, /A"
                                    value="{{ old('suffix') }}"
                                />
                                @error('suffix')
                                    <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                @enderror
                            </div>
                        </div>

                        <!-- Number Settings -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Number Settings</flux:heading>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:input
                                    name="current_number"
                                    label="Starting Number"
                                    type="number"
                                    min="1"
                                    value="{{ old('current_number', 1) }}"
                                    required
                                />
                                @error('current_number')
                                    <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                @enderror
                                
                                <flux:input
                                    name="number_length"
                                    label="Number Length"
                                    type="number"
                                    min="1"
                                    max="10"
                                    value="{{ old('number_length', 4) }}"
                                    required
                                />
                                @error('number_length')
                                    <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                @enderror
                            </div>
                        </div>

                        <!-- Date Format Options -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Date Format Options</flux:heading>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <flux:field>
                                    <flux:label>Include Year</flux:label>
                                    <flux:checkbox name="include_year" value="1" {{ old('include_year') ? 'checked' : '' }} />
                                    <flux:input
                                        name="year_format"
                                        label="Year Format"
                                        placeholder="Y, y, YY"
                                        value="{{ old('year_format', 'Y') }}"
                                        class="mt-2"
                                    />
                                </flux:field>
                                
                                <flux:field>
                                    <flux:label>Include Month</flux:label>
                                    <flux:checkbox name="include_month" value="1" {{ old('include_month') ? 'checked' : '' }} />
                                    <flux:input
                                        name="month_format"
                                        label="Month Format"
                                        placeholder="m, M, MM"
                                        value="{{ old('month_format', 'm') }}"
                                        class="mt-2"
                                    />
                                </flux:field>
                                
                                <flux:field>
                                    <flux:label>Include Day</flux:label>
                                    <flux:checkbox name="include_day" value="1" {{ old('include_day') ? 'checked' : '' }} />
                                    <flux:input
                                        name="day_format"
                                        label="Day Format"
                                        placeholder="d, D, DD"
                                        value="{{ old('day_format', 'd') }}"
                                        class="mt-2"
                                    />
                                </flux:field>
                            </div>
                        </div>

                        <!-- Reset Options -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Reset Options</flux:heading>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <flux:field>
                                    <flux:label>Reset Daily</flux:label>
                                    <flux:checkbox name="reset_daily" value="1" {{ old('reset_daily') ? 'checked' : '' }} />
                                    <flux:text class="text-sm text-gray-500 mt-1">Reset number every day</flux:text>
                                </flux:field>
                                
                                <flux:field>
                                    <flux:label>Reset Monthly</flux:label>
                                    <flux:checkbox name="reset_monthly" value="1" {{ old('reset_monthly') ? 'checked' : '' }} />
                                    <flux:text class="text-sm text-gray-500 mt-1">Reset number every month</flux:text>
                                </flux:field>
                                
                                <flux:field>
                                    <flux:label>Reset Yearly</flux:label>
                                    <flux:checkbox name="reset_yearly" value="1" {{ old('reset_yearly') ? 'checked' : '' }} />
                                    <flux:text class="text-sm text-gray-500 mt-1">Reset number every year</flux:text>
                                </flux:field>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Status</flux:heading>
                            <flux:field>
                                <flux:label>Active</flux:label>
                                <flux:checkbox name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} />
                                <flux:text class="text-sm text-gray-500 mt-1">Enable this numbering configuration</flux:text>
                            </flux:field>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('admin.document-numbering.index') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Create Configuration
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
