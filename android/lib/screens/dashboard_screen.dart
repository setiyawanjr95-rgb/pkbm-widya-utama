import 'package:flutter/material.dart';

import '../core/format.dart';
import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';
import 'jadwal_screen.dart';
import 'kelas_screen.dart';
import 'materi_screen.dart';
import 'nilai_screen.dart';
import 'tugas_screen.dart';

class DashboardScreen extends StatelessWidget {
  final ValueChanged<int> onOpenTab;

  const DashboardScreen({super.key, required this.onOpenTab});

  void _push(BuildContext context, Widget page) =>
      Navigator.of(context).push(MaterialPageRoute(builder: (_) => page));

  @override
  Widget build(BuildContext context) {
    final repo = AppConfig.repository;
    return Scaffold(
      body: Loader<List<dynamic>>(
        future: Future.wait([
          repo.currentStudent(),
          repo.courses(),
          repo.assignments(),
          repo.announcements(),
        ]),
        builder: (data) {
          final student = data[0] as Student;
          final courses = (data[1] as List<Course>).where((c) => !c.finished).toList();
          final tasks =
              (data[2] as List<Assignment>).where((t) => t.status == TaskStatus.belum).toList();
          final announcements = data[3] as List<Announcement>;
          final totalMateri = courses.fold<int>(0, (s, c) => s + c.totalMateri);
          final doneMateri = courses.fold<int>(
            0,
            (s, c) => s + (c.progress * c.totalMateri).round(),
          );
          final progress = totalMateri == 0 ? 0.0 : doneMateri / totalMateri;

          return CustomScrollView(
            slivers: [
              SliverToBoxAdapter(
                child: _Header(
                  name: student.name,
                  progress: progress,
                  done: doneMateri,
                  total: totalMateri,
                  onBell: () => onOpenTab(2),
                ),
              ),
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(16, 18, 16, 24),
                sliver: SliverList.list(
                  children: [
                    // Menu ikon seperti deretan produk di Traveloka
                    GridView.count(
                      crossAxisCount: 4,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      mainAxisSpacing: 14,
                      crossAxisSpacing: 8,
                      childAspectRatio: 0.95,
                      children: [
                        _MenuItem(
                          'Kelas',
                          Icons.menu_book_rounded,
                          AppColors.primary,
                          () => onOpenTab(1),
                        ),
                        _MenuItem(
                          'Tugas',
                          Icons.assignment_rounded,
                          AppColors.accent,
                          () => _push(context, const TugasListScreen()),
                        ),
                        _MenuItem(
                          'Kuis',
                          Icons.quiz_rounded,
                          AppColors.success,
                          () => _push(context, const KelasScreen()),
                        ),
                        _MenuItem(
                          'Nilai',
                          Icons.bar_chart_rounded,
                          AppColors.warning,
                          () => _push(context, const NilaiScreen()),
                        ),
                        _MenuItem(
                          'Jadwal',
                          Icons.calendar_month_rounded,
                          AppColors.primaryDark,
                          () => _push(context, const JadwalScreen()),
                        ),
                        _MenuItem(
                          'Pengumuman',
                          Icons.campaign_rounded,
                          AppColors.danger,
                          () => _push(
                            context,
                            PengumumanScreen(items: announcements),
                          ),
                        ),
                        _MenuItem(
                          'Notifikasi',
                          Icons.notifications_rounded,
                          AppColors.purple,
                          () => onOpenTab(2),
                        ),
                        _MenuItem(
                          'Diskusi',
                          Icons.forum_rounded,
                          AppColors.primary,
                          () => showSnack(context, 'Fitur diskusi segera hadir'),
                        ),
                      ],
                    ),
                    if (announcements.isNotEmpty) ...[
                      const SizedBox(height: 18),
                      SectionHeader(
                        title: 'Info Terbaru',
                        onSeeAll: () => _push(
                          context,
                          PengumumanScreen(items: announcements),
                        ),
                      ),
                      const SizedBox(height: 6),
                      SizedBox(
                        height: 128,
                        child: ListView.separated(
                          scrollDirection: Axis.horizontal,
                          clipBehavior: Clip.none,
                          itemCount: announcements.length,
                          separatorBuilder: (_, __) => const SizedBox(width: 12),
                          itemBuilder: (_, i) => _PromoBanner(
                            announcements[i],
                            index: i,
                            onTap: () => _push(
                              context,
                              PengumumanScreen(items: announcements),
                            ),
                          ),
                        ),
                      ),
                    ],
                    if (courses.isNotEmpty) ...[
                      const SizedBox(height: 20),
                      SectionHeader(
                        title: 'Lanjutkan Belajar',
                        onSeeAll: () => onOpenTab(1),
                      ),
                      const SizedBox(height: 6),
                      SizedBox(
                        height: 164,
                        child: ListView.separated(
                          scrollDirection: Axis.horizontal,
                          clipBehavior: Clip.none,
                          itemCount: courses.length,
                          separatorBuilder: (_, __) => const SizedBox(width: 12),
                          itemBuilder: (_, i) => _CourseTile(
                            courses[i],
                            onTap: () => _push(
                              context,
                              MateriScreen(course: courses[i]),
                            ),
                          ),
                        ),
                      ),
                    ],
                    const SizedBox(height: 20),
                    SectionHeader(
                      title: 'Tugas Mendatang',
                      onSeeAll: () => _push(context, const TugasListScreen()),
                    ),
                    const SizedBox(height: 6),
                    if (tasks.isEmpty)
                      const AppCard(
                        child: Text('Tidak ada tugas yang menunggu 🎉'),
                      ),
                    for (final t in tasks.take(3)) ...[
                      AppCard(
                        padding: const EdgeInsets.all(12),
                        onTap: () => _push(context, TugasDetailScreen(assignment: t)),
                        child: Row(
                          children: [
                            const IconBadge(
                              icon: Icons.assignment_rounded,
                              color: AppColors.accent,
                              size: 40,
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    t.title,
                                    style: const TextStyle(
                                      fontWeight: FontWeight.w700,
                                      fontSize: 14,
                                    ),
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    'Batas: ${shortDate(t.deadline)}',
                                    style: const TextStyle(
                                      color: AppColors.textMuted,
                                      fontSize: 12,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            const Text(
                              'Kerjakan',
                              style: TextStyle(
                                color: AppColors.accent,
                                fontWeight: FontWeight.w800,
                                fontSize: 13,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 10),
                    ],
                  ],
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}

class _Header extends StatelessWidget {
  final String name;
  final double progress;
  final int done;
  final int total;
  final VoidCallback onBell;

  const _Header({
    required this.name,
    required this.progress,
    required this.done,
    required this.total,
    required this.onBell,
  });

  @override
  Widget build(BuildContext context) {
    final top = MediaQuery.of(context).padding.top;
    // Header biru + kartu putih yang menimpa bagian bawahnya (seperti kotak cari Traveloka)
    return Stack(
      children: [
        Container(
          height: top + 150,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [AppColors.primary, AppColors.primaryDark],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
          child: const Stack(
            children: [
              Positioned(
                right: -50,
                top: -40,
                child: _Bubble(size: 180, opacity: 0.10),
              ),
              Positioned(
                left: -30,
                bottom: -60,
                child: _Bubble(size: 140, opacity: 0.08),
              ),
            ],
          ),
        ),
        Padding(
          padding: EdgeInsets.fromLTRB(16, top + 12, 16, 0),
          child: Column(
            children: [
              Row(
                children: [
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(
                      Icons.school_rounded,
                      color: AppColors.primary,
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Halo, $name 👋',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 17,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                        const Text(
                          'Mau belajar apa hari ini?',
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 12.5,
                          ),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    onPressed: onBell,
                    style: IconButton.styleFrom(
                      backgroundColor: Colors.white24,
                    ),
                    icon: const Icon(
                      Icons.notifications_none_rounded,
                      color: Colors.white,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 22),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: const [
                    BoxShadow(
                      color: Color(0x1A0A4A87),
                      blurRadius: 18,
                      offset: Offset(0, 6),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    SizedBox(
                      width: 56,
                      height: 56,
                      child: Stack(
                        alignment: Alignment.center,
                        children: [
                          SizedBox.expand(
                            child: CircularProgressIndicator(
                              value: progress,
                              strokeWidth: 6,
                              backgroundColor: AppColors.primaryLight,
                              color: AppColors.primary,
                              strokeCap: StrokeCap.round,
                            ),
                          ),
                          Text(
                            '${(progress * 100).round()}%',
                            style: const TextStyle(
                              fontWeight: FontWeight.w800,
                              color: AppColors.primary,
                              fontSize: 13,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Progres Belajar',
                            style: TextStyle(
                              fontWeight: FontWeight.w800,
                              fontSize: 15,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            '$done dari $total materi selesai',
                            style: const TextStyle(
                              color: AppColors.textMuted,
                              fontSize: 12.5,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const Icon(
                      Icons.chevron_right_rounded,
                      color: AppColors.textMuted,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

class _Bubble extends StatelessWidget {
  final double size;
  final double opacity;

  const _Bubble({required this.size, required this.opacity});

  @override
  Widget build(BuildContext context) => Container(
        width: size,
        height: size,
        decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: opacity),
          shape: BoxShape.circle,
        ),
      );
}

class _MenuItem extends StatelessWidget {
  final String label;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  const _MenuItem(this.label, this.icon, this.color, this.onTap);

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 50,
            height: 50,
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.border),
            ),
            child: Icon(icon, color: color, size: 26),
          ),
          const SizedBox(height: 6),
          Text(
            label,
            textAlign: TextAlign.center,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(fontSize: 11.5, fontWeight: FontWeight.w600),
          ),
        ],
      ),
    );
  }
}

class _PromoBanner extends StatelessWidget {
  final Announcement a;
  final int index;
  final VoidCallback onTap;

  const _PromoBanner(this.a, {required this.index, required this.onTap});

  static const _gradients = [
    [AppColors.primary, AppColors.primaryDeep],
    [Color(0xFF36B5F7), AppColors.primaryDark],
    [Color(0xFF5B6CF0), AppColors.primaryDark],
  ];

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 270,
        padding: const EdgeInsets.all(16),
        clipBehavior: Clip.antiAlias,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          gradient: LinearGradient(
            colors: _gradients[index % _gradients.length],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    a.title,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w800,
                      fontSize: 15,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    a.body,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: Colors.white70,
                      fontSize: 12,
                      height: 1.35,
                    ),
                  ),
                  const Spacer(),
                  Text(
                    shortDate(a.date),
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 8),
            const Icon(Icons.campaign_rounded, color: Colors.white54, size: 40),
          ],
        ),
      ),
    );
  }
}

class _CourseTile extends StatelessWidget {
  final Course course;
  final VoidCallback onTap;

  const _CourseTile(this.course, {required this.onTap});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 160,
      child: AppCard(
        padding: const EdgeInsets.all(12),
        onTap: onTap,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            IconBadge(icon: course.icon, color: course.color, size: 40),
            const SizedBox(height: 10),
            Text(
              course.name,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14),
            ),
            Text(
              '${course.totalMateri} materi',
              style: const TextStyle(color: AppColors.textMuted, fontSize: 12),
            ),
            const Spacer(),
            ProgressBar(value: course.progress, color: course.color, height: 6),
            const SizedBox(height: 4),
            Text(
              '${(course.progress * 100).round()}% selesai',
              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
            ),
          ],
        ),
      ),
    );
  }
}

class _AnnouncementCard extends StatelessWidget {
  final Announcement a;

  const _AnnouncementCard(this.a);

  @override
  Widget build(BuildContext context) {
    return AppCard(
      color: const Color(0xFFFFF8E8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const IconBadge(
            icon: Icons.campaign_rounded,
            color: AppColors.warning,
            size: 40,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  a.title,
                  style: const TextStyle(fontWeight: FontWeight.w700),
                ),
                const SizedBox(height: 4),
                Text(
                  a.body,
                  style: const TextStyle(
                    color: AppColors.textMuted,
                    fontSize: 12.5,
                    height: 1.4,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class PengumumanScreen extends StatelessWidget {
  final List<Announcement> items;

  const PengumumanScreen({super.key, required this.items});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pengumuman')),
      body: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (_, i) => Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              shortDate(items[i].date),
              style: const TextStyle(color: AppColors.textMuted, fontSize: 12),
            ),
            const SizedBox(height: 6),
            _AnnouncementCard(items[i]),
          ],
        ),
      ),
    );
  }
}
