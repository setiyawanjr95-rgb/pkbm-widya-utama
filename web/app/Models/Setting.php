<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/** Pengaturan website (profil sekolah, kontak, teks beranda) dalam bentuk key-value. */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    public const DEFAULTS = [
        'site_name' => 'PKBM Widya Utama',
        'tagline' => 'Belajar Tanpa Batas, Raih Masa Depan',
        'hero_title' => 'Pendidikan Kesetaraan untuk Semua Usia',
        'hero_subtitle' => 'Program Paket A, B, dan C setara SD, SMP, dan SMA. Jadwal fleksibel, tutor berpengalaman, dan belajar bisa dari aplikasi Android.',
        'hero_image' => null,
        'logo' => null,
        'about_short' => 'PKBM Widya Utama adalah Pusat Kegiatan Belajar Masyarakat yang menyelenggarakan pendidikan kesetaraan dan keterampilan bagi masyarakat yang ingin melanjutkan pendidikan tanpa batasan usia.',
        'history' => 'PKBM Widya Utama berdiri atas kepedulian terhadap warga yang putus sekolah dan ingin memperoleh ijazah setara. Sejak awal berdiri, PKBM terus berkembang dengan menghadirkan pembelajaran tatap muka dan daring yang fleksibel bagi pekerja, ibu rumah tangga, dan remaja.',
        'vision' => 'Menjadi pusat pembelajaran masyarakat yang unggul, inklusif, dan berdaya saing.',
        'missions' => [
            'Menyelenggarakan pendidikan kesetaraan Paket A, B, dan C yang bermutu.',
            'Memberikan layanan belajar yang fleksibel sesuai kebutuhan warga belajar.',
            'Membekali warga belajar dengan keterampilan hidup dan kewirausahaan.',
            'Memanfaatkan teknologi untuk pembelajaran yang mudah diakses.',
        ],
        'head_name' => 'Kepala PKBM',
        'head_photo' => null,
        'head_message' => 'Selamat datang di PKBM Widya Utama. Kami percaya setiap orang berhak mendapatkan pendidikan, kapan pun dan di usia berapa pun. Mari bergabung dan raih masa depan yang lebih baik bersama kami.',
        'address' => 'Alamat PKBM Widya Utama',
        'phone' => '',
        'whatsapp' => '',
        'email' => '',
        'office_hours' => 'Senin - Sabtu, 08.00 - 16.00',
        'maps_embed' => '',
        'facebook' => '',
        'instagram' => '',
        'youtube' => '',
        'apk_url' => 'https://apk-pkbm.netwarga.my.id/pkbm-widya-utama.apk',
        'registration_open' => true,
        'registration_info' => 'Syarat umum: fotokopi KTP/KK, ijazah terakhir (jika ada), dan pas foto 3x4 sebanyak 2 lembar.',
        'stats' => [
            ['label' => 'Warga Belajar', 'value' => '300+'],
            ['label' => 'Tutor', 'value' => '20+'],
            ['label' => 'Lulusan', 'value' => '1.000+'],
            ['label' => 'Tahun Berdiri', 'value' => '2010'],
        ],
        'features' => [
            ['title' => 'Jadwal Fleksibel', 'text' => 'Belajar di sela kerja atau urusan rumah tangga, pagi maupun sore.'],
            ['title' => 'Ijazah Resmi', 'text' => 'Ijazah setara SD, SMP, dan SMA yang diakui untuk melanjutkan kuliah atau bekerja.'],
            ['title' => 'Belajar dari HP', 'text' => 'Materi, tugas, dan nilai bisa diakses lewat aplikasi Android PKBM.'],
            ['title' => 'Tutor Berpengalaman', 'text' => 'Didampingi tutor yang sabar dan memahami kebutuhan warga belajar dewasa.'],
        ],
    ];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    public static function allValues(): array
    {
        return Cache::rememberForever('site_settings', fn () => array_merge(
            self::DEFAULTS,
            self::query()->get()->mapWithKeys(fn (Setting $s) => [$s->key => $s->value])->all(),
        ));
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::allValues()[$key] ?? $default;
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            self::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('site_settings');
    }
}
