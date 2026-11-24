<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::with(['assignedUser', 'customerType'])->latest()->paginate(12);

        return view('customers.index', ['customers' => $customers]);
    }

    public function create(): View
    {
        $nextId = (Customer::max('id') ?? 0) + 1;
        $suggestedCode = 'OUT-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);
        $customerTypes = CustomerType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('customers.create', [
            'users' => User::role('Sales Motoris')->orderBy('name')->get(),
            'suggestedCode' => $suggestedCode,
            'customerTypes' => $customerTypes,
        ]);
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('customers', 'public');
        }

        unset($data['delete_photo']);

        Customer::create($data);

        return to_route('customers.index')->with('status', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer): View
    {
        $customerTypes = CustomerType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('customers.create', [
            'customer' => $customer,
            'users' => User::role('Sales Motoris')->orderBy('name')->get(),
            'customerTypes' => $customerTypes,
        ]);
    }

    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        if ($request->boolean('delete_photo')) {
            if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                Storage::disk('public')->delete($customer->photo);
            }
            $data['photo'] = null;
        } elseif ($request->hasFile('photo')) {
            if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                Storage::disk('public')->delete($customer->photo);
            }
            $data['photo'] = $request->file('photo')->store('customers', 'public');
        }

        unset($data['delete_photo']);

        $customer->update($data);

        return to_route('customers.index')->with('status', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->update(['is_active' => false]);

        return to_route('customers.index')->with('status', 'Pelanggan dinonaktifkan.');
    }
}
