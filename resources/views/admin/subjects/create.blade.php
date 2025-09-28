<x-layouts.app :title="__('Create Subject')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('admin.subjects.index') }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Subjects
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">Create New Subject</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Add a new subject to the system</flux:text>
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

    <flux:card>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.subjects.store') }}" class="space-y-6">
                @csrf
                
                <flux:input
                    name="name"
                    label="Subject Name"
                    placeholder="Enter subject name"
                    value="{{ old('name') }}"
                    required
                />
                
                <flux:field>
                    <flux:label>Subject Code</flux:label>
                    <flux:input
                        name="code"
                        placeholder="Leave empty for auto-generation"
                        value="{{ old('code') }}"
                        readonly
                    />
                    <flux:description>Subject code will be automatically generated based on document numbering configuration</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea
                        name="description"
                        placeholder="Enter subject description"
                        rows="4"
                    >{{ old('description') }}</flux:textarea>
                </flux:field>

                <div class="flex justify-end space-x-3">
                    <flux:button variant="ghost" href="{{ route('admin.subjects.index') }}">
                        Cancel
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        <flux:icon.plus class="h-4 w-4 mr-2" />
                        Create Subject
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:card>
</x-layouts.app>