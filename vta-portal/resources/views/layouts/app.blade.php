<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-gray-700">
    <div x-data="{ sidebarOpen: false }" class="flex h-full overflow-hidden">

        <div x-show="sidebarOpen" x-cloak @@click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/50 lg:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <aside class="fixed inset-y-0 left-0 z-30 flex w-64 shrink-0 flex-col border-r border-gray-200 bg-white lg:static lg:translate-x-0" x-cloak x-show="sidebarOpen || window.innerWidth >= 1024" x-transition:enter="transition-transform ease-in-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition-transform ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">

            <div class="flex h-16 items-center gap-2 border-b border-gray-200 px-6">
                <x-application-logo class="h-8 w-auto fill-current text-[#0092b4]" />
                <span class="text-lg font-semibold text-gray-800">{{ config('app.name', 'VTA Portal') }}</span>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1 text-sm font-medium">
                @if(in_array(Auth::user()->role, ['admin', 'staff']))
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                    Dashboard
                </a>
                @endif

                @if(in_array(Auth::user()->role, ['associate']))
                <a href="{{ route('associate-portal.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('associate-portal.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-user-md w-5 text-center"></i>
                    My Portal
                </a>
                @endif

                @if(in_array(Auth::user()->role, ['case_manager']))
                <a href="{{ route('case-manager-portal.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('case-manager-portal.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-briefcase w-5 text-center"></i>
                    My Portal
                </a>
                @endif

                @if(in_array(Auth::user()->role, ['admin', 'staff']))
                <a href="{{ route('enquiries.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('enquiries.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-circle-question w-5 text-center"></i>
                    Enquiries
                </a>
                @endif

                @if(in_array(Auth::user()->role, ['admin', 'staff']))
                <a href="{{ route('patients.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('patients.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-user-injured w-5 text-center"></i>
                    Patients
                </a>

                <a href="{{ route('companies.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('companies.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-building w-5 text-center"></i>
                    Companies
                </a>

                <div class="border-t border-gray-200 my-2">
                    <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Clinical</p>
                </div>

                <a href="{{ route('appointments.calendar') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('appointments.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                    Appointments
                </a>

                <a href="{{ route('case-notes.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('case-notes.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-note-sticky w-5 text-center"></i>
                    Case Notes
                </a>
                @endif

                @if(in_array(Auth::user()->role, ['admin']))
                <div class="border-t border-gray-200 my-2">
                    <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Finance</p>
                </div>

                <a href="{{ route('associate-invoices.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('associate-invoices.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                    Associate Invoices
                </a>
                <a href="{{ route('vta-invoices.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('vta-invoices.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-file-invoice w-5 text-center"></i>
                    VTA Invoices
                </a>
                <a href="{{ route('finance.reports') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('finance.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i>
                    Reports
                </a>
                @endif

                <div class="border-t border-gray-200 my-2">
                    <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Admin</p>
                </div>

                @if(in_array(Auth::user()->role, ['admin', 'staff']))
                <a href="{{ route('email-intake.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('email-intake.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-envelope-open-text w-5 text-center"></i>
                    Email Intake
                </a>
                @endif

                @if(in_array(Auth::user()->role, ['admin']))
                <div class="border-t border-gray-200 my-2">
                    <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Settings</p>
                </div>

                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('settings.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-gear w-5 text-center"></i>
                    Settings
                </a>
                @endif

                <div class="border-t border-gray-200 my-2">
                    <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Support</p>
                </div>

                <a href="{{ url('VTA_Portal_UAT_Guide.html') }}" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-100">
                    <i class="fa-solid fa-circle-question w-5 text-center"></i>
                    Help
                </a>
            </nav>

            <div class="border-t border-gray-200 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-red-600">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                        Log Out
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex flex-1 flex-col overflow-hidden">
            <header class="flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 lg:px-6">
                <button @@click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1"></div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">{{ Auth::user()->name }}</span>
                    <span class="inline-flex items-center rounded-full bg-[#0092b4]/10 px-2.5 py-0.5 text-xs font-medium text-[#0092b4] capitalize">
                        {{ Auth::user()->role }}
                    </span>
                    <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-user-circle text-xl"></i>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @isset($header)
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">{{ $header }}</h1>
                </div>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
    @livewireScripts

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Toast from flash messages ──
    @if(session('success'))
    Swal.fire({
        icon: 'success', title: 'Success', text: {!! json_encode(session('success')) !!},
        timer: 4000, toast: true, position: 'top-end', showConfirmButton: false
    });
    @endif
    @if(session('error'))
    Swal.fire({
        icon: 'error', title: 'Error', text: {!! json_encode(session('error')) !!},
        timer: 5000, toast: true, position: 'top-end', showConfirmButton: false
    });
    @endif
    @if($errors->any())
    Swal.fire({
        icon: 'error', title: 'Validation Error',
        text: {!! json_encode($errors->first()) !!},
        timer: 6000, toast: true, position: 'top-end', showConfirmButton: false
    });
    @endif

    // ── Form handling ──
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var method = (this.querySelector('input[name="_method"]')?.value || this.method || 'POST').toUpperCase();
            var isDelete = method === 'DELETE';
            var confirmText = this.getAttribute('data-swal');

            if (confirmText) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, proceed',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var btn = form.querySelector('button[type="submit"]');
                        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Processing...'; }
                        form.submit();
                    }
                });
                return;
            }

            if (isDelete) {
                e.preventDefault();
                var label = this.getAttribute('data-swal-label') || 'this item';
                Swal.fire({
                    title: 'Delete ' + label + '?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var btn = form.querySelector('button[type="submit"]');
                        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...'; }
                        form.submit();
                    }
                });
                return;
            }

            // Show processing state for non-GET forms
            if (method !== 'GET') {
                var btn = this.querySelector('button[type="submit"]');
                if (btn && !btn.disabled) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Processing...';
                }
            }
        });
    });
});
</script>
</body>
</html>
