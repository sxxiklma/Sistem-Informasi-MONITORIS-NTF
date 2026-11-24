<x-guest-layout>
    <div class="mb-6" x-data="{
        email: '{{ old('email', 'admin@motoris.test') }}',
        password: 'password',
        showPassword: false,
        setAccount(em) {
            this.email = em;
            this.password = 'password';
        }
    }">
        {{-- Header & Tagline --}}
        <div class="mb-5">
            <div class="inline-flex items-center gap-2 rounded-full border border-sky-400/30 bg-sky-500/10 px-3 py-1 text-[11px] font-semibold text-sky-300 mb-3">
                <span class="relative flex h-2 w-2">
                    <span class="anim-pulse-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Sistem Operasional Motoris</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Masuk ke Akun</h1>
            <p class="mt-1.5 text-xs text-slate-400 leading-relaxed">Gunakan kredensial Anda untuk memantau aktivitas sales dan rute distribusi.</p>
        </div>

        {{-- 1-Click Quick Account Switcher --}}
        <div class="mb-5 p-2.5 rounded-2xl border border-slate-800 bg-slate-950/70">
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 px-0.5">
                <span>Pilih Akun Login Cepat:</span>
                <span class="text-sky-400 font-mono">Pass: password</span>
            </div>
            <div class="grid grid-cols-3 gap-1.5 text-xs">
                <button type="button" x-on:click="setAccount('admin@motoris.test')" class="px-2 py-1.5 rounded-xl border border-slate-700/80 bg-slate-900/90 hover:bg-sky-500/20 hover:border-sky-400/50 text-slate-200 font-medium transition text-center truncate active:scale-95">
                    👑 Admin
                </button>
                <button type="button" x-on:click="setAccount('sofan@motoris.test')" class="px-2 py-1.5 rounded-xl border border-slate-700/80 bg-slate-900/90 hover:bg-sky-500/20 hover:border-sky-400/50 text-slate-200 font-medium transition text-center truncate active:scale-95">
                    🛵 Sofan (Sales)
                </button>
                <button type="button" x-on:click="setAccount('supervisor@motoris.test')" class="px-2 py-1.5 rounded-xl border border-slate-700/80 bg-slate-900/90 hover:bg-sky-500/20 hover:border-sky-400/50 text-slate-200 font-medium transition text-center truncate active:scale-95">
                    👔 Supervisor
                </button>
            </div>
        </div>

        <x-auth-session-status class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-950/60 p-3 text-xs text-emerald-300" :status="session('status')" />

        {{-- Login Form with Dark Inputs (NO stark white boxes!) --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            
            {{-- Email Field --}}
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Alamat Email
                </label>
                <div class="relative mt-1.5">
                    <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-sky-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    </div>
                    <input id="email" x-model="email" type="email" name="email" required autofocus autocomplete="username" 
                           class="block w-full rounded-xl border border-slate-700/80 bg-slate-950/80 ps-10 pe-4 py-3 text-sm text-white placeholder-slate-500 focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition" 
                           placeholder="nama@motoris.test" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-400" />
            </div>

            {{-- Password Field --}}
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Kata Sandi
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-xs font-medium text-sky-400 hover:text-sky-300 transition" href="{{ route('password.request') }}">
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>
                <div class="relative mt-1.5">
                    <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-sky-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input id="password" x-model="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" 
                           class="block w-full rounded-xl border border-slate-700/80 bg-slate-950/80 ps-10 pe-11 py-3 text-sm text-white placeholder-slate-500 focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition" 
                           placeholder="Masukkan kata sandi" />
                    <button type="button" x-on:click="showPassword = !showPassword" class="absolute inset-y-0 end-0 flex w-11 items-center justify-center text-slate-400 hover:text-sky-400 transition">
                        <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-400" />
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-950 text-sky-500 shadow-sm focus:ring-sky-400 focus:ring-offset-slate-900">
                    <span>Ingat sesi masuk ini</span>
                </label>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 via-blue-600 to-sky-600 hover:from-sky-400 hover:to-blue-500 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition active:scale-[0.98]">
                <span>Masuk ke Dashboard</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>
    </div>
</x-guest-layout>
