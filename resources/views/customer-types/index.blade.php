<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">Pengaturan Master Data</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">Kategori & Jenis Pelanggan / Outlet</h2>
                <p class="mt-0.5 text-xs text-slate-500">Atur klasifikasi jenis outlet (Toko, Kios, Warung, Grosir, dll) untuk formulir pendaftaran dan segmentasi operasional.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('customers.index') }}" 
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Daftar Pelanggan</span>
                </a>
                <button type="button" 
                        @click="openCreateModal()"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition active:scale-95">
                    <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Kategori Baru</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-6"
         x-data="{
             createModalOpen: false,
             editModalOpen: false,
             activeType: null,
             editForm: {
                 actionUrl: '',
                 name: '',
                 code: '',
                 description: '',
                 badge_color: 'sky',
                 sort_order: 0,
                 is_active: true
             },
             openCreateModal() {
                 this.createModalOpen = true;
             },
             openEditModal(type) {
                 this.activeType = type;
                 this.editForm.actionUrl = '/customer-types/' + type.id;
                 this.editForm.name = type.name;
                 this.editForm.code = type.code;
                 this.editForm.description = type.description || '';
                 this.editForm.badge_color = type.badge_color || 'amber';
                 this.editForm.sort_order = type.sort_order || 0;
                 this.editForm.is_active = Boolean(type.is_active);
                 this.editModalOpen = true;
             },
             generateCode(sourceVal, targetProp) {
                 return sourceVal.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
             }
         }">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Flash Alert Messages --}}
            @if(session('status'))
                <div class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-2xs">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900">Perubahan Berhasil</h4>
                            <p class="text-xs text-emerald-800">{{ session('status') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="this.closest('div').remove()" class="rounded-lg p-1.5 text-emerald-600 hover:bg-emerald-100 transition">&times;</button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-xs text-rose-800 mb-1">
                        <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Perhatian:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. METRIC STATS CARDS --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Kategori --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Kategori</p>
                            <h3 class="mt-1.5 text-2xl font-black text-slate-900">{{ $metrics['total_types'] }}</h3>
                            <p class="mt-1 text-[11px] text-slate-500">Jenis klasifikasi outlet</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-blue-600 border border-amber-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Kategori Aktif --}}
                <div class="rounded-2xl border border-emerald-200/80 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Kategori Aktif</p>
                            <h3 class="mt-1.5 text-2xl font-black text-emerald-700">{{ $metrics['active_types'] }}</h3>
                            <p class="mt-1 text-[11px] text-emerald-600/80">Tampil di pilihan pendaftaran</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Kategori Non-Aktif --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Non-Aktif</p>
                            <h3 class="mt-1.5 text-2xl font-black text-slate-700">{{ $metrics['inactive_types'] }}</h3>
                            <p class="mt-1 text-[11px] text-slate-500">Disembunyikan dari form</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 border border-slate-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Total Outlet --}}
                <div class="rounded-2xl border border-sky-200/80 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Total Outlet Terdata</p>
                            <h3 class="mt-1.5 text-2xl font-black text-sky-700">{{ $metrics['total_customers'] }}</h3>
                            <p class="mt-1 text-[11px] text-sky-600/80">Outlet terhubung dalam sistem</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 border border-sky-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. SEARCH & FILTER BAR --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                <form method="GET" action="{{ route('customer-types.index') }}" class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-3 flex-1">
                        <div class="relative w-full max-w-sm">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode kategori..." 
                                   class="w-full rounded-xl border-slate-200 bg-slate-50/70 pl-8 pr-3 py-2 text-xs focus:border-amber-400 focus:bg-white focus:ring-amber-400">
                            <svg class="absolute left-2.5 top-2.5 h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <select name="status" onchange="this.form.submit()" class="rounded-xl border-slate-200 bg-slate-50/70 py-2 text-xs focus:border-amber-400 focus:bg-white focus:ring-amber-400">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hanya Aktif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Hanya Non-Aktif</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 px-3.5 py-2 text-xs font-bold text-slate-950 shadow-xs hover:bg-amber-300 transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span>Cari</span>
                        </button>
                        <a href="{{ route('customer-types.index') }}" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition" title="Reset">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- 3. DATA TABLE --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                <div class="border-b border-slate-100 bg-slate-50/60 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                        <h3 class="text-sm font-bold text-slate-800">Daftar Jenis / Kategori Pelanggan</h3>
                        <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-[11px] font-bold text-slate-700">
                            {{ $customerTypes->total() }} Data
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="bg-slate-50 uppercase tracking-wider text-slate-500 border-b border-slate-200/80 text-[10px]">
                            <tr>
                                <th class="px-5 py-3.5 font-bold text-center" style="width: 5%;">No</th>
                                <th class="px-5 py-3.5 font-bold" style="width: 25%;">Nama Kategori / Label</th>
                                <th class="px-5 py-3.5 font-bold" style="width: 15%;">Kode Sistem</th>
                                <th class="px-5 py-3.5 font-bold" style="width: 25%;">Keterangan / Deskripsi</th>
                                <th class="px-5 py-3.5 font-bold text-center" style="width: 10%;">Jumlah Outlet</th>
                                <th class="px-5 py-3.5 font-bold text-center" style="width: 10%;">Status</th>
                                <th class="px-5 py-3.5 font-bold text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($customerTypes as $index => $type)
                                <tr class="hover:bg-slate-50/80 transition">
                                    {{-- No / Urutan --}}
                                    <td class="px-5 py-3.5 text-center font-mono text-slate-400">
                                        {{ $index + 1 }}
                                    </td>

                                    {{-- Nama Kategori --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold border {{ $type->badge_class }}">
                                                <span class="h-2 w-2 rounded-full bg-current opacity-60"></span>
                                                <span>{{ $type->name }}</span>
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Kode Unik Sistem --}}
                                    <td class="px-5 py-3.5 font-mono text-[11px] font-bold text-slate-600">
                                        <code>{{ $type->code }}</code>
                                    </td>

                                    {{-- Deskripsi --}}
                                    <td class="px-5 py-3.5 text-slate-600">
                                        {{ $type->description ?: '-' }}
                                    </td>

                                    {{-- Jumlah Outlet Terdaftar --}}
                                    <td class="px-5 py-3.5 text-center">
                                        <a href="{{ route('customers.index', ['type' => $type->code]) }}" 
                                           class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700 hover:bg-amber-100 hover:text-amber-800 transition"
                                           title="Lihat outlet dengan jenis ini">
                                            <span>{{ $type->customers_count }}</span>
                                            <span class="text-[10px] text-slate-400">outlet</span>
                                        </a>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-5 py-3.5 text-center">
                                        <form method="POST" action="{{ route('customer-types.toggle', $type) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    title="Klik untuk mengubah status aktif / non-aktif"
                                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-bold transition active:scale-95 {{ $type->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $type->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                <span>{{ $type->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                                            </button>
                                        </form>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-5 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            {{-- Button Edit --}}
                                            <button type="button" 
                                                    @click="openEditModal({{ json_encode($type) }})"
                                                    title="Edit Kategori" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-700 shadow-2xs hover:bg-amber-100 transition active:scale-95">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>

                                            {{-- Button Hapus (Hanya jika belum ada customer terkait) --}}
                                            @if($type->customers_count === 0)
                                                <form method="POST" action="{{ route('customer-types.destroy', $type) }}" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ addslashes($type->name) }}?');" 
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            title="Hapus Kategori" 
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 shadow-2xs hover:bg-rose-100 transition active:scale-95">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" 
                                                        disabled
                                                        title="Kategori tidak dapat dihapus karena digunakan oleh {{ $type->customers_count }} outlet. Nonaktifkan jika tidak ingin digunakan."
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                        <p class="font-semibold text-slate-700">Belum ada kategori yang cocok</p>
                                        <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Tambah Kategori Baru" untuk menambahkan jenis outlet baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customerTypes->hasPages())
                    <div class="border-t border-slate-100 bg-white px-6 py-4">
                        {{ $customerTypes->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL TAMBAH KATEGORI BARU                               --}}
        {{-- ======================================================== --}}
        <div x-show="createModalOpen" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
            <div @click.outside="createModalOpen = false"
                 class="w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/70 px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-blue-600 border border-amber-200 shadow-2xs">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Tambah Kategori / Jenis Pelanggan</h3>
                            <p class="text-[11px] text-slate-500">Definisikan klasifikasi outlet baru untuk form registrasi.</p>
                        </div>
                    </div>
                    <button type="button" @click="createModalOpen = false" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('customer-types.store') }}" method="POST" class="p-6 space-y-4"
                      x-data="{ newName: '', newCode: '' }">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Kategori / Label <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" x-model="newName" 
                               @input="if(!newCode || newCode === generateCode($event.target.value.slice(0, -1))) newCode = generateCode(newName)" 
                               required placeholder="Contoh: Supermarket Mandiri, Kios Pupuk..." 
                               class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Kode Sistem <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="code" x-model="newCode" required placeholder="Contoh: supermarket" 
                                   class="w-full rounded-xl border-slate-200 font-mono text-xs focus:border-amber-400 focus:ring-amber-400">
                            <p class="mt-1 text-[10px] text-slate-400">Huruf kecil & garis bawah (_).</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Warna Badge
                            </label>
                            <select name="badge_color" class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400">
                                <option value="amber">🟡 Amber / Oranye</option>
                                <option value="sky">🔵 Biru Langit</option>
                                <option value="emerald">🟢 Hijau Zamrud</option>
                                <option value="purple">🟣 Ungu</option>
                                <option value="indigo">🔷 Indigo</option>
                                <option value="rose">🔴 Merah Mawar</option>
                                <option value="slate">⚪ Abu-Abu Netral</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Urutan Tampilan (Sort Order)
                        </label>
                        <input type="number" name="sort_order" value="0" min="0" 
                               class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400">
                        <p class="mt-1 text-[10px] text-slate-400">Angka lebih kecil tampil lebih awal di dropdown pilihan.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Deskripsi / Keterangan (Opsional)
                        </label>
                        <textarea name="description" rows="2" placeholder="Penjelasan tentang jenis outlet ini..." 
                                  class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="create_is_active" name="is_active" value="1" checked 
                               class="rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                        <label for="create_is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">
                            Aktifkan langsung kategori ini (siap dipilih di formulir outlet)
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="createModalOpen = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-950 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition active:scale-95">
                            <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Kategori</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL EDIT KATEGORI                                      --}}
        {{-- ======================================================== --}}
        <div x-show="editModalOpen" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
            <div @click.outside="editModalOpen = false"
                 class="w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/70 px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-blue-600 border border-amber-200 shadow-2xs">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Edit Kategori Pelanggan</h3>
                            <p class="text-[11px] text-slate-500" x-text="activeType?.name"></p>
                        </div>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="editForm.actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Kategori / Label <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" x-model="editForm.name" required 
                               class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Kode Sistem <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="code" x-model="editForm.code" required 
                                   class="w-full rounded-xl border-slate-200 font-mono text-xs focus:border-amber-400 focus:ring-amber-400">
                            <p class="mt-1 text-[10px] text-slate-400">Perubahan kode akan mengupdate data outlet terkait otomatis.</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Warna Badge
                            </label>
                            <select name="badge_color" x-model="editForm.badge_color" class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400">
                                <option value="amber">🟡 Amber / Oranye</option>
                                <option value="sky">🔵 Biru Langit</option>
                                <option value="emerald">🟢 Hijau Zamrud</option>
                                <option value="purple">🟣 Ungu</option>
                                <option value="indigo">🔷 Indigo</option>
                                <option value="rose">🔴 Merah Mawar</option>
                                <option value="slate">⚪ Abu-Abu Netral</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Urutan Tampilan (Sort Order)
                        </label>
                        <input type="number" name="sort_order" x-model="editForm.sort_order" min="0" 
                               class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Deskripsi / Keterangan (Opsional)
                        </label>
                        <textarea name="description" x-model="editForm.description" rows="2" 
                                  class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="edit_is_active" name="is_active" value="1" x-model="editForm.is_active" 
                               class="rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                        <label for="edit_is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">
                            Status Aktif (Ditampilkan dalam pilihan registrasi outlet)
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-300 transition active:scale-95">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

