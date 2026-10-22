import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import '../models/models.dart';
import 'mock_data.dart';

/// Kontrak data aplikasi. [ApiRepository] dipakai untuk server Laravel,
/// [MockRepository] untuk uji tampilan tanpa server (ubah [AppConfig.useMock]).
abstract class Repository {
  Future<Student> login(String username, String password);
  Future<void> logout();
  Future<Student> currentStudent();
  Future<List<Course>> courses();
  Future<List<LearningMaterial>> materials(int courseId);
  Future<void> completeMaterial(int materialId);
  Future<List<QuizQuestion>> quiz(int materialId);
  Future<QuizResult> submitQuiz(int materialId, Map<int, int> answers);
  Future<List<Assignment>> assignments();
  Future<List<Grade>> grades();
  Future<List<Schedule>> schedules();
  Future<List<Announcement>> announcements();
  Future<List<AppNotification>> notifications();
  Future<void> markNotificationsRead();
  Future<void> submitAssignment(int assignmentId, String filePath);
}

class AppConfig {
  /// true = pakai data dummy, false = pakai REST API Laravel.
  static const useMock = false;
  static const baseUrl = 'https://pkbm.netwarga.my.id/api';

  static final Repository repository =
      useMock ? MockRepository() : ApiRepository(baseUrl);
}

class MockRepository implements Repository {
  Future<T> _delay<T>(T value) =>
      Future.delayed(const Duration(milliseconds: 300), () => value);

  @override
  Future<Student> login(String username, String password) {
    if (username.trim().isEmpty || password.isEmpty) {
      return Future.error('Username/NISN dan password wajib diisi');
    }
    return _delay(MockData.student);
  }

  @override
  Future<void> logout() => _delay(null);
  @override
  Future<Student> currentStudent() => _delay(MockData.student);
  @override
  Future<List<Course>> courses() => _delay(MockData.courses);
  @override
  Future<List<LearningMaterial>> materials(int courseId) =>
      _delay(MockData.materials.where((m) => m.courseId == courseId).toList());
  @override
  Future<void> completeMaterial(int materialId) => _delay(null);
  @override
  Future<List<QuizQuestion>> quiz(int materialId) => _delay(MockData.quiz);
  @override
  Future<QuizResult> submitQuiz(int materialId, Map<int, int> answers) {
    final correct =
        MockData.quiz.where((q) => answers[q.id] == MockData.quizAnswers[q.id]).length;
    return _delay(QuizResult(
        score: (correct / MockData.quiz.length * 100).round(),
        correct: correct,
        total: MockData.quiz.length));
  }

  @override
  Future<List<Assignment>> assignments() => _delay(MockData.assignments);
  @override
  Future<List<Grade>> grades() => _delay(MockData.grades);
  @override
  Future<List<Schedule>> schedules() => _delay(MockData.schedules);
  @override
  Future<List<Announcement>> announcements() => _delay(MockData.announcements);
  @override
  Future<List<AppNotification>> notifications() => _delay(MockData.notifications);
  @override
  Future<void> markNotificationsRead() => _delay(null);
  @override
  Future<void> submitAssignment(int assignmentId, String filePath) =>
      Future.delayed(const Duration(seconds: 1));
}

/// Client REST untuk backend Laravel (Sanctum token, response dibungkus "data").
class ApiRepository implements Repository {
  final String baseUrl;
  String? _token;

  ApiRepository(this.baseUrl);

  static const _timeout = Duration(seconds: 30);

  Future<Map<String, String>> _headers() async {
    _token ??= (await SharedPreferences.getInstance()).getString('token');
    return {
      'Accept': 'application/json',
      if (_token != null) 'Authorization': 'Bearer $_token',
    };
  }

  /// Ambil pesan error dari Laravel (validasi 422 / abort) supaya tampil rapi di app.
  Future<Never> _fail(http.Response res, String fallback) async {
    if (res.statusCode == 401) {
      _token = null;
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('token');
      await prefs.remove('logged_in');
      throw 'Sesi habis, silakan keluar lalu login ulang';
    }
    try {
      final message = jsonDecode(res.body)['message'];
      if (message is String && message.isNotEmpty) throw message;
    } on FormatException {
      // body bukan JSON
    }
    throw '$fallback (${res.statusCode})';
  }

  Future<dynamic> _send(Future<http.Response> Function(Map<String, String>) request,
      String fallback) async {
    final http.Response res;
    try {
      res = await request(await _headers()).timeout(_timeout);
    } on Exception {
      throw 'Tidak bisa terhubung ke server, periksa koneksi internet';
    }
    if (res.statusCode >= 400) await _fail(res, fallback);
    return jsonDecode(res.body);
  }

  Future<dynamic> _get(String path) async =>
      (await _send((h) => http.get(Uri.parse('$baseUrl$path'), headers: h),
          'Gagal memuat data'))['data'];

  Future<dynamic> _post(String path, [Map<String, String>? body]) async =>
      (await _send((h) => http.post(Uri.parse('$baseUrl$path'), headers: h, body: body),
          'Gagal mengirim data'))['data'];

  Future<List<T>> _list<T>(String path, T Function(Map<String, dynamic>) fromJson) async =>
      (await _get(path) as List).map((e) => fromJson(e as Map<String, dynamic>)).toList();

  @override
  Future<Student> login(String username, String password) async {
    if (username.trim().isEmpty || password.isEmpty) {
      throw 'Username/NISN dan password wajib diisi';
    }
    final body = await _send(
      (h) => http.post(Uri.parse('$baseUrl/login'),
          headers: h, body: {'username': username.trim(), 'password': password}),
      'Login gagal, periksa NISN dan password',
    );
    _token = body['token'];
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', _token!);
    return Student.fromJson(body['user']);
  }

  @override
  Future<void> logout() async {
    try {
      await _post('/logout');
    } catch (_) {
      // token mungkin sudah tidak berlaku, tetap hapus di perangkat
    }
    _token = null;
    await (await SharedPreferences.getInstance()).remove('token');
  }

  @override
  Future<Student> currentStudent() async => Student.fromJson(await _get('/me'));
  @override
  Future<List<Course>> courses() => _list('/courses', Course.fromJson);
  @override
  Future<List<LearningMaterial>> materials(int courseId) =>
      _list('/courses/$courseId/materials', LearningMaterial.fromJson);
  @override
  Future<void> completeMaterial(int materialId) => _post('/materials/$materialId/complete');

  @override
  Future<List<QuizQuestion>> quiz(int materialId) async {
    final data = await _get('/materials/$materialId/quiz');
    return (data['questions'] as List)
        .map((e) => QuizQuestion.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  @override
  Future<QuizResult> submitQuiz(int materialId, Map<int, int> answers) async {
    final data = await _post('/materials/$materialId/quiz', {
      for (final e in answers.entries) 'answers[${e.key}]': '${e.value}',
    });
    return QuizResult(score: data['score'], correct: data['correct'], total: data['total']);
  }

  @override
  Future<List<Assignment>> assignments() => _list('/assignments', Assignment.fromJson);
  @override
  Future<List<Grade>> grades() => _list('/grades', Grade.fromJson);
  @override
  Future<List<Schedule>> schedules() => _list('/schedules', Schedule.fromJson);
  @override
  Future<List<Announcement>> announcements() =>
      _list('/announcements', Announcement.fromJson);
  @override
  Future<List<AppNotification>> notifications() =>
      _list('/notifications', AppNotification.fromJson);
  @override
  Future<void> markNotificationsRead() => _post('/notifications/read');

  @override
  Future<void> submitAssignment(int assignmentId, String filePath) async {
    final req = http.MultipartRequest(
        'POST', Uri.parse('$baseUrl/assignments/$assignmentId/submit'))
      ..headers.addAll(await _headers())
      ..files.add(await http.MultipartFile.fromPath('file', filePath));
    final http.Response res;
    try {
      res = await http.Response.fromStream(await req.send().timeout(const Duration(minutes: 3)));
    } on Exception {
      throw 'Tidak bisa terhubung ke server, periksa koneksi internet';
    }
    if (res.statusCode >= 400) await _fail(res, 'Gagal mengirim tugas');
  }
}
