<x-layouts.app :title="__('Create Time Schedule')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('admin.time-schedules.index') }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Time Schedules
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">Create Time Schedule</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Add a new time schedule for a subject and teacher</flux:text>
    </div>

    @if ($errors->any())
        <flux:callout class="mb-6" variant="danger" icon="x-mark">
            <flux:heading size="md" class="mb-2">Please correct the following errors:</flux:heading>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </flux:callout>
    @endif

    <flux:card>
        <div class="px-6 py-4 border-b border-gray-200">
            <flux:heading size="lg">Schedule Information</flux:heading>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.time-schedules.store') }}" class="space-y-6">
                @csrf
                
                <!-- Subject-Teacher Selection -->
                <flux:field>
                    <flux:label for="subject_teacher">Subject & Teacher <span class="text-red-500">*</span></flux:label>
                    <flux:select name="subject_teacher" id="subject_teacher" required>
                        <option value="">Select a subject and teacher</option>
                        @foreach($subjectTeacherOptions as $option)
                            <option value="{{ $option['subject_id'] }}_{{ $option['teacher_id'] ?? 'null' }}" {{ old('subject_teacher') == $option['subject_id'] . '_' . ($option['teacher_id'] ?? 'null') ? 'selected' : '' }}>
                                {{ $option['display_name'] }}
                            </option>
                        @endforeach
                    </flux:select>
                    @error('subject_teacher')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <!-- Hidden input for subject_id -->
                <input type="hidden" name="subject_id" id="subject_id" value="{{ old('subject_id') }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Day of Week -->
                    <flux:field>
                        <flux:label for="day_of_week">Day of Week <span class="text-red-500">*</span></flux:label>
                        <flux:select name="day_of_week" id="day_of_week" required>
                            <option value="">Select a day</option>
                            @foreach($days as $key => $value)
                                <option value="{{ $key }}" {{ old('day_of_week') === $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('day_of_week')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- Start Time -->
                    <flux:field>
                        <flux:label for="start_time">Start Time <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="time" 
                            name="start_time" 
                            id="start_time" 
                            value="{{ old('start_time') }}" 
                            required 
                        />
                        @error('start_time')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- End Time -->
                    <flux:field>
                        <flux:label for="end_time">End Time <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="time" 
                            name="end_time" 
                            id="end_time" 
                            value="{{ old('end_time') }}" 
                            required 
                        />
                        @error('end_time')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Room -->
                <flux:field>
                    <flux:label for="room">Room</flux:label>
                    <flux:input 
                        type="text" 
                        name="room" 
                        id="room" 
                        value="{{ old('room') }}" 
                        placeholder="e.g., Room 101, Lab A, etc."
                        maxlength="50"
                    />
                    @error('room')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <!-- Notes -->
                <flux:field>
                    <flux:label for="notes">Notes</flux:label>
                    <flux:textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        placeholder="Additional notes about this schedule..."
                        maxlength="500"
                    >{{ old('notes') }}</flux:textarea>
                    @error('notes')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <flux:button variant="ghost" href="{{ route('admin.time-schedules.index') }}">
                        Cancel
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        <flux:icon.plus class="h-4 w-4 mr-2" />
                        Create Schedule
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:card>

    <script>
        // Auto-update end time when start time changes
        document.getElementById('start_time').addEventListener('change', function() {
            const startTime = this.value;
            const endTimeInput = document.getElementById('end_time');
            
            if (startTime && !endTimeInput.value) {
                // Set end time to 1 hour after start time
                const start = new Date('2000-01-01T' + startTime);
                start.setHours(start.getHours() + 1);
                endTimeInput.value = start.toTimeString().slice(0, 5);
            }
        });

        // Update hidden subject_id when subject_teacher selection changes
        document.getElementById('subject_teacher').addEventListener('change', function() {
            const selectedValue = this.value;
            if (selectedValue) {
                const subjectId = selectedValue.split('_')[0];
                document.getElementById('subject_id').value = subjectId;
            } else {
                document.getElementById('subject_id').value = '';
            }
        });
    </script>
</x-layouts.app>
