import 'package:flutter/material.dart';
import '../services/room_service.dart';

class RoomScreen extends StatefulWidget {
  const RoomScreen({super.key});

  @override
  State<RoomScreen> createState() => _RoomScreenState();
}

class _RoomScreenState extends State<RoomScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  
  List<dynamic> rooms = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadRooms();
  }

  Future<void> _loadRooms() async {
    final data = await RoomService.fetchRooms(); 
    if (mounted) {
      setState(() {
        rooms = data!;
        isLoading = false;
      });
    }
  }

  // Fungsi untuk nentuin warna badge status
  Color _getStatusColor(String status) {
    if (status == 'AVAILABLE') return Colors.green;
    if (status == 'BOOKED') return Colors.orange;
    if (status == 'OCCUPIED') return Colors.red;
    return Colors.grey; // Untuk kotor/maintenance
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade100,
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : rooms.isEmpty
              ? const Center(child: Text('Belum ada data kamar.', style: TextStyle(color: Colors.grey)))
              
              // ==========================================
              // INI DIA SIHIR PULL-TO-REFRESH NYA BRO!
              // ==========================================
              : RefreshIndicator(
                  color: primaryMaroon,
                  backgroundColor: Colors.white,
                  onRefresh: _loadRooms, // Akan manggil API lagi pas ditarik
                  
                  child: ListView.builder(
                    padding: const EdgeInsets.only(top: 20, left: 16, right: 16, bottom: 30),
                    // Wajib pakai AlwaysScrollable biar tetep bisa ditarik walau item dikit
                    physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()), 
                    itemCount: rooms.length,
                    itemBuilder: (context, index) {
                      final room = rooms[index];
                      String status = room['status_label'] ?? 'UNKNOWN';

                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          boxShadow: [
                            BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))
                          ],
                        ),
                        child: Row(
                          children: [
                            // FOTO KAMAR (Sisi Kiri)
                            ClipRRect(
                              borderRadius: const BorderRadius.only(
                                topLeft: Radius.circular(20),
                                bottomLeft: Radius.circular(20),
                              ),
                              child: Image.network(
                                room['foto_display'],
                                width: 120,
                                height: 120,
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) {
                                  // Kalau gambarnya gagal diload, tampilkan kotak abu-abu
                                  return Container(
                                    width: 120,
                                    height: 120,
                                    color: Colors.grey.shade300,
                                    child: const Icon(Icons.broken_image, color: Colors.grey),
                                  );
                                },
                              ),
                            ),
                            
                            // DETAIL KAMAR (Sisi Kanan)
                            Expanded(
                              child: Padding(
                                padding: const EdgeInsets.all(16.0),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    // Header: Tipe & Status
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(
                                          room['type'].toString().toUpperCase(),
                                          style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1),
                                        ),
                                        Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                          decoration: BoxDecoration(
                                            color: _getStatusColor(status).withOpacity(0.1),
                                            borderRadius: BorderRadius.circular(8),
                                          ),
                                          child: Text(
                                            status,
                                            style: TextStyle(
                                              fontSize: 8,
                                              fontWeight: FontWeight.w900,
                                              color: _getStatusColor(status),
                                            ),
                                          ),
                                        )
                                      ],
                                    ),
                                    const SizedBox(height: 8),
                                    
                                    // Nomor Kamar
                                    Text(
                                      'Kamar ${room['room_number']}',
                                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF1A1A1A)),
                                    ),
                                    const SizedBox(height: 8),
                                    
                                    // Harga
                                    Text(
                                      'Rp ${room['price']}',
                                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: primaryMaroon),
                                    ),
                                  ],
                                ),
                              ),
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