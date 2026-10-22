import 'package:flutter/material.dart';

import '../core/format.dart';
import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';
import 'jadwal_screen.dart';
import 'kelas_screen.dart';
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
          final tasks = (data[2] as List<Assignment>)
              .where((t) => t.status == TaskStatus.belum)
              .toList();
          final announcements = data[3] as List<Announcement>;
          final totalMateri = courses.fold<int>(0, (s, c) => s + c.totalMateri);
          final doneMateri =
              courses.fold<int>(0, (s, c) => s + (c.progress * c.totalMateri).round());
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
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 24),
                sliver: SliverList.list(children: [
                  GridView.count(
                    crossAxisCount: 3,
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    mainAxisSpacing: 12,
                    crossAxisSpacing: 12,
                    childAspectRatio: 1.05,
                    children: [
                      _MenuItem('Kelas Saya', Icons.menu_book_rounded, AppColors.primary,
                          () => onOpenTab(1)),
                      _MenuItem('Tugas', Icons.assignment_rounded, AppColors.success,
                          () => _push(context, const TugasListScreen())),
                      _MenuItem('Kuis', Icons.check_circle_rounded, AppColors.success,
                          () => _push(context, const KelasScreen())),
                      _MenuItem('Nilai', Icons.bar_chart_rounded, AppColors.warning,
                          () => _push(context, const NilaiScreen())),
                      _MenuItem('Jadwal', Icons.calendar_month_rounded, AppColors.primary,
                          () => _push(context, const JadwalScreen())),
                      _MenuItem('Diskusi', Icons.forum_rounded, AppColors.purple,
                          () => showSnack(context, 'Fitur diskusi segera hadir')),
                    ],
                  ),
                  const SizedBox(height: 20),
                  SectionHeader(
                    title: 'Tugas Mendatang',
                    onSeeAll: () => _push(context, const TugasListScreen()),
                  ),
                  const SizedBox(height: 4),
                  if (tasks.isEmpty)
                    const AppCard(child: Text('Tidak ada tugas yang menunggu 🎉')),
                  for (final t in tasks.take(2)) ...[
                    AppCard(
                      padding: const EdgeInsets.all(12),
                      onTap: () => _push(context, TugasDetailScreen(assignment: t)),
                      child: Row(
                        children: [
                          const IconBadge(
                              icon: Icons.assignment_rounded, color: AppColors.success, size: 40),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(t.title,
                                    style: const TextStyle(
                                        fontWeight: FontWeight.w700, fontSize: 14)),
                                const SizedBox(height: 2),
                                Text('Batas: ${shortDate(t.deadline)}',
                                    style: const TextStyle(
                                        color: AppColors.textMuted, fontSize: 12)),
                              ],
                            ),
                          ),
                          const Icon(Icons.chevron_right_rounded, color: AppColors.textMuted),
                        ],
                      ),
                    ),
                    const SizedBox(height: 10),
                  ],
                  const SizedBox(height: 10),
                  SectionHeader(
                    title: 'Pengumuman',
                    onSeeAll: () => _push(context, PengumumanScreen(items: announcements)),
                  ),
                  const SizedBox(height: 4),
                  if (announcements.isNotEmpty)
                    _AnnouncementCard(announcements.first),
                ]),
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
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [AppColors.primary, AppColors.primaryDark],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.vertical(bottom: Radius.circular(28)),
      ),
      padding: EdgeInsets.fromLTRB(20, MediaQuery.of(context).padding.top + 16, 20, 22),
      child: Column(
        children: [
          Row(
            children: [
              const CircleAvatar(
                radius: 24,
                backgroundColor: Colors.white,
                child: Icon(Icons.person_rounded, color: AppColors.primary, size: 30),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Halo, $name 👋',
                        style: const TextStyle(
                            color: Colors.white, fontSize: 18, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 2),
                    const Text('Selamat belajar hari ini!',
                        style: TextStyle(color: Colors.white70, fontSize: 13)),
                  ],
                ),
              ),
              Stack(
                children: [
                  IconButton(
                    onPressed: onBell,
                    icon: const Icon(Icons.notifications_none_rounded,
                        color: Colors.white, size: 28),
                  ),
                  Positioned(
                    right: 10,
                    top: 10,
                    child: Container(
                      width: 9,
                      height: 9,
                      decoration: const BoxDecoration(
                          color: AppColors.danger, shape: BoxShape.circle),
                    ),
                  ),
                ],
              ),
            ],
          ),
          const SizedBox(height: 18),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Expanded(
                      child: Text('Progres Belajar Anda',
                          style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                    ),
                    Text('${(progress * 100).round()}%',
                        style: const TextStyle(
                            fontWeight: FontWeight.w800,
                            color: AppColors.primary,
                            fontSize: 16)),
                  ],
                ),
                const SizedBox(height: 10),
                ProgressBar(value: progress, height: 8),
                const SizedBox(height: 8),
                Text('$done dari $total materi selesai',
                    style: const TextStyle(color: AppColors.textMuted, fontSize: 12)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _MenuItem extends StatelessWidget {
  final String label;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  const _MenuItem(this.label, this.icon, this.color, this.onTap);

  @override
  Widget build(BuildContext context) {
    return AppCard(
      padding: const EdgeInsets.all(8),
      onTap: onTap,
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          IconBadge(icon: icon, color: color),
          const SizedBox(height: 8),
          Text(label,
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w600)),
        ],
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
          const IconBadge(icon: Icons.campaign_rounded, color: AppColors.warning, size: 40),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(a.title, style: const TextStyle(fontWeight: FontWeight.w700)),
                const SizedBox(height: 4),
                Text(a.body,
                    style: const TextStyle(
                        color: AppColors.textMuted, fontSize: 12.5, height: 1.4)),
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
            Text(shortDate(items[i].date),
                style: const TextStyle(color: AppColors.textMuted, fontSize: 12)),
            const SizedBox(height: 6),
            _AnnouncementCard(items[i]),
          ],
        ),
      ),
    );
  }
}
