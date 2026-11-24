<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/png" href="{{ asset('images/netafarm-logo.png') }}">
        <title>{{ config('app.name', 'Netafarm') }}</title>
        @include('partials.pwa-head')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 min-h-screen text-slate-900 selection:bg-blue-600 selection:text-white" 
      x-data="{ 
          mobileNavOpen: false,
          sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
          sidebarHover: false,
          toggleSidebar() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
          }
      }">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- DESKTOP TOP BAR (Only visible on lg+ screens) -->
            <div class="hidden lg:block border-b border-slate-200 bg-white sticky top-0 z-30 shadow-2xs transition-all duration-300 ease-in-out"
                 :class="(sidebarCollapsed && !sidebarHover) ? 'lg:pl-20' : 'lg:pl-64'">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    {{-- System status & breadcrumb indicator + Sidebar Toggle --}}
                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="toggleSidebar()" 
                                class="hidden lg:inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-600 shadow-2xs active:scale-95 transition-all duration-200"
                                :title="sidebarCollapsed ? 'Perluas Sidebar Navigasi' : 'Ciutkan Sidebar Navigasi'">
                            <svg class="h-4 w-4 transition-transform duration-300" 
                                 :class="sidebarCollapsed ? 'rotate-180 text-blue-600' : 'text-slate-500'" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div class="hidden lg:block h-4 w-px bg-slate-200"></div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="font-medium text-slate-600">CV. Netafarm Indolestari</span>
                            <span class="text-slate-300">/</span>
                            <span class="font-bold text-blue-900">Distributor Pertanian, Hidroponik & Greenhouse</span>
                        </div>
                    </div>

                    <!-- Right Controls: Notification Bell & Quick User info -->
                    <div class="flex items-center gap-3" x-data="{ notifOpen: false, unreadCount: 0, notifList: [] }"
                        x-init="
                            fetch('/notifications/count').then(r=>r.json()).then(d=>unreadCount=d.count).catch(()=>{});
                            setInterval(() => {
                                fetch('/notifications/count').then(r=>r.json()).then(d=>unreadCount=d.count).catch(()=>{});
                            }, 15000);
                        ">
                        <!-- Notification Bell Button -->
                        <div class="relative">
                            <button @click="
                                notifOpen = !notifOpen;
                                if (notifOpen) {
                                    fetch('/notifications').then(r=>r.json()).then(d=>notifList=d).catch(()=>{});
                                }
                            " class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition active:scale-95" aria-label="Notifikasi">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <template x-if="unreadCount > 0">
                                    <span class="absolute top-1 right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white shadow-xs"
                                        x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                </template>
                            </button>

                            <!-- Notification Dropdown -->
                            <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition
                                class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white p-3 shadow-2xl border border-slate-200 z-50 text-xs">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2 px-1">
                                    <span class="font-bold text-slate-900">Notifikasi Tugas & Lapangan</span>
                                    <button @click="
                                        fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                            .then(() => { unreadCount = 0; notifList.forEach(n=>n.is_read=true); });
                                    " class="text-[11px] text-blue-600 hover:underline font-semibold">Tandai semua dibaca</button>
                                </div>
                                <div class="max-h-72 overflow-y-auto divide-y divide-slate-100 py-1">
                                    <template x-if="notifList.length === 0">
                                        <p class="py-6 text-center text-slate-400">Belum ada notifikasi baru.</p>
                                    </template>
                                    <template x-for="n in notifList" :key="n.id">
                                        <div :class="n.is_read ? 'opacity-70 bg-white' : 'bg-blue-50/50'"
                                            class="p-2.5 rounded-lg hover:bg-slate-50 transition">
                                            <div class="flex items-start justify-between gap-1">
                                                <strong class="font-semibold text-slate-900" x-text="n.title"></strong>
                                                <span class="text-[10px] text-slate-400 shrink-0" x-text="n.time_ago"></span>
                                            </div>
                                            <p class="text-slate-600 mt-0.5 line-clamp-2" x-text="n.message"></p>
                                            <template x-if="n.action_url">
                                                <a :href="n.action_url" class="mt-1 inline-block text-[11px] font-bold text-blue-600 hover:underline">
                                                    Buka Detail &rarr;
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- User Avatar Pill -->
                        <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200">
                            <a href="{{ route('profile.edit') }}" class="h-9 w-9 rounded-full overflow-hidden bg-slate-900 text-white font-bold flex items-center justify-center text-xs shadow-2xs hover:ring-2 hover:ring-blue-500 transition active:scale-95 shrink-0" title="Kelola Profil Saya">
                                @if(Auth::user()->avatar_url)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                @endif
                            </a>
                            <div class="hidden sm:block text-left text-xs leading-tight">
                                <span class="font-bold text-slate-800 block">{{ Auth::user()->name }}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] text-blue-600 uppercase font-bold">
                                        {{ Auth::user()->roles->first()?->name ?? 'User' }}
                                    </span>
                                    @if(Auth::user()->team)
                                    <span class="text-[10px] text-slate-400">&bull; {{ Auth::user()->team->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Heading (if present) -->
            @isset($header)
                <header class="bg-white border-b border-slate-200 transition-all duration-300 ease-in-out" :class="(sidebarCollapsed && !sidebarHover) ? 'lg:pl-20' : 'lg:pl-64'">
                    <div class="max-w-7xl mx-auto py-4 sm:py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content (With bottom safe-area padding for mobile bottom nav) -->
            <main class="flex-1 pb-24 lg:pb-10 transition-all duration-300 ease-in-out" :class="(sidebarCollapsed && !sidebarHover) ? 'lg:pl-20' : 'lg:pl-64'">
                {{ $slot }}
            </main>
        </div>

        <script>
            window.addEventListener('load', function () {
                if (!window.Swal) return;
                @if (session('status'))
                    window.Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('status')), timer: 2400, showConfirmButton: false });
                @elseif ($errors->any())
                    window.Swal.fire({ icon: 'error', title: 'Periksa kembali', html: @json($errors->all('<div class="text-left">:message</div>')), confirmButtonColor: '#0f172a' });
                @endif
                document.querySelectorAll('form').forEach(function (form) {
                    if (form.dataset.swalBound || form.method.toLowerCase() !== 'post' || !form.querySelector('input[name="_method"][value="DELETE"]')) return;
                    form.dataset.swalBound = 'true';
                    form.addEventListener('submit', function (event) {
                        if (form.dataset.confirmed === 'true') return;
                        event.preventDefault();
                        window.Swal.fire({ icon: 'warning', title: 'Nonaktifkan data?', text: 'Data tidak akan dihapus permanen.', showCancelButton: true, confirmButtonText: 'Ya, lanjutkan', cancelButtonText: 'Batal', confirmButtonColor: '#0f172a' }).then(function (result) { if (result.isConfirmed) { form.dataset.confirmed = 'true'; form.submit(); } });
                    });
                });
            });
        </script>
        @include('partials.pwa-script')
    </body>
</html>
