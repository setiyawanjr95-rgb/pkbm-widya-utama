import 'package:flutter/material.dart';

import '../core/theme.dart';

/// Kunci ikon & warna dari server (diatur di admin panel) -> ikon & warna Flutter.
IconData iconFromKey(String? key) => switch (key) {
      'calculate' => Icons.calculate_rounded,
      'menu_book' => Icons.menu_book_rounded,
      'translate' => Icons.translate_rounded,
      'science' => Icons.science_rounded,
      'public' => Icons.public_rounded,
      'flag' => Icons.flag_rounded,
      'mosque' => Icons.mosque_rounded,
      'palette' => Icons.palette_rounded,
      'computer' => Icons.computer_rounded,
      'sports' => Icons.sports_soccer_rounded,
      'work' => Icons.handyman_rounded,
      _ => Icons.book_rounded,
    };

Color colorFromKey(String? key) => switch (key) {
      'success' => AppColors.success,
      'danger' => AppColors.danger,
      'warning' => AppColors.warning,
      _ => AppColors.primary,
    };

DateTime _date(dynamic v) => DateTime.tryParse('${v ?? ''}')?.toLocal() ?? DateTime.now();

class Student {
  final String name;
  final String nisn;
  final String paket;
  final String kelas;
  final String email;
  final String phone;
  final String address;

  const Student({
    required this.name,
    required this.nisn,
    required this.paket,
    required this.kelas,
    required this.email,
    required this.phone,
    required this.address,
  });

  factory Student.fromJson(Map<String, dynamic> j) => Student(
        name: j['name'] ?? '',
        nisn: j['nisn'] ?? '',
        paket: j['paket'] ?? '',
        kelas: j['kelas'] ?? '',
        email: j['email'] ?? '',
        phone: j['phone'] ?? '',
        address: j['address'] ?? '',
      );
}

class Course {
  final int id;
  final String name;
  final String paket;
  final int totalMateri;
  final int totalTugas;
  final double progress;
  final bool finished;
  final IconData icon;
  final Color color;

  const Course({
    required this.id,
    required this.name,
    required this.paket,
    required this.totalMateri,
    required this.totalTugas,
    required this.progress,
    this.finished = false,
    required this.icon,
    required this.color,
  });

  factory Course.fromJson(Map<String, dynamic> j) => Course(
        id: j['id'],
        name: j['name'] ?? '',
        paket: j['paket'] ?? '',
        totalMateri: j['total_materi'] ?? 0,
        totalTugas: j['total_tugas'] ?? 0,
        progress: (j['progress'] ?? 0).toDouble(),
        finished: j['finished'] ?? false,
        icon: iconFromKey(j['icon']),
        color: colorFromKey(j['color']),
      );
}

enum ContentType { pdf, video, quiz, assignment }

class LearningMaterial {
  final int id;
  final int courseId;
  final String title;
  final ContentType type;
  final String meta;
  final String description;
  final bool done;
  final String? attachmentName;
  final String? attachmentSize;
  final String? url;

  const LearningMaterial({
    required this.id,
    required this.courseId,
    required this.title,
    required this.type,
    required this.meta,
    required this.description,
    this.done = false,
    this.attachmentName,
    this.attachmentSize,
    this.url,
  });

  factory LearningMaterial.fromJson(Map<String, dynamic> j) => LearningMaterial(
        id: j['id'],
        courseId: j['course_id'],
        title: j['title'] ?? '',
        type: ContentType.values.firstWhere((t) => t.name == j['type'],
            orElse: () => ContentType.pdf),
        meta: j['meta'] ?? '',
        description: j['description'] ?? '',
        done: j['done'] ?? false,
        attachmentName: j['attachment_name'],
        attachmentSize: j['attachment_size'],
        url: j['url'],
      );
}

class QuizQuestion {
  final int id;
  final String text;
  final List<String> options;

  const QuizQuestion({required this.id, required this.text, required this.options});

  factory QuizQuestion.fromJson(Map<String, dynamic> j) => QuizQuestion(
        id: j['id'],
        text: j['question'] ?? '',
        options: List<String>.from(j['options'] ?? const []),
      );
}

/// Hasil kuis yang dinilai server.
class QuizResult {
  final int score;
  final int correct;
  final int total;

  const QuizResult({required this.score, required this.correct, required this.total});
}

enum TaskStatus { belum, dikumpulkan, dinilai }

class Assignment {
  final int id;
  final int courseId;
  final String courseName;
  final String title;
  final DateTime deadline;
  final String instruction;
  final String attachmentName;
  final String attachmentSize;
  final String? attachmentUrl;
  final TaskStatus status;
  final int? score;
  final String? feedback;

  const Assignment({
    required this.id,
    required this.courseId,
    required this.courseName,
    required this.title,
    required this.deadline,
    required this.instruction,
    required this.attachmentName,
    required this.attachmentSize,
    this.attachmentUrl,
    this.status = TaskStatus.belum,
    this.score,
    this.feedback,
  });

  factory Assignment.fromJson(Map<String, dynamic> j) => Assignment(
        id: j['id'],
        courseId: j['course_id'],
        courseName: j['course_name'] ?? '',
        title: j['title'] ?? '',
        deadline: _date(j['deadline']),
        instruction: j['instruction'] ?? '',
        attachmentName: j['attachment_name'] ?? '',
        attachmentSize: j['attachment_size'] ?? '',
        attachmentUrl: j['attachment_url'],
        status: TaskStatus.values.firstWhere((s) => s.name == j['status'],
            orElse: () => TaskStatus.belum),
        score: j['score'],
        feedback: j['feedback'],
      );
}

class Grade {
  final String courseName;
  final String category; // Tugas / Kuis
  final int score;
  final IconData icon;
  final Color color;

  const Grade({
    required this.courseName,
    required this.category,
    required this.score,
    required this.icon,
    required this.color,
  });

  factory Grade.fromJson(Map<String, dynamic> j) => Grade(
        courseName: j['course_name'] ?? '',
        category: j['category'] ?? '',
        score: j['score'] ?? 0,
        icon: iconFromKey(j['icon']),
        color: colorFromKey(j['color']),
      );

  String get predicate {
    if (score >= 85) return 'Sangat Baik';
    if (score >= 70) return 'Baik';
    if (score >= 60) return 'Cukup';
    return 'Perlu Bimbingan';
  }
}

class Schedule {
  final DateTime date;
  final String courseName;
  final String time;
  final String tutor;
  final String room;

  const Schedule({
    required this.date,
    required this.courseName,
    required this.time,
    required this.tutor,
    required this.room,
  });

  factory Schedule.fromJson(Map<String, dynamic> j) => Schedule(
        date: DateTime.parse(j['date']),
        courseName: j['course_name'] ?? '',
        time: j['time'] ?? '',
        tutor: j['tutor'] ?? '-',
        room: j['room'] ?? '-',
      );
}

class Announcement {
  final String title;
  final String body;
  final DateTime date;

  const Announcement({required this.title, required this.body, required this.date});

  factory Announcement.fromJson(Map<String, dynamic> j) =>
      Announcement(title: j['title'] ?? '', body: j['body'] ?? '', date: _date(j['date']));
}

class AppNotification {
  final String title;
  final String body;
  final String time;
  final IconData icon;
  final bool read;

  const AppNotification({
    required this.title,
    required this.body,
    required this.time,
    required this.icon,
    this.read = false,
  });

  factory AppNotification.fromJson(Map<String, dynamic> j) => AppNotification(
        title: j['title'] ?? '',
        body: j['body'] ?? '',
        time: j['time'] ?? '',
        icon: switch (j['type']) {
          'tugas' => Icons.assignment_rounded,
          'nilai' => Icons.grade_rounded,
          'materi' => Icons.menu_book_rounded,
          'jadwal' => Icons.event_rounded,
          _ => Icons.campaign_rounded,
        },
        read: j['read'] ?? false,
      );
}
