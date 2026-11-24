<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['roles', 'team'])->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter = $request->get('role')) {
            if ($roleFilter !== 'semua') {
                $query->role($roleFilter);
            }
        }

        $users = $query->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->roles->first()?->name ?? 'Sales Motoris',
            'team' => $u->team?->name ?? 'Tanpa Tim',
            'team_id' => $u->team_id,
            'is_active' => (bool) $u->is_active,
        ]);

        $roles = Role::orderBy('name')->pluck('name');
        $teams = Team::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'total' => $users->count(),
            'roles' => $roles,
            'teams' => $teams,
            'users' => $users,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'team_name' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $teamId = null;
        if (! empty($validated['team_name'])) {
            $team = Team::findOrCreateByName($validated['team_name']);
            $teamId = $team?->id;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'team_id' => $teamId,
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'success' => true,
            'message' => "Pengguna {$user->name} ({$validated['role']}) berhasil ditambahkan.",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $validated['role'],
                'team' => $user->team?->name ?? 'Tanpa Tim',
            ],
        ]);
    }
}
