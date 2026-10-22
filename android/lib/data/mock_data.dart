import 'package:flutter/material.dart';

import '../core/theme.dart';
import '../models/models.dart';

class MockData {
  static const student = Student(
    name: 'Siti Aisyah',
    nisn: '1234567890',
    paket: 'Paket C',
    kelas: 'Kelas 12',
    email: 'siti.aisyah@email.com',
    phone: '0812-3456-7890',
    address: 'Jl. Melati No. 12, Kota',
  );

  static const courses = [
    Course(
      id: 1,
      name: 'Matematika',
      paket: 'Paket C · Kelas 12',
      totalMateri: 3,
      totalTugas: 2,
      progress: 0.6,
      icon: Icons.calculate_rounded,
      color: AppColors.danger,
    ),
    Course(
      id: 2,
      name: 'Bahasa Indonesia',
      paket: 'Paket C · Kelas 12',
      totalMateri: 2,
      totalTugas: 1,
      progress: 0.4,
      icon: Icons.menu_book_rounded,
      color: AppColors.success,
    ),
    Course(
      id: 3,
      name: 'Bahasa Inggris',
      paket: 'Paket C · Kelas 12',
      totalMateri: 2,
      totalTugas: 1,
      progress: 0.5,
      icon: Icons.translate_rounded,
      color: AppColors.danger,
    ),
    Course(
      id: 4,
      name: 'IPA',
      paket: 'Paket C · Kelas 12',
      totalMateri: 1,
      totalTugas: 1,
      progress: 0.3,
      icon: Icons.science_rounded,
      color: AppColors.success,
    ),
    Course(
      id: 5,
      name: 'Pendidikan Pancasila',
      paket: 'Paket C · Kelas 11',
      totalMateri: 4,
      totalTugas: 2,
      progress: 1,
      finished: true,
      icon: Icons.flag_rounded,
      color: AppColors.primary,
    ),
  ];

  static const materials = [
    LearningMaterial(
      id: 1,
      courseId: 1,
      title: 'Bab 1 – Persamaan Linear',
      type: ContentType.pdf,
      meta: 'PDF · 2.4 MB',
      description:
          'Pelajari konsep dasar persamaan linear satu variabel dan contoh penyelesaiannya dalam kehidupan sehari-hari.',
      done: true,
      attachmentName: 'Contoh Soal.pdf',
      attachmentSize: '1.2 MB',
    ),
    LearningMaterial(
      id: 2,
      courseId: 1,
      title: 'Video Pembelajaran',
      type: ContentType.video,
      meta: 'Video · 12 menit',
      description:
          'Video penjelasan langkah-langkah menyelesaikan persamaan linear dua variabel dengan metode substitusi dan eliminasi.',
      done: true,
    ),
    LearningMaterial(
      id: 3,
      courseId: 1,
      title: 'Latihan Soal',
      type: ContentType.quiz,
      meta: 'Kuis · 10 soal',
      description: 'Kerjakan 10 soal pilihan ganda untuk menguji pemahaman Bab 1.',
    ),
    LearningMaterial(
      id: 4,
      courseId: 1,
      title: 'Ringkasan Materi',
      type: ContentType.pdf,
      meta: 'PDF · 1.1 MB',
      description: 'Ringkasan rumus dan konsep penting Bab 1 untuk persiapan ujian.',
      attachmentName: 'Ringkasan_Bab1.pdf',
      attachmentSize: '1.1 MB',
    ),
    LearningMaterial(
      id: 5,
      courseId: 2,
      title: 'Bab 1 – Teks Eksposisi',
      type: ContentType.pdf,
      meta: 'PDF · 1.8 MB',
      description: 'Struktur, ciri kebahasaan, dan contoh teks eksposisi.',
      done: true,
      attachmentName: 'Teks_Eksposisi.pdf',
      attachmentSize: '1.8 MB',
    ),
    LearningMaterial(
      id: 6,
      courseId: 2,
      title: 'Video Menulis Opini',
      type: ContentType.video,
      meta: 'Video · 9 menit',
      description: 'Cara menyusun argumen dalam teks opini yang meyakinkan.',
    ),
    LearningMaterial(
      id: 7,
      courseId: 3,
      title: 'Unit 1 – Asking and Giving Opinion',
      type: ContentType.pdf,
      meta: 'PDF · 1.5 MB',
      description: 'Ungkapan untuk meminta dan memberi pendapat dalam bahasa Inggris.',
      attachmentName: 'Unit1_Opinion.pdf',
      attachmentSize: '1.5 MB',
    ),
    LearningMaterial(
      id: 8,
      courseId: 3,
      title: 'Listening Practice',
      type: ContentType.video,
      meta: 'Video · 15 menit',
      description: 'Latihan menyimak percakapan sehari-hari.',
    ),
    LearningMaterial(
      id: 9,
      courseId: 4,
      title: 'Bab 1 – Sistem Pencernaan',
      type: ContentType.pdf,
      meta: 'PDF · 3.0 MB',
      description: 'Organ dan proses pencernaan pada manusia.',
      attachmentName: 'Sistem_Pencernaan.pdf',
      attachmentSize: '3.0 MB',
    ),
  ];

  static final assignments = [
    Assignment(
      id: 1,
      courseId: 1,
      courseName: 'Matematika',
      title: 'Tugas Matematika',
      deadline: DateTime(2025, 4, 20, 23, 59),
      instruction:
          'Kerjakan soal berikut dan unggah file jawaban dalam format PDF, JPG, atau DOC.',
      attachmentName: 'Soal_Tugas_Matematika.pdf',
      attachmentSize: '1.1 MB',
    ),
    Assignment(
      id: 2,
      courseId: 2,
      courseName: 'Bahasa Indonesia',
      title: 'Menulis Teks Eksposisi',
      deadline: DateTime(2025, 4, 22, 23, 59),
      instruction:
          'Tulis teks eksposisi minimal 3 paragraf dengan tema lingkungan sekitar.',
      attachmentName: 'Panduan_Tugas.pdf',
      attachmentSize: '640 KB',
    ),
    Assignment(
      id: 3,
      courseId: 4,
      courseName: 'IPA',
      title: 'Laporan Pengamatan',
      deadline: DateTime(2025, 4, 15, 23, 59),
      instruction: 'Buat laporan hasil pengamatan sistem pencernaan.',
      attachmentName: 'Format_Laporan.docx',
      attachmentSize: '220 KB',
      status: TaskStatus.dinilai,
    ),
  ];

  static const grades = [
    Grade(
        courseName: 'Matematika',
        category: 'Tugas',
        score: 80,
        icon: Icons.calculate_rounded,
        color: AppColors.success),
    Grade(
        courseName: 'Bahasa Indonesia',
        category: 'Tugas',
        score: 85,
        icon: Icons.menu_book_rounded,
        color: AppColors.primary),
    Grade(
        courseName: 'Bahasa Inggris',
        category: 'Kuis',
        score: 75,
        icon: Icons.translate_rounded,
        color: AppColors.danger),
    Grade(
        courseName: 'IPA',
        category: 'Kuis',
        score: 90,
        icon: Icons.science_rounded,
        color: AppColors.success),
  ];

  static final schedules = [
    Schedule(
        date: DateTime(2025, 4, 14),
        courseName: 'Matematika',
        time: '08:00 - 10:00',
        tutor: 'Bpk. Ahmad Fauzi',
        room: 'Ruang 1'),
    Schedule(
        date: DateTime(2025, 4, 15),
        courseName: 'Bahasa Indonesia',
        time: '08:00 - 10:00',
        tutor: 'Ibu Rina Lestari',
        room: 'Ruang 2'),
    Schedule(
        date: DateTime(2025, 4, 16),
        courseName: 'Bahasa Inggris',
        time: '08:00 - 10:00',
        tutor: 'Ibu Maya Sari',
        room: 'Online (Zoom)'),
    Schedule(
        date: DateTime(2025, 4, 17),
        courseName: 'IPA',
        time: '08:00 - 10:00',
        tutor: 'Bpk. Hendra',
        room: 'Lab IPA'),
    Schedule(
        date: DateTime(2025, 4, 18),
        courseName: 'TIK',
        time: '08:00 - 10:00',
        tutor: 'Bpk. Dimas',
        room: 'Lab Komputer'),
  ];

  static final announcements = [
    Announcement(
      title: 'Jadwal Ujian Tengah Semester',
      body: 'Ujian Tengah Semester dilaksanakan pada 20–22 Mei 2025. Persiapkan diri dengan baik.',
      date: DateTime(2025, 5, 2),
    ),
    Announcement(
      title: 'Libur Hari Raya',
      body: 'Kegiatan belajar diliburkan tanggal 31 Maret – 7 April 2025.',
      date: DateTime(2025, 3, 25),
    ),
  ];

  static const notifications = [
    AppNotification(
      title: 'Tugas baru: Tugas Matematika',
      body: 'Batas pengumpulan 20 Apr 2025, 23:59',
      time: '2 jam lalu',
      icon: Icons.assignment_rounded,
    ),
    AppNotification(
      title: 'Nilai IPA sudah keluar',
      body: 'Laporan Pengamatan mendapat nilai 90',
      time: 'Kemarin',
      icon: Icons.bar_chart_rounded,
    ),
    AppNotification(
      title: 'Pengumuman UTS',
      body: 'Jadwal Ujian Tengah Semester 20–22 Mei 2025',
      time: '3 hari lalu',
      icon: Icons.campaign_rounded,
      read: true,
    ),
  ];

  static const quiz = [
    QuizQuestion(id: 1, text: 'Nilai x dari persamaan 2x + 4 = 10 adalah...', options: ['2', '3', '4', '5']),
    QuizQuestion(id: 2, text: 'Jika 3x - 5 = 7, maka x = ...', options: ['3', '4', '5', '6']),
    QuizQuestion(id: 3, text: 'Penyelesaian dari x + 7 = 12 adalah...', options: ['4', '5', '6', '19']),
    QuizQuestion(id: 4, text: 'Nilai y dari 5y = 25 adalah...', options: ['3', '4', '5', '6']),
    QuizQuestion(id: 5, text: 'Jika 2(x + 1) = 8, maka x = ...', options: ['2', '3', '4', '5']),
  ];

  static const quizAnswers = {1: 1, 2: 1, 3: 1, 4: 2, 5: 1};
}
