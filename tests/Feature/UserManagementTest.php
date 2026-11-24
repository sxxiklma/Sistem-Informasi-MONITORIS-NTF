<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Role $adminRole;

    protected Role $salesRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $this->salesRole = Role::firstOrCreate(['name' => 'Sales Motoris', 'guard_name' => 'web']);

        $permission = Permission::firstOrCreate(['name' => 'kelola pengguna', 'guard_name' => 'web']);
        $this->adminRole->givePermissionTo($permission);

        $this->admin = User::factory()->create();
        $this->admin->assignRole($this->adminRole);
    }

    public function test_admin_can_create_user_with_custom_typed_team_name(): void
    {
        $response = $this->actingAs($this->admin)->post(route('modules.users.store'), [
            'name' => 'Budi Sales Baru',
            'email' => 'budi.baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Sales Motoris',
            'team_name' => 'Tim Sales Surabaya Barat',
        ]);

        $response->assertRedirect(route('modules.users'));

        $this->assertDatabaseHas('teams', [
            'name' => 'Tim Sales Surabaya Barat',
        ]);

        $user = User::where('email', 'budi.baru@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->team_id);
        $this->assertEquals('Tim Sales Surabaya Barat', $user->team->name);
    }

    public function test_admin_creating_user_with_existing_team_reuses_same_team(): void
    {
        $team = Team::create([
            'name' => 'Tim Surabaya Pusat',
            'code' => 'TM-SBY-PUSAT',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('modules.users.store'), [
            'name' => 'Joko Sales',
            'email' => 'joko@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Sales Motoris',
            'team_name' => 'tim surabaya pusat', // Case-insensitive match
        ]);

        $response->assertRedirect(route('modules.users'));

        $user = User::where('email', 'joko@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals($team->id, $user->team_id);
        $this->assertEquals(1, Team::where('name', 'Tim Surabaya Pusat')->count());
    }

    public function test_admin_can_update_user_team_name(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->salesRole);

        $response = $this->actingAs($this->admin)->put(route('modules.users.update', $user), [
            'name' => 'Updated User',
            'team_name' => 'Tim Sales Malang',
        ]);

        $response->assertRedirect(route('modules.users'));

        $user->refresh();
        $this->assertEquals('Tim Sales Malang', $user->team?->name);

        // Update again to empty string clears the team
        $response = $this->actingAs($this->admin)->put(route('modules.users.update', $user), [
            'team_name' => '',
        ]);

        $response->assertRedirect(route('modules.users'));
        $user->refresh();
        $this->assertNull($user->team_id);
    }

    public function test_admin_can_update_user_without_password_confirmation_error(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('originalpassword'),
        ]);
        $user->assignRole($this->salesRole);

        // Submit form with empty password field (as happens when user only changes team)
        $response = $this->actingAs($this->admin)->put(route('modules.users.update', $user), [
            'name' => 'User Name Kept',
            'email' => $user->email,
            'role' => 'Sales Motoris',
            'team_name' => 'Tim Surabaya',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('modules.users'));

        $user->refresh();
        $this->assertEquals('Tim Surabaya', $user->team?->name);
        $this->assertTrue(Hash::check('originalpassword', $user->password));
    }

    public function test_admin_can_update_user_password_when_explicitly_filled(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);
        $user->assignRole($this->salesRole);

        $response = $this->actingAs($this->admin)->put(route('modules.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'Sales Motoris',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('modules.users'));

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $user->password));
    }
}
