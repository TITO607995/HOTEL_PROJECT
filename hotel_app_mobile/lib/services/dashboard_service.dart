import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'auth_service.dart'; // Untuk pinjam baseUrl

class DashboardService {
  static Future<Map<String, dynamic>?> fetchDashboardData() async {
    try {
      // Ambil token dari memori HP
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      if (token == null) return null; // Kalau nggak ada token, batalkan

      final response = await http.get(
        Uri.parse('${AuthService.baseUrl}/dashboard'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token', // SELIPKAN TOKEN DI SINI
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['data']; // Kembalikan data mentahnya
      } else {
        print('❌ Gagal ambil data dashboard: ${response.statusCode}');
        return null;
      }
    } catch (e) {
      print('⚠️ Error Koneksi Dashboard: $e');
      return null;
    }
  }
}