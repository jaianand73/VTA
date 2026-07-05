<x-app-layout>
    <x-slot name="header">Edit Patient</x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            @include('patients.form', ['patient' => $patient])
        </div>
    </div>
</x-app-layout>
