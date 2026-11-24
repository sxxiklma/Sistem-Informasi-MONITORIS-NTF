<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Team;
use App\Models\User;
use App\Models\VisitTask;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class VisitTaskWorkflowTest extends TestCase
{
    protected User $admin;

    protected User $supervisor;

    protected User $sales;

    protected Team $team;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(DatabaseSeeder::class);

        $this->team = Team::firstOrCreate(['code' => 'TIM-TEST'], [
            'name' => 'Tim Test',
            'is_active' => true,
        ]);

        $this->admin = User::where('email', 'admin@motoris.test')->firstOrFail();
        $this->supervisor = User::where('email', 'supervisor@motoris.test')->firstOrFail();
        $this->sales = User::where('email', 'sales1@motoris.test')->firstOrFail();

        $this->supervisor->update(['team_id' => $this->team->id]);
        $this->sales->update(['team_id' => $this->team->id]);

        $this->customer = Customer::firstOrCreate(['code' => 'OUT-TEST'], [
            'name' => 'Toko Uji Coba',
            'address' => 'Jl. Uji No. 1',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'is_active' => true,
        ]);
    }

    public function test_supervisor_can_create_task_for_team_sales(): void
    {
        $response = $this->actingAs($this->supervisor)->post('/visit-tasks', [
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'priority' => 'tinggi',
            'purpose' => 'Kunjungan rutin testing',
        ]);

        $response->assertRedirect('/visit-tasks');
        $this->assertDatabaseHas('visit_tasks', [
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'status' => 'MENUNGGU_RESPONS',
            'priority' => 'tinggi',
        ]);
    }

    public function test_sales_can_accept_task(): void
    {
        $task = VisitTask::create([
            'task_code' => VisitTask::generateCode(),
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->supervisor->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'MENUNGGU_RESPONS',
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($this->sales)
            ->from('/visit-tasks')
            ->post("/visit-tasks/{$task->id}/accept");

        $response->assertSessionHas('status');
        $this->assertEquals('DITERIMA', $task->fresh()->status);
        $this->assertDatabaseHas('visit_task_responses', [
            'task_id' => $task->id,
            'response' => 'DITERIMA',
        ]);
    }

    public function test_sales_can_reject_task_with_reason(): void
    {
        $task = VisitTask::create([
            'task_code' => VisitTask::generateCode(),
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->supervisor->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'MENUNGGU_RESPONS',
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($this->sales)
            ->from('/visit-tasks')
            ->post("/visit-tasks/{$task->id}/reject", [
                'rejection_category' => 'Jadwal Bentrok',
                'reason' => 'Ada jadwal audit toko lain di jam yang sama.',
            ]);

        $response->assertSessionHas('status');
        $this->assertEquals('DITOLAK', $task->fresh()->status);
        $this->assertDatabaseHas('visit_task_responses', [
            'task_id' => $task->id,
            'response' => 'DITOLAK',
            'rejection_category' => 'Jadwal Bentrok',
        ]);
    }

    public function test_inactive_user_cannot_access_dashboard(): void
    {
        $inactiveSales = User::factory()->create([
            'is_active' => false,
            'password' => 'password',
        ]);
        $inactiveSales->assignRole('Sales Motoris');

        $response = $this->actingAs($inactiveSales)->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_sales_can_complete_visit_from_sedang_kunjungan(): void
    {
        $task = VisitTask::create([
            'task_code' => VisitTask::generateCode(),
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->supervisor->id,
            'scheduled_at' => now(),
            'status' => 'SEDANG_KUNJUNGAN',
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($this->sales)
            ->from("/visit-tasks/{$task->id}")
            ->post("/visit-tasks/{$task->id}/status", [
                'status' => 'SELESAI',
                'visit_result' => 'Toko berhasil order 5 box.',
                'visit_notes' => 'Tindak lanjut minggu depan.',
                'latitude' => -6.2001,
                'longitude' => 106.8001,
            ]);

        $response->assertSessionHas('status');
        $this->assertEquals('SELESAI', $task->fresh()->status);
        $this->assertEquals('Toko berhasil order 5 box.', $task->fresh()->visit_result);
    }

    public function test_duplicate_status_update_is_handled_idempotently_without_error(): void
    {
        $task = VisitTask::create([
            'task_code' => VisitTask::generateCode(),
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->supervisor->id,
            'scheduled_at' => now(),
            'status' => 'SELESAI',
            'priority' => 'normal',
        ]);

        // Second duplicate submit should NOT throw 422
        $response = $this->actingAs($this->sales)
            ->from("/visit-tasks/{$task->id}")
            ->post("/visit-tasks/{$task->id}/status", [
                'status' => 'SELESAI',
                'visit_result' => 'Toko berhasil order 5 box.',
            ]);

        $response->assertRedirect("/visit-tasks/{$task->id}");
        $response->assertSessionHas('status');
        $this->assertEquals('SELESAI', $task->fresh()->status);
    }

    public function test_sales_can_complete_visit_directly_from_tiba_di_lokasi(): void
    {
        $task = VisitTask::create([
            'task_code' => VisitTask::generateCode(),
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->supervisor->id,
            'scheduled_at' => now(),
            'status' => 'TIBA_DI_LOKASI',
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($this->sales)
            ->from("/visit-tasks/{$task->id}")
            ->post("/visit-tasks/{$task->id}/status", [
                'status' => 'SELESAI',
                'visit_result' => 'Drop pesanan cepat selesai.',
            ]);

        $response->assertSessionHas('status');
        $this->assertEquals('SELESAI', $task->fresh()->status);
    }

    public function test_sales_can_complete_visit_with_documentation_photo(): void
    {
        Storage::fake('public');

        $task = VisitTask::create([
            'task_code' => VisitTask::generateCode(),
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->supervisor->id,
            'scheduled_at' => now(),
            'status' => 'SEDANG_KUNJUNGAN',
            'priority' => 'normal',
        ]);

        $photo = UploadedFile::fake()->image('bukti_toko.jpg', 800, 600)->size(450); // 450 KB

        $response = $this->actingAs($this->sales)
            ->from("/visit-tasks/{$task->id}")
            ->post("/visit-tasks/{$task->id}/status", [
                'status' => 'SELESAI',
                'visit_result' => 'Toko telah diverifikasi dan display produk telah rapi.',
                'visit_notes' => 'Tanda terima invoice diserahkan ke kasir.',
                'documentation_image' => $photo,
            ]);

        $response->assertSessionHas('status');
        $task = $task->fresh();

        $this->assertEquals('SELESAI', $task->status);
        $this->assertNotNull($task->documentation_image);
        Storage::disk('public')->assertExists($task->documentation_image);

        // Journey payload includes documentation image
        $payload = $task->getJourneyPayload();
        $this->assertNotNull($payload['check_out']['documentation_image']);
        $this->assertStringContainsString('storage/documentation_photos/', $payload['check_out']['documentation_image']);
    }

    public function test_admin_and_supervisor_can_view_visit_documentation_photo(): void
    {
        Storage::fake('public');
        $stored = UploadedFile::fake()->image('bukti.jpg')->store('documentation_photos', 'public');

        $task = VisitTask::create([
            'task_code' => VisitTask::generateCode(),
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->supervisor->id,
            'scheduled_at' => now(),
            'status' => 'SELESAI',
            'priority' => 'normal',
            'visit_result' => 'Audit selesai.',
            'documentation_image' => $stored,
        ]);

        // 1. Admin views detail
        $adminResp = $this->actingAs($this->admin)->get("/visit-tasks/{$task->id}");
        $adminResp->assertOk();
        $adminResp->assertSee('Foto Dokumentasi Kunjungan Lapangan');
        $adminResp->assertSee($stored);

        // 2. Supervisor views detail
        $spvResp = $this->actingAs($this->supervisor)->get("/visit-tasks/{$task->id}");
        $spvResp->assertOk();
        $spvResp->assertSee('Foto Dokumentasi Kunjungan Lapangan');
        $spvResp->assertSee($stored);

        // 3. Admin views Rute & Navigasi Peta page
        $mapResp = $this->actingAs($this->admin)->get('/visits');
        $mapResp->assertOk();
        $mapResp->assertSee('Foto Dokumentasi');
    }

    public function test_documentation_photo_rejects_invalid_file_types(): void
    {
        Storage::fake('public');

        $task = VisitTask::create([
            'task_code' => VisitTask::generateCode(),
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->supervisor->id,
            'scheduled_at' => now(),
            'status' => 'SEDANG_KUNJUNGAN',
            'priority' => 'normal',
        ]);

        $invalidFile = UploadedFile::fake()->create('malicious.pdf', 100);

        $response = $this->actingAs($this->sales)
            ->from("/visit-tasks/{$task->id}")
            ->post("/visit-tasks/{$task->id}/status", [
                'status' => 'SELESAI',
                'visit_result' => 'Uji upload file non-gambar.',
                'documentation_image' => $invalidFile,
            ]);

        $response->assertSessionHasErrors(['documentation_image']);
        $this->assertEquals('SEDANG_KUNJUNGAN', $task->fresh()->status);
    }
}
