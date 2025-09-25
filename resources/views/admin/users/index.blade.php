<x-layouts.app :title="__('Admin User Management')">
    <flux:heading size="xl" level="1">Admin User Management</flux:heading>
    <flux:text class="mb-6 mt-2 text-base">Manage admin users in the system</flux:text>

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
                        <form method="GET" action="{{ route('admin.users') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <flux:field>
                                    <flux:label>Search Admin Users</flux:label>
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

                <!-- Users Table -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">Users ({{ $users->total() }})</flux:heading>
                            <flux:button variant="primary" href="{{ route('admin.users.create') }}">
                                <flux:icon.plus class="h-4 w-4 mr-2" />
                                Add User
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
                                @forelse($users as $user)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <flux:avatar name="{{ $user->name }}" />
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium">{{ $user->name }}</div>
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                                        <flux:table.cell>
                                            @if($user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <flux:badge variant="primary">{{ ucfirst($role->name) }}</flux:badge>
                                                @endforeach
                                            @else
                                                <span>No role</span>
                                            @endif
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            @if($user->status === 'active')
                                                <flux:badge color="lime">Active</flux:badge>
                                            @elseif($user->status === 'pending')
                                                <flux:badge color="yellow">Pending</flux:badge>
                                            @else
                                                <flux:badge color="red">Inactive</flux:badge>
                                            @endif
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $user->created_at->format('M d, Y') }}</flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex space-x-2">
                                                <flux:button variant="ghost" size="sm" href="{{ route('admin.users.edit', $user) }}">
                                                    <flux:icon.pencil-square class="h-4 w-4" />
                                                </flux:button>
                                                
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <flux:button variant="ghost" size="sm" type="submit">
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
                    @if($users->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $users->links() }}
                        </div>
                    @endif
                </flux:card>
            </div>
</x-layouts.app>