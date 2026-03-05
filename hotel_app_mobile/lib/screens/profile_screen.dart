import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;

import '../services/auth_service.dart'; // Buat ngambil baseUrl API lu
import 'login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFFAFBFC);

  Map<String, dynamic>? userData;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchUserData(); // Ambil data user pas halaman dibuka
  }

  // Fungsi narik data user berdasarkan token login
  Future<void> _fetchUserData() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      if (token == null) return;

      final response = await http.get(
        Uri.parse('${AuthService.baseUrl}/user'), 
        headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200 && mounted) {
        setState(() {
          userData = jsonDecode(response.body);
          isLoading = false;
        });
      } else {
        setState(() => isLoading = false);
      }
    } catch (e) {
      print('Error fetch user: $e');
      if (mounted) setState(() => isLoading = false);
    }
  }

  Future<void> _logout(BuildContext context) async {
    showDialog(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: const Text('Ganti Akun', style: TextStyle(fontWeight: FontWeight.bold)),
        content: const Text('Apakah Anda yakin ingin keluar dan berganti akun?'),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext), 
            child: const Text('Batal', style: TextStyle(color: Colors.grey))
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))
            ),
            onPressed: () async {
              SharedPreferences prefs = await SharedPreferences.getInstance();
              await prefs.remove('auth_token');
              
              if (!context.mounted) return;
              
              Navigator.pushAndRemoveUntil(
                context, 
                MaterialPageRoute(builder: (context) => const LoginScreen()),
                (route) => false 
              );
            },
            child: const Text('Ya, Keluar', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgGrey,
      appBar: AppBar(
        backgroundColor: bgGrey,
        elevation: 0,
        title: const Text('Akun Saya', style: TextStyle(color: Color(0xFF1B212D), fontWeight: FontWeight.w900, fontSize: 18)),
        centerTitle: false,
      ),
      body: isLoading 
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              physics: const BouncingScrollPhysics(),
              child: Column(
                children: [
                  // KARTU PROFIL DINAMIS
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 5))],
                    ),
                    child: Row(
                      children: [
                        CircleAvatar(
                          radius: 35,
                          backgroundColor: primaryMaroon.withOpacity(0.1),
                          child: Icon(Icons.person, size: 40, color: primaryMaroon),
                        ),
                        const SizedBox(width: 20),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // DATA NAMA DARI DATABASE
                              Text(
                                userData?['name'] ?? 'Guest User', 
                                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF1B212D))
                              ),
                              const SizedBox(height: 5),
                              // DATA EMAIL DARI DATABASE
                              Text(
                                userData?['email'] ?? 'Tidak ada email', 
                                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey)
                              ),
                              const SizedBox(height: 10),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: const BoxDecoration(color: Colors.green, borderRadius: BorderRadius.all(Radius.circular(10))),
                                child: const Text('AKTIF', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 1)),
                              )
                            ],
                          ),
                        )
                      ],
                    ),
                  ),
                  const SizedBox(height: 30),

                  // TOMBOL GANTI AKUN / LOGOUT
                  SizedBox(
                    width: double.infinity,
                    height: 55,
                    child: OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        side: const BorderSide(color: Colors.red, width: 2),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))
                      ),
                      icon: const Icon(Icons.swap_horiz, color: Colors.red),
                      label: const Text('GANTI AKUN / LOGOUT', style: TextStyle(color: Colors.red, fontSize: 14, fontWeight: FontWeight.w900, letterSpacing: 1)),
                      onPressed: () => _logout(context),
                    ),
                  )
                ],
              ),
            ),
    );
  }
}