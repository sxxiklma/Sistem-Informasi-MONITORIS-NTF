<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Team;
use App\Models\User;
use App\Models\VisitTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $sales;

    private Team $team;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('lihat laporan');
        $adminRole = Role::findOrCreate('Admin');
        $adminRole->givePermissionTo('lihat laporan');

        $salesRole = Role::findOrCreate('Sales Motoris');

        $this->team = Team::create([
            'name' => 'Tim Surabaya Pusat',
            'code' => 'TEAM-SBY-01',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Motoris',
            'email' => 'admin@motoris.test',
            'is_active' => true,
        ]);
        $this->admin->assignRole($adminRole);

        $this->sales = User::factory()->create([
            'name' => 'Budi Sales',
            'email' => 'budi@motoris.test',
            'team_id' => $this->team->id,
            'is_active' => true,
        ]);
        $this->sales->assignRole($salesRole);

        $this->customer = Customer::create([
            'code' => 'CUST-001',
            'name' => 'Toko Berkah Sejahtera',
            'address' => 'Jl. Embong Malang No. 12 Surabaya',
            'phone' => '08123456789',
        ]);
    }

    public function test_admin_can_view_reports_sales_page(): void
    {
        SalesOrder::create([
            'order_number' => 'ORD-TEST-001',
            'user_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'order_date' => now()->toDateString(),
            'status' => 'completed',
            'total_amount' => 500000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('modules.reports', ['type' => 'sales']));

        $response->assertStatus(200);
        $response->assertSee('Pusat Laporan');
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('ORD-TEST-001');
        $response->assertSee('Toko Berkah Sejahtera');
        $response->assertSee('Export Excel (.xlsx)');
        $response->assertSee('Export PDF (.pdf)');
    }

    public function test_admin_can_view_reports_visits_duration_page(): void
    {
        VisitTask::create([
            'task_code' => 'TASK-TEST-001',
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->admin->id,
            'scheduled_at' => now()->subHours(2),
            'check_in_at' => now()->subHours(2),
            'check_out_at' => now()->subHour(),
            'check_in_distance_meters' => 15.0,
            'status' => 'SELESAI',
            'purpose' => 'Kunjungan rutin dan cek stok',
        ]);

        $response = $this->actingAs($this->admin)->get(route('modules.reports', ['type' => 'visits']));

        $response->assertStatus(200);
        $response->assertSee('Laporan Kunjungan');
        $response->assertSee('TASK-TEST-001');
        $response->assertSee('Toko Berkah Sejahtera');
        $response->assertSee('Durasi Waktu');
        $response->assertSee('SELESAI');
    }

    public function test_admin_can_filter_reports_by_search_query(): void
    {
        SalesOrder::create([
            'order_number' => 'ORD-ALPHA-99',
            'user_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'order_date' => now()->toDateString(),
            'status' => 'completed',
            'total_amount' => 250000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('modules.reports', [
            'type' => 'sales',
            'search' => 'ALPHA',
        ]));

        $response->assertStatus(200);
        $response->assertSee('ORD-ALPHA-99');
    }

    public function test_admin_can_export_sales_report_to_excel(): void
    {
        SalesOrder::create([
            'order_number' => 'ORD-EXP-001',
            'user_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'order_date' => now()->toDateString(),
            'status' => 'completed',
            'total_amount' => 150000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.excel', ['type' => 'sales']));

        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));
    }

    public function test_admin_can_export_sales_report_to_pdf(): void
    {
        SalesOrder::create([
            'order_number' => 'ORD-PDF-001',
            'user_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'order_date' => now()->toDateString(),
            'status' => 'completed',
            'total_amount' => 350000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.pdf', ['type' => 'sales']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_admin_can_export_visits_report_to_excel_and_pdf(): void
    {
        VisitTask::create([
            'task_code' => 'TASK-EXP-001',
            'sales_id' => $this->sales->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->admin->id,
            'scheduled_at' => now(),
            'check_in_at' => now()->subMinutes(45),
            'check_out_at' => now(),
            'status' => 'SELESAI',
            'purpose' => 'Penagihan nota',
        ]);

        $excelRes = $this->actingAs($this->admin)->get(route('reports.export.excel', ['type' => 'visits']));
        $excelRes->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $excelRes->headers->get('Content-Type'));

        $pdfRes = $this->actingAs($this->admin)->get(route('reports.export.pdf', ['type' => 'visits']));
        $pdfRes->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfRes->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $pdfRes->getContent());
    }

    public function test_user_without_permission_cannot_access_reports(): void
    {
        $unauthorized = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($unauthorized)->get(route('modules.reports'));
        $response->assertStatus(403);

        $excelRes = $this->actingAs($unauthorized)->get(route('reports.export.excel'));
        $excelRes->assertStatus(403);

        $pdfRes = $this->actingAs($unauthorized)->get(route('reports.export.pdf'));
        $pdfRes->assertStatus(403);
    }
}
