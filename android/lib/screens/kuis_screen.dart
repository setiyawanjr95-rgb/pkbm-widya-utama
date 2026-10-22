import 'package:flutter/material.dart';

import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';

class KuisScreen extends StatefulWidget {
  final LearningMaterial material;

  const KuisScreen({super.key, required this.material});

  @override
  State<KuisScreen> createState() => _KuisScreenState();
}

class _KuisScreenState extends State<KuisScreen> {
  late final Future<List<QuizQuestion>> _future =
      AppConfig.repository.quiz(widget.material.id);
  List<QuizQuestion> _questions = const [];
  int _current = 0;
  final _answers = <int, int>{};
  bool _finished = false;
  bool _sending = false;
  QuizResult? _result;

  Future<void> _finish() async {
    setState(() => _sending = true);
    try {
      final answers = {
        for (final e in _answers.entries) _questions[e.key].id: e.value,
      };
      final result = await AppConfig.repository.submitQuiz(widget.material.id, answers);
      setState(() {
        _result = result;
        _finished = true;
      });
    } catch (e) {
      if (mounted) showSnack(context, '$e');
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.material.title)),
      body: Loader<List<QuizQuestion>>(
        future: _future,
        builder: (questions) {
          _questions = questions;
          if (questions.isEmpty) {
            return const Center(child: Text('Soal kuis belum tersedia'));
          }
          return _finished ? _resultView() : _question();
        },
      ),
    );
  }

  Widget _resultView() {
    final score = _result?.score ?? 0;
    return Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(score >= 70 ? Icons.emoji_events_rounded : Icons.refresh_rounded,
              size: 80, color: score >= 70 ? AppColors.gold : AppColors.primary),
          const SizedBox(height: 16),
          const Text('Nilai Kamu', style: TextStyle(color: AppColors.textMuted)),
          Text('$score',
              style: const TextStyle(
                  fontSize: 56, fontWeight: FontWeight.w800, color: AppColors.primary)),
          const SizedBox(height: 8),
          Text('Benar ${_result?.correct ?? 0} dari ${_result?.total ?? 0} soal',
              style: const TextStyle(color: AppColors.textMuted)),
          const SizedBox(height: 4),
          Text(score >= 70 ? 'Mantap, pertahankan!' : 'Ayo coba lagi, kamu pasti bisa!'),
          const SizedBox(height: 32),
          ElevatedButton(
            onPressed: () => setState(() {
              _answers.clear();
              _current = 0;
              _finished = false;
              _result = null;
            }),
            child: const Text('Ulangi Kuis'),
          ),
          const SizedBox(height: 10),
          OutlinedButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Kembali'),
          ),
        ],
      ),
    );
  }

  Widget _question() {
    final q = _questions[_current];
    final selected = _answers[_current];
    final last = _current == _questions.length - 1;
    return ListView(
      padding: const EdgeInsets.all(20),
      children: [
        Text('Soal ${_current + 1} dari ${_questions.length}',
            style: const TextStyle(color: AppColors.textMuted)),
        const SizedBox(height: 8),
        ProgressBar(value: (_current + 1) / _questions.length),
        const SizedBox(height: 20),
        Text(q.text, style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w700)),
        const SizedBox(height: 16),
        for (var i = 0; i < q.options.length; i++) ...[
          AppCard(
            padding: const EdgeInsets.all(14),
            color: selected == i ? AppColors.primaryLight : null,
            onTap: () => setState(() => _answers[_current] = i),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 14,
                  backgroundColor: selected == i ? AppColors.primary : AppColors.background,
                  child: Text(String.fromCharCode(65 + i),
                      style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: selected == i ? Colors.white : AppColors.text)),
                ),
                const SizedBox(width: 12),
                Text(q.options[i], style: const TextStyle(fontSize: 15)),
              ],
            ),
          ),
          const SizedBox(height: 10),
        ],
        const SizedBox(height: 16),
        Row(
          children: [
            if (_current > 0)
              Expanded(
                child: OutlinedButton(
                  onPressed: () => setState(() => _current--),
                  child: const Text('Sebelumnya'),
                ),
              ),
            if (_current > 0) const SizedBox(width: 12),
            Expanded(
              child: ElevatedButton(
                onPressed: selected == null || _sending
                    ? null
                    : () {
                        if (last) {
                          _finish();
                        } else {
                          setState(() => _current++);
                        }
                      },
                child: Text(_sending ? 'Mengirim...' : (last ? 'Selesai' : 'Berikutnya')),
              ),
            ),
          ],
        ),
      ],
    );
  }
}
