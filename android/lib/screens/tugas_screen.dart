import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';

import '../core/format.dart';
import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';

class TugasListScreen extends StatelessWidget {
  const TugasListScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Tugas')),
      body: Loader<List<Assignment>>(
        future: AppConfig.repository.assignments(),
        builder: (list) => ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: list.length,
          separatorBuilder: (_, __) => const SizedBox(height: 10),
          itemBuilder: (_, i) {
            final t = list[i];
            return AppCard(
              onTap: () => Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => TugasDetailScreen(assignment: t))),
              child: Row(
                children: [
                  const IconBadge(icon: Icons.assignment_rounded, color: AppColors.success),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(t.title, style: const TextStyle(fontWeight: FontWeight.w700)),
                        const SizedBox(height: 2),
                        Text('${t.courseName} · Batas ${shortDate(t.deadline)}',
                            style:
                                const TextStyle(color: AppColors.textMuted, fontSize: 12)),
                        const SizedBox(height: 8),
                        _statusChip(t.status),
                      ],
                    ),
                  ),
                  const Icon(Icons.chevron_right_rounded, color: AppColors.textMuted),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}

Widget _statusChip(TaskStatus s) => switch (s) {
      TaskStatus.belum => const StatusChip(
          label: 'Belum dikumpulkan', color: AppColors.warning, icon: Icons.schedule_rounded),
      TaskStatus.dikumpulkan => const StatusChip(
          label: 'Sudah dikumpulkan', color: AppColors.primary, icon: Icons.check_rounded),
      TaskStatus.dinilai => const StatusChip(
          label: 'Sudah dinilai', color: AppColors.success, icon: Icons.verified_rounded),
    };

class TugasDetailScreen extends StatefulWidget {
  final Assignment assignment;

  const TugasDetailScreen({super.key, required this.assignment});

  @override
  State<TugasDetailScreen> createState() => _TugasDetailScreenState();
}

class _TugasDetailScreenState extends State<TugasDetailScreen> {
  PlatformFile? _file;
  bool _sending = false;
  late TaskStatus _status = widget.assignment.status;

  Future<void> _pick() async {
    final res = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip'],
    );
    if (res != null && res.files.isNotEmpty) setState(() => _file = res.files.first);
  }

  Future<void> _submit() async {
    if (_file == null) {
      showSnack(context, 'Pilih file jawaban terlebih dahulu');
      return;
    }
    setState(() => _sending = true);
    try {
      await AppConfig.repository.submitAssignment(widget.assignment.id, _file!.path ?? '');
      setState(() => _status = TaskStatus.dikumpulkan);
      if (mounted) showSnack(context, 'Tugas berhasil dikirim');
    } catch (e) {
      if (mounted) showSnack(context, '$e');
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  String _size(int bytes) => bytes >= 1024 * 1024
      ? '${(bytes / 1024 / 1024).toStringAsFixed(1)} MB'
      : '${(bytes / 1024).toStringAsFixed(0)} KB';

  @override
  Widget build(BuildContext context) {
    final t = widget.assignment;
    final canSubmit = _status == TaskStatus.belum;
    return Scaffold(
      appBar: AppBar(title: const Text('Tugas')),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          Text(t.title, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
          const SizedBox(height: 6),
          Row(
            children: [
              const Icon(Icons.event_rounded, size: 16, color: AppColors.success),
              const SizedBox(width: 6),
              Text('Batas: ${dateTime(t.deadline)}',
                  style: const TextStyle(color: AppColors.textMuted, fontSize: 13)),
            ],
          ),
          const SizedBox(height: 16),
          Text(t.instruction, style: const TextStyle(height: 1.5)),
          const SizedBox(height: 20),
          const Text('Lampiran', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
          const SizedBox(height: 8),
          if (t.attachmentName.isNotEmpty)
            FileTile(
              name: t.attachmentName,
              size: t.attachmentSize,
              onTap: () => openLink(context, t.attachmentUrl),
            )
          else
            const Text('Tidak ada lampiran', style: TextStyle(color: AppColors.textMuted)),
          const SizedBox(height: 20),
          if (canSubmit) ...[
            const Text('Jawaban Anda',
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: AppColors.primaryLight,
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Row(
                children: [
                  Icon(Icons.info_rounded, color: AppColors.primary, size: 20),
                  SizedBox(width: 8),
                  Expanded(
                    child: Text('Unggah jawaban tugas dalam format PDF, JPG atau DOC',
                        style: TextStyle(fontSize: 12.5)),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 10),
            InkWell(
              onTap: _pick,
              borderRadius: BorderRadius.circular(16),
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 28),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppColors.primary.withValues(alpha: 0.4), width: 1.5),
                ),
                child: const Column(
                  children: [
                    Icon(Icons.cloud_upload_outlined, color: AppColors.primary, size: 40),
                    SizedBox(height: 6),
                    Text('Pilih File', style: TextStyle(fontWeight: FontWeight.w700)),
                    Text('ketuk untuk memilih dari perangkat',
                        style: TextStyle(color: AppColors.textMuted, fontSize: 12)),
                  ],
                ),
              ),
            ),
            if (_file != null) ...[
              const SizedBox(height: 10),
              FileTile(
                name: _file!.name,
                size: _size(_file!.size),
                trailing: IconButton(
                  icon: const Icon(Icons.close_rounded),
                  onPressed: () => setState(() => _file = null),
                ),
              ),
            ],
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: _sending ? null : _submit,
              child: _sending
                  ? const SizedBox(
                      width: 22,
                      height: 22,
                      child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white))
                  : const Text('Kumpulkan Tugas'),
            ),
          ],
          const SizedBox(height: 20),
          Row(
            children: [
              const Text('Status', style: TextStyle(fontWeight: FontWeight.w700)),
              const Spacer(),
              _statusChip(_status),
            ],
          ),
          if (t.score != null) ...[
            const SizedBox(height: 16),
            AppCard(
              child: Row(
                children: [
                  Text('${t.score}',
                      style: const TextStyle(
                          fontSize: 32, fontWeight: FontWeight.w800, color: AppColors.primary)),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Text(
                        t.feedback?.isNotEmpty == true ? t.feedback! : 'Nilai dari tutor',
                        style: const TextStyle(height: 1.4)),
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }
}
