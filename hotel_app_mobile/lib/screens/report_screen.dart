import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/report_service.dart';

class ReportScreen extends StatefulWidget {
  const ReportScreen({super.key});

  @override
  State<ReportScreen> createState() => _ReportScreenState();
}

class _ReportScreenState extends State<ReportScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFFAFBFC);
  
  Map<String, dynamic>? opsData;
  Map<String, dynamic>? finData;
  bool isLoading = true;

  final currencyFormat = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _loadAllData();
  }

  Future<void> _loadAllData() async {
    setState(() => isLoading = true);
    final ops = await ReportService.fetchOperasional();
    final fin = await ReportService.fetchKeuangan();
    if (mounted) {
      setState(() {
        opsData = ops;
        finData = fin;
        isLoading = false;
      });
    }
  }

  // Fungsi helper untuk parsing angka dengan aman
  int _safeInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is double) return value.toInt();
    // Kalau String, bersihkan titik desimal (kalau ada) lalu parse
    return int.tryParse(value.toString().split('.')[0]) ?? 0;
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2, 
      child: Scaffold(
        backgroundColor: bgGrey,
        appBar: AppBar(
          title: const Text('EXECUTIVE REPORTS', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF1B212D), fontStyle: FontStyle.italic)),
          backgroundColor: Colors.white,
          elevation: 0,
          iconTheme: const IconThemeData(color: Colors.black),
          bottom: TabBar(
            indicatorColor: primaryMaroon,
            indicatorWeight: 4,
            labelColor: primaryMaroon,
            unselectedLabelColor: Colors.grey,
            labelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
            tabs: const [
              Tab(text: 'OPERASIONAL'),
              Tab(text: 'KEUANGAN'),
            ],
          ),
        ),
        body: isLoading
            ? Center(child: CircularProgressIndicator(color: primaryMaroon))
            : TabBarView(
                children: [
                  _buildOperasionalTab(),
                  _buildKeuanganTab(),
                ],
              ),
      ),
    );
  }

  // ==========================================
  // TAB 1: OPERASIONAL
  // ==========================================
  Widget _buildOperasionalTab() {
    if (opsData == null) return const Center(child: Text('Gagal memuat data operasional.'));
    
    List logs = opsData!['logs'] ?? [];

    return RefreshIndicator(
      color: primaryMaroon,
      onRefresh: _loadAllData,
      child: ListView(
        physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
        padding: const EdgeInsets.all(20),
        children: [
          Row(
            children: [
              Expanded(child: _buildStatCard('AVAILABLE', opsData!['available'].toString(), Icons.door_front_door, Colors.green)),
              const SizedBox(width: 10), // 🔥 Perkecil jarak antar card sedikit biar lega
              Expanded(child: _buildStatCard('OUT OF ORDER', opsData!['oo'].toString(), Icons.build, Colors.red)),
            ],
          ),
          const SizedBox(height: 15),
          Row(
            children: [
              Expanded(child: _buildStatCard('STAFF ACTIVE', opsData!['staff'].toString(), Icons.people, Colors.blue)),
              const SizedBox(width: 10),
              Expanded(child: _buildStatCard('OCCUPANCY', '${opsData!['occupancy']}%', Icons.analytics, Colors.orange)),
            ],
          ),
          const SizedBox(height: 30),

          const Text('LOG AKTIVITAS TERBARU', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Color(0xFF1B212D), letterSpacing: 1)),
          const SizedBox(height: 15),
          ...logs.map((log) => Container(
            margin: const EdgeInsets.only(bottom: 10),
            padding: const EdgeInsets.all(15),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                  decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(8)),
                  child: Text('KMR ${log['room_number']}', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                ),
                const SizedBox(width: 15),
                Expanded(child: Text('Status diubah menjadi: ${log['status'].toString().toUpperCase()}', style: const TextStyle(fontSize: 12, color: Colors.black87))),
              ],
            ),
          )).toList(),
        ],
      ),
    );
  }

  // ==========================================
  // TAB 2: KEUANGAN
  // ==========================================
  Widget _buildKeuanganTab() {
    if (finData == null) return const Center(child: Text('Gagal memuat data keuangan.'));

    List transactions = finData!['transactions'] ?? [];

    return RefreshIndicator(
      color: primaryMaroon,
      onRefresh: _loadAllData,
      child: ListView(
        physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
        padding: const EdgeInsets.all(20),
        children: [
          _buildMoneyCard('TOTAL PENDAPATAN', finData!['total_pendapatan'], Icons.account_balance_wallet, Colors.green),
          const SizedBox(height: 15),
          Row(
            children: [
              Expanded(child: _buildMoneyCard('PENDAPATAN KAMAR', finData!['pendapatan_kamar'], Icons.bed, Colors.blue, isSmall: true)),
              const SizedBox(width: 10),
              Expanded(child: _buildMoneyCard('BIAYA TAMBAHAN', finData!['biaya_tambahan'], Icons.add_circle, Colors.orange, isSmall: true)),
            ],
          ),
          const SizedBox(height: 15),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(color: Colors.purple.shade50, borderRadius: BorderRadius.circular(15)),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('TOTAL CHECK-OUT', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.purple, fontSize: 11)),
                Text('${finData!['total_transaksi']} Transaksi', style: const TextStyle(fontWeight: FontWeight.w900, color: Colors.purple, fontSize: 16)),
              ],
            ),
          ),
          const SizedBox(height: 30),

          const Text('RIWAYAT TRANSAKSI CHECK-OUT', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Color(0xFF1B212D), letterSpacing: 1)),
          const SizedBox(height: 15),
          ...transactions.map((t) => Container(
            margin: const EdgeInsets.only(bottom: 10),
            padding: const EdgeInsets.all(15),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(t['date'], style: const TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold)),
                    Text('Kamar ${t['room_number']}', style: const TextStyle(fontSize: 10, color: Colors.red, fontWeight: FontWeight.bold)),
                  ],
                ),
                const SizedBox(height: 8),
                Text(t['guest_name'], style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14)),
                const Divider(color: Colors.black12, height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Total Dibayar', style: TextStyle(fontSize: 11, color: Colors.grey, fontWeight: FontWeight.bold)),
                    Text(currencyFormat.format(_safeInt(t['total_amount'])), style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: Colors.green)),
                  ],
                )
              ],
            ),
          )).toList(),
        ],
      ),
    );
  }

  // 🔥 FIX OVERFLOW: Bungkus text judul pakai Flexible
  Widget _buildStatCard(String title, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Icon(icon, color: color, size: 20),
              const SizedBox(width: 5),
              Flexible(
                child: Text(title, textAlign: TextAlign.right, style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: color)),
              ),
            ],
          ),
          const SizedBox(height: 15),
          FittedBox(
            fit: BoxFit.scaleDown,
            alignment: Alignment.centerLeft,
            child: Text(value, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: Color(0xFF1B212D))),
          ),
        ],
      ),
    );
  }

  // 🔥 FIX OVERFLOW: Sama kayak stat card, kita bikin dia flexible
  Widget _buildMoneyCard(String title, dynamic rawAmount, IconData icon, Color color, {bool isSmall = false}) {
    int amount = _safeInt(rawAmount); 

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Icon(icon, color: color, size: isSmall ? 20 : 24),
              const SizedBox(width: 5),
              Flexible(
                child: Text(title, textAlign: TextAlign.right, style: TextStyle(fontSize: isSmall ? 8 : 11, fontWeight: FontWeight.w900, color: color)),
              ),
            ],
          ),
          const SizedBox(height: 15),
          FittedBox(
            fit: BoxFit.scaleDown,
            alignment: Alignment.centerLeft,
            child: Text(currencyFormat.format(amount), style: TextStyle(fontSize: isSmall ? 18 : 28, fontWeight: FontWeight.w900, color: const Color(0xFF1B212D))),
          ),
        ],
      ),
    );
  }
}