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
                'name' => 'Admin',
                'email' => 'admin@amartha.test',
                'business_name' => 'Amartha HQ',
                'domicile' => 'Jakarta',
                'gender' => 'female',
                'bio' => 'Admin pendamping Amartha untuk monitoring pengajuan, pembayaran, dan komunitas.',
                'role' => 'admin',
                'password' => Hash::make('Admin1234'),
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
                'bio' => 'Pemilik warung harian yang sedang mengembangkan stok dan pencatatan usaha.',
                'role' => 'user',
                'password' => Hash::make('password'),
                'approved_at' => now(),
            ]
        );

        $modules = collect([
            [
                'title' => 'Mengenal Pinjaman Produktif',
                'slug' => 'mengenal-pinjaman-produktif',
                'duration_label' => '5 menit bacaan',
                'format' => 'teks',
                'summary' => 'Memahami perbedaan pinjaman produktif dan konsumtif sebelum mengajukan dana usaha.',
                'content' => "Pinjaman produktif digunakan untuk kegiatan yang membantu usaha menghasilkan pemasukan, misalnya menambah stok barang, membeli alat produksi, atau memperbaiki tempat usaha.\nSebelum meminjam, tulis tujuan pinjaman secara spesifik dan hitung apakah tambahan modal benar-benar bisa meningkatkan penjualan.\nHindari memakai dana pinjaman usaha untuk kebutuhan konsumtif karena cicilan tetap harus dibayar meskipun usaha belum bertambah.",
                'display_order' => 1,
                'is_featured' => true,
            ],
            [
                'title' => 'Menghitung Kebutuhan Pinjaman',
                'slug' => 'menghitung-kebutuhan-pinjaman',
                'duration_label' => '7 menit latihan',
                'format' => 'teks',
                'summary' => 'Belajar menentukan nominal pinjaman yang sesuai dengan kebutuhan dan kemampuan usaha.',
                'content' => "Mulai dari daftar kebutuhan usaha: stok, alat, biaya pengiriman, atau renovasi kecil. Beri harga pada setiap kebutuhan agar nominal pinjaman tidak asal tebak.\nTambahkan cadangan secukupnya, tetapi jangan menaikkan pinjaman hanya karena plafon yang tersedia lebih besar.\nBandingkan rencana pinjaman dengan pemasukan usaha bulanan supaya cicilan tidak mengganggu biaya operasional harian.",
                'display_order' => 2,
                'is_featured' => true,
            ],
            [
                'title' => 'Memahami Bunga, Tenor, dan Cicilan',
                'slug' => 'memahami-bunga-tenor-cicilan',
                'duration_label' => '6 menit bacaan',
                'format' => 'teks',
                'summary' => 'Mengenal istilah bunga, tenor, tanggal jatuh tempo, dan total pembayaran pinjaman.',
                'content' => "Bunga adalah biaya atas penggunaan dana pinjaman. Tenor adalah lama waktu pembayaran, misalnya 6, 12, atau 24 bulan.\nTenor yang lebih panjang dapat membuat cicilan bulanan lebih ringan, tetapi total biaya pinjaman bisa menjadi lebih besar.\nCatat tanggal jatuh tempo dan siapkan dana cicilan beberapa hari sebelumnya agar tidak terlambat membayar.",
                'display_order' => 3,
                'is_featured' => true,
            ],
            [
                'title' => 'Menilai Kemampuan Bayar',
                'slug' => 'menilai-kemampuan-bayar',
                'duration_label' => '8 menit simulasi',
                'format' => 'teks',
                'summary' => 'Mengecek apakah arus kas usaha cukup aman untuk membayar cicilan setiap bulan.',
                'content' => "Hitung rata-rata laba bersih bulanan, bukan hanya omzet. Laba bersih adalah uang tersisa setelah dikurangi modal barang, sewa, listrik, transportasi, dan biaya lain.\nCicilan idealnya diambil dari sebagian laba bersih, bukan dari uang belanja keluarga atau modal stok bulan berikutnya.\nJika laba sering naik turun, gunakan angka laba terendah sebagai acuan agar rencana cicilan lebih aman.",
                'display_order' => 4,
                'is_featured' => false,
            ],
            [
                'title' => 'Mengelola Dana Setelah Cair',
                'slug' => 'mengelola-dana-setelah-cair',
                'duration_label' => '5 menit checklist',
                'format' => 'teks',
                'summary' => 'Membuat rencana penggunaan dana cair agar modal tidak tercampur dengan kebutuhan pribadi.',
                'content' => "Pisahkan dana pinjaman dari uang pribadi. Jika memungkinkan, gunakan rekening atau catatan khusus untuk modal usaha.\nBelanjakan dana sesuai rencana awal dan simpan bukti transaksi agar mudah mengevaluasi hasil pinjaman.\nPantau perubahan penjualan setelah dana dipakai. Jika penjualan naik, sisihkan cicilan lebih awal sebelum memakai sisa keuntungan.",
                'display_order' => 5,
                'is_featured' => false,
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
