<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-800">Users</h2>
        <button onclick="document.getElementById('newUserForm').classList.toggle('hidden')"
                class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
            <i class="fa-solid fa-plus"></i> Add User
        </button>
    </div>

    <form id="newUserForm" method="POST" action="{{ route('settings.users.store') }}" class="hidden rounded-lg border border-gray-200 bg-gray-50 p-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Name *</label>
                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Email *</label>
                <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Role *</label>
                <select name="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="associate">Associate</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Temporary Password *</label>
                <input type="password" name="password" required min="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Create User</button>
            <button type="button" onclick="document.getElementById('newUserForm').classList.add('hidden')" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Last Login</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Active</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($users as $u)
                <tr class="even:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $u->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $u->email }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize
                            {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($u->role === 'staff' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $u->role }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $u->last_login_at ? $u->last_login_at->format('d/m/Y H:i') : 'Never' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($u->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="toggleEdit('user-{{ $u->id }}')" class="text-[#0092b4] hover:text-[#007a9a] text-sm font-medium">Edit</button>
                        <button onclick="document.getElementById('resetPw-{{ $u->id }}').classList.toggle('hidden')" class="ml-2 text-amber-600 hover:text-amber-800 text-sm font-medium">Reset Password</button>
                    </td>
                </tr>
                <tr id="edit-user-{{ $u->id }}" class="hidden">
                    <td colspan="6" class="bg-gray-50 px-4 py-3">
                        <form method="POST" action="{{ route('settings.users.update', $u) }}" class="grid gap-4 sm:grid-cols-4">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $u->name }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="email" name="email" value="{{ $u->email }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <select name="role" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ $u->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="associate" {{ $u->role === 'associate' ? 'selected' : '' }}>Associate</option>
                            </select>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="is_active" value="1" {{ $u->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                                    Active
                                </label>
                                <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#007a9a]">Save</button>
                                <button type="button" onclick="toggleEdit('user-{{ $u->id }}')" class="text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                            </div>
                        </form>
                    </td>
                </tr>
                <tr id="resetPw-{{ $u->id }}" class="hidden">
                    <td colspan="6" class="bg-gray-50 px-4 py-3">
                        <form method="POST" action="{{ route('settings.users.reset-password', $u) }}" class="flex items-center gap-3" data-swal="Reset password for {{ $u->name }}?">
                            @csrf
                            <input type="password" name="password" placeholder="New password" required min="8" class="block w-64 rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="password" name="password_confirmation" placeholder="Confirm password" required min="8" class="block w-64 rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <button type="submit" class="rounded-lg bg-amber-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-amber-700">Reset</button>
                            <button type="button" onclick="document.getElementById('resetPw-{{ $u->id }}').classList.add('hidden')" class="text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
