<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[.22em] text-blue-600">Selamat datang kembali</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Masuk ke ruang kerja</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan akun Anda untuk memantau aktivitas sales motoris.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="email" value="Alamat email" class="text-sm font-medium text-slate-700" />
            <x-text-input id="email" class="mt-2 block w-full rounded-lg border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@perusahaan.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <div class="flex items-center justify-between"><x-input-label for="password" value="Kata sandi" class="text-sm font-medium text-slate-700" />@if (Route::has('password.request'))<a class="text-xs font-medium text-blue-700 hover:text-blue-800" href="{{ route('password.request') }}">Lupa kata sandi?</a>@endif</div>
            <div x-data="{ showPassword: false }" class="relative mt-2"><x-text-input id="password" class="block w-full rounded-lg border-slate-300 px-4 py-3 pe-12 text-sm focus:border-blue-500 focus:ring-blue-500" x-bind:type="showPassword ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" /><button type="button" x-on:click="showPassword = !showPassword" class="absolute inset-y-0 end-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-blue-600" x-bind:aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"><svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg><svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6 0 9.5 6 9.5 6a17.7 17.7 0 0 1-3.1 3.9M6.4 6.5C3.8 8.1 2.5 12 2.5 12s3.5 6 9.5 6c1.1 0 2.1-.2 3-.5"/></svg></button></div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <label for="remember_me" class="flex items-center gap-2 text-sm text-slate-500"><input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember"><span>Ingat perangkat ini</span></label>
        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-blue-600/30 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Masuk ke dashboard</button>
    </form>
</x-guest-layout>
