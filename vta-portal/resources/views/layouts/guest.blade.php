<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full font-sans antialiased">
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <a href="/" class="flex justify-center">
                <x-application-logo class="h-12 w-auto fill-current text-[#0092b4]" />
            </a>
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                {{ config('app.name', 'VTA Portal') }}
            </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <div class="bg-white px-6 py-8 shadow rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    Swal.fire({ icon: 'success', title: 'Success', text: {!! json_encode(session('success')) !!}, timer: 4000, toast: true, position: 'top-end', showConfirmButton: false });
    @endif
    @if(session('error'))
    Swal.fire({ icon: 'error', title: 'Error', text: {!! json_encode(session('error')) !!}, timer: 5000, toast: true, position: 'top-end', showConfirmButton: false });
    @endif
    @if($errors->any())
    Swal.fire({ icon: 'error', title: 'Validation Error', text: {!! json_encode($errors->first()) !!}, timer: 6000, toast: true, position: 'top-end', showConfirmButton: false });
    @endif
});
</script>
</body>
</html>
