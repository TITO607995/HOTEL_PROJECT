import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'auth_service.dart';

class GuestService {
  static Future<List<dynamic>> fetchGuests() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.get(
        Uri.parse('${AuthService.baseUrl}/guests'),
        headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body)['data'];
      }
      return [];
    } catch (e) {
      print('Error Fetch Guests: $e');
      return [];
    }
  }

  static Future<bool> toggleIncognito(int id) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/guests/$id/toggle-incognito'),
        headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );

      return response.statusCode == 200;
    } catch (e) {
      print('Error Toggle Incognito: $e');
      return false;
    }
  }

  // --- TAMBAHIN DUA FUNGSI INI DI BAWAH TOGGLE INCOGNITO ---

  static Future<bool> updateGuest(int id, Map<String, dynamic> data) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.put( // Pakai PUT untuk update
        Uri.parse('${AuthService.baseUrl}/guests/$id'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': 'Bearer $token'},
        body: jsonEncode(data)
      );
      return response.statusCode == 200;
    } catch (e) { 
      print('Error Update Guest: $e');
      return false; 
    }
  }

  static Future<bool> deleteGuest(int id) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.delete( // Pakai DELETE
        Uri.parse('${AuthService.baseUrl}/guests/$id'),
        headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}
      );
      return response.statusCode == 200;
    } catch (e) { 
      print('Error Delete Guest: $e');
      return false; 
    }
  }
}