<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::with('assignedUser')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            if ($type !== 'semua') {
                $query->where('type', $type);
            }
        }

        $customers = $query->get()->map(fn ($c) => [
            'id' => $c->id,
            'code' => $c->code,
            'name' => $c->name,
            'type' => $c->type,
            'phone' => $c->phone ?? '-',
            'address' => $c->address ?? '-',
            'photo' => $c->photo,
            'photo_url' => $c->photo_url,
            'latitude' => $c->latitude,
            'longitude' => $c->longitude,
            'assigned_user' => $c->assignedUser?->name ?? 'Belum Ditugaskan',
            'is_active' => (bool) $c->is_active,
        ]);

        $types = Customer::distinct()->pluck('type')->filter()->values();
        $nextId = (Customer::max('id') ?? 0) + 1;
        $suggestedCode = 'OUT-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'total' => $customers->count(),
            'types' => $types,
            'suggested_code' => $suggestedCode,
            'customers' => $customers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $nextId = (Customer::max('id') ?? 0) + 1;
        $code = $request->input('code') ?: ('OUT-'.str_pad($nextId, 5, '0', STR_PAD_LEFT));

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('customers', 'public');
        }

        $customer = Customer::create([
            ...$validated,
            'photo' => $photoPath,
            'code' => $code,
            'assigned_user_id' => $request->input('assigned_user_id', $request->user()->id),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Outlet {$customer->name} berhasil ditambahkan.",
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['sometimes', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                Storage::disk('public')->delete($customer->photo);
            }
            $validated['photo'] = $request->file('photo')->store('customers', 'public');
        }

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Data outlet {$customer->name} berhasil diperbarui.",
            'customer' => $customer,
        ]);
    }
}
