import 'package:flutter/material.dart';

import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';
import 'materi_screen.dart';

class KelasScreen extends StatefulWidget {
  final bool showBack;

  const KelasScreen({super.key, this.showBack = true});

  @override
  State<KelasScreen> createState() => _KelasScreenState();
}

class _KelasScreenState extends State<KelasScreen> {
  int _tab = 0;
  final _future = AppConfig.repository.courses();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Kelas Saya'),
        automaticallyImplyLeading: widget.showBack,
      ),
      body: Loader<List<Course>>(
        future: _future,
        builder: (all) {
          final list = all.where((c) => c.finished == (_tab == 1)).toList();
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              SegmentTabs(
                tabs: const ['Aktif', 'Selesai'],
                index: _tab,
                onChanged: (i) => setState(() => _tab = i),
              ),
              const SizedBox(height: 16),
              if (list.isEmpty)
                const Padding(
                  padding: EdgeInsets.all(32),
                  child: Center(child: Text('Belum ada kelas')),
                ),
              for (final c in list) ...[
                _CourseCard(course: c),
                const SizedBox(height: 12),
              ],
            ],
          );
        },
      ),
    );
  }
}

class _CourseCard extends StatelessWidget {
  final Course course;

  const _CourseCard({required this.course});

  @override
  Widget build(BuildContext context) {
    return AppCard(
      onTap: () => Navigator.of(context).push(
        MaterialPageRoute(builder: (_) => MateriScreen(course: course)),
      ),
      child: Row(
        children: [
          IconBadge(icon: course.icon, color: course.color, size: 52),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(course.name,
                    style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                const SizedBox(height: 2),
                Text(course.paket,
                    style: const TextStyle(color: AppColors.textMuted, fontSize: 12)),
                const SizedBox(height: 2),
                Text('${course.totalMateri} materi · ${course.totalTugas} tugas',
                    style: const TextStyle(color: AppColors.textMuted, fontSize: 12)),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Expanded(child: ProgressBar(value: course.progress)),
                    const SizedBox(width: 10),
                    Text('${(course.progress * 100).round()}%',
                        style: const TextStyle(
                            fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.primary)),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: 6),
          const Icon(Icons.chevron_right_rounded, color: AppColors.textMuted),
        ],
      ),
    );
  }
}
