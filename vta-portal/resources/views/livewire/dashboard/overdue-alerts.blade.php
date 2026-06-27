<div class="rounded-lg border border-red-200 bg-red-50 p-4">
    <h3 class="mb-3 text-lg font-semibold text-red-800">Alerts</h3>
    @forelse($alerts ?? [] as $alert)
    <div class="flex items-center gap-2 text-sm text-red-700">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ $alert }}</span>
    </div>
    @empty
    <p class="text-sm text-gray-500">No overdue items.</p>
    @endforelse
</div>
