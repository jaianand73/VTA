@php
    $header = 'Appointments Calendar';
@endphp

<x-app-layout>
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.15/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.15/main.min.css" rel="stylesheet">
    <style>
        .fc-event { cursor: pointer; }
        .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 600 !important; }
    </style>
    @endpush

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Team calendar — all appointments across all associates</p>
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                <i class="fa-solid fa-plus"></i>
                Add Appointment
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-1 space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Filters</h3>

                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Associate</label>
                            <select id="filter-associate" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="">All Associates</option>
                                @foreach($associates as $associate)
                                <option value="{{ $associate->id }}">{{ $associate->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type</label>
                            <select id="filter-activity" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="">All Activities</option>
                                @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button id="filter-apply" class="w-full rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                            Apply Filters
                        </button>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-1">Appointment Colours</h3>
                    <p class="text-xs text-gray-400 mb-3">Each colour shows an appointment's current status.</p>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full bg-blue-500"></span><span><strong>Scheduled</strong> — booked, not yet taken place</span></div>
                        <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full bg-green-500"></span><span><strong>Completed</strong> — appointment has taken place</span></div>
                        <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full bg-red-500"></span><span><strong>Cancelled</strong> — appointment was cancelled</span></div>
                        <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full bg-amber-500"></span><span><strong>DNA</strong> — patient Did Not Attend</span></div>
                        <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full shrink-0" style="background-color:#059669"></span><span><strong>Referral Session</strong> — first assessment session (pre-patient)</span></div>
                        <div class="flex items-center gap-2 pt-2 border-t border-gray-100"><span class="inline-block h-3 w-3 rounded-full shrink-0" style="background-color:#8B5CF6"></span><span><strong>Follow-up</strong> — a scheduled follow-up logged under Associate Communications</span></div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Detail Modal -->
    <div id="event-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" onclick="closeModal()"></div>
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-6 pb-4 pt-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Appointment Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-3 text-sm" id="modal-body">
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3">
                    <button onclick="closeModal()" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Close</button>
                    <button id="modal-mark-done" class="hidden rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Mark Done</button>
                    <a id="modal-view-link" href="#" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">View Details</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.15/index.global.min.js"></script>

    <script>
        let calendar;
        let modal = document.getElementById('event-modal');
        let modalBody = document.getElementById('modal-body');
        let modalViewLink = document.getElementById('modal-view-link');
        let modalMarkDoneBtn = document.getElementById('modal-mark-done');

        function closeModal() {
            modal.classList.add('hidden');
        }

        modalMarkDoneBtn.addEventListener('click', function() {
            const url = this.dataset.completeUrl;
            const eventId = this.dataset.eventId;
            if (!url) return;

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(function() {
                const ev = calendar.getEventById(eventId);
                if (ev) ev.remove();
                closeModal();
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: window.innerWidth < 768 ? 'listWeek' : 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                height: 'auto',
                events: {
                    url: '{{ route("appointments.events") }}',
                    method: 'GET',
                    extraParams: function() {
                        return {
                            associate_id: document.getElementById('filter-associate').value,
                            activity_type_id: document.getElementById('filter-activity').value,
                        };
                    },
                    failure: function() {
                        console.error('Failed to fetch calendar events');
                    }
                },
                eventClick: function(info) {
                    const props = info.event.extendedProps;

                    if (props.type === 'follow_up') {
                        document.getElementById('modal-title').textContent = 'Follow-up Reminder';
                        modalBody.innerHTML = `
                            <div class="grid grid-cols-1 gap-3">
                                <div><span class="text-gray-500">Contact:</span><br><span class="font-medium">${props.contact || '-'}</span></div>
                                <div><span class="text-gray-500">Subject:</span><br><span class="font-medium">${props.subject || '-'}</span></div>
                                ${props.notes ? `<div><span class="text-gray-500">Notes:</span><br><span class="font-medium">${props.notes}</span></div>` : ''}
                            </div>
                        `;
                        modalViewLink.href = props.url || '#';
                        modalViewLink.textContent = 'View Record';
                        modalMarkDoneBtn.classList.remove('hidden');
                        modalMarkDoneBtn.dataset.completeUrl = props.completeUrl;
                        modalMarkDoneBtn.dataset.eventId = info.event.id;
                    } else if (props.type === 'referral_session') {
                        document.getElementById('modal-title').textContent = 'Referral Assessment Session';
                        modalBody.innerHTML = `
                            <div class="grid grid-cols-2 gap-3">
                                <div><span class="text-gray-500">Patient:</span><br><span class="font-medium">${props.patient || '-'}</span></div>
                                <div><span class="text-gray-500">Ref:</span><br><span class="font-medium font-mono text-xs">${props.ref || '-'}</span></div>
                                <div><span class="text-gray-500">Associate:</span><br><span class="font-medium">${props.associate || '-'}</span></div>
                                <div><span class="text-gray-500">Activity:</span><br><span class="font-medium">${props.activity || '-'}</span></div>
                                <div><span class="text-gray-500">Location:</span><br><span class="font-medium">${props.location || '-'}</span></div>
                                <div><span class="text-gray-500">Duration:</span><br><span class="font-medium">${props.duration ? props.duration + ' min' : '—'}</span></div>
                                ${props.notes ? `<div class="col-span-2"><span class="text-gray-500">Notes:</span><br><span class="font-medium">${props.notes}</span></div>` : ''}
                            </div>
                        `;
                        modalViewLink.href = props.url || '#';
                        modalViewLink.textContent = 'View Referral';
                        modalMarkDoneBtn.classList.add('hidden');
                    } else {
                        document.getElementById('modal-title').textContent = 'Appointment Details';
                        modalBody.innerHTML = `
                            <div class="grid grid-cols-2 gap-3">
                                <div><span class="text-gray-500">Patient:</span><br><span class="font-medium">${props.patient || '-'}</span></div>
                                <div><span class="text-gray-500">Associate:</span><br><span class="font-medium">${props.associate || '-'}</span></div>
                                <div><span class="text-gray-500">Activity:</span><br><span class="font-medium">${props.activity || '-'}</span></div>
                                <div><span class="text-gray-500">Status:</span><br><span class="font-medium">${props.status || '-'}</span></div>
                                <div><span class="text-gray-500">Location:</span><br><span class="font-medium">${props.location || '-'}</span></div>
                                <div><span class="text-gray-500">Duration:</span><br><span class="font-medium">${props.duration || 60} min</span></div>
                                ${props.notes ? `<div class="col-span-2"><span class="text-gray-500">Notes:</span><br><span class="font-medium">${props.notes}</span></div>` : ''}
                            </div>
                        `;
                        modalViewLink.href = props.url || `/appointments/${info.event.id}`;
                        modalViewLink.textContent = 'View Details';
                        modalMarkDoneBtn.classList.add('hidden');
                    }

                    modal.classList.remove('hidden');
                },
                eventDidMount: function(info) {
                    info.el.setAttribute('title', info.event.title);
                },
                windowResize: function(view) {
                    if (window.innerWidth < 768) {
                        calendar.changeView('listWeek');
                    }
                }
            });

            calendar.render();

            document.getElementById('filter-apply').addEventListener('click', function() {
                calendar.refetchEvents();
            });
        });
    </script>
    @endpush
</x-app-layout>
