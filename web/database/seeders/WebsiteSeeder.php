<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Program;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/** Isi awal website (program, berita, testimoni contoh). Hanya diisi kalau tabel masih kosong. */
class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        if (Program::count() === 0) {
            $programs = [
                ['Paket A', 'Setara SD', 'paket-a', '6 tahun (bisa lebih cepat sesuai hasil penilaian)',
                    'Pendidikan kesetaraan untuk anak, remaja, dan dewasa yang belum menamatkan SD.',
                    ['Ijazah setara SD', 'Belajar membaca, menulis, berhitung', 'Pendampingan tutor', 'Jadwal fleksibel']],
                ['Paket B', 'Setara SMP', 'paket-b', '3 tahun',
                    'Pendidikan kesetaraan bagi lulusan SD/Paket A yang ingin mendapatkan ijazah setara SMP.',
                    ['Ijazah setara SMP', 'Materi sesuai kurikulum nasional', 'Belajar lewat aplikasi Android', 'Keterampilan dasar']],
                ['Paket C', 'Setara SMA', 'paket-c', '3 tahun',
                    'Pendidikan kesetaraan bagi lulusan SMP/Paket B, cocok untuk pekerja yang butuh ijazah SMA untuk karier atau kuliah.',
                    ['Ijazah setara SMA, bisa untuk kuliah', 'Kelas sore & akhir pekan', 'Belajar lewat aplikasi Android', 'Bimbingan karier']],
            ];
            foreach ($programs as $i => [$name, $level, $icon, $duration, $summary, $features]) {
                Program::create([
                    'name' => $name, 'level' => $level, 'icon' => $icon, 'duration' => $duration, 'summary' => $summary,
                    'features' => $features, 'sort' => $i,
                    'body' => "<p>{$summary}</p><h2>Siapa yang bisa mendaftar?</h2><p>Program {$name} terbuka untuk semua usia, termasuk yang sudah bekerja atau berkeluarga.</p><h2>Cara belajar</h2><ul><li>Tatap muka bersama tutor sesuai jadwal</li><li>Belajar mandiri lewat aplikasi Android PKBM</li><li>Tugas dan kuis dinilai langsung oleh tutor</li></ul>",
                ]);
            }
        }

        if (Post::count() === 0) {
            Post::create([
                'title' => 'Pendaftaran Warga Belajar Baru Telah Dibuka',
                'category' => 'Pengumuman',
                'excerpt' => 'PKBM Widya Utama membuka pendaftaran Paket A, B, dan C. Daftar online lewat website atau datang langsung ke sekretariat.',
                'body' => '<p>PKBM Widya Utama membuka pendaftaran warga belajar baru untuk program Paket A (setara SD), Paket B (setara SMP), dan Paket C (setara SMA).</p><p>Pendaftaran bisa dilakukan secara online melalui menu <strong>Pendaftaran</strong> di website ini. Setelah mengisi formulir, tim kami akan menghubungi lewat WhatsApp.</p>',
                'published_at' => now(),
            ]);
            Post::create([
                'title' => 'Belajar Lebih Mudah Lewat Aplikasi Android PKBM',
                'category' => 'Berita',
                'body' => '<p>Kini warga belajar PKBM Widya Utama bisa membuka materi, mengerjakan kuis, mengumpulkan tugas, dan melihat nilai langsung dari aplikasi Android.</p><p>Akun aplikasi diberikan oleh pengelola setelah pendaftaran selesai.</p>',
                'published_at' => now()->subDays(2),
            ]);
        }

        if (Testimonial::count() === 0) {
            Testimonial::create(['name' => 'Rina', 'role' => 'Lulusan Paket C', 'quote' => 'Sambil bekerja saya tetap bisa sekolah. Tutornya sabar dan jadwalnya bisa menyesuaikan.']);
            Testimonial::create(['name' => 'Budi', 'role' => 'Lulusan Paket B', 'quote' => 'Dulu sempat putus sekolah, sekarang punya ijazah dan bisa lanjut ke Paket C.']);
            Testimonial::create(['name' => 'Siti', 'role' => 'Warga belajar Paket C', 'quote' => 'Aplikasinya membantu sekali, materi dan tugas bisa dibuka dari HP kapan saja.']);
        }
    }
}
