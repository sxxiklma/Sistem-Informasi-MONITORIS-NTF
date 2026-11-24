<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.22em] text-blue-600">Administrasi & Akses</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Manajemen Pengguna</h2>
            </div>
            <button type="button" data-open-user-modal class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm flex items-center justify-between" data-alert="{{ session('status') }}">
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
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Role Akses</th>
                                <th class="px-6 py-4">Tim Penjualan</th>
                                <th class="px-6 py-4">Status Akun</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-medium text-slate-900">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-700 text-xs border border-slate-200">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                                <div class="text-[11px] text-slate-400">ID #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-800 border border-amber-200">
                                                <svg class="h-3 w-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <span>{{ $user->team?->name ?: 'Belum ditentukan' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->is_active)
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
                                            <!-- Button View / Detail -->
                                            <button type="button" 
                                                    onclick='viewUserDetail(@json($user))'
                                                    title="Lihat Detail Pengguna" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 shadow-2xs hover:bg-blue-100 hover:border-blue-300 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>

                                            <!-- Button Edit User Role / Team -->
                                            <button type="button" 
                                                    onclick='openEditUserModal(@json($user))'
                                                    title="Edit Role & Tim" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-300 bg-amber-50 text-amber-700 shadow-2xs hover:bg-amber-100 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>

                                            <!-- Button Deactivate (Soft Delete) -->
                                            @if($user->id !== auth()->id())
                                                @if($user->is_active)
                                                <form method="POST" action="{{ route('modules.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Nonaktifkan akun {{ $user->name }}? Pengguna tidak akan bisa login.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Nonaktifkan Akun" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 shadow-2xs hover:bg-rose-100 transition">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    </button>
                                                </form>
                                                @else
                                                <form method="POST" action="{{ route('modules.users.update', $user) }}" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_active" value="1">
                                                    <button type="submit" title="Aktifkan Kembali Akun" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-600 shadow-2xs hover:bg-emerald-100 transition">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </button>
                                                </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </div>
                                        Belum ada data pengguna yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE USER CARDS (md:hidden) --}}
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($users as $user)
                        <div class="p-4 space-y-2.5 hover:bg-slate-50/70 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-800 text-xs border border-slate-200">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-900 text-sm truncate">{{ $user->name }}</h4>
                                        <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                                @if($user->is_active)
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

                            <div class="flex flex-wrap items-center gap-1.5 pt-1 text-xs">
                                @forelse($user->roles as $role)
                                    <span class="rounded bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-800 border border-amber-200/60">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-slate-400 text-[11px]">Tanpa Role</span>
                                @endforelse
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-slate-600 text-xs font-medium">
                                    {{ $user->team ? $user->team->name : 'Belum ditentukan' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 border-t border-slate-100 pt-2">
                                <button type="button" 
                                        onclick='viewUserDetail(@json($user))'
                                        class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 active:scale-95 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>

                                <button type="button"
                                        onclick='openEditUserModal(@json($user))'
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100 active:scale-95 transition" title="Edit">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                @if($user->id !== auth()->id())
                                    @if($user->is_active)
                                    <form method="POST" action="{{ route('modules.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Nonaktifkan akun {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 active:scale-95 transition" title="Nonaktifkan">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <p class="text-xs">Belum ada data pengguna yang terdaftar.</p>
                        </div>
                    @endforelse
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH PENGGUNA -->
    <div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-2xs">
        <div class="w-full max-w-lg rounded-2xl bg-white p-5 sm:p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-blue-600">Akun Pengguna Baru</p>
                    <h3 class="mt-1 text-xl font-bold text-slate-900">Tambah Akun Pegawai</h3>
                </div>
                <button type="button" data-close-user-modal class="rounded-lg p-1 text-2xl leading-none text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">&times;</button>
            </div>

            <form method="POST" action="{{ route('modules.users.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input name="name" required placeholder="Contoh: Budi Santoso" class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Alamat Email <span class="text-rose-500">*</span></label>
                    <input name="email" type="email" required placeholder="budi@perusahaan.com" class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Role / Hak Akses <span class="text-rose-500">*</span></label>
                        <select name="role" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tim Sales</label>
                        <input name="team_name" type="text" list="teamNamesList" placeholder="Ketik nama tim sales (cth: Tim Surabaya)..." class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <datalist id="teamNamesList">
                            @foreach($teams as $team)
                                <option value="{{ $team->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Password <span class="text-rose-500">*</span></label>
                        <div class="relative mt-1.5">
                            <input id="createPassword" name="password" type="password" minlength="8" required placeholder="Minimal 8 karakter" class="w-full rounded-lg border-slate-300 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" onclick="togglePasswordVisibility('createPassword', this)" title="Lihat / Sembunyikan Password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Konfirmasi Password <span class="text-rose-500">*</span></label>
                        <div class="relative mt-1.5">
                            <input id="createPasswordConfirmation" name="password_confirmation" type="password" minlength="8" required placeholder="Ketik ulang password" class="w-full rounded-lg border-slate-300 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" onclick="togglePasswordVisibility('createPasswordConfirmation', this)" title="Lihat / Sembunyikan Password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" data-close-user-modal class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                        <span>Batal</span>
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-slate-800 transition">
                        <span>Simpan Pengguna</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PENGGUNA -->
    <div id="editUserModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-2xs">
        <div class="w-full max-w-lg rounded-2xl bg-white p-5 sm:p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-blue-600">Perbarui Akses</p>
                    <h3 class="mt-1 text-xl font-bold text-slate-900" id="editUserNameTitle">Edit Pengguna</h3>
                </div>
                <button type="button" onclick="document.getElementById('editUserModal').classList.replace('flex', 'hidden')" class="rounded-lg p-1 text-2xl leading-none text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">&times;</button>
            </div>

            <form id="formEditUser" method="POST" autocomplete="off" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Nama Lengkap</label>
                    <input id="editName" name="name" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Alamat Email</label>
                    <input id="editEmail" name="email" type="email" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Role / Hak Akses</label>
                        <select id="editRole" name="role" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tim Sales</label>
                        <input id="editTeam" name="team_name" type="text" list="teamNamesList" placeholder="Ketik nama tim sales (cth: Tim Surabaya)..." class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Ganti Password (Opsional)</label>
                        <div class="relative mt-1.5">
                            <input id="editPassword" name="password" type="password" minlength="8" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah" class="w-full rounded-lg border-slate-300 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" onclick="togglePasswordVisibility('editPassword', this)" title="Lihat / Sembunyikan Password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Ulangi Password Baru</label>
                        <div class="relative mt-1.5">
                            <input id="editPasswordConfirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" placeholder="Ketik ulang jika diganti" class="w-full rounded-lg border-slate-300 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" onclick="togglePasswordVisibility('editPasswordConfirmation', this)" title="Lihat / Sembunyikan Password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('editUserModal').classList.replace('flex', 'hidden')" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-amber-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const userModal = document.getElementById('userModal');
        document.querySelector('[data-open-user-modal]').addEventListener('click', () => userModal.classList.replace('hidden', 'flex'));
        document.querySelectorAll('[data-close-user-modal]').forEach(b => b.addEventListener('click', () => userModal.classList.replace('flex', 'hidden')));

        function viewUserDetail(u) {
            if (!window.Swal) return;
            const roles = u.roles ? u.roles.map(r => r.name).join(', ') : '-';
            window.Swal.fire({
                title: u.name,
                html: `
                    <div class="text-left text-xs space-y-2 p-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <p><span class="font-bold text-slate-900">Nama:</span> ${u.name}</p>
                        <p><span class="font-bold text-slate-900">Email:</span> ${u.email}</p>
                        <p><span class="font-bold text-slate-900">Role Akses:</span> ${roles || 'Tanpa role'}</p>
                        <p><span class="font-bold text-slate-900">Tim:</span> ${u.team ? u.team.name : 'Belum ditentukan'}</p>
                        <p><span class="font-bold text-slate-900">Status:</span> ${u.is_active ? 'Aktif' : 'Nonaktif'}</p>
                    </div>
                `,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#0f172a'
            });
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            btn.innerHTML = isPassword ? `
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
            ` : `
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            `;
        }

        function openEditUserModal(u) {
            document.getElementById('editUserNameTitle').textContent = `Edit Pengguna: ${u.name}`;
            document.getElementById('formEditUser').action = `/users/${u.id}`;
            document.getElementById('editName').value = u.name;
            document.getElementById('editEmail').value = u.email;
            if (u.roles && u.roles.length > 0) {
                document.getElementById('editRole').value = u.roles[0].name;
            }
            document.getElementById('editTeam').value = u.team ? u.team.name : '';

            // Reset password inputs to prevent unwanted browser autofill
            const p1 = document.getElementById('editPassword');
            const p2 = document.getElementById('editPasswordConfirmation');
            if (p1) { p1.value = ''; p1.type = 'password'; }
            if (p2) { p2.value = ''; p2.type = 'password'; }

            document.getElementById('editUserModal').classList.replace('hidden', 'flex');
        }
    </script>
</x-app-layout>
