<x-layouts.app :title="__('Time Schedules Calendar')">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <flux:heading size="xl" level="1">Time Schedules Calendar</flux:heading>
                <flux:text class="mb-6 mt-2 text-base">Professional calendar view with FullCalendar.js</flux:text>
            </div>
            <div class="flex space-x-3">
                <flux:button variant="ghost" href="{{ route('admin.time-schedules.index') }}">
                    <flux:icon.table-cells class="h-4 w-4 mr-2" />
                    Table View
                </flux:button>
                <flux:button variant="primary" href="{{ route('admin.time-schedules.create') }}">
                    <flux:icon.plus class="h-4 w-4 mr-2" />
                    Add Schedule
                </flux:button>
            </div>
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
            <form method="GET" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <flux:input
                    name="search"
                    label="Search Schedules"
                    placeholder="Search by subject, teacher, or room..."
                    value="{{ request('search') }}"
                />
                
                <flux:field>
                    <flux:label>Day of Week</flux:label>
                    <flux:select name="day">
                        <option value="">All Days</option>
                        @foreach($days as $key => $value)
                            <option value="{{ $key }}" {{ request('day') === $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </flux:select>
                </flux:field>
                
                <div class="flex items-end space-x-3">
                    <flux:button variant="primary" type="submit">
                        <flux:icon.magnifying-glass class="h-4 w-4 mr-2" />
                        Search
                    </flux:button>
                    <flux:button variant="ghost" href="{{ route('admin.time-schedules.calendar-fullcalendar') }}">
                        Clear
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:card>

    <!-- FullCalendar Container -->
    <flux:card>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <flux:heading size="lg">Weekly Schedule Calendar</flux:heading>
                <div class="flex space-x-2">
                    <flux:button variant="ghost" size="sm" id="prev-week">
                        <flux:icon.chevron-left class="h-4 w-4" />
                    </flux:button>
                    <flux:button variant="ghost" size="sm" id="today">
                        Today
                    </flux:button>
                    <flux:button variant="ghost" size="sm" id="next-week">
                        <flux:icon.chevron-right class="h-4 w-4" />
                    </flux:button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div id="calendar" style="min-height: 400px; width: 100%; background: white; border: 1px solid #e5e7eb; border-radius: 8px;">
                <div style="padding: 20px; text-align: center; color: #666;">
                    Loading calendar...
                </div>
            </div>
        </div>
    </flux:card>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <flux:card>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <flux:icon.calendar class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <flux:text class="text-sm font-medium text-gray-500">Total Schedules</flux:text>
                        <flux:text class="text-2xl font-bold text-gray-900">{{ $schedules->count() }}</flux:text>
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <flux:icon.book-open class="h-6 w-6 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <flux:text class="text-sm font-medium text-gray-500">Unique Subjects</flux:text>
                        <flux:text class="text-2xl font-bold text-gray-900">{{ $schedules->pluck('subject_id')->unique()->count() }}</flux:text>
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <flux:icon.user-group class="h-6 w-6 text-purple-600" />
                    </div>
                    <div class="ml-4">
                        <flux:text class="text-sm font-medium text-gray-500">Teachers Involved</flux:text>
                        <flux:text class="text-2xl font-bold text-gray-900">{{ $schedules->pluck('teacher_id')->filter()->unique()->count() }}</flux:text>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- FullCalendar Implementation with Real Data -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            console.log('FullCalendar available:', typeof FullCalendar !== 'undefined');
            
            // Real events from controller
            const events = @json($events);
            console.log('Real events from controller:', events);
            
            // Add test events for debugging
            const testEvents = [
                {
                    id: 'test-1',
                    title: 'Test Event 1',
                    start: '2024-09-26T08:00:00',
                    end: '2024-09-26T10:00:00',
                    backgroundColor: '#3B82F6',
                    borderColor: '#3B82F6',
                    textColor: '#ffffff'
                },
                {
                    id: 'test-2',
                    title: 'Test Event 2',
                    start: '2024-09-27T09:00:00',
                    end: '2024-09-27T11:00:00',
                    backgroundColor: '#10B981',
                    borderColor: '#10B981',
                    textColor: '#ffffff'
                }
            ];
            
            // Combine real and test events
            const allEvents = [...events, ...testEvents];
            console.log('All events (real + test):', allEvents);

            const calendarEl = document.getElementById('calendar');
            console.log('Calendar element found:', calendarEl);
            
            if (!calendarEl) {
                console.error('Calendar element not found!');
                return;
            }
            
            if (typeof FullCalendar === 'undefined') {
                console.error('FullCalendar library not loaded!');
                return;
            }
            
            // Clear loading text
            calendarEl.innerHTML = '';
            
            console.log('Creating FullCalendar instance...');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                aspectRatio: 1.8,
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                slotDuration: '00:30:00',
                slotLabelInterval: '01:00:00',
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                expandRows: true,
                events: allEvents,
                eventDidMount: function(info) {
                    // Add special class for short duration events (less than 1 hour)
                    const start = new Date(info.event.start);
                    const end = new Date(info.event.end);
                    const duration = end - start;
                    const oneHour = 60 * 60 * 1000; // 1 hour in milliseconds
                    
                    if (duration < oneHour) {
                        info.el.classList.add('fc-short');
                    }
                }
            });

            console.log('Rendering calendar...');
            calendar.render();
            
            console.log('Calendar rendered successfully');
            console.log('Events in calendar:', calendar.getEvents().length);
            
            // Navigation buttons
            const prevBtn = document.getElementById('prev-week');
            const nextBtn = document.getElementById('next-week');
            const todayBtn = document.getElementById('today');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    calendar.prev();
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    calendar.next();
                });
            }
            
            if (todayBtn) {
                todayBtn.addEventListener('click', function() {
                    calendar.today();
                });
            }
        });
    </script>
    
    <style>
        #calendar {
            min-height: 400px !important;
            width: 100% !important;
            background: white !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
        }
        
        .fc-event {
            border-radius: 3px !important;
            border: none !important;
            font-size: 0.7rem !important;
            font-weight: 500 !important;
            padding: 2px 4px !important;
            margin: 1px !important;
            line-height: 1.1 !important;
            white-space: normal !important;
            word-wrap: break-word !important;
        }
        
        .fc-event-title {
            font-weight: 600 !important;
            font-size: 0.7rem !important;
            line-height: 1.1 !important;
        }
        
        .fc-timegrid-slot {
            height: auto !important;
            min-height: 2rem !important;
        }
        
        .fc-timegrid-event-harness {
            height: auto !important;
        }
        
        .fc-timegrid-axis {
            font-size: 0.8rem !important;
        }
        
        .fc-col-header-cell {
            font-size: 0.85rem !important;
            padding: 0.75rem 0.5rem !important;
        }
        
        .fc-col-header-cell {
            background-color: #f8fafc !important;
            font-weight: 600 !important;
        }
        
        /* Special styling for short duration events */
        .fc-event.fc-short {
            font-size: 0.65rem !important;
            padding: 1px 3px !important;
            line-height: 1.0 !important;
        }
        
        .fc-event.fc-short .fc-event-title {
            font-size: 0.65rem !important;
            line-height: 1.0 !important;
        }
        
        /* Ensure minimum height for events */
        .fc-timegrid-event-harness {
            min-height: 1.5rem !important;
        }
        
        .fc-timegrid-axis {
            background-color: #f8fafc !important;
        }
        
        .fc-scrollgrid {
            border: 1px solid #e5e7eb !important;
        }
        
        .fc-timegrid-event-harness {
            border-radius: 4px !important;
        }
        
        .fc {
            font-family: inherit !important;
        }
        
        .fc-toolbar {
            margin-bottom: 1rem !important;
        }
        
        .fc-button {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
        }
        
        .fc-button:hover {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }
    </style>
</x-layouts.app>