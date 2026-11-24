<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">Inventori & Katalog</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Edit Data Produk</h2>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Katalog</span>
            </a>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                @csrf 
                @method('PUT')
                
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-900">Perbarui Informasi Produk</h3>
                    <p class="text-xs text-slate-500 mt-1">Ubah data SKU, nama, foto produk, kategori, harga jual, dan stok barang.</p>
                </div>

                {{-- UPLOAD / GANTI GAMBAR PRODUK --}}
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 sm:p-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Foto / Gambar Produk
                        <span class="ml-1 text-[11px] font-semibold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full">Maksimal 2 MB</span>
                    </label>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        {{-- Image Preview Box --}}
                        <div class="relative h-28 w-28 shrink-0 overflow-hidden rounded-2xl border-2 border-slate-300 bg-white flex items-center justify-center shadow-2xs group" id="previewContainer">
                            @if($product->image_url)
                                <img id="imagePreview" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                <div id="placeholderPreview" class="hidden flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                    <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] mt-1 text-slate-400">Belum ada foto</span>
                                </div>
                            @else
                                <img id="imagePreview" src="#" alt="Preview" class="h-full w-full object-cover hidden">
                                <div id="placeholderPreview" class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                    <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] mt-1 text-slate-400">Belum ada foto</span>
                                </div>
                            @endif
                        </div>

                        {{-- Upload Control & Actions --}}
                        <div class="flex-1 w-full space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <label for="productImageInput" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-500 cursor-pointer active:scale-95 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $product->image_url ? 'Ganti Foto' : 'Pilih Gambar' }}</span>
                                </label>
                                <input type="file" id="productImageInput" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                                
                                @if($product->image_url)
                                    <label class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100 cursor-pointer transition">
                                        <input type="checkbox" name="remove_image" value="1" id="removeImageCheckbox" onchange="toggleRemoveImage(this)" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                        <span>Hapus Foto Saat Ini</span>
                                    </label>
                                @endif

                                <button type="button" id="btnResetUpload" onclick="resetImageSelection()" class="hidden inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Batal Pilih</span>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-500" id="fileInfoText">Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong>. Maksimal ukuran file <strong>2 MB</strong>.</p>
                            <p class="text-[11px] font-semibold text-rose-600 hidden" id="fileErrorText"></p>
                        </div>
                    </div>
                </div>

                {{-- FORM FIELDS --}}
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">SKU / Kode Produk <span class="text-rose-500">*</span></label>
                        <input name="sku" value="{{ old('sku', $product->sku) }}" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('sku') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Nama Produk <span class="text-rose-500">*</span></label>
                        <input name="name" value="{{ old('name', $product->name) }}" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Kategori</label>
                        <input name="category" value="{{ old('category', $product->category) }}" placeholder="Contoh: Hidroponik, Greenhouse" class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('category') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Satuan <span class="text-rose-500">*</span></label>
                        <input name="unit" value="{{ old('unit', $product->unit) }}" required placeholder="Pcs, Box, Karton, Slab" class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('unit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Harga Jual (Rp) <span class="text-rose-500">*</span></label>
                        <input name="price" value="{{ old('price', $product->price) }}" type="number" min="0" step="0.01" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('price') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Stok Gudang <span class="text-rose-500">*</span></label>
                        <input name="stock_on_hand" value="{{ old('stock_on_hand', $product->stock_on_hand) }}" type="number" min="0" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('stock_on_hand') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Stok Dibawa Sales</label>
                        <input name="stock_carried" value="{{ old('stock_carried', $product->stock_carried) }}" type="number" min="0" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('stock_carried') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Barang Retur</label>
                        <input name="stock_returned" value="{{ old('stock_returned', $product->stock_returned ?? 0) }}" type="number" min="0" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('stock_returned') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batal</span>
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-600/30 hover:bg-blue-500 transition active:scale-95">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const originalImageUrl = @json($product->image_url);
        const MAX_SIZE_MB = 2;
        const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;
        const input = document.getElementById('productImageInput');
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('placeholderPreview');
        const btnReset = document.getElementById('btnResetUpload');
        const infoText = document.getElementById('fileInfoText');
        const errorText = document.getElementById('fileErrorText');

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Check file size (max 2MB)
            if (file.size > MAX_SIZE_BYTES) {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                errorText.textContent = `Ukuran file terlalu besar (${sizeMB} MB)! Maksimal ukuran yang diizinkan adalah ${MAX_SIZE_MB} MB.`;
                errorText.classList.remove('hidden');
                
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Gambar Terlalu Besar',
                        html: `Ukuran file gambar yang dipilih adalah <b>${sizeMB} MB</b>.<br>Maksimal ukuran gambar yang diizinkan adalah <b>${MAX_SIZE_MB} MB</b>. Silakan pilih gambar lain atau kompres terlebih dahulu.`,
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#2563eb'
                    });
                }
                resetImageSelection();
                return;
            }

            errorText.classList.add('hidden');
            const sizeKB = (file.size / 1024).toFixed(0);
            infoText.innerHTML = `<span class="text-emerald-600 font-semibold">✓ ${file.name}</span> (${sizeKB} KB - baru)`;

            const reader = new FileReader();
            reader.onload = function(evt) {
                preview.src = evt.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                btnReset.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        function resetImageSelection() {
            input.value = '';
            btnReset.classList.add('hidden');
            errorText.classList.add('hidden');

            if (originalImageUrl) {
                preview.src = originalImageUrl;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                infoText.innerHTML = 'Menampilkan foto produk saat ini.';
            } else {
                preview.src = '#';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                infoText.innerHTML = 'Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong>. Maksimal ukuran file <strong>2 MB</strong>.';
            }
        }

        function toggleRemoveImage(checkbox) {
            if (checkbox.checked) {
                preview.classList.add('opacity-30', 'grayscale');
                infoText.innerHTML = '<span class="text-rose-600 font-semibold">Foto akan dihapus saat disimpan.</span>';
            } else {
                preview.classList.remove('opacity-30', 'grayscale');
                infoText.innerHTML = 'Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong>. Maksimal ukuran file <strong>2 MB</strong>.';
            }
        }
    </script>
</x-app-layout>
