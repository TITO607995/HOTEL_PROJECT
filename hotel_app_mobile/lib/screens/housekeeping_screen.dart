import 'package:flutter/material.dart';
import '../services/maintenance_service.dart'; // Kita pinjam service maintenance karena fungsinya mirip

class HousekeepingScreen extends StatefulWidget {
  const HousekeepingScreen({super.key});

  @override
  State<HousekeepingScreen> createState() => _HousekeepingScreenState();
}

class _HousekeepingScreenState extends State<HousekeepingScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  
  bool isLoading = true;
  List<dynamic> dirtyRooms = [];

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final data = await MaintenanceService.fetchMaintenanceData();
    if (mounted && data != null) {
      setState(() {
        // FILTER: CUMA TAMPILIN YANG VACANT DIRTY
        dirtyRooms = (data['rooms'] as List).where((r) => r['status'].toString().toLowerCase() == 'vacant dirty').toList();
        isLoading = false;
      });
    } else {
      setState(() => isLoading = false);
    }
  }

  Future<void> _markAsClean(int id, String roomNumber) async {
    setState(() => isLoading = true);
    // Update status ke 'available'
    bool success = await MaintenanceService.updateRoomStatus(id, 'available', 'Selesai dibersihkan oleh Housekeeping');
    
    if (!mounted) return;
    
    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Kamar $roomNumber sekarang Available! 🎉'), backgroundColor: Colors.green));
      _loadData();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal memperbarui status!'), backgroundColor: Colors.red));
      setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFBFC),
      appBar: AppBar(
        title: const Text('Tugas Kebersihan', style: TextStyle(color: Color(0xFF1B212D), fontWeight: FontWeight.w900, fontSize: 18)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : dirtyRooms.isEmpty
              ? const Center(child: Text('Hore! Tidak ada kamar kotor hari ini. 🎉', style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold)))
              : RefreshIndicator(
                  color: primaryMaroon,
                  onRefresh: _loadData,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(20),
                    physics: const AlwaysScrollableScrollPhysics(),
                    itemCount: dirtyRooms.length,
                    itemBuilder: (context, index) {
                      final room = dirtyRooms[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 15),
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.orange.shade200)),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('KAMAR ${room['room_number']}', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
                                const SizedBox(height: 5),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(color: Colors.orange.shade100, borderRadius: BorderRadius.circular(5)),
                                  child: const Text('VACANT DIRTY', style: TextStyle(color: Colors.orange, fontSize: 10, fontWeight: FontWeight.bold)),
                                )
                              ],
                            ),
                            ElevatedButton.icon(
                              style: ElevatedButton.styleFrom(backgroundColor: Colors.green, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
                              icon: const Icon(Icons.cleaning_services, color: Colors.white, size: 16),
                              label: const Text('TANDAI BERSIH', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                              onPressed: () => _markAsClean(room['id'], room['room_number'].toString()),
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