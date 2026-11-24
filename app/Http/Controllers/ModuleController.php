<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ModuleController extends Controller
{
    public function targets(): View
    {
        return $this->page('targets', 'Performa', 'Target & KPI', 'Tetapkan target omzet dan kunjungan, lalu pantau pencapaian setiap sales.', [['Target aktif', SalesTarget::whereDate('period_start', '<=', today())->whereDate('period_end', '>=', today())->count()], ['Sales terdaftar', User::role('Sales Motoris')->count()]]);
    }

    public function reports(): View
    {
        return $this->page('reports', 'Analitik', 'Laporan & monitoring', 'Akses rekap operasional dan unduh laporan penjualan untuk kebutuhan monitoring.', [['Format tersedia', 'Excel'], ['Zona waktu', 'Asia/Jakarta']]);
    }

    public function users(): View
    {
        return $this->page('users', 'Administrasi', 'Manajemen pengguna', 'Kelola akun, role, hak akses, tim, dan pembagian wilayah sales.', [['Total akun', User::count()], ['Sales motoris', User::role('Sales Motoris')->count()]]);
    }

    public function settings(): View
    {
        return $this->page('settings', 'Kontrol sistem', 'Pengaturan & audit log', 'Atur konfigurasi operasional dan telusuri histori perubahan data yang tercatat.', [['Aktivitas tercatat', Activity::count()], ['Status audit', 'Aktif']]);
    }

    private function page(string $module, string $eyebrow, string $title, string $description, array $metrics): View
    {
        return view('modules.index', compact('module', 'eyebrow', 'title', 'description', 'metrics'))->with('modals', $this->modalDefinitions($module));
    }

    private function modalDefinitions(string $module): array
    {
        $common = ['tanggal' => ['label' => 'Tanggal', 'name' => 'tanggal', 'type' => 'date'], 'catatan' => ['label' => 'Catatan', 'name' => 'catatan', 'type' => 'textarea', 'wide' => true]];

        return match ($module) {
            'targets' => [
                ['id' => 'target-create', 'title' => 'Tambah Target', 'fields' => array_merge([['label' => 'Nama target', 'name' => 'nama_target', 'required' => true], ['label' => 'Periode', 'name' => 'periode', 'type' => 'select', 'options' => ['Bulanan', 'Mingguan', 'Tahunan']], ['label' => 'Tanggal mulai', 'name' => 'tanggal_mulai', 'type' => 'date'], ['label' => 'Tanggal akhir', 'name' => 'tanggal_akhir', 'type' => 'date'], ['label' => 'Sales motoris', 'name' => 'sales_motoris'], ['label' => 'Tim sales', 'name' => 'tim_sales'], ['label' => 'Wilayah', 'name' => 'wilayah'], ['label' => 'Target omzet', 'name' => 'target_omzet', 'type' => 'number'], ['label' => 'Target pesanan', 'name' => 'target_pesanan', 'type' => 'number'], ['label' => 'Target kunjungan', 'name' => 'target_kunjungan', 'type' => 'number'], ['label' => 'Target outlet baru', 'name' => 'target_outlet_baru', 'type' => 'number'], ['label' => 'Target penagihan', 'name' => 'target_penagihan', 'type' => 'number']], [$common['catatan']]), 'submitLabel' => 'Simpan target'],
                ['id' => 'target-detail', 'title' => 'Detail Pencapaian', 'fields' => [['label' => 'Nama sales', 'name' => 'nama_sales'], ['label' => 'Periode', 'name' => 'periode'], ['label' => 'Target omzet', 'name' => 'target_omzet', 'type' => 'number'], ['label' => 'Realisasi omzet', 'name' => 'realisasi_omzet', 'type' => 'number'], ['label' => 'Persentase pencapaian', 'name' => 'persentase', 'type' => 'number'], ['label' => 'Target kunjungan', 'name' => 'target_kunjungan', 'type' => 'number'], ['label' => 'Realisasi kunjungan', 'name' => 'realisasi_kunjungan', 'type' => 'number'], ['label' => 'Status KPI', 'name' => 'status_kpi', 'type' => 'select', 'options' => ['On track', 'Perlu perhatian', 'Tercapai']], $common['catatan']]],
            ],
            'reports' => [
                ['id' => 'report-filter', 'title' => 'Filter Laporan', 'fields' => [['label' => 'Jenis laporan', 'name' => 'jenis_laporan', 'type' => 'select', 'options' => ['Penjualan', 'Kunjungan', 'Produktivitas tim', 'Stok']], ['label' => 'Tanggal mulai', 'name' => 'tanggal_mulai', 'type' => 'date'], ['label' => 'Tanggal akhir', 'name' => 'tanggal_akhir', 'type' => 'date'], ['label' => 'Sales motoris', 'name' => 'sales_motoris'], ['label' => 'Tim sales', 'name' => 'tim_sales'], ['label' => 'Wilayah', 'name' => 'wilayah'], ['label' => 'Status', 'name' => 'status'], ['label' => 'Format ekspor', 'name' => 'format', 'type' => 'select', 'options' => ['Excel', 'PDF', 'CSV']]], 'submitLabel' => 'Terapkan filter'],
                ['id' => 'report-detail', 'title' => 'Detail Laporan', 'fields' => [['label' => 'Nomor laporan', 'name' => 'nomor_laporan'], ['label' => 'Jenis laporan', 'name' => 'jenis_laporan'], ['label' => 'Periode', 'name' => 'periode'], ['label' => 'Dibuat oleh', 'name' => 'dibuat_oleh'], ['label' => 'Waktu pembuatan', 'name' => 'waktu_pembuatan', 'type' => 'datetime-local'], $common['catatan']]],
            ],
            'settings' => [
                ['id' => 'system-settings', 'title' => 'Pengaturan Sistem', 'fields' => [['label' => 'Nama perusahaan', 'name' => 'nama_perusahaan'], ['label' => 'Logo', 'name' => 'logo', 'type' => 'file'], ['label' => 'Alamat', 'name' => 'alamat', 'type' => 'textarea', 'wide' => true], ['label' => 'Zona waktu', 'name' => 'zona_waktu', 'type' => 'select', 'options' => ['Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura']], ['label' => 'Format tanggal', 'name' => 'format_tanggal'], ['label' => 'Mata uang', 'name' => 'mata_uang', 'value' => 'IDR'], ['label' => 'Batas akurasi GPS', 'name' => 'akurasi_gps', 'type' => 'number'], ['label' => 'Radius validasi', 'name' => 'radius_validasi', 'type' => 'number'], ['label' => 'Wajib foto', 'name' => 'wajib_foto', 'type' => 'checkbox'], ['label' => 'Wajib GPS', 'name' => 'wajib_gps', 'type' => 'checkbox'], ['label' => 'Notifikasi email', 'name' => 'notifikasi_email', 'type' => 'checkbox']], 'submitLabel' => 'Simpan pengaturan'],
                ['id' => 'audit-detail', 'title' => 'Detail Audit Log', 'fields' => [['label' => 'Waktu aktivitas', 'name' => 'waktu_aktivitas', 'type' => 'datetime-local'], ['label' => 'Nama pengguna', 'name' => 'nama_pengguna'], ['label' => 'Role', 'name' => 'role'], ['label' => 'Modul', 'name' => 'modul'], ['label' => 'Jenis aktivitas', 'name' => 'jenis_aktivitas'], ['label' => 'ID referensi', 'name' => 'id_referensi'], ['label' => 'Nilai sebelum', 'name' => 'nilai_sebelum', 'type' => 'textarea'], ['label' => 'Nilai sesudah', 'name' => 'nilai_sesudah', 'type' => 'textarea'], ['label' => 'IP address', 'name' => 'ip_address'], $common['catatan']]],
            ],
            default => [],
        };
    }
}
