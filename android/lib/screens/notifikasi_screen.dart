import 'package:flutter/material.dart';

import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';

class NotifikasiScreen extends StatelessWidget {
  const NotifikasiScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Notifikasi'), automaticallyImplyLeading: false),
      body: Loader<List<AppNotification>>(
        future: AppConfig.repository.notifications().then((list) {
          AppConfig.repository.markNotificationsRead().ignore();
          return list;
        }),
        builder: (list) => list.isEmpty
            ? const Center(child: Text('Belum ada notifikasi'))
            : ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: list.length,
          separatorBuilder: (_, __) => const SizedBox(height: 10),
          itemBuilder: (_, i) {
            final n = list[i];
            return AppCard(
              color: n.read ? null : AppColors.primaryLight.withValues(alpha: 0.5),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  IconBadge(icon: n.icon, color: AppColors.primary, size: 40),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(n.title, style: const TextStyle(fontWeight: FontWeight.w700)),
                        const SizedBox(height: 2),
                        Text(n.body,
                            style:
                                const TextStyle(color: AppColors.textMuted, fontSize: 12.5)),
                        const SizedBox(height: 6),
                        Text(n.time,
                            style: const TextStyle(color: AppColors.textMuted, fontSize: 11)),
                      ],
                    ),
                  ),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
