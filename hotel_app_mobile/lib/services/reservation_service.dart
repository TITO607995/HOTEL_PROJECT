import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'auth_service.dart';

class ReservationService {
  // Ambil Data
  static Future<List<dynamic>> fetchReservations() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      if (token == null) return [];

      final response = await http.get(
        Uri.parse('${AuthService.baseUrl}/reservations'),
        headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body)['data'];
      }
      return [];
    } catch (e) {
      print('Error Fetch: $e');
      return [];
    }
  }


  static Future<bool> createReservation(Map<String, dynamic> data) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/reservations'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode(data),
      );

      return response.statusCode == 201 || response.statusCode == 200;
    } catch (e) {
      print('Error Create: $e');
      return false;
    }
  }

  static Future<bool> checkOut(int id, {int additionalCharges = 0, String notes = ''}) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/reservations/$id/checkout'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token'
        },
        body: jsonEncode({
          'additional_charges': additionalCharges,
          'notes': notes,
        }),
      );
      return response.statusCode == 200;
    } catch (e) {
      print('Error Check-Out: $e');
      return false;
    }
  }

  static Future<bool> checkIn(int id, {String? paymentMethod}) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      // Siapkan body, kalau paymentMethod ada isinya, kirim ke Laravel
      Map<String, dynamic> bodyData = {};
      if (paymentMethod != null) {
        bodyData['payment_method'] = paymentMethod;
      }

      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/reservations/$id/checkin'),
        headers: {
          'Accept': 'application/json', 
          'Content-Type': 'application/json', // Tambah ini karena mau kirim body JSON
          'Authorization': 'Bearer $token'
        },
        body: jsonEncode(bodyData),
      );
      
      return response.statusCode == 200;
    } catch (e) {
      print('Error Check-In: $e');
      return false;
    }
  }

  // Proses Perpanjang Menginap (Extend)
  static Future<bool> extendReservation(int id, String newDate) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/reservations/$id/extend'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token'
        },
        body: jsonEncode({'new_departure_date': newDate}),
      );
      return response.statusCode == 200;
    } catch (e) {
      print('Error Extend: $e');
      return false;
    }
  }

  // Proses Batal Reservasi
  static Future<bool> cancelReservation(int id) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse('${AuthService.baseUrl}/reservations/$id/cancel'),
        headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );
      return response.statusCode == 200;
    } catch (e) {
      print('Error Cancel: $e');
      return false;
    }
  }
}