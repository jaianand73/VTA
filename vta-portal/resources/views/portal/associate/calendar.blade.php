@php
    $header = 'My Calendar';
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
        <p class="text-sm text-gray-500">Your appointments only</p>

        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div id="associate-calendar"></div>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="event-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" onclick="closeModal()"></div>
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-6 pb-4 pt-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Appointment Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-3 text-sm" id="modal-body"></div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex justify-end">
                    <button onclick="closeModal()" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.15/index.global.min.js"></script>

    <script>
        let modal = document.getElementById('event-modal');
        let modalBody = document.getElementById('modal-body');

        function closeModal() {
            modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const calendar = new FullCalendar.Calendar(document.getElementById('associate-calendar'), {
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
                    extraParams: {
                        associate_id: {{ $associate->id }}
                    }
                },
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    modalBody.innerHTML = `
                        <div class="grid grid-cols-2 gap-3">
                            <div><span class="text-gray-500">Patient:</span><br><span class="font-medium">${props.patient || '-'}</span></div>
                            <div><span class="text-gray-500">Activity:</span><br><span class="font-medium">${props.activity || '-'}</span></div>
                            <div><span class="text-gray-500">Status:</span><br><span class="font-medium">${props.status || '-'}</span></div>
                            <div><span class="text-gray-500">Location:</span><br><span class="font-medium">${props.location || '-'}</span></div>
                            <div><span class="text-gray-500">Duration:</span><br><span class="font-medium">${props.duration || 60} min</span></div>
                            ${props.notes ? `<div class="col-span-2"><span class="text-gray-500">Notes:</span><br><span class="font-medium">${props.notes}</span></div>` : ''}
                        </div>
                    `;
                    modal.classList.remove('hidden');
                },
                windowResize: function(view) {
                    if (window.innerWidth < 768) {
                        calendar.changeView('listWeek');
                    }
                }
            });

            calendar.render();
        });
    </script>
    @endpush
</x-app-layout>
