import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class AuthService {
  // GANTI INI SESUAI ATURAN DI ATAS (10.0.2.2 buat Emulator, atau IP WiFi buat HP Asli)
  static const String baseUrl = 'http://10.108.239.240:8000/api';

  // Fungsi untuk nembak API Login Laravel
  static Future<bool> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {
          'Accept': 'application/json',
        },
        body: {
          'email': email,
          'password': password,
        },
      );
      

      if (response.statusCode == 200) {
        // Kalau sukses, kita ambil token-nya dari JSON Laravel
        final data = jsonDecode(response.body);
        final token = data['access_token'];

        // Simpan token ke brankas HP (Shared Preferences)
        SharedPreferences prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', token);

        print('🎉 Login Sukses! Token: $token');
        return true;
      } else {
        print('❌ Login Gagal: ${response.body}');
        return false;
      }
    } catch (e) {
      print('⚠️ Error Koneksi: $e');
      return false;
    }
  }

  // Fungsi untuk ngecek apakah user udah login (punya token)
  static Future<bool> isLoggedIn() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    return prefs.containsKey('auth_token');
  }
}