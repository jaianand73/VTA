@php $header = 'My Referrals — First Assessments'; @endphp

<x-app-layout>
    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-sm text-green-800">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            @if($referrals->isEmpty())
                <div class="px-8 py-16 text-center text-gray-400">
                    <i class="fa-solid fa-file-medical text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm">No active referrals assigned to you.</p>
                </div>
            @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Ref</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Patient</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Go-ahead Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Case Manager</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($referrals as $ref)
                    @php
                    $sc = [
                        'Assessment'       => ['bg'=>'#f0fdf4','txt'=>'#15803d','border'=>'#bbf7d0'],
                        'Proposal Submitted'=> ['bg'=>'#fdf4ff','txt'=>'#7e22ce','border'=>'#e9d5ff'],
                        'Approved'         => ['bg'=>'#f0fdfa','txt'=>'#0f766e','border'=>'#99f6e4'],
                    ][$ref->status] ?? ['bg'=>'#f3f4f6','txt'=>'#374151','border'=>'#d1d5db'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $ref->referral_ref ?? '#'.$ref->id }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $ref->patient_full_name }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                style="background:{{ $sc['bg'] }};color:{{ $sc['txt'] }};border:1px solid {{ $sc['border'] }};">
                                {{ $ref->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $ref->visit_approved_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $ref->caseManager?->first_name }} {{ $ref->caseManager?->last_name }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('associate-portal.referral', $ref) }}"
                                class="text-xs font-medium" style="color:#0092b4;">View →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

    </div>
</x-app-layout>
