<x-layouts.app :title="__('Document Numbering Configuration')">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">Document Numbering Configuration</flux:heading>
                <flux:text class="mb-6 mt-2 text-base">Manage automatic numbering for entities</flux:text>
            </div>
            <flux:button variant="primary" href="{{ route('admin.document-numbering.create') }}">
                <flux:icon.plus class="h-4 w-4 mr-2" />
                Add Configuration
            </flux:button>
        </div>
    </div>

    @if (session()->has('success'))
        <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
    @endif

    @if (session()->has('error'))
        <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
    @endif

    <!-- Search and Filter Form -->
    <flux:card class="mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <flux:heading size="lg">Search & Filter</flux:heading>
        </div>
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:input
                    name="search"
                    label="Search"
                    placeholder="Search by entity type, prefix, or description"
                    value="{{ request('search') }}"
                />
                
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </flux:select>
                </flux:field>
                
                <div class="flex items-end space-x-2">
                    <flux:button variant="primary" type="submit">
                        <flux:icon.magnifying-glass class="h-4 w-4 mr-2" />
                        Search
                    </flux:button>
                    <flux:button variant="ghost" href="{{ route('admin.document-numbering.index') }}">
                        Clear
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:card>

    <!-- Document Numbering Configurations Table -->
    <flux:card>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Numbering Configurations</flux:heading>
                <flux:text class="text-sm text-gray-500">{{ $configs->total() }} configurations found</flux:text>
            </div>
        </div>
        
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Entity Type</flux:table.column>
                <flux:table.column>Format Preview</flux:table.column>
                <flux:table.column>Current Number</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Description</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            
            <flux:table.rows>
                @forelse($configs as $config)
                    <flux:table.row>
                        <flux:table.cell>
                            <div>
                                <div class="font-medium">{{ ucfirst($config->entity_type) }}</div>
                                <div class="text-sm text-gray-500">{{ $config->entity_type }}</div>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <div class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                {{ $config->getPreview() }}
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <div class="text-center">
                                <div class="text-lg font-bold">{{ $config->current_number }}</div>
                                <div class="text-xs text-gray-500">Next number</div>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ $config->is_active ? 'green' : 'red' }}">
                                {{ $config->is_active ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <div class="max-w-xs">
                                <flux:text class="text-sm">{{ $config->description ?: 'No description' }}</flux:text>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <div class="flex space-x-2">
                                <flux:button variant="ghost" size="sm" href="{{ route('admin.document-numbering.show', $config) }}" title="View Details">
                                    <flux:icon.eye class="h-4 w-4" />
                                </flux:button>
                                <flux:button variant="ghost" size="sm" href="{{ route('admin.document-numbering.edit', $config) }}" title="Edit">
                                    <flux:icon.pencil class="h-4 w-4" />
                                </flux:button>
                                <form method="POST" action="{{ route('admin.document-numbering.toggle-status', $config) }}" class="inline">
                                    @csrf
                                    <flux:button variant="ghost" size="sm" type="submit" title="{{ $config->is_active ? 'Deactivate' : 'Activate' }}">
                                        <flux:icon.power class="h-4 w-4" />
                                    </flux:button>
                                </form>
                                <form method="POST" action="{{ route('admin.document-numbering.reset-number', $config) }}" class="inline" onsubmit="return confirm('Are you sure you want to reset the current number to 1?')">
                                    @csrf
                                    <flux:button variant="ghost" size="sm" type="submit" title="Reset Number">
                                        <flux:icon.arrow-path class="h-4 w-4" />
                                    </flux:button>
                                </form>
                                <form method="POST" action="{{ route('admin.document-numbering.destroy', $config) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this configuration?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button variant="ghost" size="sm" type="submit" title="Delete">
                                        <flux:icon.trash class="h-4 w-4" />
                                    </flux:button>
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8">
                            <flux:icon.document-text class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                            <p class="text-lg font-medium text-gray-500">No configurations found</p>
                            <p class="text-sm text-gray-400">Create your first document numbering configuration</p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        
        @if($configs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $configs->links() }}
            </div>
        @endif
    </flux:card>
</x-layouts.app>
