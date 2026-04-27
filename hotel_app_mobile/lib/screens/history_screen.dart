import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/reservation_service.dart';

class HistoryScreen extends StatefulWidget {
  const HistoryScreen({super.key});

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFFAFBFC);

  List<dynamic> historyList = [];
  bool isLoading = true;

  // --- Variabel Rekapitulasi ---
  int totalStay = 0;
  int checkedOutCount = 0;
  int canceledCount = 0;
  int activeNowCount = 0;
  int totalRevenue = 0;

  final currencyFormat = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  // Helper pengaman angka
  int _safeInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is double) return value.toInt();
    return int.tryParse(value.toString().split('.')[0]) ?? 0;
  }

  // Helper inisial nama tamu (Contoh: "Praditya Rafid" -> "PR")
  String _getInitials(String name) {
    List<String> names = name.trim().split(' ');
    if (names.isEmpty) return 'G';
    if (names.length == 1) return names[0][0].toUpperCase();
    return '${names[0][0]}${names[names.length - 1][0]}'.toUpperCase();
  }

  Future<void> _loadData() async {
    setState(() => isLoading = true);
    final allReservations = await ReservationService.fetchReservations();
    
    if (!mounted) return;

    // Reset Counter
    int tempTotalStay = 0;
    int tempCheckedOut = 0;
    int tempCanceled = 0;
    int tempActive = 0;
    int tempRevenue = 0;
    List<dynamic> tempHistory = [];

    for (var res in allReservations) {
      String status = res['status'].toString().toUpperCase();

      // Hitung yang masih aktif di hotel
      if (status == 'CHECKED-IN' || status == 'IN-HOUSE') {
        tempActive++;
      } 
      // Masukkan ke History kalau udah Checkout atau Batal
      else if (status == 'CHECKED-OUT' || status == 'CANCELED') {
        tempHistory.add(res);
        tempTotalStay++;
        
        if (status == 'CHECKED-OUT') {
          tempCheckedOut++;
          tempRevenue += _safeInt(res['total_price']);
        } else if (status == 'CANCELED') {
          tempCanceled++;
        }
      }
    }

    setState(() {
      totalStay = tempTotalStay;
      checkedOutCount = tempCheckedOut;
      canceledCount = tempCanceled;
      activeNowCount = tempActive;
      totalRevenue = tempRevenue;
      historyList = tempHistory;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgGrey,
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('HISTORY TAMU', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF1B212D), fontStyle: FontStyle.italic)),
            Text('ARSIP DATA & REKAPITULASI RESERVASI', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: primaryMaroon, letterSpacing: 1.5)),
          ],
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : RefreshIndicator(
              color: primaryMaroon,
              onRefresh: _loadData,
              child: CustomScrollView(
                physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
                slivers: [
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // =====================================
                          // 1. KARTU REKAPITULASI (HORIZONTAL)
                          // =====================================
                          SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            physics: const BouncingScrollPhysics(),
                            child: Row(
                              children: [
                                _buildSummaryCard('TOTAL STAY', totalStay.toString(), Colors.blueGrey),
                                const SizedBox(width: 15),
                                _buildSummaryCard('CHECKED OUT', checkedOutCount.toString(), Colors.green),
                                const SizedBox(width: 15),
                                _buildSummaryCard('CANCELED', canceledCount.toString(), Colors.red),
                                const SizedBox(width: 15),
                                _buildSummaryCard('TOTAL REVENUE', currencyFormat.format(totalRevenue), primaryMaroon),
                                const SizedBox(width: 15),
                                _buildSummaryCard('ACTIVE NOW', activeNowCount.toString(), Colors.blue),
                              ],
                            ),
                          ),
                          const SizedBox(height: 30),
                          
                          const Text('MASTER DATABASE', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 1.5)),
                          const SizedBox(height: 15),
                        ],
                      ),
                    ),
                  ),

                  // =====================================
                  // 2. LIST HISTORY RESERVASI
                  // =====================================
                  historyList.isEmpty
                      ? SliverToBoxAdapter(
                          child: Padding(
                            padding: const EdgeInsets.only(top: 50),
                            child: Center(child: Text('BELUM ADA ARSIP TAMU', style: TextStyle(color: Colors.grey.shade400, fontWeight: FontWeight.bold, letterSpacing: 2))),
                          ),
                        )
                      : SliverPadding(
                          padding: const EdgeInsets.symmetric(horizontal: 20),
                          sliver: SliverList(
                            delegate: SliverChildBuilderDelegate(
                              (context, index) {
                                final res = historyList[index];
                                bool isCanceled = res['status'].toString().toUpperCase() == 'CANCELED';
                                String guestName = res['guest_name'] ?? 'Tamu';

                                return Container(
                                  margin: const EdgeInsets.only(bottom: 15),
                                  padding: const EdgeInsets.all(20),
                                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade200)),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          // AVATAR INISIAL
                                          Container(
                                            width: 45, height: 45,
                                            decoration: BoxDecoration(color: isCanceled ? Colors.red.shade50 : primaryMaroon, borderRadius: BorderRadius.circular(12)),
                                            alignment: Alignment.center,
                                            child: Text(_getInitials(guestName), style: TextStyle(color: isCanceled ? Colors.red : Colors.white, fontWeight: FontWeight.w900, fontSize: 16)),
                                          ),
                                          const SizedBox(width: 15),
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Text(guestName, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: Color(0xFF1B212D))),
                                                const SizedBox(height: 3),
                                                Text(res['email'] ?? '-', style: const TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold)),
                                              ],
                                            ),
                                          ),
                                          // TOMBOL STRUK / DETAIL (Opsional buat ke depannya)
                                          Container(
                                            width: 35, height: 35,
                                            decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.grey.shade200)),
                                            child: Icon(Icons.receipt_long, size: 16, color: Colors.grey.shade400),
                                          )
                                        ],
                                      ),
                                      const Padding(padding: EdgeInsets.symmetric(vertical: 15), child: Divider(color: Colors.black12, height: 1)),
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              const Text('KAMAR', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
                                              const SizedBox(height: 4),
                                              Container(
                                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                                decoration: BoxDecoration(color: Colors.blueGrey.shade900, borderRadius: BorderRadius.circular(6)),
                                                child: Text('RM-${res['room_number']}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Colors.black87)),
                                              )
                                            ],
                                          ),
                                          Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              const Text('PERIODE INAP', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
                                              const SizedBox(height: 4),
                                              Text('${res['arrival']} — ${res['departure']}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Colors.black87)),
                                            ],
                                          ),
                                          Column(
                                            crossAxisAlignment: CrossAxisAlignment.end,
                                            children: [
                                              const Text('STATUS AKHIR', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
                                              const SizedBox(height: 4),
                                              Text(isCanceled ? 'CANCELED' : 'ARCHIVED', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: isCanceled ? Colors.red : Colors.grey, letterSpacing: 1)),
                                            ],
                                          ),
                                        ],
                                      )
                                    ],
                                  ),
                                );
                              },
                              childCount: historyList.length,
                            ),
                          ),
                        ),
                  const SliverToBoxAdapter(child: SizedBox(height: 50)),
                ],
              ),
            ),
    );
  }

  // Komponen Kartu Rekapitulasi yang bisa di-scroll ke samping
  Widget _buildSummaryCard(String title, String value, Color color) {
    return Container(
      width: 140,
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.3), width: 1.5),
        boxShadow: [BoxShadow(color: color.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 5))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 1)),
          const SizedBox(height: 10),
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: color)),
          ),
        ],
      ),
    );
  }
}