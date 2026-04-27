<?php

namespace Database\Seeders;

use App\Models\CommunityComment;
use App\Models\CommunityPost;
use App\Models\Installment;
use App\Models\LearningModule;
use App\Models\LearningProgress;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\Notification;
use App\Models\OjkReport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['phone' => '081111111111'],
            [
                'name' => 'Admin Amartha',
                'email' => 'admin@amartha.test',
                'business_name' => 'Amartha HQ',
                'domicile' => 'Jakarta',
                'gender' => 'female',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'approved_at' => now(),
            ]
        );

        $user = User::updateOrCreate(
            ['phone' => '081234567890'],
            [
                'name' => 'Ala Gen Z',
                'email' => 'user@amartha.test',
                'business_name' => 'Warung Ibu Ala',
                'domicile' => 'Bandung',
                'gender' => 'female',
                'role' => 'user',
                'password' => Hash::make('password'),
                'approved_at' => now(),
            ]
        );

        $modules = collect([
            [
                'title' => 'Cara Mengatur Pengeluaran Harian',
                'slug' => 'mengatur-pengeluaran-harian',
                'duration_label' => '1-3 menit video singkat',
                'summary' => 'Belajar mengelola arus kas harian usaha kecil dengan cara praktis.',
                'display_order' => 1,
                'is_featured' => true,
            ],
            [
                'title' => 'Tips Investasi Aman',
                'slug' => 'tips-investasi-aman',
                'duration_label' => '2 menit infografis',
                'summary' => 'Memahami pilihan investasi sederhana yang aman untuk pelaku usaha.',
                'display_order' => 2,
                'is_featured' => true,
            ],
            [
                'title' => 'Tips Mengelola Stok Barang',
                'slug' => 'mengelola-stok-barang',
                'duration_label' => '3 menit video singkat',
                'summary' => 'Mencatat keluar masuk barang agar usaha lebih stabil.',
                'display_order' => 3,
                'is_featured' => true,
            ],
        ])->map(fn ($module) => LearningModule::updateOrCreate(['slug' => $module['slug']], $module));

        $loan = Loan::updateOrCreate(
            ['application_code' => 'AE-SAMPLE01'],
            [
                'user_id' => $user->id,
                'amount' => 5000000,
                'tenor_months' => 24,
                'interest_rate' => 2.5,
                'status' => 'ongoing',
                'submitted_at' => now()->subDays(10)->toDateString(),
                'approved_at' => now()->subDays(7)->toDateString(),
                'disbursed_at' => now()->subDays(5)->toDateString(),
                'admin_notes' => 'Dokumen lengkap, pengajuan diproses dan dicairkan.',
            ]
        );

        LoanDocument::updateOrCreate(
            ['loan_id' => $loan->id, 'document_type' => 'KTP'],
            ['file_name' => 'ktp-ala.pdf', 'verification_status' => 'verified']
        );

        LoanDocument::updateOrCreate(
            ['loan_id' => $loan->id, 'document_type' => 'Foto Usaha'],
            ['file_name' => 'usaha-ala.jpg', 'verification_status' => 'verified']
        );

        foreach (range(1, 24) as $sequence) {
            Installment::updateOrCreate(
                ['loan_id' => $loan->id, 'sequence' => $sequence],
                [
                    'due_date' => now()->startOfDay()->addMonths($sequence)->toDateString(),
                    'amount' => 218750,
                    'status' => $sequence <= 7 ? 'paid' : 'pending',
                    'paid_at' => $sequence <= 7 ? now()->subDays(3)->toDateString() : null,
                ]
            );
        }

        foreach ($modules as $index => $module) {
            LearningProgress::updateOrCreate(
                ['user_id' => $user->id, 'learning_module_id' => $module->id],
                [
                    'completion_percent' => $index === 0 ? 100 : 40,
                    'is_completed' => $index === 0,
                    'completed_at' => $index === 0 ? now()->subDay() : null,
                ]
            );
        }

        Notification::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Status Pinjaman Aktif'],
            [
                'message' => 'Pinjaman Rp 5.000.000 sudah aktif. Silakan cek jadwal cicilan dan lanjutkan Ruang Belajar.',
                'type' => 'loan',
            ]
        );

        Notification::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Akses Komunitas Terbuka'],
            [
                'message' => 'Anda sekarang bisa masuk ke Komunitas Usaha Perempuan dan mengikuti pelatihan offline.',
                'type' => 'community',
            ]
        );

        $eventPost = CommunityPost::updateOrCreate(
            ['title' => 'Pelatihan Offline Pekan Ini'],
            [
                'user_id' => $admin->id,
                'content' => 'Pendamping Amartha akan mengadakan sesi pencatatan laba rugi sederhana pada Jumat sore.',
                'category' => 'event',
                'event_date' => now()->addDays(3)->toDateString(),
                'event_location' => 'Bandung Barat',
            ]
        );

        $chatPost = CommunityPost::updateOrCreate(
            ['title' => 'Tips jualan saat sepi pembeli'],
            [
                'user_id' => $user->id,
                'content' => 'Saya mulai bundling produk mingguan, ternyata lebih cepat habis. Ada yang punya ide lain?',
                'category' => 'chat',
            ]
        );

        CommunityComment::updateOrCreate(
            ['community_post_id' => $chatPost->id, 'user_id' => $admin->id],
            ['content' => 'Coba tambah promo repeat order dan unggah testimoni pelanggan setiap 2 hari.']
        );

        CommunityComment::updateOrCreate(
            ['community_post_id' => $eventPost->id, 'user_id' => $user->id],
            ['content' => 'Siap hadir, saya ingin belajar catatan arus kas harian.']
        );

        OjkReport::updateOrCreate(
            ['report_date' => now()->toDateString()],
            [
                'female_borrowers' => 1,
                'male_borrowers' => 0,
                'active_loans' => 1,
                'total_disbursed' => 5000000,
                'total_outstanding' => 3718750,
            ]
        );
    }
}
