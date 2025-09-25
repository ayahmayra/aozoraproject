<x-layouts.app :title="__('Edit Subject')">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Edit Subject</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Update subject information</flux:text>
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
            <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <flux:input
                        name="name"
                        label="Subject Name"
                        placeholder="Enter subject name"
                        value="{{ old('name', $subject->name) }}"
                        required
                    />
                    
                    <flux:input
                        name="code"
                        label="Subject Code"
                        placeholder="Enter subject code (e.g., MATH101)"
                        value="{{ old('code', $subject->code) }}"
                        required
                    />
                </div>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea
                        name="description"
                        placeholder="Enter subject description"
                        rows="4"
                    >{{ old('description', $subject->description) }}</flux:textarea>
                </flux:field>

                <flux:field>
                    <flux:label>Assigned Teachers</flux:label>
                    
                    <!-- Display Selected Teachers -->
                    <div id="selected-teachers" class="mb-4">
                        @if(count($assignedTeachers) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($subject->teachers as $teacher)
                                    <div class="flex items-center bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">
                                        <span>{{ $teacher->user->name }}</span>
                                        <button type="button" onclick="removeTeacher({{ $teacher->id }})" class="ml-2 text-blue-600 hover:text-blue-800">
                                            <flux:icon.x-mark class="h-3 w-3" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-gray-500 text-sm">No teachers assigned</div>
                        @endif
                    </div>

                    <!-- Add Teacher Dropdown -->
                    <div class="flex gap-2">
                        <flux:select id="teacher-select" class="flex-1">
                            <option value="">Select a teacher to add...</option>
                            @foreach($teachers as $teacher)
                                @if(!in_array($teacher->id, $assignedTeachers))
                                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }} ({{ $teacher->employee_number }})</option>
                                @endif
                            @endforeach
                        </flux:select>
                        <flux:button type="button" onclick="addTeacher()" variant="primary">
                            <flux:icon.plus class="h-4 w-4 mr-1" />
                            Add
                        </flux:button>
                    </div>
                    
                    <!-- Hidden input to store selected teachers -->
                    <input type="hidden" name="teachers" id="teachers-input" value="{{ implode(',', $assignedTeachers) }}">
                    
                    <flux:description>Select teachers who will teach this subject. Click "Add" to assign a teacher.</flux:description>
                </flux:field>

                <script>
                    let selectedTeachers = {{ json_encode($assignedTeachers) }};
                    let teacherData = {!! json_encode($subject->teachers->mapWithKeys(function($teacher) {
                        return [$teacher->id => [
                            'id' => $teacher->id,
                            'name' => $teacher->user->name,
                            'employee_number' => $teacher->employee_number
                        ]];
                    })) !!};
                    
                    function addTeacher() {
                        const select = document.getElementById('teacher-select');
                        const teacherId = parseInt(select.value);
                        const teacherName = select.options[select.selectedIndex].text;
                        
                        if (teacherId && !selectedTeachers.includes(teacherId)) {
                            selectedTeachers.push(teacherId);
                            
                            // Store teacher data
                            const teacherInfo = teacherName.split(' (');
                            teacherData[teacherId] = {
                                id: teacherId,
                                name: teacherInfo[0],
                                employee_number: teacherInfo[1] ? teacherInfo[1].replace(')', '') : ''
                            };
                            
                            updateTeachersInput();
                            updateSelectedTeachersDisplay();
                            
                            // Remove option from dropdown
                            select.remove(select.selectedIndex);
                            select.value = '';
                        }
                    }
                    
                    function removeTeacher(teacherId) {
                        selectedTeachers = selectedTeachers.filter(id => id !== teacherId);
                        updateTeachersInput();
                        updateSelectedTeachersDisplay();
                        
                        // Add option back to dropdown
                        const select = document.getElementById('teacher-select');
                        const option = document.createElement('option');
                        option.value = teacherId;
                        option.textContent = `${teacherData[teacherId].name} (${teacherData[teacherId].employee_number})`;
                        select.appendChild(option);
                        
                        // Remove from teacherData
                        delete teacherData[teacherId];
                    }
                    
                    function updateTeachersInput() {
                        document.getElementById('teachers-input').value = selectedTeachers.join(',');
                    }
                    
                    function updateSelectedTeachersDisplay() {
                        const container = document.getElementById('selected-teachers');
                        if (selectedTeachers.length === 0) {
                            container.innerHTML = '<div class="text-gray-500 text-sm">No teachers assigned</div>';
                        } else {
                            let html = '<div class="flex flex-wrap gap-2">';
                            selectedTeachers.forEach(teacherId => {
                                if (teacherData[teacherId]) {
                                    html += `
                                        <div class="flex items-center bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">
                                            <span>${teacherData[teacherId].name}</span>
                                            <button type="button" onclick="removeTeacher(${teacherId})" class="ml-2 text-blue-600 hover:text-blue-800">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    `;
                                }
                            });
                            html += '</div>';
                            container.innerHTML = html;
                        }
                    }
                </script>

                <div class="flex justify-end space-x-3">
                    <flux:button variant="ghost" href="{{ route('admin.subjects') }}">
                        Cancel
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        
                        Update Subject
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:card>
</x-layouts.app>
