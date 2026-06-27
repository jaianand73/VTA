<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-800">Document Permissions</h2>
        <p class="text-sm text-gray-500">Control which document types each role can view in their portal.</p>
    </div>

    <form method="POST" action="{{ route('settings.document-permissions.update') }}" data-swal="Update all document permissions? This affects what each role can see.">
        @csrf
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document Type</th>
                        @foreach($roles as $role)
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase capitalize">{{ $role }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($documentTypes as $dt)
                    <tr class="even:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $dt->name }}</td>
                        @foreach($roles as $role)
                        @php
                            $key = $dt->id . '-' . $role;
                            $perm = $permissions->get($key);
                            $canView = $perm ? $perm->can_view : false;
                        @endphp
                        <td class="px-4 py-3 text-center">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="hidden" name="permissions[{{ $key }}]" value="0">
                                <input type="checkbox" name="permissions[{{ $key }}]" value="1" {{ $canView ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                            </label>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                Save All Permissions
            </button>
        </div>
    </form>
</div>
