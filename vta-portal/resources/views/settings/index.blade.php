@php
    $tabs = [
        'activity-types' => ['label' => 'Activity Types', 'icon' => 'fa-arrows-rotate'],
        'document-types' => ['label' => 'Document Types', 'icon' => 'fa-file-lines'],
        'document-permissions' => ['label' => 'Document Permissions', 'icon' => 'fa-lock'],
        'companies' => ['label' => 'Companies', 'icon' => 'fa-building'],
        'associates' => ['label' => 'Associates', 'icon' => 'fa-user-md'],
        'users' => ['label' => 'Users', 'icon' => 'fa-users'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800">Settings</h1>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            @foreach($tabs as $key => $tabInfo)
                <a href="{{ route('settings.index', ['tab' => $key]) }}" class="inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium {{ $key === $tab ? 'border-[#0092b4] text-[#0092b4]' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    <i class="fa-solid {{ $tabInfo['icon'] }}"></i>
                    {{ $tabInfo['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    @if($tab === 'activity-types')
        @include('settings.tabs.activity-types')
    @elseif($tab === 'document-types')
        @include('settings.tabs.document-types')
    @elseif($tab === 'document-permissions')
        @include('settings.tabs.document-permissions')
    @elseif($tab === 'companies')
        @include('settings.tabs.companies')
    @elseif($tab === 'associates')
        @include('settings.tabs.associates')
    @elseif($tab === 'users')
        @include('settings.tabs.users')
    @endif

    @push('scripts')
    <script>
        function toggleEdit(id) {
            const row = document.getElementById('edit-' + id);
            if (row) row.classList.toggle('hidden');
        }
    </script>
    @endpush
</x-app-layout>
