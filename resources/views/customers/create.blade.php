<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.22em] text-blue-600">Master Data Pelanggan</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ isset($customer) ? 'Edit Data Outlet / Pelanggan' : 'Pendaftaran Outlet Baru' }}</h2>
                <p class="mt-0.5 text-xs text-slate-500">Kelola informasi outlet, kontak PIC, dan penentuan titik koordinat GPS secara fleksibel.</p>
            </div>
            <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- ERROR VALIDATION ALERT --}}
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-rose-900 mb-1">
                        <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Terjadi kesalahan pada input data:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if(isset($customer))
                    @method('PUT')
                @endif

                {{-- CARD 1: INFORMASI UTAMA OUTLET --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 font-bold text-sm">1</span>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Identitas & Kontak Outlet</h3>
                                <p class="text-xs text-slate-500">Informasi nama outlet, klasifikasi usaha, dan sales motoris penanggung jawab.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- FOTO OUTLET / TOKO --}}
                        <div class="sm:col-span-2 border-b border-slate-100 pb-5" 
                             x-data="{
                                 photoPreview: '{{ isset($customer) && $customer->photo_url ? $customer->photo_url : '' }}',
                                 deletePhoto: false,
                                 previewPhoto(event) {
                                     const file = event.target.files[0];
                                     if (file) {
                                         const reader = new FileReader();
                                         reader.onload = (e) => {
                                             this.photoPreview = e.target.result;
                                             this.deletePhoto = false;
                                         };
                                         reader.readAsDataURL(file);
                                     }
                                 },
                                 removePhoto() {
                                     this.photoPreview = '';
                                     this.deletePhoto = true;
                                     if (this.$refs.photoInput) {
                                         this.$refs.photoInput.value = '';
                                     }
                                 }
                             }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Foto Tampak Depan Outlet / Toko <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                            </label>

                            <input type="hidden" name="delete_photo" :value="deletePhoto ? '1' : '0'">

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                                {{-- Preview Box (Fixed Compact Size) --}}
                                <div class="relative shrink-0 overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center shadow-xs"
                                     style="width: 112px; height: 112px; min-width: 112px; min-height: 112px; max-width: 112px; max-height: 112px;">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" alt="Preview Foto Outlet" 
                                             class="rounded-xl"
                                             style="width: 112px; height: 112px; max-width: 112px; max-height: 112px; object-fit: cover; display: block;">
                                    </template>
                                    <template x-if="!photoPreview">
                                        <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center"
                                             style="width: 112px; height: 112px;">
                                            <svg class="h-8 w-8 text-slate-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-[10px] leading-tight font-medium">Belum ada foto</span>
                                        </div>
                                    </template>
                                </div>

                                {{-- Upload & Action Controls --}}
                                <div class="space-y-2 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <label class="cursor-pointer inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-2xs hover:bg-slate-800 active:scale-95 transition">
                                            <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span x-text="photoPreview ? 'Ganti Foto' : 'Pilih / Ambil Foto'">Pilih Foto</span>
                                            <input type="file" 
                                                   x-ref="photoInput" 
                                                   name="photo" 
                                                   id="photo" 
                                                   accept="image/png,image/jpeg,image/webp,image/jpg" 
                                                   class="hidden" 
                                                   @change="previewPhoto($event)">
                                        </label>

                                        <template x-if="photoPreview">
                                            <button type="button" 
                                                    @click="removePhoto()" 
                                                    class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-100 active:scale-95 transition">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus Foto</span>
                                            </button>
                                        </template>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong> (Maksimal 5 MB). Memudahkan identifikasi toko bagi Sales Motoris di lapangan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- KODE PELANGGAN --}}
                        <div>
                            <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Kode Pelanggan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="code" name="code" 
                                   value="{{ old('code', $customer->code ?? $suggestedCode ?? '') }}" 
                                   required
                                   class="mt-1.5 w-full rounded-xl border-slate-300 font-mono text-sm uppercase focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Contoh: OUT-00001">
                            <p class="mt-1 text-[11px] text-slate-400">Kode unik identifikasi outlet dalam sistem.</p>
                        </div>

                        {{-- NAMA OUTLET --}}
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Nama Outlet / Toko <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" 
                                   value="{{ old('name', $customer->name ?? '') }}" 
                                   required
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Contoh: Toko Berkah Jaya Mandiri">
                        </div>

                        {{-- JENIS PELANGGAN --}}
                        <div>
                            <label for="type" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Jenis Pelanggan <span class="text-rose-500">*</span>
                            </label>
                            @php($currentType = old('type', $customer->type ?? 'toko'))
                            <select id="type" name="type" required class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @if(isset($customerTypes) && $customerTypes->isNotEmpty())
                                    @foreach($customerTypes as $cType)
                                        <option value="{{ $cType->code }}" {{ $currentType === $cType->code ? 'selected' : '' }}>
                                            {{ $cType->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="toko" {{ $currentType === 'toko' ? 'selected' : '' }}>Toko Retail / Kelontong</option>
                                    <option value="kios" {{ $currentType === 'kios' ? 'selected' : '' }}>Kios / Lapak Pasar</option>
                                    <option value="warung" {{ $currentType === 'warung' ? 'selected' : '' }}>Warung / Agen Kecil</option>
                                    <option value="reseller" {{ $currentType === 'reseller' ? 'selected' : '' }}>Reseller</option>
                                    <option value="grosir" {{ $currentType === 'grosir' ? 'selected' : '' }}>Grosir / Agen Besar</option>
                                    <option value="minimarket" {{ $currentType === 'minimarket' ? 'selected' : '' }}>Minimarket Modern</option>
                                @endif
                            </select>
                        </div>

                        {{-- SALES PENANGGUNG JAWAB --}}
                        <div>
                            <label for="assigned_user_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Sales Penanggung Jawab
                            </label>
                            @php($assignedId = old('assigned_user_id', $customer->assigned_user_id ?? ''))
                            <select id="assigned_user_id" name="assigned_user_id" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Belum Ditugaskan / Bebas --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (string)$assignedId === (string)$user->id ? 'selected' : '' }}>
                                        {{ $user->name }} {{ $user->team ? '(' . $user->team->name . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- TELEPON --}}
                        <div class="sm:col-span-2">
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input type="text" id="phone" name="phone" 
                                   value="{{ old('phone', $customer->phone ?? '') }}" 
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Contoh: 081234567890">
                        </div>

                        {{-- ALAMAT LENGKAP --}}
                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between">
                                <label for="customer_address" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Alamat Lengkap Outlet
                                </label>
                                <span class="text-[11px] text-slate-400">Jalan, RT/RW, Kelurahan, Kecamatan, Kota</span>
                            </div>
                            <textarea id="customer_address" name="address" rows="3" 
                                      class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" 
                                      placeholder="Masukkan alamat lengkap outlet secara detail...">{{ old('address', $customer->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: PETA INTERAKTIF & KOORDINAT GPS (OPSIONAL) --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-5">
                    <div class="border-b border-slate-100 pb-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 font-bold text-sm">2</span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-slate-900">Titik Koordinat & Peta Lokasi</h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                            Opsional
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">Anda dapat menyimpan tanpa koordinat, atau gunakan peta interaktif di bawah untuk pin titik wilayah / cari alamat.</p>
                                </div>
                            </div>

                            <button type="button" id="btnResetPin" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus / Kosongkan Titik Pin</span>
                            </button>
                        </div>
                    </div>

                    {{-- SEARCH & TOOLS BAR --}}
                    <div class="rounded-xl bg-slate-50 p-4 border border-slate-200 space-y-3">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" id="mapSearchQuery" 
                                       placeholder="Ketik nama jalan, kelurahan, kecamatan, kota, atau nama toko untuk cari..." 
                                       class="w-full rounded-lg border-slate-300 pl-9 text-xs focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <button type="button" id="btnSearchMap" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition shadow-2xs">
                                <svg class="h-3.5 w-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span>Cari di Peta</span>
                            </button>
                            <button type="button" id="btnSearchFromAddress" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-amber-300 bg-blue-50 px-3.5 py-2 text-xs font-bold text-amber-800 hover:bg-amber-100 transition shadow-2xs" title="Cari di peta berdasarkan alamat lengkap yang diketik di atas">
                                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Cari dari Alamat di Atas</span>
                            </button>
                            <button type="button" id="btnCurrentLocation" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3.5 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100 transition shadow-2xs" title="Gunakan lokasi GPS perangkat saat ini">
                                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
                                <span>Lokasi Saya (GPS)</span>
                            </button>
                        </div>

                        {{-- STATUS INDICATOR --}}
                        <div id="pinStatusBanner" class="flex flex-wrap items-center justify-between gap-2 text-xs rounded-lg bg-white px-3 py-2 border border-slate-200">
                            <div class="flex items-center gap-2">
                                <span id="pinStatusDot" class="h-2 w-2 rounded-full bg-slate-400"></span>
                                <span id="pinStatusText" class="font-medium text-slate-700">Belum ada titik dipilih (koordinat opsional/kosong)</span>
                            </div>
                            <span class="text-[11px] text-slate-400 flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Klik peta atau geser pin untuk menentukan titik
                            </span>
                        </div>
                    </div>

                    {{-- LEAFLET MAP CANVAS --}}
                    <div class="relative">
                        <div id="customerLocationMap" class="h-[380px] w-full rounded-xl border border-slate-300 bg-slate-100 shadow-inner z-0"></div>
                        <div id="mapLoadingOverlay" class="hidden absolute inset-0 bg-white/70 backdrop-blur-2xs flex items-center justify-center rounded-xl z-10 text-xs font-bold text-slate-800">
                            <div class="flex items-center gap-2 rounded-lg bg-white p-3 shadow-md border border-slate-200">
                                <svg class="animate-spin h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Mencari lokasi di peta...</span>
                            </div>
                        </div>
                    </div>

                    {{-- INPUT LATITUDE & LONGITUDE (OPSIONAL) --}}
                    <div class="grid gap-4 sm:grid-cols-2 pt-2">
                        <div>
                            <div class="flex items-center justify-between">
                                <label for="latitude" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Latitude GPS
                                </label>
                                <span class="text-[11px] font-semibold text-blue-600">(Opsional)</span>
                            </div>
                            <input type="text" id="latitude" name="latitude" 
                                   value="{{ old('latitude', $customer->latitude ?? '') }}" 
                                   placeholder="Contoh: -7.2575000" 
                                   class="mt-1.5 w-full rounded-xl border-slate-300 font-mono text-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-[11px] text-slate-400">Terisi otomatis dari klik peta atau cari alamat.</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <label for="longitude" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Longitude GPS
                                </label>
                                <span class="text-[11px] font-semibold text-blue-600">(Opsional)</span>
                            </div>
                            <input type="text" id="longitude" name="longitude" 
                                   value="{{ old('longitude', $customer->longitude ?? '') }}" 
                                   placeholder="Contoh: 112.7521000" 
                                   class="mt-1.5 w-full rounded-xl border-slate-300 font-mono text-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-[11px] text-slate-400">Bisa dibiarkan kosong jika titik belum diketahui.</p>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batal</span>
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-slate-800 transition">
                        <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ isset($customer) ? 'Simpan Perubahan Outlet' : 'Daftarkan Outlet Baru' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- LEAFLET MAP & GEOCODING SCRIPT --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let map = null;
        let marker = null;

        const defaultLat = -7.2575;
        const defaultLng = 112.7521;

        const inputLat = document.getElementById('latitude');
        const inputLng = document.getElementById('longitude');
        const inputAddress = document.getElementById('customer_address');
        const searchInput = document.getElementById('mapSearchQuery');
        const searchBtn = document.getElementById('btnSearchMap');
        const searchFromAddressBtn = document.getElementById('btnSearchFromAddress');
        const currentLocBtn = document.getElementById('btnCurrentLocation');
        const resetPinBtn = document.getElementById('btnResetPin');
        const loadingOverlay = document.getElementById('mapLoadingOverlay');
        const statusDot = document.getElementById('pinStatusDot');
        const statusText = document.getElementById('pinStatusText');

        // Check if initial lat & lng exist
        const initialLat = parseFloat(inputLat.value);
        const initialLng = parseFloat(inputLng.value);
        const hasInitialCoords = !isNaN(initialLat) && !isNaN(initialLng) && initialLat !== 0;

        function createPinIcon() {
            return window.L.divIcon({
                className: 'custom-outlet-pin',
                html: `
                    <div style="position: relative; width: 34px; height: 42px; transform: translate(-50%, -100%);">
                        <svg width="34" height="42" viewBox="0 0 34 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 0C7.611 0 0 7.611 0 17C0 29.75 17 42 17 42C17 42 34 29.75 34 17C34 7.611 26.389 0 17 0Z" fill="#F59E0B"/>
                            <path d="M17 2C8.716 2 2 8.716 2 17C2 28.5 17 39.5 17 39.5C17 39.5 32 28.5 32 17C32 8.716 25.284 2 17 2Z" fill="#D97706"/>
                            <circle cx="17" cy="17" r="8" fill="#0F172A"/>
                            <circle cx="17" cy="17" r="4" fill="#FFFFFF"/>
                        </svg>
                    </div>
                `,
                iconSize: [34, 42],
                iconAnchor: [17, 42],
                popupAnchor: [0, -38]
            });
        }

        function updateStatusUI(lat, lng) {
            if (lat !== null && lng !== null) {
                statusDot.className = 'h-2 w-2 rounded-full bg-emerald-500 animate-pulse';
                statusText.innerHTML = `<span class="inline-flex items-center gap-1"><svg class="inline-block h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><strong class="text-emerald-700">Titik Terpilih:</strong></span> Lat ${parseFloat(lat).toFixed(6)}, Lng ${parseFloat(lng).toFixed(6)} <span class="text-slate-400 text-[11px]">(Geser pin untuk koreksi)</span>`;
            } else {
                statusDot.className = 'h-2 w-2 rounded-full bg-slate-400';
                statusText.innerHTML = `Belum ada titik dipilih <span class="text-blue-600 font-semibold">(Koordinat opsional/kosong)</span>`;
            }
        }

        function setMarker(lat, lng, pan = true, updateAddress = false) {
            const numLat = parseFloat(lat);
            const numLng = parseFloat(lng);

            if (isNaN(numLat) || isNaN(numLng)) return;

            inputLat.value = numLat.toFixed(7);
            inputLng.value = numLng.toFixed(7);
            updateStatusUI(numLat, numLng);

            if (!marker) {
                marker = window.L.marker([numLat, numLng], {
                    draggable: true,
                    icon: createPinIcon()
                }).addTo(map);

                marker.on('dragend', function (e) {
                    const pos = e.target.getLatLng();
                    inputLat.value = pos.lat.toFixed(7);
                    inputLng.value = pos.lng.toFixed(7);
                    updateStatusUI(pos.lat, pos.lng);
                    marker.bindPopup(`<strong>Titik Outlet</strong><br>Lat: ${pos.lat.toFixed(5)}<br>Lng: ${pos.lng.toFixed(5)}`).openPopup();
                });
            } else {
                marker.setLatLng([numLat, numLng]);
            }

            marker.bindPopup(`<strong>Titik Lokasi Outlet</strong><br>Lat: ${numLat.toFixed(5)}<br>Lng: ${numLng.toFixed(5)}`).openPopup();

            if (pan) {
                map.setView([numLat, numLng], Math.max(map.getZoom(), 15));
            }
        }

        function removeMarker() {
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }
            inputLat.value = '';
            inputLng.value = '';
            updateStatusUI(null, null);
        }

        // Initialize Map
        if (window.L) {
            const startCenter = hasInitialCoords ? [initialLat, initialLng] : [defaultLat, defaultLng];
            const startZoom = hasInitialCoords ? 16 : 12;

            map = window.L.map('customerLocationMap').setView(startCenter, startZoom);

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap kontributor',
                maxZoom: 19
            }).addTo(map);

            if (hasInitialCoords) {
                setMarker(initialLat, initialLng, false);
            }

            // Click map to set or move pin
            map.on('click', function (e) {
                setMarker(e.latlng.lat, e.latlng.lng, false);
            });
        }

        // Search location via OpenStreetMap Nominatim
        async function searchLocation(query) {
            if (!query || query.trim().length < 3) {
                window.Swal?.fire({
                    icon: 'warning',
                    title: 'Kata Kunci Kurang Lengkap',
                    text: 'Masukkan minimal 3 karakter nama jalan, wilayah, atau nama toko.',
                    confirmButtonColor: '#0f172a'
                });
                return;
            }

            loadingOverlay.classList.remove('hidden');

            try {
                const encoded = encodeURIComponent(query.trim());
                // Prefer Indonesia results
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encoded}&countrycodes=id&limit=1`, {
                    headers: { 'Accept-Language': 'id' }
                });
                const data = await res.json();

                if (data && data.length > 0) {
                    const first = data[0];
                    setMarker(first.lat, first.lon, true);

                    // If address is still blank, offer to fill it
                    if (!inputAddress.value.trim() && first.display_name) {
                        inputAddress.value = first.display_name;
                    }

                    window.Swal?.fire({
                        icon: 'success',
                        title: 'Lokasi Ditemukan!',
                        text: first.display_name,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    window.Swal?.fire({
                        icon: 'info',
                        title: 'Lokasi Tidak Ditemukan',
                        text: 'Coba kata kunci yang lebih umum seperti nama jalan utama, kecamatan, atau kota.',
                        confirmButtonColor: '#0f172a'
                    });
                }
            } catch (err) {
                console.error(err);
                window.Swal?.fire({
                    icon: 'error',
                    title: 'Gagal Mencari Lokasi',
                    text: 'Periksa koneksi internet Anda atau coba klik langsung pada peta.',
                    confirmButtonColor: '#0f172a'
                });
            } finally {
                loadingOverlay.classList.add('hidden');
            }
        }

        // Search button triggers
        searchBtn?.addEventListener('click', () => searchLocation(searchInput.value));
        searchInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchLocation(searchInput.value);
            }
        });

        // Search from address textarea
        searchFromAddressBtn?.addEventListener('click', function () {
            const addr = inputAddress.value.trim();
            if (!addr) {
                window.Swal?.fire({
                    icon: 'warning',
                    title: 'Alamat Masih Kosong',
                    text: 'Ketik alamat lengkap outlet di kolom atas terlebih dahulu.',
                    confirmButtonColor: '#0f172a'
                });
                return;
            }
            searchInput.value = addr;
            searchLocation(addr);
        });

        // Current Location (GPS)
        currentLocBtn?.addEventListener('click', function () {
            if (!navigator.geolocation) {
                window.Swal?.fire({
                    icon: 'warning',
                    title: 'GPS Tidak Didukung',
                    text: 'Browser Anda tidak mendukung geolokasi GPS.',
                    confirmButtonColor: '#0f172a'
                });
                return;
            }

            loadingOverlay.classList.remove('hidden');

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    loadingOverlay.classList.add('hidden');
                    setMarker(pos.coords.latitude, pos.coords.longitude, true);
                    window.Swal?.fire({
                        icon: 'success',
                        title: 'Lokasi GPS Ditemukan',
                        text: `Akurasi sekitar ${Math.round(pos.coords.accuracy)} meter.`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                function (err) {
                    loadingOverlay.classList.add('hidden');
                    window.Swal?.fire({
                        icon: 'warning',
                        title: 'Izin Lokasi Ditolak',
                        text: 'Pastikan Anda memberikan izin akses lokasi GPS di peramban.',
                        confirmButtonColor: '#0f172a'
                    });
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });

        // Reset Pin
        resetPinBtn?.addEventListener('click', function () {
            removeMarker();
            window.Swal?.fire({
                icon: 'info',
                title: 'Titik Pin Dikosongkan',
                text: 'Koordinat GPS telah dihapus. Outlet akan disimpan tanpa titik koordinat (opsional).',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Manual text input change sync
        inputLat?.addEventListener('change', function () {
            const lat = parseFloat(this.value);
            const lng = parseFloat(inputLng.value);
            if (!isNaN(lat) && !isNaN(lng)) setMarker(lat, lng, true);
        });
        inputLng?.addEventListener('change', function () {
            const lat = parseFloat(inputLat.value);
            const lng = parseFloat(this.value);
            if (!isNaN(lat) && !isNaN(lng)) setMarker(lat, lng, true);
        });
    });
    </script>
</x-app-layout>
