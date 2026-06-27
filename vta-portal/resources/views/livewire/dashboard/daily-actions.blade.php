<div class="rounded-lg border border-gray-200 bg-white p-4">
    <h3 class="mb-3 text-lg font-semibold text-gray-800">Today's Actions</h3>
    <div class="space-y-2">
        <p class="text-sm text-gray-500">Appointments today: <span class="font-medium text-gray-800">{{ $appointmentsToday ?? 0 }}</span></p>
        <p class="text-sm text-gray-500">Follow-ups due: <span class="font-medium text-amber-600">{{ $followUpsDue ?? 0 }}</span></p>
        <p class="text-sm text-gray-500">Invoices to send: <span class="font-medium text-gray-800">{{ $invoicesToSend ?? 0 }}</span></p>
    </div>
</div>
