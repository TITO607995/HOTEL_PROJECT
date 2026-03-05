import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart'; // Wajib import ini
import 'auth_service.dart';

class RoomService {
  static Future<List<dynamic>?> fetchRooms() async {
    try {
      // 1. BONGKAR LACI BUAT NGAMBIL TOKEN
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      // 2. KIRIM TOKENNYA KE LARAVEL BARENGAN SAMA REQUEST
      final response = await http.get(
        Uri.parse('${AuthService.baseUrl}/rooms'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token', // INI DIA OBAT ANTI 401! 🔥
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body)['data'];
      } else {
        print('❌ Gagal ambil data kamar: ${response.statusCode}');
        return null;
      }
    } catch (e) {
      print('Error Fetch Rooms: $e');
      return null;
    }
  }

  // ... (Kalau ada fungsi lain kayak updateRoomStatus, jangan lupa tambahin headers 'Authorization': 'Bearer $token' juga ya!)
}