<x-layouts.app :title="__('Document Numbering Configuration Details')">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Document Numbering Configuration</flux:heading>
                        <flux:text class="mt-2">{{ ucfirst($documentNumberingConfig->entity_type) }} - {{ $documentNumberingConfig->description ?: 'No description' }}</flux:text>
                    </div>
                    <div class="flex space-x-2">
                        <flux:button variant="ghost" href="{{ route('admin.document-numbering.index') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Configurations
                        </flux:button>
                        <flux:button variant="primary" href="{{ route('admin.document-numbering.edit', $documentNumberingConfig) }}">
                            <flux:icon.pencil class="h-4 w-4 mr-2" />
                            Edit Configuration
                        </flux:button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Basic Information -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Basic Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Entity Type</flux:text>
                                <flux:text class="text-sm font-medium">{{ ucfirst($documentNumberingConfig->entity_type) }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Description</flux:text>
                                <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->description ?: 'No description' }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Status</flux:text>
                                <flux:badge size="sm" color="{{ $documentNumberingConfig->is_active ? 'green' : 'red' }}">
                                    {{ $documentNumberingConfig->is_active ? 'Active' : 'Inactive' }}
                                </flux:badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Created</flux:text>
                                <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->created_at->format('M j, Y H:i') }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Last Updated</flux:text>
                                <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->updated_at->format('M j, Y H:i') }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Numbering Format -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Numbering Format</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Prefix</flux:text>
                                <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->prefix ?: 'None' }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Separator</flux:text>
                                <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->separator ?: 'None' }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Suffix</flux:text>
                                <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->suffix ?: 'None' }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Number Length</flux:text>
                                <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->number_length }} digits</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Current Number</flux:text>
                                <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->current_number }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Date Format Options -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Date Format Options</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Include Year</flux:text>
                                <flux:badge size="sm" color="{{ $documentNumberingConfig->include_year ? 'green' : 'red' }}">
                                    {{ $documentNumberingConfig->include_year ? 'Yes' : 'No' }}
                                </flux:badge>
                            </div>
                            @if($documentNumberingConfig->include_year)
                                <div class="flex justify-between items-center">
                                    <flux:text class="text-sm text-gray-600">Year Format</flux:text>
                                    <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->year_format }}</flux:text>
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Include Month</flux:text>
                                <flux:badge size="sm" color="{{ $documentNumberingConfig->include_month ? 'green' : 'red' }}">
                                    {{ $documentNumberingConfig->include_month ? 'Yes' : 'No' }}
                                </flux:badge>
                            </div>
                            @if($documentNumberingConfig->include_month)
                                <div class="flex justify-between items-center">
                                    <flux:text class="text-sm text-gray-600">Month Format</flux:text>
                                    <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->month_format }}</flux:text>
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Include Day</flux:text>
                                <flux:badge size="sm" color="{{ $documentNumberingConfig->include_day ? 'green' : 'red' }}">
                                    {{ $documentNumberingConfig->include_day ? 'Yes' : 'No' }}
                                </flux:badge>
                            </div>
                            @if($documentNumberingConfig->include_day)
                                <div class="flex justify-between items-center">
                                    <flux:text class="text-sm text-gray-600">Day Format</flux:text>
                                    <flux:text class="text-sm font-medium">{{ $documentNumberingConfig->day_format }}</flux:text>
                                </div>
                            @endif
                        </div>
                    </div>
                </flux:card>

                <!-- Reset Options -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Reset Options</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Reset Daily</flux:text>
                                <flux:badge size="sm" color="{{ $documentNumberingConfig->reset_daily ? 'green' : 'red' }}">
                                    {{ $documentNumberingConfig->reset_daily ? 'Yes' : 'No' }}
                                </flux:badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Reset Monthly</flux:text>
                                <flux:badge size="sm" color="{{ $documentNumberingConfig->reset_monthly ? 'green' : 'red' }}">
                                    {{ $documentNumberingConfig->reset_monthly ? 'Yes' : 'No' }}
                                </flux:badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Reset Yearly</flux:text>
                                <flux:badge size="sm" color="{{ $documentNumberingConfig->reset_yearly ? 'green' : 'red' }}">
                                    {{ $documentNumberingConfig->reset_yearly ? 'Yes' : 'No' }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>

            <!-- Format Preview -->
            <flux:card class="mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Format Preview</flux:heading>
                </div>
                <div class="p-6">
                    <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg text-center">
                        <flux:text class="font-mono text-2xl font-bold">{{ $documentNumberingConfig->getPreview() }}</flux:text>
                        <flux:text class="text-sm text-gray-500 mt-2">This is how the generated numbers will look</flux:text>
                    </div>
                </div>
            </flux:card>

            <!-- Actions -->
            <flux:card class="mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Actions</flux:heading>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-4">
                        <flux:button variant="primary" href="{{ route('admin.document-numbering.edit', $documentNumberingConfig) }}">
                            <flux:icon.pencil class="h-4 w-4 mr-2" />
                            Edit Configuration
                        </flux:button>
                        
                        <form method="POST" action="{{ route('admin.document-numbering.toggle-status', $documentNumberingConfig) }}" class="inline">
                            @csrf
                            <flux:button variant="{{ $documentNumberingConfig->is_active ? 'ghost' : 'primary' }}" type="submit">
                                <flux:icon.power class="h-4 w-4 mr-2" />
                                {{ $documentNumberingConfig->is_active ? 'Deactivate' : 'Activate' }}
                            </flux:button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.document-numbering.reset-number', $documentNumberingConfig) }}" class="inline" onsubmit="return confirm('Are you sure you want to reset the current number to 1?')">
                            @csrf
                            <flux:button variant="ghost" type="submit">
                                <flux:icon.arrow-path class="h-4 w-4 mr-2" />
                                Reset Number
                            </flux:button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.document-numbering.destroy', $documentNumberingConfig) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this configuration?')">
                            @csrf
                            @method('DELETE')
                            <flux:button variant="danger" type="submit">
                                <flux:icon.trash class="h-4 w-4 mr-2" />
                                Delete Configuration
                            </flux:button>
                        </form>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
