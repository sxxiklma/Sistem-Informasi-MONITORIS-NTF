<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerTypeController extends Controller
{
    /**
     * Tampilkan halaman daftar pengaturan jenis/kategori pelanggan
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && ($user->can('kelola master data') || $user->can('kelola pengaturan')), 403);

        $query = CustomerType::withCount('customers')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $customerTypes = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_types' => CustomerType::count(),
            'active_types' => CustomerType::where('is_active', true)->count(),
            'inactive_types' => CustomerType::where('is_active', false)->count(),
            'total_customers' => Customer::count(),
        ];

        return view('customer-types.index', compact('customerTypes', 'metrics'));
    }

    /**
     * Simpan jenis/kategori pelanggan baru
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User && ($user->can('kelola master data') || $user->can('kelola pengaturan')), 403);

        // Auto-generate code if empty
        if (! $request->filled('code') && $request->filled('name')) {
            $baseCode = Str::slug($request->get('name'), '_');
            $request->merge(['code' => $baseCode]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:customer_types,code', 'regex:/^[a-zA-Z0-9_\-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'badge_color' => ['nullable', 'in:amber,sky,emerald,purple,indigo,rose,slate'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.unique' => 'Kode jenis pelanggan sudah digunakan, gunakan kode unik lain.',
            'code.regex' => 'Kode hanya boleh menggunakan huruf, angka, garis bawah (_), atau strip (-).',
        ]);

        $validated['badge_color'] = $validated['badge_color'] ?? 'amber';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        CustomerType::create($validated);

        return back()->with('status', "Kategori / Jenis Pelanggan '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data jenis/kategori pelanggan
     */
    public function update(Request $request, CustomerType $customerType): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User && ($user->can('kelola master data') || $user->can('kelola pengaturan')), 403);

        $oldCode = $customerType->code;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', Rule::unique('customer_types', 'code')->ignore($customerType->id), 'regex:/^[a-zA-Z0-9_\-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'badge_color' => ['nullable', 'in:amber,sky,emerald,purple,indigo,rose,slate'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['badge_color'] = $validated['badge_color'] ?? 'amber';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $customerType->update($validated);

        // If code changed, cascade update to existing customers
        if ($oldCode !== $validated['code']) {
            Customer::where('type', $oldCode)->update(['type' => $validated['code']]);
        }

        return back()->with('status', "Kategori / Jenis Pelanggan '{$validated['name']}' berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / non-aktif
     */
    public function toggleStatus(CustomerType $customerType): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User && ($user->can('kelola master data') || $user->can('kelola pengaturan')), 403);

        $customerType->update([
            'is_active' => ! $customerType->is_active,
        ]);

        $statusText = $customerType->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('status', "Kategori '{$customerType->name}' berhasil {$statusText}.");
    }

    /**
     * Hapus jenis/kategori pelanggan
     */
    public function destroy(CustomerType $customerType): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User && ($user->can('kelola master data') || $user->can('kelola pengaturan')), 403);

        $customerCount = $customerType->customers()->count();

        if ($customerCount > 0) {
            return back()->withErrors("Kategori '{$customerType->name}' tidak dapat dihapus karena sedang digunakan oleh {$customerCount} outlet/pelanggan. Anda dapat menonaktifkan statusnya agar tidak muncul di form input.");
        }

        $name = $customerType->name;
        $customerType->delete();

        return back()->with('status', "Kategori '{$name}' berhasil dihapus.");
    }
}
