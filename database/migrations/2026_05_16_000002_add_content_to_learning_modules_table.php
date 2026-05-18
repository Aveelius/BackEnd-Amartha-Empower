<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->text('content')->nullable()->after('summary');
        });

        $now = now();
        $modules = [
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
        ];

        foreach ($modules as $module) {
            DB::table('learning_modules')->updateOrInsert(
                ['slug' => $module['slug']],
                [
                    ...$module,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};
