<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            {{ __('Perbarui data akun, alamat email, dan foto profil yang tampil pada bilah atas sistem.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Profile Photo Upload & Preview --}}
        <div x-data="{
            photoPreview: null,
            removePhoto: false,
            updatePreview(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.removePhoto = false;
                const reader = new FileReader();
                reader.onload = (e) => { this.photoPreview = e.target.result; };
                reader.readAsDataURL(file);
            },
            triggerFileInput() {
                this.$refs.photoInput.click();
            },
            deletePhoto() {
                this.photoPreview = null;
                this.removePhoto = true;
                if (this.$refs.photoInput) this.$refs.photoInput.value = '';
            }
        }" class="space-y-3 pb-5 border-b border-slate-100">
            <x-input-label :value="__('Foto Profil')" class="font-bold text-slate-800 text-xs uppercase tracking-wider" />

            <input type="file" 
                   x-ref="photoInput" 
                   name="avatar" 
                   id="avatar" 
                   accept="image/*" 
                   class="hidden" 
                   @change="updatePreview($event)">
            
            <input type="hidden" name="remove_avatar" :value="removePhoto ? '1' : '0'">

            <div class="flex items-center gap-5">
                {{-- Avatar Preview Frame --}}
                <div class="relative h-20 w-20 sm:h-24 sm:w-24 shrink-0 rounded-2xl overflow-hidden border-2 border-slate-200 bg-slate-100 shadow-inner flex items-center justify-center">
                    {{-- New Photo Preview --}}
                    <template x-if="photoPreview">
                        <img :src="photoPreview" alt="Preview Foto Baru" class="h-full w-full object-cover">
                    </template>

                    {{-- Current Avatar or Initials --}}
                    <template x-if="!photoPreview">
                        <div class="h-full w-full flex items-center justify-center">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" 
                                     x-show="!removePhoto" 
                                     alt="{{ $user->name }}" 
                                     class="h-full w-full object-cover">
                                <div x-show="removePhoto" class="h-full w-full bg-slate-900 text-white font-bold text-xl flex items-center justify-center">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @else
                                <div class="h-full w-full bg-slate-900 text-white font-bold text-xl flex items-center justify-center">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                    </template>
                </div>

                {{-- Action Buttons & Guidance --}}
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" 
                                @click="triggerFileInput()" 
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-400 active:scale-95 transition">
                            <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Pilih Foto Baru</span>
                        </button>

                        @if($user->avatar)
                        <button type="button" 
                                x-show="!removePhoto" 
                                @click="deletePhoto()" 
                                class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 shadow-2xs hover:bg-rose-100 hover:border-rose-300 active:scale-95 transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Foto</span>
                        </button>
                        @endif

                        <button type="button" 
                                x-show="photoPreview || removePhoto" 
                                @click="photoPreview = null; removePhoto = false; if ($refs.photoInput) $refs.photoInput.value = '';" 
                                class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 transition">
                            <span>Batal</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-500">
                        Format yang didukung: JPG, PNG, WEBP, atau GIF (Maks. 2MB). Foto akan otomatis muncul pada bilah atas (*top bar*), menu navigasi, dan profil akun Anda.
                    </p>
                    <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="font-bold text-slate-800 text-xs uppercase tracking-wider" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="font-bold text-slate-800 text-xs uppercase tracking-wider" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-sm hover:bg-slate-800 active:scale-95 transition">
                <span>{{ __('Simpan Perubahan') }}</span>
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1.5"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ __('Profil berhasil diperbarui.') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>