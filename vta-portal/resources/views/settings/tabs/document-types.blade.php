<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-800">Document Types</h2>
        <button onclick="document.getElementById('newDocumentTypeForm').classList.toggle('hidden')"
                class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
            <i class="fa-solid fa-plus"></i> Add Document Type
        </button>
    </div>

    <form id="newDocumentTypeForm" method="POST" action="{{ route('settings.document-types.store') }}" class="hidden rounded-lg border border-gray-200 bg-gray-50 p-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Name *</label>
                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Description</label>
                <input type="text" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</label>
                <input type="number" name="sort_order" min="0" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Save</button>
            <button type="button" onclick="document.getElementById('newDocumentTypeForm').classList.add('hidden')" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Active</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sort</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($documentTypes as $dt)
                <tr class="even:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $dt->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $dt->description ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($dt->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $dt->sort_order }}</td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="toggleEdit('document-type-{{ $dt->id }}')" class="text-[#0092b4] hover:text-[#007a9a] text-sm font-medium">Edit</button>
                        <form method="POST" action="{{ route('settings.document-types.destroy', $dt) }}" class="inline" data-swal-label="this document type">
                            @csrf @method('DELETE')
                            <button type="submit" class="ml-2 text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                <tr id="edit-document-type-{{ $dt->id }}" class="hidden">
                    <td colspan="5" class="bg-gray-50 px-4 py-3">
                        <form method="POST" action="{{ route('settings.document-types.update', $dt) }}" class="grid gap-4 sm:grid-cols-4">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $dt->name }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="text" name="description" value="{{ $dt->description }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="number" name="sort_order" value="{{ $dt->sort_order }}" min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="is_active" value="1" {{ $dt->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                                    Active
                                </label>
                                <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#007a9a]">Save</button>
                                <button type="button" onclick="toggleEdit('document-type-{{ $dt->id }}')" class="text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
