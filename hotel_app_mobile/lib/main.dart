import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'screens/main_screen.dart';
import 'screens/login_screen.dart';

// 1. Ubah main jadi async
void main() async {
  // 2. Wajib ditambahkan kalau mau pakai fungsi async sebelum runApp
  WidgetsFlutterBinding.ensureInitialized();

  // 3. Cek apakah ada token yang tersimpan di memori HP
  SharedPreferences prefs = await SharedPreferences.getInstance();
  String? token = prefs.getString('auth_token');

  // 4. Jalankan aplikasi dan kirim status login-nya
  runApp(MyApp(isLoggedIn: token != null));
}

class MyApp extends StatelessWidget {
  final bool isLoggedIn;

  // 5. Tangkap status login dari main()
  const MyApp({super.key, required this.isLoggedIn});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Hotel SIG',
      debugShowCheckedModeBanner: false, // Ngilangin pita debug di pojok kanan atas
      theme: ThemeData(
        primaryColor: const Color(0xFF800000),
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF800000)),
        useMaterial3: true,
      ),
      
      // 6. Kalau isLoggedIn true, arahin ke MainScreen. Kalau false, ke LoginScreen.
      home: isLoggedIn ? const MainScreen() : const LoginScreen(),
    );
  }
}