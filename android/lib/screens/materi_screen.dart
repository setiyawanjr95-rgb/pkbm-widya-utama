import 'package:flutter/material.dart';

import '../core/format.dart';
import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';
import 'kuis_screen.dart';
import 'tugas_screen.dart';

class MateriScreen extends StatefulWidget {
  final Course course;

  const MateriScreen({super.key, required this.course});

  @override
  State<MateriScreen> createState() => _MateriScreenState();
}

class _MateriScreenState extends State<MateriScreen> {
  int _tab = 0;
  late final Future<List<dynamic>> _future = Future.wait([
    AppConfig.repository.materials(widget.course.id),
    AppConfig.repository.assignments(),
  ]);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.course.name)),
      body: Loader<List<dynamic>>(
        future: _future,
        builder: (data) {
          final materials = data[0] as List<LearningMaterial>;
          final tasks = (data[1] as List<Assignment>)
              .where((t) => t.courseId == widget.course.id)
              .toList();
          final List<Widget> items;
          if (_tab == 0) {
            items = materials
                .where((m) => m.type == ContentType.pdf || m.type == ContentType.video)
                .map((m) => _MaterialTile(m))
                .toList();
          } else if (_tab == 1) {
            items = tasks
                .map((t) => _Tile(
                      icon: Icons.assignment_rounded,
                      color: AppColors.warning,
                      title: t.title,
                      subtitle: 'Kumpulkan sebelum ${shortDate(t.deadline)}',
                      onTap: () => Navigator.of(context).push(MaterialPageRoute(
                          builder: (_) => TugasDetailScreen(assignment: t))),
                    ))
                .toList();
          } else {
            items = materials
                .where((m) => m.type == ContentType.quiz)
                .map((m) => _MaterialTile(m))
                .toList();
          }
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              SegmentTabs(
                tabs: const ['Materi', 'Tugas', 'Kuis'],
                index: _tab,
                onChanged: (i) => setState(() => _tab = i),
              ),
              const SizedBox(height: 16),
              if (items.isEmpty)
                const Padding(
                  padding: EdgeInsets.all(32),
                  child: Center(child: Text('Belum ada konten')),
                ),
              for (final w in items) ...[w, const SizedBox(height: 10)],
            ],
          );
        },
      ),
    );
  }
}

class _MaterialTile extends StatelessWidget {
  final LearningMaterial m;

  const _MaterialTile(this.m);

  @override
  Widget build(BuildContext context) {
    final (icon, color) = switch (m.type) {
      ContentType.pdf => (Icons.description_rounded, AppColors.primary),
      ContentType.video => (Icons.play_circle_fill_rounded, AppColors.success),
      ContentType.quiz => (Icons.quiz_rounded, AppColors.purple),
      ContentType.assignment => (Icons.assignment_rounded, AppColors.warning),
    };
    return _Tile(
      icon: icon,
      color: color,
      title: m.title,
      subtitle: m.meta,
      done: m.done,
      onTap: () => Navigator.of(context).push(MaterialPageRoute(
        builder: (_) =>
            m.type == ContentType.quiz ? KuisScreen(material: m) : DetailMateriScreen(material: m),
      )),
    );
  }
}

class _Tile extends StatelessWidget {
  final IconData icon;
  final Color color;
  final String title;
  final String subtitle;
  final bool done;
  final VoidCallback onTap;

  const _Tile({
    required this.icon,
    required this.color,
    required this.title,
    required this.subtitle,
    this.done = false,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return AppCard(
      padding: const EdgeInsets.all(12),
      onTap: onTap,
      child: Row(
        children: [
          IconBadge(icon: icon, color: color),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                const SizedBox(height: 2),
                Text(subtitle,
                    style: const TextStyle(color: AppColors.textMuted, fontSize: 12)),
              ],
            ),
          ),
          done
              ? const Icon(Icons.check_circle_rounded, color: AppColors.success)
              : const Icon(Icons.chevron_right_rounded, color: AppColors.textMuted),
        ],
      ),
    );
  }
}

class DetailMateriScreen extends StatefulWidget {
  final LearningMaterial material;

  const DetailMateriScreen({super.key, required this.material});

  @override
  State<DetailMateriScreen> createState() => _DetailMateriScreenState();
}

class _DetailMateriScreenState extends State<DetailMateriScreen> {
  LearningMaterial get material => widget.material;

  @override
  void initState() {
    super.initState();
    // Membuka materi dihitung sebagai selesai untuk progress belajar.
    if (!material.done) AppConfig.repository.completeMaterial(material.id).ignore();
  }

  @override
  Widget build(BuildContext context) {
    final isVideo = material.type == ContentType.video;
    return Scaffold(
      appBar: AppBar(title: const Text('Detail Materi')),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          if (isVideo)
            AspectRatio(
              aspectRatio: 16 / 9,
              child: Container(
                decoration: BoxDecoration(
                  color: AppColors.primaryDark,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: const Icon(Icons.play_circle_fill_rounded,
                    color: Colors.white, size: 64),
              ),
            ),
          if (isVideo) const SizedBox(height: 16),
          Text(material.title,
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
          const SizedBox(height: 6),
          Row(
            children: [
              Icon(isVideo ? Icons.videocam_rounded : Icons.description_rounded,
                  size: 16, color: AppColors.textMuted),
              const SizedBox(width: 6),
              Text(material.meta,
                  style: const TextStyle(color: AppColors.textMuted, fontSize: 13)),
            ],
          ),
          const SizedBox(height: 18),
          ElevatedButton.icon(
            onPressed: () => openLink(context, material.url),
            icon: Icon(isVideo ? Icons.play_arrow_rounded : Icons.download_rounded),
            label: Text(isVideo ? 'Putar Video' : 'Unduh'),
          ),
          const SizedBox(height: 24),
          const Text('Deskripsi',
              style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
          const SizedBox(height: 8),
          Text(material.description,
              style: const TextStyle(color: AppColors.textMuted, height: 1.5)),
          if (material.attachmentName != null) ...[
            const SizedBox(height: 24),
            const Text('Lampiran',
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
            const SizedBox(height: 8),
            FileTile(
              name: material.attachmentName!,
              size: material.attachmentSize ?? '',
              onTap: () => openLink(context, material.url),
            ),
          ],
        ],
      ),
    );
  }
}
