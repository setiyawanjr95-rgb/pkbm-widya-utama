<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\LearningMaterial;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator', 'email' => 'admin@pkbm.local', 'role' => 'admin',
            'password' => env('ADMIN_PASSWORD', 'admin12345'),
        ]);

        $this->call(WebsiteSeeder::class);

        if (! app()->environment('local') && ! env('SEED_DEMO')) {
            return;
        }
        $this->demo();
    }

    /** Data contoh, sama dengan data dummy di app Android. */
    private function demo(): void
    {
        $user = User::updateOrCreate(['username' => 'siti'], [
            'name' => 'Siti Aisyah', 'email' => 'siti.aisyah@email.com', 'role' => 'student', 'password' => 'siswa123',
        ]);
        $student = Student::updateOrCreate(['nisn' => '1234567890'], [
            'user_id' => $user->id, 'paket' => 'Paket C', 'kelas' => 'Kelas 12',
            'phone' => '0812-3456-7890', 'address' => 'Jl. Melati No. 12, Kota',
        ]);

        $courses = collect([
            ['Matematika', 'calculate', 'danger', 'Bpk. Andi Saputra'],
            ['Bahasa Indonesia', 'menu_book', 'success', 'Ibu Rina Marlina'],
            ['Bahasa Inggris', 'translate', 'danger', 'Ibu Dewi Lestari'],
            ['IPA', 'science', 'success', 'Bpk. Budi Santoso'],
        ])->map(fn ($c) => Course::create([
            'name' => $c[0], 'paket' => 'Paket C', 'kelas' => 'Kelas 12', 'icon' => $c[1], 'color' => $c[2], 'tutor' => $c[3],
        ]));
        [$mtk, $bindo, $bing, $ipa] = $courses->all();

        $mtk->materials()->createMany([
            ['title' => 'Bab 1 – Persamaan Linear', 'type' => 'pdf', 'description' => 'Konsep dasar persamaan linear satu variabel.', 'sort' => 1],
            ['title' => 'Video Pembelajaran', 'type' => 'video', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 12,
                'description' => 'Metode substitusi dan eliminasi.', 'sort' => 2],
        ]);
        $quiz = $mtk->materials()->create(['title' => 'Latihan Soal', 'type' => 'quiz', 'description' => 'Uji pemahaman Bab 1.', 'sort' => 3]);
        $quiz->questions()->createMany([
            ['question' => 'Jika 2x + 3 = 11, maka x = ...', 'options' => ['2', '3', '4', '5'], 'answer' => 2],
            ['question' => 'Jika 5x - 5 = 20, maka x = ...', 'options' => ['3', '4', '5', '6'], 'answer' => 2],
            ['question' => 'Jika x/2 = 6, maka x = ...', 'options' => ['3', '8', '10', '12'], 'answer' => 3],
        ]);
        $bindo->materials()->create(['title' => 'Bab 1 – Teks Eksposisi', 'type' => 'pdf', 'description' => 'Struktur dan ciri teks eksposisi.']);
        $bing->materials()->create(['title' => 'Unit 1 – Asking and Giving Opinion', 'type' => 'pdf', 'description' => 'Ungkapan meminta dan memberi pendapat.']);
        $ipa->materials()->create(['title' => 'Bab 1 – Sistem Pencernaan', 'type' => 'pdf', 'description' => 'Organ dan proses pencernaan manusia.']);

        Assignment::create(['course_id' => $mtk->id, 'title' => 'Tugas Matematika', 'deadline' => now()->addDays(5)->setTime(23, 59),
            'instruction' => 'Kerjakan soal dan unggah jawaban dalam format PDF, JPG, atau DOC.']);
        Assignment::create(['course_id' => $bindo->id, 'title' => 'Menulis Teks Eksposisi', 'deadline' => now()->addDays(7)->setTime(23, 59),
            'instruction' => 'Tulis teks eksposisi minimal 3 paragraf bertema lingkungan sekitar.']);

        foreach ([[$mtk, 1, '08:00', '09:30', 'Ruang 1'], [$bindo, 1, '10:00', '11:30', 'Ruang 2'],
            [$bing, 3, '08:00', '09:30', 'Ruang 1'], [$ipa, 5, '13:00', '14:30', 'Lab IPA']] as [$c, $day, $start, $end, $room]) {
            for ($w = 0; $w < 4; $w++) {
                Schedule::create(['course_id' => $c->id, 'date' => now()->startOfWeek()->addDays($day - 1 + 7 * $w),
                    'start_time' => $start, 'end_time' => $end, 'tutor' => $c->tutor, 'room' => $room]);
            }
        }

        Announcement::create(['title' => 'Ujian Akhir Semester', 'published_at' => now(),
            'body' => 'Ujian akhir semester dilaksanakan mulai tanggal 25 bulan ini. Persiapkan diri dengan baik.']);
        Announcement::create(['title' => 'Libur Nasional', 'published_at' => now()->subDays(3),
            'body' => 'Kegiatan belajar diliburkan pada hari libur nasional.']);
    }
}
