<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-sm text-gray-500">Total Patients</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalPatients ?? '—' }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-sm text-gray-500">Active Treatments</p>
        <p class="text-2xl font-bold text-gray-800">{{ $activeTreatments ?? '—' }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-sm text-gray-500">Needs Review</p>
        <p class="text-2xl font-bold text-amber-600">{{ $needsReview ?? '—' }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-sm text-gray-500">Pending Enquiries</p>
        <p class="text-2xl font-bold text-gray-800">{{ $pendingEnquiries ?? '—' }}</p>
    </div>
</div>
