<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/netafarm-logo.png') }}">
    <title>{{ config('app.name', 'Netafarm') }}</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900 antialiased selection:bg-blue-600 selection:text-white">
    <div class="grid min-h-screen lg:grid-cols-[.9fr_1.1fr]">
        <aside class="relative hidden overflow-hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(245,158,11,.16),transparent_34%),linear-gradient(145deg,#020617,#172554)]"></div>
            <div class="relative">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-xl font-bold tracking-tight">
                    <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-9 w-9 shrink-0 object-contain">
                    <span>Neta<span class="text-sky-400">farm</span></span>
                </a><p class="mt-2 text-sm text-slate-400">CV. Netafarm Indolestari - Sidoarjo, Jawa Timur</p></div>
            <div class="relative max-w-md"><p class="text-sm font-semibold uppercase tracking-[.24em] text-sky-400">Ruang kerja lapangan</p><h1 class="mt-5 text-4xl font-semibold leading-tight">Semua aktivitas sales, dalam satu pandangan.</h1><p class="mt-5 leading-7 text-slate-400">Pantau kunjungan, outlet, pesanan, dan pencapaian tim dengan data yang siap digunakan.</p></div>
            <p class="relative text-xs text-slate-500">&copy; {{ date('Y') }} MotorisTrack</p>
        </aside>
        <main class="flex min-h-screen items-center justify-center bg-slate-100 px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-xl font-bold tracking-tight text-slate-950">
                        <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-9 w-9 shrink-0 object-contain">
                        <span>Neta<span class="text-blue-600">farm</span></span>
                    </a><p class="mt-2 text-sm text-slate-500">CV. Netafarm Indolestari - Sidoarjo, Jawa Timur</p></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-900/5 sm:p-9">{{ $slot }}</div>
                <p class="mt-6 text-center text-xs text-slate-400">Data operasional Anda tersimpan secara aman.</p>
            </div>
        </main>
    </div>
    <script>
        window.addEventListener('load', function () {
            if (!window.Swal) return;
            @if ($errors->any()) window.Swal.fire({ icon: 'error', title: 'Login belum berhasil', html: @json($errors->all('<div class="text-left">:message</div>')), confirmButtonColor: '#0f172a' }); @endif
            @if (session('status')) window.Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('status')), timer: 2400, showConfirmButton: false }); @endif
        });
    </script>
    @include('partials.pwa-script')
</body>
</html>
