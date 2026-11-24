<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::with('team', 'roles')->latest()->paginate(15),
            'roles' => Role::orderBy('name')->get(),
            'teams' => Team::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return $this->index();
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role']);

        if ($request->filled('team_name')) {
            $team = Team::findOrCreateByName($request->input('team_name'));
            $data['team_id'] = $team?->id;
        } elseif ($request->has('team_name') && empty($request->input('team_name'))) {
            $data['team_id'] = null;
        }
        unset($data['team_name']);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        $user = User::create($data);
        $user->assignRole($role);

        if ($user->hasRole('Supervisor Sales') && $user->team_id) {
            Team::where('id', $user->team_id)->whereNull('supervisor_id')->update(['supervisor_id' => $user->id]);
        }

        return to_route('modules.users')->with('status', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $authUser = Auth::user();

        // Prevent self-privilege-escalation
        abort_if($user->id === $authUser->id && $request->has('role'), 403, 'Anda tidak dapat mengubah role akun sendiri.');

        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,'.$user->id],
            'role' => ['sometimes', 'string', 'exists:roles,name'],
            'team_name' => ['nullable', 'string', 'max:100'],
            'team_id' => ['nullable'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        // Validasi password hanya jika diisi secara eksplisit (hindari error konfirmasi saat autofill browser)
        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8'];
            if ($request->filled('password_confirmation')) {
                $rules['password'][] = 'confirmed';
            }
        }

        $validated = $request->validate($rules);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (isset($validated['role'])) {
            $role = $validated['role'];
            unset($validated['role']);
            $user->syncRoles([$role]);
        }

        if ($request->has('team_name')) {
            $teamName = trim($request->input('team_name') ?? '');
            if ($teamName !== '') {
                $team = Team::findOrCreateByName($teamName);
                $validated['team_id'] = $team?->id;

                if ($user->hasRole('Supervisor Sales') || ($request->has('role') && $request->input('role') === 'Supervisor Sales')) {
                    $team?->update(['supervisor_id' => $user->id]);
                }
            } else {
                $validated['team_id'] = null;
            }
            unset($validated['team_name']);
        }

        $user->update($validated);

        return to_route('modules.users')->with('status', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 403, 'Anda tidak dapat menonaktifkan akun sendiri.');

        // Soft deactivate instead of delete to preserve data integrity
        $user->update(['is_active' => false]);

        return to_route('modules.users')->with('status', "Akun {$user->name} telah dinonaktifkan.");
    }
}
