<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#047857">
        <meta name="service-worker-url" content="{{ asset('sw.js') }}">

        <title>@yield('judul', 'Sistem Cuti') - Klinik Utama Medika Antapani</title>
        <link rel="icon" type="image/png" href="{{ asset('logo-cuti.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('icons/icon-180.png') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        @yield('konten')
    </body>
</html>
