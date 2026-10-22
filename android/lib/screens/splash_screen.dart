import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../core/theme.dart';
import '../widgets/common.dart';
import 'home_shell.dart';
import 'login_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _next();
  }

  Future<void> _next() async {
    final prefs = await SharedPreferences.getInstance();
    await Future.delayed(const Duration(seconds: 2));
    if (!mounted) return;
    final loggedIn = prefs.getBool('logged_in') ?? false;
    Navigator.of(context).pushReplacement(MaterialPageRoute(
      builder: (_) => loggedIn ? const HomeShell() : const LoginScreen(),
    ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.primary,
      body: Stack(
        children: [
          Positioned.fill(child: CustomPaint(painter: _WavePainter())),
          const Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                PkbmLogo(size: 110, color: Colors.white),
                SizedBox(height: 20),
                Text('PKBM Widya Utama',
                    style: TextStyle(
                        color: Colors.white, fontSize: 26, fontWeight: FontWeight.w800)),
                SizedBox(height: 8),
                Text('Belajar Tanpa Batas,\nRaih Masa Depan',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.white70, fontSize: 15, height: 1.4)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _WavePainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final h = size.height;
    final w = size.width;
    final p1 = Paint()..color = Colors.white.withValues(alpha: 0.08);
    final p2 = Paint()..color = Colors.white.withValues(alpha: 0.12);
    final path1 = Path()
      ..moveTo(0, h * 0.82)
      ..quadraticBezierTo(w * 0.3, h * 0.76, w * 0.6, h * 0.83)
      ..quadraticBezierTo(w * 0.85, h * 0.89, w, h * 0.84)
      ..lineTo(w, h)
      ..lineTo(0, h)
      ..close();
    final path2 = Path()
      ..moveTo(0, h * 0.9)
      ..quadraticBezierTo(w * 0.4, h * 0.84, w * 0.7, h * 0.92)
      ..quadraticBezierTo(w * 0.9, h * 0.96, w, h * 0.93)
      ..lineTo(w, h)
      ..lineTo(0, h)
      ..close();
    canvas.drawPath(path1, p1);
    canvas.drawPath(path2, p2);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
