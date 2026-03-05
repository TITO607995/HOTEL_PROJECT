import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'auth_service.dart';

class MaintenanceService {
  static Future<Map<String, dynamic>?> fetchMaintenanceData() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.get(
        Uri.parse('${AuthService.baseUrl}/maintenance'),
        headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body)['data'];
      }
      return null;
    } catch (e) {
      print('Error Fetch Maintenance: $e');
      return null;
    }
  }

  static Future<bool> updateRoomStatus(int id, String status, String notes) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/maintenance/$id'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token'
        },
        body: jsonEncode({
          'status': status,
          'notes': notes,
        }),
      );

      return response.statusCode == 200;
    } catch (e) {
      print('Error Update Maintenance: $e');
      return false;
    }
  }
}