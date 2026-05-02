<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =========================================================
        // USERS
        // =========================================================

        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'               => 'Super Admin',
                'role'               => 'superadmin',
                'badge'              => 'premium_admin',
                'password'           => bcrypt('password'),
                'phone'              => '081234567890',
                'whatsapp'           => '6281234567890',
                'ewallet_type'       => 'dana',
                'ewallet_number'     => '081234567890',
                'ewallet_name'       => 'Super Admin',
                'email_verified_at'  => now(),
            ]
        );

        $admin1 = User::updateOrCreate(
            ['email' => 'admin1@gmail.com'],
            [
                'name'               => 'Budi Santoso',
                'role'               => 'admin',
                'badge'              => 'premium_admin',
                'password'           => bcrypt('password'),
                'phone'              => '081111222333',
                'whatsapp'           => '6281111222333',
                'ewallet_type'       => 'gopay',
                'ewallet_number'     => '081111222333',
                'ewallet_name'       => 'Budi Santoso',
                'email_verified_at'  => now(),
            ]
        );

        $admin2 = User::updateOrCreate(
            ['email' => 'admin2@gmail.com'],
            [
                'name'               => 'Siti Rahayu',
                'role'               => 'admin',
                'badge'              => 'senior',
                'password'           => bcrypt('password'),
                'phone'              => '082222333444',
                'whatsapp'           => '6282222333444',
                'ewallet_type'       => 'ovo',
                'ewallet_number'     => '082222333444',
                'ewallet_name'       => 'Siti Rahayu',
                'email_verified_at'  => now(),
            ]
        );

        // Regular users
        $user1 = User::updateOrCreate(
            ['email' => 'test@gmail.com'],
            [
                'name'               => 'Andi Wijaya',
                'role'               => 'user',
                'badge'              => 'junior',
                'password'           => bcrypt('password'),
                'phone'              => '083333444555',
                'whatsapp'           => '6283333444555',
                'ewallet_type'       => 'dana',
                'ewallet_number'     => '083333444555',
                'ewallet_name'       => 'Andi Wijaya',
                'email_verified_at'  => now(),
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'user2@gmail.com'],
            [
                'name'               => 'Dewi Kusuma',
                'role'               => 'user',
                'badge'              => 'senior',
                'password'           => bcrypt('password'),
                'phone'              => '084444555666',
                'whatsapp'           => '6284444555666',
                'ewallet_type'       => 'shopeepay',
                'ewallet_number'     => '084444555666',
                'ewallet_name'       => 'Dewi Kusuma',
                'email_verified_at'  => now(),
            ]
        );

        $user3 = User::updateOrCreate(
            ['email' => 'user3@gmail.com'],
            [
                'name'               => 'Rizky Firmansyah',
                'role'               => 'user',
                'badge'              => 'none',
                'password'           => bcrypt('password'),
                'phone'              => '085555666777',
                'whatsapp'           => '6285555666777',
                'ewallet_type'       => 'gopay',
                'ewallet_number'     => '085555666777',
                'ewallet_name'       => 'Rizky Firmansyah',
                'email_verified_at'  => now(),
            ]
        );

        // =========================================================
        // CATEGORIES
        // =========================================================

        $catKomunitas = Category::create([
            'name'        => 'Grup Komunitas',
            'description' => 'Tugas-tugas yang berkaitan dengan memperluas anggota grup komunitas online, forum diskusi, dan jaringan pertemanan berbasis minat.',
            'is_active'   => true,
            'expired_at'  => now()->addMonths(3),
            'created_by'  => $admin1->id,
        ]);

        $catBisnis = Category::create([
            'name'        => 'Grup Bisnis & UMKM',
            'description' => 'Tugas untuk memperluas jaringan bisnis, menghubungkan pelaku UMKM, reseller, dan dropshipper ke dalam grup yang relevan.',
            'is_active'   => true,
            'expired_at'  => now()->addMonths(3),
            'created_by'  => $admin2->id,
        ]);

        $catEdukasi = Category::create([
            'name'        => 'Grup Edukasi & Pelatihan',
            'description' => 'Tugas untuk merekrut peserta ke grup belajar online, kelas digital, dan komunitas pengembangan diri.',
            'is_active'   => true,
            'expired_at'  => now()->addMonths(3),
            'created_by'  => $admin1->id,
        ]);

        $catMarketing = Category::create([
            'name'        => 'Grup Marketing & Afiliasi',
            'description' => 'Tugas untuk merekrut marketer, promotor konten, dan mitra afiliasi ke dalam grup strategi pemasaran.',
            'is_active'   => true,
            'expired_at'  => now()->addMonths(3),
            'created_by'  => $superadmin->id,
        ]);

        // =========================================================
        // TASKS — semua data realistis, bahasa Indonesia
        // =========================================================

        $tasks = collect();

        // ---------- KATEGORI: KOMUNITAS ----------

        $tasks->push(Task::create([
            'category_id'        => $catKomunitas->id,
            'admin_id'           => $admin1->id,
            'created_by'         => $admin1->id,
            'title'              => 'Grup Komunitas Freelancer Indonesia',
            'description'        => "<p><strong>Misi:</strong> Ajak minimal <strong>20 freelancer aktif</strong> untuk bergabung ke grup WhatsApp Komunitas Freelancer Indonesia.</p>

<p><strong>Syarat anggota yang direkrut:</strong></p>
<ul>
<li>Berstatus freelancer aktif (desainer, penulis, programmer, dll.)</li>
<li>Wajib menyapa dan memperkenalkan diri di grup setelah join</li>
<li>Aktifkan notifikasi grup selama minimal 48 jam</li>
</ul>

<p><strong>Waktu pengerjaan:</strong> Bukti baru diproses mulai pukul <strong>16.00 WIB</strong>. Submit sebelum jam tersebut tidak akan dihitung.</p>

<p><strong>Bukti yang harus dikirimkan (Tahap 1):</strong></p>
<ol>
<li>Screenshot daftar member sebelum & sesudah kamu rekrut</li>
<li>List minimal 20 nomor WhatsApp yang berhasil join</li>
</ol>

<p><strong>Bukti yang harus dikirimkan (Tahap 2):</strong></p>
<ol>
<li>Screenshot percakapan perkenalan anggota baru di grup</li>
<li>Konfirmasi bahwa anggota masih aktif setelah 24 jam</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_EASY,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/FreelancerID123',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=UXQaVLGbGz0',
            'estimated_amount'   => 15000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 1,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catKomunitas->id,
            'admin_id'           => $admin1->id,
            'created_by'         => $admin1->id,
            'title'              => 'Komunitas Pengusaha Muda Nusantara',
            'description'        => "<p><strong>Misi:</strong> Rekrut minimal <strong>25 pengusaha muda</strong> (usia 18–35 tahun) ke dalam grup diskusi bisnis.</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Memiliki usaha aktif atau sedang merintis bisnis</li>
<li>Bersedia memperkenalkan usaha mereka di dalam grup</li>
<li>Join grup dan tetap aktif minimal 2 hari</li>
</ul>

<p><strong>Catatan penting:</strong> Bukti baru diproses mulai pukul <strong>16.00 WIB</strong>.</p>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot peningkatan jumlah member di grup</li>
<li>Daftar 25+ nomor beserta nama usaha masing-masing</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot percakapan perkenalan usaha di grup</li>
<li>Bukti anggota masih dalam grup setelah 48 jam</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_MEDIUM,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/PengusahaMuda456',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=Z0QASm1nR9A',
            'estimated_amount'   => 25000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 2,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catKomunitas->id,
            'admin_id'           => $admin1->id,
            'created_by'         => $admin1->id,
            'title'              => 'Komunitas Ibu Rumah Tangga Produktif',
            'description'        => "<p><strong>Misi:</strong> Ajak minimal <strong>30 ibu rumah tangga</strong> yang ingin aktif secara digital untuk bergabung ke grup produktivitas.</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Ibu rumah tangga yang ingin meningkatkan penghasilan dari rumah</li>
<li>Bersedia bergabung dan aktif berdiskusi di grup</li>
<li>Mengisi form perkenalan singkat yang disediakan admin</li>
</ul>

<p><strong>Catatan:</strong> Validasi bukti dimulai pukul <strong>16.00 WIB</strong>.</p>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot jumlah member sebelum & sesudah</li>
<li>Daftar 30+ nomor yang berhasil join</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot pengisian form perkenalan</li>
<li>Screenshot aktivitas chat anggota baru di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_HARD,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/IbuProduktif789',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=KlujizzsCVk',
            'estimated_amount'   => 35000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 3,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catKomunitas->id,
            'admin_id'           => $admin1->id,
            'created_by'         => $admin1->id,
            'title'              => 'Grup Hobi Fotografi Ponsel',
            'description'        => "<p><strong>Misi:</strong> Rekrut minimal <strong>18 pengguna aktif fotografi ponsel</strong> ke grup berbagi foto dan tips.</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Aktif menggunakan kamera ponsel untuk fotografi</li>
<li>Bersedia berbagi satu foto di grup setelah join</li>
<li>Tidak mempromosikan produk/jasa di dalam grup</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot list member yang bertambah</li>
<li>Daftar 18+ nomor anggota baru</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot foto yang dibagikan anggota baru di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_EASY,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/FotoPosel2024',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=ssrY9j9Vwqk',
            'estimated_amount'   => 12000,
            'expired_at'         => now()->addWeeks(2),
            'is_expired'         => false,
            'priority_order'     => 4,
        ]));

        // ---------- KATEGORI: BISNIS & UMKM ----------

        $tasks->push(Task::create([
            'category_id'        => $catBisnis->id,
            'admin_id'           => $admin2->id,
            'created_by'         => $admin2->id,
            'title'              => 'Jaringan Reseller Produk Skincare Lokal',
            'description'        => "<p><strong>Misi:</strong> Rekrut minimal <strong>20 reseller aktif</strong> produk skincare lokal ke dalam grup jaringan penjualan.</p>

<p><strong>Kriteria reseller:</strong></p>
<ul>
<li>Sudah pernah atau sedang berjualan produk kecantikan/skincare</li>
<li>Memiliki akun marketplace aktif (Shopee/Tokopedia/Instagram)</li>
<li>Bersedia drop link toko/katalog setelah masuk grup</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot konfirmasi join dari 20 reseller</li>
<li>Daftar 20+ nomor beserta nama toko/akun marketplace</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot link toko/katalog yang dibagikan di grup</li>
<li>Bukti anggota masih aktif setelah 24 jam</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_MEDIUM,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/ResellerSkincare001',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=YkS6iVqxl2E',
            'estimated_amount'   => 22000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 5,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catBisnis->id,
            'admin_id'           => $admin2->id,
            'created_by'         => $admin2->id,
            'title'              => 'Jaringan Supplier & Dropshipper Fashion',
            'description'        => "<p><strong>Misi:</strong> Kumpulkan minimal <strong>28 supplier atau dropshipper fashion</strong> ke grup jaringan bisnis.</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Memiliki stok produk fashion siap kirim atau sistem pre-order (PO)</li>
<li>Bersedia mention kategori produk (pakaian wanita, pria, anak, aksesoris, dll.)</li>
<li>Aktif berjualan di platform online</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot pengumuman masuk dan perkenalan anggota baru</li>
<li>Daftar 28+ nomor dengan keterangan kategori produk</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot stok/PO yang dibagikan di grup</li>
<li>Konfirmasi anggota aktif setelah 48 jam</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_HARD,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/SupplierFashion002',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=E4N0pBSr9m0',
            'estimated_amount'   => 32000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 6,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catBisnis->id,
            'admin_id'           => $admin2->id,
            'created_by'         => $admin2->id,
            'title'              => 'Grup UMKM Kuliner Nusantara',
            'description'        => "<p><strong>Misi:</strong> Ajak minimal <strong>22 pelaku UMKM kuliner</strong> untuk bergabung ke grup berbagi tips dan promosi produk makanan.</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Memiliki usaha kuliner aktif (warung, catering, oleh-oleh, frozen food, dll.)</li>
<li>Bersedia memperkenalkan produk unggulan di grup</li>
<li>Tertarik untuk saling promosi sesama pelaku kuliner</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot daftar member sebelum & sesudah</li>
<li>Daftar 22+ nomor beserta jenis usaha kuliner</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot produk kuliner yang dibagikan di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_MEDIUM,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/UMKMKuliner003',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=v4xZf2-NIV0',
            'estimated_amount'   => 20000,
            'expired_at'         => now()->addWeeks(2),
            'is_expired'         => false,
            'priority_order'     => 7,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catBisnis->id,
            'admin_id'           => $admin2->id,
            'created_by'         => $admin2->id,
            'title'              => 'Grup Jasa & Layanan Lokal',
            'description'        => "<p><strong>Misi:</strong> Rekrut minimal <strong>15 penyedia jasa lokal</strong> (tukang, ojek, laundry, dll.) ke grup kolaborasi layanan.</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Memiliki usaha jasa yang aktif beroperasi</li>
<li>Siap menerima order online dari pelanggan</li>
<li>Bersedia memperkenalkan layanan di grup</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot peningkatan anggota di grup</li>
<li>Daftar 15+ nomor beserta jenis layanan</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot perkenalan layanan yang diposting di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_EASY,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/JasaLokal004',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=_nBlN9yp9R8',
            'estimated_amount'   => 13000,
            'expired_at'         => now()->addWeeks(2),
            'is_expired'         => false,
            'priority_order'     => 8,
        ]));

        // ---------- KATEGORI: EDUKASI & PELATIHAN ----------

        $tasks->push(Task::create([
            'category_id'        => $catEdukasi->id,
            'admin_id'           => $admin1->id,
            'created_by'         => $admin1->id,
            'title'              => 'Kelas Belajar Digital Marketing Gratis',
            'description'        => "<p><strong>Misi:</strong> Rekrut minimal <strong>25 peserta baru</strong> ke kelas belajar digital marketing online gratis.</p>

<p><strong>Kriteria peserta:</strong></p>
<ul>
<li>Tertarik belajar marketing digital (media sosial, SEO, konten, iklan)</li>
<li>Bersedia mengisi form registrasi yang disediakan admin</li>
<li>Aktif bertanya atau berdiskusi di grup kelas</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot form registrasi yang terisi</li>
<li>Daftar 25+ nomor peserta yang bergabung</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot minimal 5 pertanyaan/diskusi peserta baru di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_MEDIUM,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/BelajarDigitalMkt',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=K4TOrB7at0Y',
            'estimated_amount'   => 25000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 9,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catEdukasi->id,
            'admin_id'           => $admin1->id,
            'created_by'         => $admin1->id,
            'title'              => 'Bootcamp Pemrograman Web untuk Pemula',
            'description'        => "<p><strong>Misi:</strong> Ajak minimal <strong>20 pemula</strong> (pelajar/mahasiswa/fresh graduate) untuk mendaftar ke bootcamp pemrograman web gratis.</p>

<p><strong>Kriteria peserta:</strong></p>
<ul>
<li>Belum pernah belajar coding secara formal</li>
<li>Bersedia mengikuti sesi belajar online 2x seminggu</li>
<li>Mengisi form pendaftaran lengkap</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot formulir yang sudah terisi dari 20 pendaftar</li>
<li>Daftar 20+ nama dan nomor WhatsApp peserta</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot konfirmasi peserta hadir di sesi pertama</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_HARD,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/BootcampWebPemula',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=vTJdVE_gj_I',
            'estimated_amount'   => 35000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 10,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catEdukasi->id,
            'admin_id'           => $admin1->id,
            'created_by'         => $admin1->id,
            'title'              => 'Grup Belajar Investasi Saham & Reksa Dana',
            'description'        => "<p><strong>Misi:</strong> Rekrut minimal <strong>18 orang</strong> yang ingin belajar investasi dasar ke grup edukasi saham dan reksa dana.</p>

<p><strong>Kriteria peserta:</strong></p>
<ul>
<li>Belum pernah atau baru mulai berinvestasi</li>
<li>Usia 18 tahun ke atas</li>
<li>Antusias belajar tentang keuangan dan investasi</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot daftar anggota sebelum & sesudah rekrutmen</li>
<li>Daftar 18+ nomor peserta yang join</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot diskusi/pertanyaan investasi dari anggota baru di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_EASY,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/BelajarInvestasi22',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=UXQaVLGbGz0',
            'estimated_amount'   => 14000,
            'expired_at'         => now()->addWeeks(2),
            'is_expired'         => false,
            'priority_order'     => 11,
        ]));

        // ---------- KATEGORI: MARKETING & AFILIASI ----------

        $tasks->push(Task::create([
            'category_id'        => $catMarketing->id,
            'admin_id'           => $superadmin->id,
            'created_by'         => $superadmin->id,
            'title'              => 'Tim Promosi Konten Media Sosial',
            'description'        => "<p><strong>Misi:</strong> Rekrut minimal <strong>22 kreator konten</strong> (Instagram, TikTok, atau YouTube) ke grup strategi promosi bersama.</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Aktif membuat konten di minimal satu platform media sosial</li>
<li>Memiliki minimal 500 followers/subscriber</li>
<li>Bersedia share niche konten dan link profil setelah join</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot daftar anggota baru di grup</li>
<li>Daftar 22+ nomor beserta platform dan niche konten</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot link profil/channel yang dibagikan di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_MEDIUM,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/PromosiKonten2024',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=Z0QASm1nR9A',
            'estimated_amount'   => 22000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 12,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catMarketing->id,
            'admin_id'           => $superadmin->id,
            'created_by'         => $superadmin->id,
            'title'              => 'Grup Mitra Afiliasi Produk Digital',
            'description'        => "<p><strong>Misi:</strong> Ajak minimal <strong>24 orang</strong> yang tertarik menjadi mitra afiliasi produk digital (kursus online, software, e-book, dll.).</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Pernah atau ingin mencoba program afiliasi</li>
<li>Aktif di media sosial atau memiliki blog/website</li>
<li>Bersedia share pengalaman dan platform utama promosi</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot jumlah member yang bertambah</li>
<li>Daftar 24+ nomor beserta platform promosi utama</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot percakapan atau sharing pengalaman afiliasi di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_MEDIUM,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/AfiliasiDigital555',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=KlujizzsCVk',
            'estimated_amount'   => 20000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 13,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catMarketing->id,
            'admin_id'           => $superadmin->id,
            'created_by'         => $superadmin->id,
            'title'              => 'Tim Sales & Marketing Produk Herbal',
            'description'        => "<p><strong>Misi:</strong> Kumpulkan minimal <strong>20 tenaga sales atau marketer</strong> yang tertarik menjual produk herbal/kesehatan ke dalam grup koordinasi.</p>

<p><strong>Kriteria anggota:</strong></p>
<ul>
<li>Berpengalaman atau tertarik di bidang penjualan produk kesehatan</li>
<li>Aktif berkomunikasi dengan calon pelanggan secara online</li>
<li>Siap share pengalaman dan target pasar setelah masuk grup</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot daftar anggota baru beserta perkenalan singkat</li>
<li>Daftar 20+ nomor yang berhasil direkrut</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot sharing pengalaman & target pasar dari anggota baru</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_EASY,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/SalesHerbal2024',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=ssrY9j9Vwqk',
            'estimated_amount'   => 15000,
            'expired_at'         => now()->addWeeks(2),
            'is_expired'         => false,
            'priority_order'     => 14,
        ]));

        $tasks->push(Task::create([
            'category_id'        => $catMarketing->id,
            'admin_id'           => $superadmin->id,
            'created_by'         => $superadmin->id,
            'title'              => 'Rekrutmen Brand Ambassador Lokal',
            'description'        => "<p><strong>Misi:</strong> Cari minimal <strong>15 calon brand ambassador</strong> dari berbagai kota di Indonesia untuk kampanye produk lokal.</p>

<p><strong>Kriteria:</strong></p>
<ul>
<li>Berdomisili di kota besar (Jakarta, Bandung, Surabaya, Medan, Makassar, dll.)</li>
<li>Aktif di media sosial dengan penampilan rapi dan profesional</li>
<li>Bersedia mengisi form profil lengkap setelah bergabung</li>
</ul>

<p><strong>Bukti Tahap 1:</strong></p>
<ol>
<li>Screenshot profil media sosial dari 15 calon brand ambassador</li>
<li>Daftar nomor beserta kota domisili</li>
</ol>

<p><strong>Bukti Tahap 2:</strong></p>
<ol>
<li>Screenshot pengisian form profil brand ambassador di grup</li>
</ol>",
            'difficulty_level'   => Task::DIFFICULTY_HARD,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/BrandAmbassador99',
            'tutorial_link'      => 'https://www.youtube.com/watch?v=YkS6iVqxl2E',
            'estimated_amount'   => 40000,
            'expired_at'         => now()->addWeeks(3),
            'is_expired'         => false,
            'priority_order'     => 15,
        ]));

        // =========================================================
        // USER TASKS — riwayat yang realistis untuk test & demo
        // =========================================================

        // user1 (Andi Wijaya) — 3 task selesai, 2 pending, 1 gagal
        $completedTasks = $tasks->take(3)->values();
        foreach ($completedTasks as $index => $task) {
            $takenAt          = now()->subHours(rand(48, 72));
            $proof1At         = $takenAt->copy()->addMinutes(7);
            $proof1ApprovedAt = $proof1At->copy()->addMinutes(20);
            $proof2At         = $proof1ApprovedAt->copy()->addMinutes(30);
            $completedAt      = $proof2At->copy()->addMinutes(15);
            $amounts          = [15000, 25000, 35000];

            UserTask::create([
                'task_id'                    => $task->id,
                'user_id'                    => $user1->id,
                'status'                     => UserTask::STATUS_COMPLETED,
                'taken_at'                   => $takenAt,
                'deadline_at'                => $takenAt->copy()->addHours(24),
                'completed_at'               => $completedAt,
                'verification_1_status'      => 'Submitted at ' . $proof1At->format('Y-m-d H:i:s') . '. Description: Sudah berhasil merekrut ' . rand(20, 30) . ' anggota baru ke grup. Screenshot daftar member dan chat perkenalan terlampir. - Approved by admin at ' . $proof1ApprovedAt->format('Y-m-d H:i:s'),
                'verification_1_files'       => ['task-proofs/' . $task->id . '/verification-1/bukti_join_' . $index . '.jpg'],
                'verification_1_approved_at' => $proof1ApprovedAt,
                'verification_1_approved_by' => $admin1->id,
                'verification_2_status'      => 'Submitted at ' . $proof2At->format('Y-m-d H:i:s') . '. Description: Seluruh anggota masih aktif di grup setelah 48 jam. Screenshot chat dan polling terlampir. - Approved by admin at ' . $completedAt->format('Y-m-d H:i:s'),
                'verification_2_files'       => ['task-proofs/' . $task->id . '/verification-2/bukti_aktif_' . $index . '.jpg'],
                'verification_2_approved_at' => $completedAt,
                'verification_2_approved_by' => $admin1->id,
                'payment_status'             => UserTask::PAYMENT_SUCCESS,
                'payment_amount'             => $amounts[$index],
                'payment_verified_by_admin_id' => $superadmin->id,
                'payment_verified_at'          => $completedAt->copy()->addMinutes(10),
                'failed_count'               => 0,
            ]);
        }

        // user1 — 2 task pending verification 1
        $pendingTasks = $tasks->skip(3)->take(2);
        foreach ($pendingTasks as $task) {
            $takenAt      = now()->subMinutes(rand(30, 60));
            $submittedAt  = $takenAt->copy()->addMinutes(8);

            UserTask::create([
                'task_id'               => $task->id,
                'user_id'               => $user1->id,
                'status'                => UserTask::STATUS_PENDING_VERIFICATION_1,
                'taken_at'              => $takenAt,
                'deadline_at'           => $takenAt->copy()->addHours(24),
                'verification_1_status' => 'Submitted at ' . $submittedAt->format('Y-m-d H:i:s') . '. Description: Sudah rekrut anggota baru sesuai target. Screenshot bukti join terlampir beserta daftar nomor.',
                'verification_1_files'  => ['task-proofs/' . $task->id . '/verification-1/bukti_submit.jpg'],
                'payment_status'        => UserTask::PAYMENT_PENDING,
                'failed_count'          => 0,
            ]);
        }

        // user1 — 1 task gagal (kadaluarsa)
        $expiredTask = $tasks->skip(5)->first();
        UserTask::create([
            'task_id'               => $expiredTask->id,
            'user_id'               => $user1->id,
            'status'                => UserTask::STATUS_FAILED,
            'taken_at'              => now()->subHours(3),
            'deadline_at'           => now()->subHours(2),
            'cancelled_at'          => now()->subHours(2),
            'verification_1_status' => 'Failed: Did not submit proof 1 within 10 minutes deadline. Task automatically cancelled at ' . now()->subHours(2)->format('Y-m-d H:i:s'),
            'payment_status'        => UserTask::PAYMENT_PENDING,
            'failed_count'          => 1,
        ]);

        // user2 (Dewi Kusuma) — 2 task selesai, 1 pending verification 2
        foreach ($tasks->skip(6)->take(2)->values() as $index => $task) {
            $takenAt          = now()->subHours(rand(24, 48));
            $proof1At         = $takenAt->copy()->addMinutes(6);
            $proof1ApprovedAt = $proof1At->copy()->addMinutes(15);
            $proof2At         = $proof1ApprovedAt->copy()->addMinutes(25);
            $completedAt      = $proof2At->copy()->addMinutes(20);

            UserTask::create([
                'task_id'                    => $task->id,
                'user_id'                    => $user2->id,
                'status'                     => UserTask::STATUS_COMPLETED,
                'taken_at'                   => $takenAt,
                'deadline_at'                => $takenAt->copy()->addHours(24),
                'completed_at'               => $completedAt,
                'verification_1_status'      => 'Submitted at ' . $proof1At->format('Y-m-d H:i:s') . '. Description: Rekrutmen berhasil, ' . rand(18, 25) . ' anggota bergabung. - Approved by admin at ' . $proof1ApprovedAt->format('Y-m-d H:i:s'),
                'verification_1_files'       => ['task-proofs/' . $task->id . '/verification-1/bukti_dewi_' . $index . '.jpg'],
                'verification_1_approved_at' => $proof1ApprovedAt,
                'verification_1_approved_by' => $admin2->id,
                'verification_2_status'      => 'Submitted at ' . $proof2At->format('Y-m-d H:i:s') . '. Description: Anggota aktif terbukti. - Approved by admin at ' . $completedAt->format('Y-m-d H:i:s'),
                'verification_2_files'       => ['task-proofs/' . $task->id . '/verification-2/bukti_aktif_dewi_' . $index . '.jpg'],
                'verification_2_approved_at' => $completedAt,
                'verification_2_approved_by' => $admin2->id,
                'payment_status'             => UserTask::PAYMENT_SUCCESS,
                'payment_amount'             => [20000, 32000][$index],
                'payment_verified_by_admin_id' => $superadmin->id,
                'payment_verified_at'          => $completedAt->copy()->addMinutes(5),
                'failed_count'               => 0,
            ]);
        }

        // user2 — 1 task pending verification 2 (sudah approve tahap 1, nunggu tahap 2)
        $pendingV2Task = $tasks->skip(8)->first();
        $takenAt          = now()->subHours(5);
        $proof1At         = $takenAt->copy()->addMinutes(7);
        $proof1ApprovedAt = $proof1At->copy()->addMinutes(30);
        $proof2At         = $proof1ApprovedAt->copy()->addMinutes(60);

        UserTask::create([
            'task_id'                    => $pendingV2Task->id,
            'user_id'                    => $user2->id,
            'status'                     => UserTask::STATUS_PENDING_VERIFICATION_2,
            'taken_at'                   => $takenAt,
            'deadline_at'                => $takenAt->copy()->addHours(24),
            'verification_1_status'      => 'Submitted at ' . $proof1At->format('Y-m-d H:i:s') . '. Description: 25 anggota berhasil bergabung. - Approved by admin at ' . $proof1ApprovedAt->format('Y-m-d H:i:s'),
            'verification_1_files'       => ['task-proofs/' . $pendingV2Task->id . '/verification-1/bukti_v1_dewi.jpg'],
            'verification_1_approved_at' => $proof1ApprovedAt,
            'verification_1_approved_by' => $admin2->id,
            'verification_2_status'      => 'Submitted at ' . $proof2At->format('Y-m-d H:i:s') . '. Description: Screenshot chat aktif anggota setelah 48 jam terlampir. Menunggu persetujuan admin.',
            'verification_2_files'       => ['task-proofs/' . $pendingV2Task->id . '/verification-2/bukti_v2_dewi.jpg'],
            'payment_status'             => UserTask::PAYMENT_PENDING,
            'failed_count'               => 0,
        ]);

        // user3 (Rizky) — 1 task taken (aktif, baru diambil)
        $activeTakenTask = $tasks->skip(9)->first();
        UserTask::create([
            'task_id'    => $activeTakenTask->id,
            'user_id'    => $user3->id,
            'status'     => UserTask::STATUS_TAKEN,
            'taken_at'   => now()->subMinutes(3), // baru 3 menit lalu, masih dalam deadline 10 menit
            'deadline_at' => now()->addHours(24),
            'payment_status' => UserTask::PAYMENT_PENDING,
            'failed_count'   => 0,
        ]);

        // =========================================================
        // OUTPUT
        // =========================================================
        echo "\n=== SEEDER SELESAI ===\n";
        echo "Superadmin : superadmin@gmail.com  | password\n";
        echo "Admin 1    : admin1@gmail.com      | password  (Budi Santoso)\n";
        echo "Admin 2    : admin2@gmail.com      | password  (Siti Rahayu)\n";
        echo "User 1     : test@gmail.com        | password  (Andi Wijaya)    — 3 selesai, 2 pending, 1 gagal\n";
        echo "User 2     : user2@gmail.com       | password  (Dewi Kusuma)    — 2 selesai, 1 pending V2\n";
        echo "User 3     : user3@gmail.com       | password  (Rizky Firmansyah) — 1 aktif\n";
        echo "\nKategori   : 4 (Komunitas, Bisnis UMKM, Edukasi, Marketing)\n";
        echo "Task       : 15 task dengan deskripsi lengkap bahasa Indonesia\n";
        echo "UserTask   : riwayat realistis untuk demo & pengujian\n";
        echo "==============================\n";
    }
}
