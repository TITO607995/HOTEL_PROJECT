import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class AuthService {
  // GANTI INI SESUAI ATURAN DI ATAS (10.0.2.2 buat Emulator, atau IP WiFi buat HP Asli)
  static const String baseUrl = 'http://192.168.1.10:8000/api';

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
        // Kalau sukses, kita ambil data dari JSON Laravel
        final data = jsonDecode(response.body);
        final token = data['access_token'];

        // Buka brankas HP (Shared Preferences)
        SharedPreferences prefs = await SharedPreferences.getInstance();
        
        // 1. Simpan Token
        await prefs.setString('auth_token', token);

        // ========================================================
        // 🔥 2. SIMPAN HAK AKSES (RBAC) BIKINAN KITA KE BRANKAS 🔥
        // ========================================================
        
        // Simpan status Superadmin (true/false)
        if (data['is_superadmin'] != null) {
          await prefs.setBool('is_superadmin', data['is_superadmin']);
        } else {
          await prefs.setBool('is_superadmin', false);
        }

        // Simpan daftar menu yang diizinkan (List<String>)
        if (data['allowed_menus'] != null) {
          List<String> menus = List<String>.from(data['allowed_menus']);
          await prefs.setStringList('allowed_menus', menus);
        } else {
          await prefs.setStringList('allowed_menus', []);
        }

        print('🎉 Login Sukses! Token: $token');
        print('👑 Status Superadmin: ${data['is_superadmin']}');
        print('📋 Menu Dibuka: ${data['allowed_menus']}');
        
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

  // 🔥 TAMBAHAN LOGOUT (Biar brankasnya bersih waktu ganti akun)
  static Future<void> logout() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('is_superadmin');
    await prefs.remove('allowed_menus');
  }
}