import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'auth_service.dart';

class ReportService {
  static Future<Map<String, dynamic>?> fetchOperasional() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.get(Uri.parse('${AuthService.baseUrl}/reports/operasional'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});
      if (response.statusCode == 200) return jsonDecode(response.body)['data'];
      return null;
    } catch (e) { print('Error Fetch Operasional: $e'); return null; }
  }

  static Future<Map<String, dynamic>?> fetchKeuangan() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.get(Uri.parse('${AuthService.baseUrl}/reports/keuangan'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});
      if (response.statusCode == 200) return jsonDecode(response.body)['data'];
      return null;
    } catch (e) { print('Error Fetch Keuangan: $e'); return null; }
  }
}