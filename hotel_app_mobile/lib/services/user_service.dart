import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'auth_service.dart';

class UserService {
  // Ambil Data User
  static Future<List<dynamic>> fetchUsers() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.get(Uri.parse('${AuthService.baseUrl}/users'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});
      if (response.statusCode == 200) return jsonDecode(response.body)['data'];
      return [];
    } catch (e) { print('Error Fetch Users: $e'); return []; }
  }
  // Hapus User
  static Future<bool> deleteUser(int id) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.delete(Uri.parse('${AuthService.baseUrl}/users/$id'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});
      return response.statusCode == 200;
    } catch (e) { print('Error Delete User: $e'); return false; }
  }
  

  // Ambil Data Role Dinamis (ID dan Nama)
  static Future<List<dynamic>> fetchRoles() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.get(Uri.parse('${AuthService.baseUrl}/roles'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});
      if (response.statusCode == 200) return jsonDecode(response.body)['data'];
      return [];
    } catch (e) { print('Error Fetch Roles: $e'); return []; }
  }

  // Tambah User (Kirim role_id)
  static Future<bool> createUser(Map<String, dynamic> data) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/users'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': 'Bearer $token'},
        body: jsonEncode(data),
      );
      return response.statusCode == 200;
    } catch (e) { print('Error Create User: $e'); return false; }
  }

  // Assign Role (Kirim role_id)
  static Future<bool> updateRole(int id, int roleId) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');
      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/users/$id/role'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': 'Bearer $token'},
        body: jsonEncode({'role_id': roleId}), // Pakai role_id
      );
      return response.statusCode == 200;
    } catch (e) { print('Error Update Role: $e'); return false; }
  }
}