import 'package:flutter/material.dart';

import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';

class NilaiScreen extends StatefulWidget {
  const NilaiScreen({super.key});

  @override
  State<NilaiScreen> createState() => _NilaiScreenState();
}

class _NilaiScreenState extends State<NilaiScreen> {
  int _tab = 0;
  final _future = AppConfig.repository.grades();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Nilai')),
      body: Loader<List<Grade>>(
        future: _future,
        builder: (all) {
          const cats = [null, 'Tugas', 'Kuis'];
          final list = all.where((g) => cats[_tab] == null || g.category == cats[_tab]).toList();
          final avg = list.isEmpty
              ? 0
              : (list.fold<int>(0, (s, g) => s + g.score) / list.length).round();
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              SegmentTabs(
                tabs: const ['Semua', 'Tugas', 'Kuis'],
                index: _tab,
                onChanged: (i) => setState(() => _tab = i),
              ),
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                      colors: [AppColors.primary, AppColors.primaryDark]),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Row(
                  children: [
                    const Expanded(
                      child: Text('Rata-rata Nilai',
                          style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
                    ),
                    Text('$avg',
                        style: const TextStyle(
                            color: Colors.white, fontSize: 28, fontWeight: FontWeight.w800)),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              for (final g in list) ...[
                AppCard(
                  padding: const EdgeInsets.all(12),
                  child: Row(
                    children: [
                      IconBadge(icon: g.icon, color: g.color),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(g.courseName,
                                style: const TextStyle(fontWeight: FontWeight.w700)),
                            Text(g.category,
                                style: const TextStyle(
                                    color: AppColors.textMuted, fontSize: 12)),
                          ],
                        ),
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text('${g.score}',
                              style: const TextStyle(
                                  fontSize: 20, fontWeight: FontWeight.w800)),
                          Text(g.predicate,
                              style: TextStyle(
                                  fontSize: 11.5,
                                  fontWeight: FontWeight.w600,
                                  color: g.score >= 85 ? AppColors.success : AppColors.warning)),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 10),
              ],
            ],
          );
        },
      ),
    );
  }
}
