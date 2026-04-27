import 'package:flutter/material.dart';
import '../services/dashboard_service.dart';

class HomeScreen extends StatefulWidget {
  final VoidCallback? onGoToRooms;

  const HomeScreen({super.key, this.onGoToRooms});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFF8F9FA); // Warna background abu-abu super soft

  bool isLoading = true;
  Map<String, dynamic>? dashboardData;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final data = await DashboardService.fetchDashboardData();
    if (mounted) {
      setState(() {
        dashboardData = data;
        isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircularProgressIndicator(color: primaryMaroon),
            const SizedBox(height: 16),
            const Text('Memuat data live...', style: TextStyle(color: Colors.grey, fontSize: 12)),
          ],
        ),
      );
    }

    if (dashboardData == null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.wifi_off_rounded, size: 60, color: Colors.grey),
            const SizedBox(height: 16),
            const Text('Gagal terhubung ke server', style: TextStyle(fontWeight: FontWeight.bold)),
            TextButton(
              onPressed: () {
                setState(() => isLoading = true);
                _loadData();
              },
              child: Text('Coba Lagi', style: TextStyle(color: primaryMaroon)),
            )
          ],
        ),
      );
    }

    final stats = dashboardData!['stats'];
    final List<dynamic> liveRooms = dashboardData!['live_rooms'];

    return Container(
      color: bgGrey, // Background keseluruhan halaman
      child: SingleChildScrollView(
        physics: const BouncingScrollPhysics(), // Efek scroll mental ala iOS
        padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 25.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // --- HEADER GREETING ---
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'OVERVIEW PANEL', // <-- Langsung dibikin kapital aja teksnya
                      style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: primaryMaroon, letterSpacing: 1.5),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Dashboard Hotel',
                      style: TextStyle(fontSize: 26, fontWeight: FontWeight.w900, color: Color(0xFF1A1A1A), letterSpacing: -0.5),
                    ),
                  ],
                ),
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                    boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
                  ),
                  child: Icon(Icons.notifications_active_outlined, color: primaryMaroon, size: 22),
                )
              ],
            ),
            const SizedBox(height: 8),
            const Text(
              'Pantau status real-time ketersediaan kamar dan aktivitas tamu hari ini.',
              style: TextStyle(fontSize: 13, color: Colors.grey, height: 1.4),
            ),
            const SizedBox(height: 30),

            // --- TOP CARDS (Standard, Deluxe, Suite) ---
            GestureDetector(
              onTap: widget.onGoToRooms,
              child: SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                physics: const BouncingScrollPhysics(),
                clipBehavior: Clip.none, // Biar shadow gak kepotong
                child: Row(
                  children: [
                    _buildStatCard(stats['standard'].toString(), 'STANDARD', Icons.single_bed_outlined),
                    const SizedBox(width: 16),
                    _buildStatCard(stats['deluxe'].toString(), 'DELUXE', Icons.bed_outlined),
                    const SizedBox(width: 16),
                    _buildStatCard(stats['suite'].toString(), 'SUITE', Icons.king_bed_outlined, isPremium: true),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 35),

            // --- LIVE TABLE HEADER ---
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Kamar Terisi & Maintenance',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: Color(0xFF1A1A1A)),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(color: Colors.red.withOpacity(0.1), borderRadius: BorderRadius.circular(20)),
                  child: Row(
                    children: [
                      Container(width: 6, height: 6, decoration: const BoxDecoration(color: Colors.red, shape: BoxShape.circle)),
                      const SizedBox(width: 6),
                      const Text('LIVE', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.red, letterSpacing: 1)),
                    ],
                  ),
                )
              ],
            ),
            const SizedBox(height: 16),

            // --- MOBILE NATIVE LIST (Pengganti Tabel Kaku) ---
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 15, offset: const Offset(0, 8))],
              ),
              child: liveRooms.isEmpty
                  ? const Padding(
                      padding: EdgeInsets.all(40.0),
                      child: Center(
                        child: Column(
                          children: [
                            Icon(Icons.hotel_class_outlined, size: 40, color: Colors.black12),
                            SizedBox(height: 10),
                            Text('Belum ada tamu aktif saat ini.', style: TextStyle(color: Colors.grey, fontSize: 13)),
                          ],
                        ),
                      ),
                    )
                  : ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(), // Scroll ngikut halaman utama
                      itemCount: liveRooms.length,
                      separatorBuilder: (context, index) => const Divider(height: 1, color: Colors.black12, indent: 20, endIndent: 20),
                      itemBuilder: (context, index) {
                        final room = liveRooms[index];
                        bool isPaid = room['is_paid'];
                        String status = room['status'];
                        Color statusColor = status == 'In-house' ? Colors.blue.shade700 : Colors.orange.shade700;
                        Color statusBg = status == 'In-house' ? Colors.blue.shade50 : Colors.orange.shade50;

                        return Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Row(
                            children: [
                              // Kotak Nomor Kamar
                              Container(
                                width: 50,
                                height: 50,
                                decoration: BoxDecoration(color: bgGrey, borderRadius: BorderRadius.circular(14)),
                                child: Center(
                                  child: Text(
                                    room['room_number'].toString(),
                                    style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF1A1A1A)),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 16),
                              
                              // Info Tengah (Status & Payment)
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    // Badge Status
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                      decoration: BoxDecoration(color: statusBg, borderRadius: BorderRadius.circular(6)),
                                      child: Text(
                                        status.toUpperCase(),
                                        style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: statusColor, letterSpacing: 0.5),
                                      ),
                                    ),
                                    const SizedBox(height: 6),
                                    Row(
                                      children: [
                                        Icon(Icons.account_balance_wallet_outlined, size: 12, color: Colors.grey.shade500),
                                        const SizedBox(width: 4),
                                        Text(
                                          room['payment_method'],
                                          style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.black87),
                                        ),
                                        const SizedBox(width: 8),
                                        // Icon Bayar Centang / Silang
                                        Icon(
                                          isPaid ? Icons.check_circle : Icons.error,
                                          color: isPaid ? Colors.green : Colors.redAccent,
                                          size: 14,
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                              ),

                              // Tombol Action Kanan
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                                decoration: BoxDecoration(
                                  color: status == 'In-house' ? primaryMaroon : Colors.orange.shade800,
                                  borderRadius: BorderRadius.circular(10),
                                  boxShadow: [
                                    BoxShadow(
                                      color: (status == 'In-house' ? primaryMaroon : Colors.orange).withOpacity(0.3),
                                      blurRadius: 8,
                                      offset: const Offset(0, 3),
                                    )
                                  ],
                                ),
                                child: Text(
                                  room['action_label'].toString().toUpperCase(),
                                  style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w900, letterSpacing: 0.5),
                                ),
                              ),
                            ],
                          ),
                        );
                      },
                    ),
            ),
            const SizedBox(height: 30), // Spasi bawah biar lega
          ],
        ),
      ),
    );
  }

  // --- WIDGET BANTUAN KARTU ---
  Widget _buildStatCard(String count, String type, IconData icon, {bool isPremium = false}) {
    return Container(
      width: 150,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: isPremium ? primaryMaroon : Colors.white, // Suite pakai warna Maroon
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 15, offset: const Offset(0, 8))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'UNIT KAMAR',
                style: TextStyle(
                  fontSize: 8,
                  fontWeight: FontWeight.w900,
                  color: isPremium ? Colors.white70 : Colors.grey,
                  letterSpacing: 1,
                ),
              ),
              Icon(icon, size: 18, color: isPremium ? Colors.white70 : primaryMaroon),
            ],
          ),
          const SizedBox(height: 15),
          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                count,
                style: TextStyle(
                  fontSize: 36,
                  fontWeight: FontWeight.w900,
                  color: isPremium ? Colors.white : const Color(0xFF1A1A1A),
                  height: 1,
                ),
              ),
              const SizedBox(width: 6),
              Padding(
                padding: const EdgeInsets.only(bottom: 6.0),
                child: Text(
                  type,
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                    color: isPremium ? Colors.white70 : Colors.grey,
                  ),
                ),
              ),
            ],
          )
        ],
      ),
    );
  }
}