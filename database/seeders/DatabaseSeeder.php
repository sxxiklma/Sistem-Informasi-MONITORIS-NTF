<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\Customer;
use App\Models\Team;
use App\Models\User;
use App\Models\VisitTask;
use App\Models\VisitTaskHistory;
use App\Models\VisitTaskResponse;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'lihat dashboard', 'kelola pengguna', 'kelola master data', 'kelola kunjungan',
            'kelola pesanan', 'lihat laporan', 'kelola target', 'kelola pengaturan',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $roles = [
            'Admin' => $permissions,
            'Supervisor Sales' => ['lihat dashboard', 'kelola kunjungan', 'lihat laporan', 'kelola target'],
            'Sales Motoris' => ['lihat dashboard', 'kelola kunjungan', 'kelola pesanan'],
            'Pimpinan' => ['lihat dashboard', 'lihat laporan', 'kelola kunjungan'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            $role = Role::findOrCreate($name);
            $role->syncPermissions($rolePermissions);
        }

        // Teams
        $teamSouth = Team::updateOrCreate(['code' => 'TIM-SBY-P'], [
            'name' => 'Tim Surabaya Pusat',
            'is_active' => true,
        ]);

        $teamEast = Team::updateOrCreate(['code' => 'TIM-SBY-T'], [
            'name' => 'Tim Surabaya Timur',
            'is_active' => true,
        ]);

        // 1. Admin
        $admin = User::updateOrCreate(['email' => 'admin@motoris.test'], [
            'name' => 'Administrator Motoris',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->syncRoles(['Admin']);

        // 2. Pimpinan
        $pimpinan = User::updateOrCreate(['email' => 'pimpinan@motoris.test'], [
            'name' => 'Direktur Operasional',
            'password' => 'password',
            'is_active' => true,
        ]);
        $pimpinan->syncRoles(['Pimpinan']);

        // 3. Supervisor Sales
        $spv = User::updateOrCreate(['email' => 'supervisor@motoris.test'], [
            'name' => 'Budi Supervisor',
            'password' => 'password',
            'team_id' => $teamSouth->id,
            'is_active' => true,
        ]);
        $spv->syncRoles(['Supervisor Sales']);
        $teamSouth->update(['supervisor_id' => $spv->id]);

        // 4. Sales Motoris 1 (Tim Selatan)
        $sales1 = User::updateOrCreate(['email' => 'sales1@motoris.test'], [
            'name' => 'Andi Motoris (Pusat)',
            'password' => 'password',
            'team_id' => $teamSouth->id,
            'is_active' => true,
        ]);
        $sales1->syncRoles(['Sales Motoris']);

        // 5. Sales Motoris 2 (Tim Timur)
        $sales2 = User::updateOrCreate(['email' => 'sales2@motoris.test'], [
            'name' => 'Dedi Motoris (Timur)',
            'password' => 'password',
            'team_id' => $teamEast->id,
            'is_active' => true,
        ]);
        $sales2->syncRoles(['Sales Motoris']);

        // Sample Customers
        $c1 = Customer::updateOrCreate(['code' => 'OUT-001'], [
            'name' => 'Kios Barokah Mart',
            'type' => 'kios',
            'phone' => '081234567890',
            'address' => 'Jl. Basuki Rahmat No. 12, Tegalsari, Surabaya',
            'latitude' => -7.262500,
            'longitude' => 112.740500,
            'is_active' => true,
        ]);

        $c2 = Customer::updateOrCreate(['code' => 'OUT-002'], [
            'name' => 'Toko Sembako Maju Jaya',
            'type' => 'toko',
            'phone' => '081234567891',
            'address' => 'Jl. Raya Darmo No. 45, Wonokromo, Surabaya',
            'latitude' => -7.289200,
            'longitude' => 112.738100,
            'is_active' => true,
        ]);

        $c3 = Customer::updateOrCreate(['code' => 'OUT-003'], [
            'name' => 'Warung Berkah Rejeki',
            'type' => 'warung',
            'phone' => '081234567892',
            'address' => 'Jl. Pemuda No. 20, Genteng, Surabaya',
            'latitude' => -7.263500,
            'longitude' => 112.746000,
            'is_active' => true,
        ]);

        // Sample Visit Tasks
        if (VisitTask::count() === 0) {
            // Task 1: MENUNGGU_RESPONS for Sales 1
            $t1 = VisitTask::create([
                'task_code' => 'TASK-2026-00001',
                'sales_id' => $sales1->id,
                'supervisor_id' => $spv->id,
                'customer_id' => $c1->id,
                'created_by' => $spv->id,
                'scheduled_at' => now()->addHours(2),
                'due_at' => now()->addHours(6),
                'purpose' => 'Pengecekan stok berkala & penawaran varian produk baru',
                'target_result' => 'Dapatkan PO minimal 10 karton mie & minyak goreng',
                'priority' => 'tinggi',
                'status' => 'MENUNGGU_RESPONS',
            ]);
            VisitTaskHistory::create([
                'task_id' => $t1->id,
                'changed_by' => $spv->id,
                'from_status' => null,
                'to_status' => 'MENUNGGU_RESPONS',
                'notes' => 'Tugas dibuat oleh Budi Supervisor',
                'changed_at' => now()->subMinutes(15),
            ]);

            // Task 2: DITERIMA for Sales 1
            $t2 = VisitTask::create([
                'task_code' => 'TASK-2026-00002',
                'sales_id' => $sales1->id,
                'supervisor_id' => $spv->id,
                'customer_id' => $c2->id,
                'created_by' => $spv->id,
                'scheduled_at' => now()->addHours(4),
                'due_at' => now()->addHours(8),
                'purpose' => 'Pengiriman invoice & penagihan pembayaran nota #INV-88',
                'priority' => 'normal',
                'status' => 'DITERIMA',
                'responded_at' => now()->subMinutes(10),
            ]);
            VisitTaskResponse::create([
                'task_id' => $t2->id,
                'user_id' => $sales1->id,
                'response' => 'DITERIMA',
                'responded_at' => now()->subMinutes(10),
            ]);
            VisitTaskHistory::create([
                'task_id' => $t2->id,
                'changed_by' => $sales1->id,
                'from_status' => 'MENUNGGU_RESPONS',
                'to_status' => 'DITERIMA',
                'notes' => 'Tugas diterima oleh Andi Motoris',
                'changed_at' => now()->subMinutes(10),
            ]);

            // Task 3: SELESAI for Sales 1
            $t3 = VisitTask::create([
                'task_code' => 'TASK-2026-00003',
                'sales_id' => $sales1->id,
                'supervisor_id' => $spv->id,
                'customer_id' => $c3->id,
                'created_by' => $spv->id,
                'scheduled_at' => now()->subHours(3),
                'due_at' => now()->subHours(1),
                'purpose' => 'Kunjungan rutin mingguan & display produk',
                'priority' => 'normal',
                'status' => 'SELESAI',
                'responded_at' => now()->subHours(3),
                'check_in_at' => now()->subHours(2)->subMinutes(30),
                'check_in_latitude' => -6.224350,
                'check_in_longitude' => 106.860150,
                'check_in_distance_meters' => 12.5,
                'check_out_at' => now()->subHours(2),
                'check_out_latitude' => -6.224340,
                'check_out_longitude' => 106.860160,
                'visit_result' => 'Order 5 karton minyak goreng. Display rak telah dirapikan.',
                'visit_notes' => 'Pemilik warung minta dikirimkan brosur promo minggu depan.',
            ]);
            VisitTaskHistory::create([
                'task_id' => $t3->id,
                'changed_by' => $sales1->id,
                'from_status' => 'SEDANG_KUNJUNGAN',
                'to_status' => 'SELESAI',
                'notes' => 'Kunjungan selesai dan laporan check-out tersimpan.',
                'changed_at' => now()->subHours(2),
            ]);

            // Sample Notification for Sales 1
            AppNotification::send(
                recipientId: $sales1->id,
                type: 'task_assigned',
                title: 'Tugas Kunjungan Baru: Kios Barokah Mart',
                message: 'Anda mendapat tugas kunjungan berprioritas tinggi ke Kios Barokah Mart.',
                senderId: $spv->id,
                entityType: 'visit_tasks',
                entityId: $t1->id,
                actionUrl: route('visit-tasks.show', $t1)
            );
        }

        $this->call(NetafarmSeeder::class);
    }
}
