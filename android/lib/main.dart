import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import 'core/theme.dart';
import 'screens/splash_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);
  runApp(const PkbmApp());
}

class PkbmApp extends StatelessWidget {
  const PkbmApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'PKBM Widya Utama',
      debugShowCheckedModeBanner: false,
      theme: buildTheme(),
      home: const SplashScreen(),
    );
  }
}
