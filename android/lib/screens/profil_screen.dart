import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../core/theme.dart';
import '../data/repository.dart';
import '../models/models.dart';
import '../widgets/common.dart';
import 'login_screen.dart';
import 'nilai_screen.dart';

class ProfilScreen extends StatelessWidget {
  const ProfilScreen({super.key});

  Future<void> _logout(BuildContext context) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Keluar'),
        content: const Text('Yakin ingin keluar dari aplikasi?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Batal')),
          TextButton(onPressed: () => Navigator.pop(c, true), child: const Text('Keluar')),
        ],
      ),
    );
    if (ok != true || !context.mounted) return;
    await AppConfig.repository.logout();
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('logged_in');
    if (!context.mounted) return;
    Navigator.of(context).pushAndRemoveUntil(
      MaterialPageRoute(builder: (_) => const LoginScreen()),
      (_) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Profil'), automaticallyImplyLeading: false),
      body: Loader<Student>(
        future: AppConfig.repository.currentStudent(),
        builder: (s) {
          void push(Widget page) =>
              Navigator.of(context).push(MaterialPageRoute(builder: (_) => page));
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              AppCard(
                child: Row(
                  children: [
                    const CircleAvatar(
                      radius: 32,
                      backgroundColor: AppColors.primaryLight,
                      child: Icon(Icons.person_rounded, size: 40, color: AppColors.primary),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(s.name,
                              style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
                          const SizedBox(height: 2),
                          Text('NISN: ${s.nisn}',
                              style: const TextStyle(color: AppColors.textMuted, fontSize: 13)),
                          Text('${s.paket} - ${s.kelas}',
                              style: const TextStyle(color: AppColors.textMuted, fontSize: 13)),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              AppCard(
                padding: const EdgeInsets.symmetric(vertical: 4),
                child: Column(
                  children: [
                    _item(Icons.person_outline_rounded, 'Data Pribadi',
                        () => push(DataPribadiScreen(student: s))),
                    _item(Icons.history_edu_rounded, 'Riwayat Belajar',
                        () => push(const NilaiScreen())),
                    _item(Icons.lock_outline_rounded, 'Ubah Password',
                        () => push(const UbahPasswordScreen())),
                    _item(Icons.notifications_none_rounded, 'Pengaturan Notifikasi',
                        () => push(const PengaturanNotifikasiScreen())),
                    _item(Icons.help_outline_rounded, 'Bantuan',
                        () => showSnack(context, 'Hubungi admin PKBM Widya Utama')),
                    _item(Icons.info_outline_rounded, 'Tentang Aplikasi', () {
                      showAboutDialog(
                        context: context,
                        applicationName: 'PKBM Widya Utama',
                        applicationVersion: '1.0.0',
                        applicationIcon: const PkbmLogo(size: 48),
                        children: const [
                          Text('Aplikasi pembelajaran online untuk warga belajar PKBM Widya Utama.')
                        ],
                      );
                    }),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              OutlinedButton.icon(
                style: OutlinedButton.styleFrom(
                  foregroundColor: AppColors.danger,
                  side: const BorderSide(color: AppColors.danger, width: 1.4),
                ),
                onPressed: () => _logout(context),
                icon: const Icon(Icons.logout_rounded),
                label: const Text('Keluar'),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _item(IconData icon, String label, VoidCallback onTap) {
    return ListTile(
      leading: Icon(icon, color: AppColors.text),
      title: Text(label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
      trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.textMuted),
      onTap: onTap,
    );
  }
}

class DataPribadiScreen extends StatelessWidget {
  final Student student;

  const DataPribadiScreen({super.key, required this.student});

  @override
  Widget build(BuildContext context) {
    final rows = [
      ('Nama Lengkap', student.name),
      ('NISN', student.nisn),
      ('Program', '${student.paket} - ${student.kelas}'),
      ('Email', student.email),
      ('No. HP', student.phone),
      ('Alamat', student.address),
    ];
    return Scaffold(
      appBar: AppBar(title: const Text('Data Pribadi')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          AppCard(
            child: Column(
              children: [
                for (final r in rows)
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 10),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        SizedBox(
                          width: 110,
                          child: Text(r.$1,
                              style: const TextStyle(color: AppColors.textMuted, fontSize: 13)),
                        ),
                        Expanded(
                          child: Text(r.$2,
                              style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                        ),
                      ],
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

class UbahPasswordScreen extends StatelessWidget {
  const UbahPasswordScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Ubah Password')),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          for (final h in ['Password lama', 'Password baru', 'Ulangi password baru']) ...[
            TextField(
              obscureText: true,
              decoration: InputDecoration(
                  hintText: h, prefixIcon: const Icon(Icons.lock_outline_rounded)),
            ),
            const SizedBox(height: 14),
          ],
          const SizedBox(height: 10),
          ElevatedButton(
            onPressed: () {
              showSnack(context, 'Password berhasil diubah');
              Navigator.of(context).pop();
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }
}

class PengaturanNotifikasiScreen extends StatefulWidget {
  const PengaturanNotifikasiScreen({super.key});

  @override
  State<PengaturanNotifikasiScreen> createState() => _PengaturanNotifikasiScreenState();
}

class _PengaturanNotifikasiScreenState extends State<PengaturanNotifikasiScreen> {
  final _values = {'Tugas baru': true, 'Nilai keluar': true, 'Pengumuman': true, 'Pengingat jadwal': false};

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pengaturan Notifikasi')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          AppCard(
            padding: const EdgeInsets.symmetric(vertical: 4),
            child: Column(
              children: [
                for (final k in _values.keys)
                  SwitchListTile(
                    title: Text(k, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                    value: _values[k]!,
                    onChanged: (v) => setState(() => _values[k] = v),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
