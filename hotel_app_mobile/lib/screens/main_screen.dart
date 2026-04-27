import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'dashboard_screen.dart'; 
import 'room_screen.dart';      
import 'order_screen.dart';     
import 'login_screen.dart';
import 'report_screen.dart';
import 'profile_screen.dart';
import 'user_screen.dart';
import 'role_screen.dart';
import 'housekeeping_screen.dart';
import 'guest_screen.dart';
import 'maintenance_screen.dart';
import 'history_screen.dart'; // 🔥 IMPORT HISTORY SCREEN DI SINI
import '../services/auth_service.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _currentIndex = 0;
  final Color primaryMaroon = const Color(0xFF800000);

  bool _isSuperAdmin = false;
  List<String> _allowedMenus = [];
  bool _isLoadingAccess = true; 

  final List<Widget> _pages = [
    const DashboardScreen(),
    const RoomScreen(),
    const GuestScreen(),
    const ProfileScreen(),
  ];

  @override
  void initState() {
    super.initState();
    _loadAccessFromBrankas(); 
  }

  Future<void> _loadAccessFromBrankas() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      
      setState(() {
        _isSuperAdmin = prefs.getBool('is_superadmin') ?? false;
        _allowedMenus = prefs.getStringList('allowed_menus') ?? [];
        _isLoadingAccess = false; 
      });

      print("--- MAIN SCREEN CEK AKSES ---");
      print("Superadmin: $_isSuperAdmin");
      print("Menu: $_allowedMenus");

    } catch (e) {
      print('Error baca brankas: $e');
      setState(() => _isLoadingAccess = false);
    }
  }

  bool _hasAccess(String keyword) {
    if (_isSuperAdmin) return true; 
    return _allowedMenus.any((menu) => menu.contains(keyword.toUpperCase()));
  }

  Future<void> _logout() async {
    await AuthService.logout(); 
    if (!mounted) return;
    Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
  }

  void _showAllMenus(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return Container(
          height: MediaQuery.of(context).size.height * 0.6, 
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30)),
          ),
          child: Column(
            children: [
              Container(margin: const EdgeInsets.only(top: 15, bottom: 20), width: 50, height: 5, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(10))),
              const Text('Semua Layanan Hotel', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
              const SizedBox(height: 20),
              
              Expanded(
                child: GridView.count(
                  crossAxisCount: 4, 
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  mainAxisSpacing: 20,
                  crossAxisSpacing: 10,
                  childAspectRatio: 0.8,
                  children: [
                    if (_hasAccess('RESERVASI')) ...[
                      _buildGridMenu(Icons.event_available, 'Check-In', Colors.green, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const OrderScreen(mode: 'checkin')));
                      }),
                      _buildGridMenu(Icons.exit_to_app, 'Check-Out', Colors.orange, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const OrderScreen(mode: 'checkout')));
                      }),
                      _buildGridMenu(Icons.more_time, 'Perpanjang', Colors.blueAccent, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const OrderScreen(mode: 'extend')));
                      }),
                      // 🔥 TOMBOL GUEST HISTORY BARU DITAMBAHKAN DI SINI
                      _buildGridMenu(Icons.history_edu, 'History Tamu', Colors.purple, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const HistoryScreen()));
                      }),
                    ],

                    if (_hasAccess('STATUS OO/OS')) ...[
                      _buildGridMenu(Icons.cleaning_services, 'Kebersihan', Colors.brown, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const HousekeepingScreen()));
                      }),
                      _buildGridMenu(Icons.build, 'Perbaikan', Colors.grey.shade700, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const MaintenanceScreen()));
                      }),
                    ],

                    if (_hasAccess('MANAJEMEN KARYAWAN'))
                      _buildGridMenu(Icons.manage_accounts, 'Users', Colors.indigo, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const UserScreen()));
                      }),

                    if (_hasAccess('ROLE & AKSES'))
                      _buildGridMenu(Icons.admin_panel_settings, 'Roles', Colors.deepOrange, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const RoleScreen()));
                      }),

                    if (_hasAccess('LAP. OPERASIONAL') || _hasAccess('LAP. KEUANGAN'))
                      _buildGridMenu(Icons.analytics, 'Laporan', Colors.teal, onTap: () {
                        Navigator.pop(context); Navigator.push(context, MaterialPageRoute(builder: (context) => const ReportScreen()));
                      }),

                    _buildGridMenu(Icons.logout, 'Keluar', Colors.red, onTap: _logout), 
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildGridMenu(IconData icon, String title, Color color, {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: color.withOpacity(0.1), shape: BoxShape.circle), child: Icon(icon, color: color, size: 28)),
          const SizedBox(height: 8),
          Text(title, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold), textAlign: TextAlign.center, maxLines: 1, overflow: TextOverflow.ellipsis),
        ],
      ),
    );
  }

  Widget _buildBottomTab({required IconData icon, required String label, required int index}) {
    bool isSelected = _currentIndex == index; 
    return GestureDetector(
      onTap: () => setState(() => _currentIndex = index),
      behavior: HitTestBehavior.opaque,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, color: isSelected ? primaryMaroon : Colors.grey.shade400, size: 24),
          const SizedBox(height: 4),
          Text(label, style: TextStyle(fontSize: 9, fontWeight: isSelected ? FontWeight.w900 : FontWeight.bold, color: isSelected ? primaryMaroon : Colors.grey.shade500)),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFBFC),
      
      body: _isLoadingAccess 
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : IndexedStack(index: _currentIndex, children: _pages),
          
      floatingActionButton: _isLoadingAccess ? null : FloatingActionButton(
        onPressed: () => _showAllMenus(context),
        backgroundColor: const Color(0xFF800000), 
        elevation: 8,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
        child: const Icon(Icons.menu, color: Colors.white, size: 28), 
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
      
      bottomNavigationBar: _isLoadingAccess ? const SizedBox() : BottomAppBar(
        color: Colors.white, shape: const CircularNotchedRectangle(), notchMargin: 8.0, elevation: 20,
        child: SizedBox(
          height: 60,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildBottomTab(icon: Icons.dashboard, label: 'DASHBOARD', index: 0), 
              _buildBottomTab(icon: Icons.bed, label: 'ROOMS', index: 1),          
              const SizedBox(width: 48), 
              _buildBottomTab(icon: Icons.people, label: 'TAMU', index: 2), 
              _buildBottomTab(icon: Icons.person, label: 'ACCOUNT', index: 3),
            ],
          ),
        ),
      ),
    );
  }
}