<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Referrals</h2>
                <p class="text-sm text-gray-500 mt-0.5">Stage 2 — Known patient, awaiting approval and assessment</p>
            </div>
            <a href="{{ route('enquiries.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#0092b4;">
                <i class="fa-solid fa-arrow-left"></i> New Referral? Start from Enquiry
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, ref, postcode…"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]" style="min-width:220px;">
            </div>
            <div>
                <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    <option value="">All Statuses</option>
                    @foreach(['New','In Progress','Awaiting Go-ahead','Assessment','Proposal Submitted','Approved','Not Proceeding'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg px-4 py-2 text-sm font-medium text-white" style="background:#0092b4;">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('referrals.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-50">Clear</a>
            @endif
        </form>

        {{-- Status summary chips --}}
        @php
        $statusColors = [
            'New'               => ['bg'=>'#eff6ff','txt'=>'#1d4ed8','border'=>'#bfdbfe'],
            'In Progress'       => ['bg'=>'#fefce8','txt'=>'#854d0e','border'=>'#fde68a'],
            'Awaiting Go-ahead' => ['bg'=>'#fff7ed','txt'=>'#c2410c','border'=>'#fed7aa'],
            'Assessment'        => ['bg'=>'#f0fdf4','txt'=>'#15803d','border'=>'#bbf7d0'],
            'Proposal Submitted'=> ['bg'=>'#fdf4ff','txt'=>'#7e22ce','border'=>'#e9d5ff'],
            'Approved'          => ['bg'=>'#f0fdfa','txt'=>'#0f766e','border'=>'#99f6e4'],
            'Not Proceeding'    => ['bg'=>'#fef2f2','txt'=>'#b91c1c','border'=>'#fecaca'],
        ];
        @endphp

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            @if($referrals->isEmpty())
                <div class="px-8 py-16 text-center text-gray-400">
                    <i class="fa-solid fa-file-medical text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm">No referrals found.</p>
                    <a href="{{ route('enquiries.index') }}" class="mt-3 inline-block text-sm font-medium" style="color:#0092b4;">Promote an enquiry to create a referral</a>
                </div>
            @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Ref</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Patient</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Postcode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Case Manager</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Associate</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Created</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($referrals as $referral)
                    @php $sc = $statusColors[$referral->status] ?? ['bg'=>'#f3f4f6','txt'=>'#374151','border'=>'#d1d5db']; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $referral->referral_ref ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $referral->patient_full_name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $referral->patient_postcode ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($referral->caseManager)
                                {{ $referral->caseManager->first_name }} {{ $referral->caseManager->last_name }}
                                <span class="text-xs text-gray-400 block">{{ $referral->company?->name }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $referral->associate?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                style="background:{{ $sc['bg'] }};color:{{ $sc['txt'] }};border:1px solid {{ $sc['border'] }};">
                                {{ $referral->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $referral->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('referrals.show', $referral) }}" class="text-sm font-medium" style="color:#0092b4;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $referrals->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
