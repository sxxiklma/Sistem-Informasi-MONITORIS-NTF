<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">Master data</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Pelanggan & Outlet</h2>
            </div>
            @can('kelola master data')
            <div class="flex items-center gap-2">
                <a href="{{ route('customer-types.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Kategori / Jenis Outlet</span>
                </a>
                <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Pelanggan</span>
                </a>
            </div>
            @endcan
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
                </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="hidden md:table min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Kode</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Jenis</th>
                                <th class="px-6 py-4">Sales Penanggung Jawab</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($customers as $customer)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-600">
                                        {{ $customer->code }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-900">
                                        <div class="flex items-center gap-3">
                                            @if($customer->photo_url)
                                                <div class="relative group shrink-0" style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; max-width: 44px; max-height: 44px;">
                                                    <img src="{{ $customer->photo_url }}" 
                                                         alt="{{ $customer->name }}" 
                                                         class="rounded-xl border border-slate-200 shadow-2xs cursor-pointer hover:ring-2 hover:ring-amber-400 hover:scale-105 transition"
                                                         style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; max-width: 44px; max-height: 44px; object-fit: cover; display: block;"
                                                         onclick='showPhotoModal("{{ $customer->photo_url }}", "{{ addslashes($customer->name) }}")'
                                                         title="Klik untuk melihat foto outlet">
                                                    <div class="absolute -bottom-1 -right-1 h-3.5 w-3.5 bg-amber-400 text-slate-950 rounded-full flex items-center justify-center shadow-xs pointer-events-none">
                                                        <svg class="h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="rounded-xl bg-gradient-to-br from-slate-100 to-slate-200/70 border border-slate-200 flex items-center justify-center text-slate-400 shrink-0 shadow-2xs"
                                                     style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; max-width: 44px; max-height: 44px;"
                                                     title="Belum ada foto outlet">
                                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 truncate hover:text-blue-600 transition cursor-pointer" onclick='viewCustomerDetail(@json($customer))'>
                                                    {{ $customer->name }}
                                                </div>
                                                <div class="mt-0.5 text-xs text-slate-500 flex items-center gap-1">
                                                    <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span class="truncate">{{ $customer->phone ?: 'Tanpa nomor telepon' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($customer->customerType)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $customer->customerType->badge_class }}">
                                                {{ $customer->customerType->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                                {{ ucfirst($customer->type) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>{{ $customer->assignedUser?->name ?: 'Belum ditugaskan' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($customer->is_active)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 border border-rose-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Button View (Hanya Icon SVG) -->
                                            <button type="button" 
                                                    onclick='viewCustomerDetail(@json($customer))'
                                                    title="Lihat Detail Pelanggan" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 shadow-2xs hover:bg-blue-100 hover:border-blue-300 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>

                                            @can('kelola master data')
                                            <!-- Button Edit (Hanya Icon SVG) -->
                                            <a href="{{ route('customers.edit', $customer) }}" 
                                               title="Edit Data Pelanggan" 
                                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-300 bg-amber-50 text-amber-700 shadow-2xs hover:bg-amber-100 hover:border-amber-400 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <!-- Button Delete / Nonaktifkan (Hanya Icon SVG) -->
                                            <form class="inline" method="POST" action="{{ route('customers.destroy', $customer) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        title="Hapus / Nonaktifkan Pelanggan" 
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 shadow-2xs hover:bg-rose-100 hover:border-rose-300 transition" 
                                                        onclick="return confirm('Hapus / nonaktifkan pelanggan {{ $customer->name }}?')">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        Belum ada data pelanggan atau outlet yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE OUTLET CARDS (md:hidden) --}}
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <div class="p-4 space-y-2.5 hover:bg-slate-50/70 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    @if($customer->photo_url)
                                        <img src="{{ $customer->photo_url }}" 
                                             alt="{{ $customer->name }}" 
                                             class="rounded-xl border border-slate-200 shadow-2xs shrink-0 cursor-pointer"
                                             style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; max-width: 44px; max-height: 44px; object-fit: cover; display: block;"
                                             onclick='showPhotoModal("{{ $customer->photo_url }}", "{{ addslashes($customer->name) }}")'>
                                    @else
                                        <div class="rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 shrink-0"
                                             style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; max-width: 44px; max-height: 44px;">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-xs text-slate-500">{{ $customer->code }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                                {{ ucfirst($customer->type) }}
                                            </span>
                                        </div>
                                        <h4 class="font-bold text-slate-900 text-sm mt-0.5">{{ $customer->name }}</h4>
                                    </div>
                                </div>
                                @if($customer->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200 shrink-0">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700 border border-rose-200 shrink-0">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs text-slate-500 flex items-start gap-1.5">
                                <svg class="h-4 w-4 shrink-0 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $customer->address ?: 'Alamat belum diisi' }}</span>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-2 text-xs">
                                <div>
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}" class="inline-flex items-center gap-1 text-emerald-700 font-semibold hover:underline">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>{{ $customer->phone }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Tanpa no. telepon</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" 
                                            onclick='viewCustomerDetail(@json($customer))'
                                            class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 active:scale-95 transition">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>

                                    @can('kelola master data')
                                    <a href="{{ route('customers.edit', $customer) }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100 active:scale-95 transition" title="Edit">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('Hapus pelanggan {{ $customer->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 active:scale-95 transition" title="Hapus">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <p class="text-xs">Belum ada data pelanggan / outlet.</p>
                        </div>
                    @endforelse
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPhotoModal(url, name) {
            if (!window.Swal) {
                window.open(url, '_blank');
                return;
            }
            window.Swal.fire({
                title: `<div class="text-sm font-bold text-slate-900">${name}</div>`,
                html: `
                    <div class="mt-2 overflow-hidden rounded-xl border border-slate-200 shadow-inner bg-slate-950 flex items-center justify-center" style="max-height: 60vh;">
                        <img src="${url}" alt="${name}" class="object-contain mx-auto" style="max-height: 55vh; max-width: 100%; display: block;">
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                        <span>Foto Outlet / Tampak Toko</span>
                        <a href="${url}" target="_blank" download class="font-semibold text-blue-600 hover:underline inline-flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Buka Ukuran Penuh
                        </a>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#0f172a',
                width: '520px'
            });
        }

        function viewCustomerDetail(c) {
            if (!window.Swal) return;
            const hasGps = c.latitude && c.longitude && parseFloat(c.latitude) !== 0;
            const gpsHtml = hasGps 
                ? `<div>
                     <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200 mb-1">
                       <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Ada Titik Koordinat
                     </span>
                     <div class="font-mono text-slate-800 text-[11px]">${c.latitude}, ${c.longitude}</div>
                     <a href="https://www.google.com/maps?q=${c.latitude},${c.longitude}" target="_blank" class="inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline">
                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                       Buka di Google Maps &rarr;
                     </a>
                   </div>`
                : `<span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                     <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Belum ditentukan (Opsional)
                   </span>`;

            const photoHtml = c.photo_url 
                ? `<div class="mb-3 overflow-hidden rounded-xl border border-slate-200 bg-slate-950 shadow-sm relative group cursor-pointer" style="max-height: 180px;" onclick='showPhotoModal("${c.photo_url}", "${(c.name || '').replace(/"/g, '&quot;')}")'>
                     <img src="${c.photo_url}" alt="${c.name}" class="w-full object-cover" style="max-height: 180px; width: 100%; height: 180px; object-fit: cover; display: block;">
                     <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-semibold gap-1.5 transition">
                         <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                         <span>Klik untuk perbesar foto</span>
                     </div>
                   </div>`
                : `<div class="mb-3 flex items-center gap-2.5 p-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 text-xs">
                     <div class="h-9 w-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 shrink-0">
                         <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                     </div>
                     <span>Belum ada foto tampak outlet.</span>
                   </div>`;

            window.Swal.fire({
                title: `<div class="text-base font-bold text-slate-900">${c.name}</div>`,
                html: `
                    <div class="text-left text-xs space-y-2.5 p-3.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        ${photoHtml}
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <span class="text-slate-500">Kode Outlet:</span>
                            <span class="font-mono font-bold text-slate-900">${c.code || '-'}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <span class="text-slate-500">Kategori / Jenis:</span>
                            <span class="font-semibold text-slate-800">${c.type ? c.type.toUpperCase() : '-'}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <span class="text-slate-500">Nomor Telepon:</span>
                            <span class="font-medium text-slate-800">${c.phone || '-'}</span>
                        </div>
                        <div class="border-b border-slate-200/60 pb-2">
                            <span class="text-slate-500 block mb-0.5">Alamat Lengkap:</span>
                            <span class="font-medium text-slate-900">${c.address || '<em class="text-slate-400">Belum diisi</em>'}</span>
                        </div>
                        <div class="border-b border-slate-200/60 pb-2">
                            <span class="text-slate-500 block mb-1">Koordinat GPS:</span>
                            ${gpsHtml}
                        </div>
                        <div class="flex items-center justify-between pt-0.5">
                            <span class="text-slate-500">Status Operasional:</span>
                            ${c.is_active ? '<span class="text-emerald-700 font-bold">Aktif</span>' : '<span class="text-rose-700 font-bold">Nonaktif</span>'}
                        </div>
                    </div>
                `,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#0f172a',
                width: '500px'
            });
        }
    </script>
</x-app-layout>
