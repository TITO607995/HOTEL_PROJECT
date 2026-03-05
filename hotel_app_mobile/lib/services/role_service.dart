import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'auth_service.dart';

class RoleService {
  static Future<Map<String, dynamic>?> fetchRolesAndMenus() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.get(Uri.parse('${AuthService.baseUrl}/roles-management'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});
      if (response.statusCode == 200) return jsonDecode(response.body)['data'];
      return null;
    } catch (e) { print('Error Fetch Roles: $e'); return null; }
  }

  static Future<bool> createRole(String name, List<int> menuIds) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/roles-management'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': 'Bearer $token'},
        body: jsonEncode({'name': name, 'menu_ids': menuIds}),
      );
      return response.statusCode == 200;
    } catch (e) { return false; }
  }

  static Future<bool> updateRole(int id, String name, List<int> menuIds) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/roles-management/$id'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': 'Bearer $token'},
        body: jsonEncode({'name': name, 'menu_ids': menuIds}),
      );
      return response.statusCode == 200;
    } catch (e) { return false; }
  }

  static Future<bool> deleteRole(int id) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.delete(Uri.parse('${AuthService.baseUrl}/roles-management/$id'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});
      return response.statusCode == 200;
    } catch (e) { return false; }
  }
}