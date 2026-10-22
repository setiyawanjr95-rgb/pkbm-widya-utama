# PKBM Widya Utama – Aplikasi Android Warga Belajar

Aplikasi pembelajaran online (Flutter) untuk warga belajar PKBM Widya Utama.
Admin & API akan dibuat terpisah dengan Laravel + MySQL.

## Fitur
- Splash screen & login (Username/NISN + password)
- Beranda: progres belajar, menu cepat, tugas mendatang, pengumuman
- Kelas Saya (aktif/selesai) → Materi per mapel (materi, tugas, kuis)
- Detail materi (PDF/video + lampiran), kuis pilihan ganda dengan skor
- Tugas: lihat soal, unggah jawaban (PDF/JPG/DOC), status pengumpulan
- Nilai (semua/tugas/kuis) + rata-rata, Jadwal per bulan, Notifikasi
- Profil: data pribadi, riwayat belajar, ubah password, pengaturan notifikasi, keluar

## Struktur
```
lib/
  core/       tema warna & format tanggal
  models/     model data (Student, Course, Assignment, Grade, ...)
  data/       Repository (MockRepository sekarang, ApiRepository untuk Laravel)
  widgets/    komponen UI bersama
  screens/    semua halaman
```

## Data dummy vs backend
Saat ini aplikasi memakai data dummy (`lib/data/mock_data.dart`).
Untuk menyambung ke Laravel, ubah di `lib/data/repository.dart`:
```dart
static const useMock = false;
static const baseUrl = 'https://domain-anda/api';
```
lalu lengkapi mapping JSON di `ApiRepository`.

## Build
```
flutter pub get
flutter build apk --release
```
APK: `build/app/outputs/flutter-apk/app-release.apk`.
Setiap push ke `main`, GitHub Actions otomatis build APK (lihat tab Actions → Artifacts).

> Catatan: APK saat ini ditandatangani dengan debug key. Sebelum rilis ke Play Store, buat keystore sendiri.

## Build di server sendiri (mini PC)
Cukup install Docker, lalu dari folder project jalankan `./build-apk.sh`. Hasilnya di `dist/pkbm-widya-utama.apk`.
