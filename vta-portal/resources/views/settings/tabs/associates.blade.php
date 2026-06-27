<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-800">Associates</h2>
        <button onclick="document.getElementById('newAssociateForm').classList.toggle('hidden')"
                class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
            <i class="fa-solid fa-plus"></i> Add Associate
        </button>
    </div>

    <form id="newAssociateForm" method="POST" action="{{ route('settings.associates.store') }}" class="hidden rounded-lg border border-gray-200 bg-gray-50 p-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Name *</label>
                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Region *</label>
                <input type="text" name="region" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Speciality</label>
                <input type="text" name="speciality" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Qualifications</label>
                <input type="text" name="qualifications" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Session Rate (£)</label>
                <input type="number" name="session_rate" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Rate (£/mile)</label>
                <input type="number" name="travel_rate_per_mile" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label>
                <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm"></textarea>
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Save</button>
            <button type="button" onclick="document.getElementById('newAssociateForm').classList.add('hidden')" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Region</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Speciality</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Session Rate</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Travel Rate</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Active</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Portal</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($associates as $assoc)
                <tr class="even:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $assoc->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $assoc->region }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $assoc->speciality ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-600">{{ $assoc->session_rate ? '£' . number_format($assoc->session_rate, 2) : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-600">{{ $assoc->travel_rate_per_mile ? '£' . number_format($assoc->travel_rate_per_mile, 2) : '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($assoc->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($assoc->user_id)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                                <i class="fa-solid fa-check mr-1"></i> Active
                            </span>
                        @else
                            <button onclick="document.getElementById('createLogin-{{ $assoc->id }}').classList.toggle('hidden')"
                                    class="text-xs text-[#0092b4] hover:text-[#007a9a] font-medium">
                                Create Login
                            </button>
                            <form id="createLogin-{{ $assoc->id }}" method="POST" action="{{ route('settings.associates.create-login', $assoc) }}" class="hidden mt-2 flex gap-2" data-swal="Create portal login for {{ $assoc->name }}?">
                                @csrf
                                <input type="email" name="email" placeholder="Email" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-xs">
                                <input type="password" name="password" placeholder="Password" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-xs">
                                <button type="submit" class="rounded bg-[#0092b4] px-2 py-1 text-xs font-medium text-white hover:bg-[#007a9a]">Go</button>
                            </form>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="toggleEdit('associate-{{ $assoc->id }}')" class="text-[#0092b4] hover:text-[#007a9a] text-sm font-medium">Edit</button>
                    </td>
                </tr>
                <tr id="edit-associate-{{ $assoc->id }}" class="hidden">
                    <td colspan="8" class="bg-gray-50 px-4 py-3">
                        <form method="POST" action="{{ route('settings.associates.update', $assoc) }}" class="grid gap-4 sm:grid-cols-3">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $assoc->name }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="email" name="email" value="{{ $assoc->email }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="text" name="phone" value="{{ $assoc->phone }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="text" name="region" value="{{ $assoc->region }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="text" name="speciality" value="{{ $assoc->speciality }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="text" name="qualifications" value="{{ $assoc->qualifications }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="number" name="session_rate" value="{{ $assoc->session_rate }}" step="0.01" min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="number" name="travel_rate_per_mile" value="{{ $assoc->travel_rate_per_mile }}" step="0.01" min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="is_active" value="1" {{ $assoc->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                                    Active
                                </label>
                                <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#007a9a]">Save</button>
                                <button type="button" onclick="toggleEdit('associate-{{ $assoc->id }}')" class="text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
