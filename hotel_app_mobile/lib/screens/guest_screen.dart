import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/guest_service.dart';

class GuestScreen extends StatefulWidget {
  const GuestScreen({super.key});

  @override
  State<GuestScreen> createState() => _GuestScreenState();
}

class _GuestScreenState extends State<GuestScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFFAFBFC);
  
  List<dynamic> guests = [];
  bool isLoading = true;

  // VARIABEL PENENTU HAK AKSES
  bool canEditGuest = false; 

  @override
  void initState() {
    super.initState();
    _checkPermissionsAndLoad();
  }

  Future<void> _checkPermissionsAndLoad() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    bool isSuper = prefs.getBool('is_superadmin') ?? false;
    List<String> allowedMenus = prefs.getStringList('allowed_menus') ?? [];
    
    setState(() {
      // JIKA DIA PUNYA HAK 'EDIT', MAKA SWITCH INCOGNITO AKTIF!
      canEditGuest = isSuper || allowedMenus.contains('DATA TAMU - EDIT');
    });

    _loadData();
  }
  Future<void> _loadData() async {
    setState(() => isLoading = true);
    final data = await GuestService.fetchGuests();
    if (mounted) {
      setState(() { 
        guests = data; // Antisipasi kalau data null dari API
        isLoading = false; 
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgGrey,
      appBar: AppBar(
        title: const Text('Buku Tamu Hotel', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: Color(0xFF1B212D))),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : guests.isEmpty 
              ? const Center(child: Text('Belum ada data tamu', style: TextStyle(color: Colors.grey)))
              : RefreshIndicator(
                  color: primaryMaroon,
                  onRefresh: _loadData,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(20),
                    physics: const AlwaysScrollableScrollPhysics(),
                    itemCount: guests.length,
                    itemBuilder: (context, index) {
                      final guest = guests[index];
                      
                      // ==========================================
                      // PROTEKSI NULL SAFETY 🔥
                      // ==========================================
                      String safeName = guest['name']?.toString() ?? 'Tamu Tanpa Nama';
                      String safeEmail = guest['email']?.toString() ?? '-';
                      bool isIncognito = guest['is_incognito'] == 1 || guest['is_incognito'] == true;

                      return Container(
                        margin: const EdgeInsets.only(bottom: 15),
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
                        child: Row(
                          children: [
                            CircleAvatar(radius: 25, backgroundColor: primaryMaroon.withOpacity(0.1), child: Icon(Icons.person, color: primaryMaroon)),
                            const SizedBox(width: 15),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Pakai variabel yang udah aman dari Null
                                  Text(safeName, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                                  const SizedBox(height: 5),
                                  Text(safeEmail, style: const TextStyle(color: Colors.grey, fontSize: 12)),
                                ],
                              ),
                            ),
                            
                            if (canEditGuest)
                              Column(
                                children: [
                                  const Text('Incognito', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                                  Switch(
                                    value: isIncognito,
                                    activeColor: primaryMaroon,
                                    onChanged: (val) async {
                                      setState(() => isLoading = true);
                                      await GuestService.toggleIncognito(guest['id']);
                                      _loadData();
                                    },
                                  )
                                ],
                              )
                            else
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                decoration: BoxDecoration(color: isIncognito ? primaryMaroon.withOpacity(0.1) : Colors.grey.shade100, borderRadius: BorderRadius.circular(5)),
                                child: Text(isIncognito ? 'VIP / SECRET' : 'REGULER', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: isIncognito ? primaryMaroon : Colors.grey)),
                              )
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}