<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-[#0092b4]/10 p-3">
                    <i class="fa-solid fa-user-injured text-xl text-[#0092b4]"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Patients</p>
                    <p class="text-2xl font-semibold text-gray-800">--</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-amber-100 p-3">
                    <i class="fa-solid fa-calendar-check text-xl text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Appointments Today</p>
                    <p class="text-2xl font-semibold text-gray-800">--</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-green-100 p-3">
                    <i class="fa-solid fa-circle-question text-xl text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Open Enquiries</p>
                    <p class="text-2xl font-semibold text-gray-800">--</p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Welcome to VTA Portal</h3>
        <p class="text-gray-600">
            You are logged in as <strong>{{ Auth::user()->name }}</strong>
            (<span class="capitalize">{{ Auth::user()->role }}</span>).
        </p>
    </div>
</x-app-layout>
