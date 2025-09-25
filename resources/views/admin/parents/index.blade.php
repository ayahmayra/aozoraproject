<x-layouts.app :title="__('Parent User Management')">
    <flux:heading size="xl" level="1">Parent User Management</flux:heading>
    <flux:text class="mb-6 mt-2 text-base">Manage parent users in the system</flux:text>

            @if (session()->has('success'))
                <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
            @endif

            @if (session()->has('error'))
                <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
            @endif

            <div class="space-y-6">
                <!-- Filters -->
                <flux:card>
                    <div class="p-6">
                        <form method="GET" action="{{ route('admin.parents') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <flux:field>
                                    <flux:label>Search Parent Users</flux:label>
                                    <flux:input 
                                        name="search" 
                                        placeholder="Search by name or email..." 
                                        value="{{ request('search') }}"
                                    />
                                </flux:field>
                            </div>
                            <div class="flex items-end">
                                <flux:button variant="outline" type="submit" class="w-full">
                                    Search
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </flux:card>

                <!-- Parents Table -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">Parents ({{ $parents->total() }})</flux:heading>
                            <flux:button variant="primary" href="{{ route('admin.parents.create') }}">
                                <flux:icon.plus class="h-4 w-4 mr-2" />
                                Add Parent
                            </flux:button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>Name</flux:table.column>
                                    <flux:table.column>Email</flux:table.column>
                                    <flux:table.column>Role</flux:table.column>
                                    <flux:table.column>Status</flux:table.column>
                                    <flux:table.column>Created</flux:table.column>
                                    <flux:table.column>Actions</flux:table.column>
                                </flux:table.columns>

                            <flux:table.rows>
                                @forelse($parents as $parent)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <flux:avatar name="{{ $parent->name }}" />
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium">{{ $parent->name }}</div>
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $parent->email }}</flux:table.cell>
                                        <flux:table.cell>
                                            @if($parent->roles->count() > 0)
                                                @foreach($parent->roles as $role)
                                                    <flux:badge variant="primary">{{ ucfirst($role->name) }}</flux:badge>
                                                @endforeach
                                            @else
                                                <span>No role</span>
                                            @endif
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            @if($parent->status === 'active')
                                                <flux:badge color="lime">Active</flux:badge>
                                            @elseif($parent->status === 'pending')
                                                <flux:badge color="yellow">Pending</flux:badge>
                                            @else
                                                <flux:badge color="red">Inactive</flux:badge>
                                            @endif
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $parent->created_at->format('M d, Y') }}</flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex space-x-2">
                                               
                                                <flux:button variant="ghost" size="sm" href="{{ route('admin.parents.edit', $parent) }}" title="Edit User">
                                                    <flux:icon.pencil class="h-4 w-4" />
                                                </flux:button>
                                                <form method="POST" action="{{ route('admin.parents.destroy', $parent) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <flux:button variant="ghost" size="sm" type="submit" title="Delete User">
                                                        <flux:icon.trash class="h-4 w-4" />
                                                    </flux:button>
                                                </form>
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="6" class="text-center ">
                                            No users found.
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($parents->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $parents->links() }}
                        </div>
                    @endif
                </flux:card>
            </div>
</x-layouts.app>