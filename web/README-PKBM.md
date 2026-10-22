# Web Admin + API PKBM Widya Utama

Laravel 13 + Filament 5 (admin panel) + Sanctum (API app Android) + MariaDB.

## Cara pasang (shell Proxmox node `pve`)
1. Jalankan:
   `bash <(curl -fsSL https://raw.githubusercontent.com/setiyawanjr95-rgb/pkbm-widya-utama/main/deploy/install-web.sh)`
   Script bikin LXC 102 `pkbm-web` (Debian 12, 2 core, 2 GB RAM, 20 GB disk), install nginx + PHP 8.3 + MariaDB, pasang aplikasi, dan backup harian.
2. Tunggu 5-10 menit. Di akhir muncul IP kontainer, password admin, dan password root kontainer. Catat.
3. Cloudflare: Zero Trust > Networks > Tunnels > tunnel yang dipakai LXC 100 > Public Hostname > Add
   - Subdomain `pkbm`, domain `netwarga.my.id`
   - Service `HTTP`, URL `IP-KONTAINER:80`
4. Buka `https://pkbm.netwarga.my.id/admin`, login `admin@pkbm.local` + password dari langkah 2.

Opsi (taruh di depan `bash`): `IP_CONFIG="192.168.101.7/24,gw=192.168.101.1"` untuk IP statis,
`SEED_DEMO=false` kalau tidak mau data contoh, `CTID=103` kalau ID 102 sudah dipakai.

## Perintah berguna (shell Proxmox)
- Update aplikasi dari GitHub: `pct exec 102 -- /usr/local/bin/update-pkbm-web`
- Backup manual: `pct exec 102 -- /usr/local/bin/backup-pkbm` (otomatis tiap 02.30, disimpan 14 hari di /var/backups/pkbm)
- Lihat password: `pct exec 102 -- cat /root/pkbm-web-credentials.txt`

## Website publik
`https://pkbm.netwarga.my.id` berisi Beranda, Profil, Program, Berita, Galeri, Pendaftaran online, dan Kontak.
Semua isi diatur dari admin, grup menu **Website**: Pengaturan Website (identitas, beranda, profil, kontak, peta, WA),
Program, Berita & Artikel, Galeri, Testimoni, Pengelola & Tutor, dan Pendaftaran Online.
Pendaftar yang diterima bisa langsung dibuatkan akun warga belajar (tombol "Buat akun siswa").

Tampilan (CSS/JS) sudah dibuild dan ikut di repo (`public/build`). Kalau mengubah file di `resources/css` atau `resources/js`,
jalankan `npm install && npm run build` lalu commit hasilnya.

## Isi admin panel
- Dasbor: jumlah warga belajar aktif, mapel, tugas menunggu dinilai, jadwal hari ini
- Warga Belajar: tambah/edit akun (login pakai NISN atau username), reset password, filter paket/kelas
- Mata Pelajaran: per paket & kelas, tutor, ikon & warna di app
- Materi: PDF/dokumen (upload), video (link YouTube/Drive), kuis pilihan ganda
- Tugas & Penilaian: buat tugas + lampiran, lihat pengumpulan, unduh file, beri nilai + catatan
- Rekap Nilai: semua nilai (otomatis dari tugas & kuis) + input nilai ujian manual
- Jadwal: bisa diulang mingguan sekaligus (misal 12 minggu)
- Pengumuman: untuk semua paket atau paket tertentu
- Akun Admin & Tutor (khusus admin)

Notifikasi ke app otomatis dibuat saat ada materi baru, tugas baru, tugas dinilai, dan pengumuman.

## API untuk app Android
Base URL `https://pkbm.netwarga.my.id/api`, header `Accept: application/json`, `Authorization: Bearer <token>`.
Semua response dibungkus `data`, kecuali login.

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | /login | `username` (NISN/username/email), `password` → `{token, user}` |
| POST | /logout | hapus token |
| POST | /password | `current_password`, `password`, `password_confirmation` |
| GET/PUT | /me | profil; PUT bisa ubah `email`, `phone`, `address` |
| GET | /courses | mapel sesuai paket & kelas + progress |
| GET | /courses/{id}/materials | materi + status selesai |
| POST | /materials/{id}/complete | tandai materi selesai |
| GET/POST | /materials/{id}/quiz | ambil soal / kirim `answers[question_id]=index` → nilai |
| GET | /assignments | tugas + status belum/dikumpulkan/dinilai + nilai |
| POST | /assignments/{id}/submit | multipart `file` (pdf, jpg, png, doc, docx, xls, ppt, zip; maks 20 MB) |
| GET | /grades | rata-rata per mapel per kategori + rincian |
| GET | /schedules | `from`, `to` opsional (default 1 bulan lalu s/d 2 bulan ke depan) |
| GET | /announcements | pengumuman |
| GET | /notifications, POST /notifications/read | notifikasi + tandai sudah dibaca |
