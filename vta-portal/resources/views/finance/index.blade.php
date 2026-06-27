<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800">Finance Dashboard</h1>
    </x-slot>

    <div class="grid gap-6 sm:grid-cols-3 mb-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">Associate Invoices</p>
            <p class="mt-2 text-3xl font-semibold text-gray-800">{{ \App\Models\AssociateInvoice::count() }}</p>
            <p class="mt-1 text-sm text-gray-500">Total received</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">VTA Invoices</p>
            <p class="mt-2 text-3xl font-semibold text-gray-800">{{ \App\Models\VtaInvoice::count() }}</p>
            <p class="mt-1 text-sm text-gray-500">Total created</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">Reports</p>
            <p class="mt-2 text-3xl font-semibold text-gray-800">&nbsp;</p>
            <a href="{{ route('finance.reports') }}" class="mt-1 inline-flex items-center text-sm text-[#0092b4] hover:text-[#007a9a]">
                View Reports <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <a href="{{ route('associate-invoices.index') }}" class="rounded-lg border border-gray-200 bg-white p-6 hover:border-[#0092b4] transition-colors">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800">Associate Invoices</h3>
                    <p class="text-sm text-gray-500">Manage invoices received from associates</p>
                </div>
            </div>
        </a>
        <a href="{{ route('vta-invoices.index') }}" class="rounded-lg border border-gray-200 bg-white p-6 hover:border-[#0092b4] transition-colors">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <i class="fa-solid fa-file-invoice text-xl"></i>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800">VTA Invoices</h3>
                    <p class="text-sm text-gray-500">Manage invoices sent to funders</p>
                </div>
            </div>
        </a>
    </div>
</x-app-layout>
