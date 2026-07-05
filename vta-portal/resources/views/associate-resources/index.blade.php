@php $header = 'Associate Resources'; @endphp
<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full" style="background:#e0f2fe;">
                <i class="fa-solid fa-graduation-cap text-2xl" style="color:#0092b4;"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Associate CPD &amp; Training Platform</h2>
            <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                This module will provide associates with access to CPD resources, training materials, and professional development content. Currently under development.
            </p>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium" style="background:#fef3c7;color:#92400e;">
                <i class="fa-solid fa-clock mr-1"></i> Coming Soon
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Planned Features</h3>
            <ul class="space-y-3">
                <li class="flex items-start gap-3 text-sm text-gray-600">
                    <i class="fa-solid fa-circle-check mt-0.5 text-gray-300"></i>
                    <span>CPD training modules and course materials for VTA associates</span>
                </li>
                <li class="flex items-start gap-3 text-sm text-gray-600">
                    <i class="fa-solid fa-circle-check mt-0.5 text-gray-300"></i>
                    <span>Resource library: clinical guidelines, protocols, and documentation templates</span>
                </li>
                <li class="flex items-start gap-3 text-sm text-gray-600">
                    <i class="fa-solid fa-circle-check mt-0.5 text-gray-300"></i>
                    <span>Training completion tracking and certification records</span>
                </li>
                <li class="flex items-start gap-3 text-sm text-gray-600">
                    <i class="fa-solid fa-circle-check mt-0.5 text-gray-300"></i>
                    <span>Announcements and updates relevant to associates</span>
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>
