<x-app-layout>
    <x-slot name="header">Create Patient</x-slot>

    <div class="mx-auto max-w-3xl rounded-lg border border-gray-200 bg-white p-6">
        @include('patients.form', ['patient' => null, 'enquiry' => $enquiry ?? null])
    </div>
</x-app-layout>
