import 'dart:async';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';
import '../services/dashboard_service.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color textDark = const Color(0xFF1B212D);
  final Color pinkBg = const Color(0xFFFCE9EC);
  final Color pinkText = const Color(0xFFE56A82);

  bool isLoading = true;
  Map<String, dynamic>? dashboardData;
  
  // Variabel untuk Search
  List<dynamic> allRooms = [];
  List<dynamic> filteredRooms = [];
  final TextEditingController _searchController = TextEditingController();

  late Timer _timer;
  DateTime _currentTime = DateTime.now();

  @override
  void initState() {
    super.initState();
    initializeDateFormatting('id_ID', null);
    _loadData();
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (mounted) setState(() => _currentTime = DateTime.now());
    });
  }

  @override
  void dispose() {
    _timer.cancel();
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    final data = await DashboardService.fetchDashboardData();
    if (mounted && data != null) {
      // Simulasi data banyak (bisa hapus bagian addAll ini jika sudah pakai API asli)
      List<dynamic> rooms = List.from(data['room_list']);
      rooms.addAll([
        {'no': '104', 'left_status': 'Standard', 'payment': 'Cash', 'is_paid': 1, 'action': 'VACANT DIRTY'},
        {'no': '205', 'left_status': 'Deluxe', 'payment': 'Transfer', 'is_paid': 0, 'action': 'BOOKED'},
        {'no': '301', 'left_status': 'Suite', 'payment': 'Credit Card', 'is_paid': 1, 'action': 'OCCUPIED'},
        {'no': '108', 'left_status': 'Standard', 'payment': '-', 'is_paid': 0, 'action': 'OO'},
        {'no': '210', 'left_status': 'Deluxe', 'payment': 'Cash', 'is_paid': 1, 'action': 'AVAILABLE'},
      ]);

      setState(() {
        dashboardData = data;
        allRooms = rooms;
        filteredRooms = rooms;
        isLoading = false;
      });
    }
  }

  // Fungsi untuk memfilter list saat mengetik di Search Bar
  void _filterRooms(String query) {
    setState(() {
      filteredRooms = allRooms
          .where((room) =>
              room['no'].toString().toLowerCase().contains(query.toLowerCase()))
          .toList();
    });
  }

  Color _getActionColor(String action) {
    if (action == 'OCCUPIED') return const Color(0xFFDC2626);
    if (action == 'BOOKED') return const Color(0xFF3B82F6);
    if (action == 'VACANT DIRTY') return const Color(0xFFEAB308);
    if (action == 'OO') return Colors.black;
    if (action == 'OS') return const Color(0xFF4B5563);
    return const Color(0xFF22C55E);
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return Scaffold(
        backgroundColor: const Color(0xFFFAFBFC),
        body: Center(child: CircularProgressIndicator(color: primaryMaroon)),
      );
    }

    final stats = dashboardData!['stats'];

    return Scaffold(
      backgroundColor: const Color(0xFFFAFBFC),
      body: RefreshIndicator(
        color: primaryMaroon,
        onRefresh: _loadData,
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. HEADER & JAM (Tetap Sama)
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 50, 20, 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(width: 30, height: 3, color: pinkText, margin: const EdgeInsets.only(right: 10)),
                        Text('OVERVIEW PANEL', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: pinkText, letterSpacing: 2)),
                      ],
                    ),
                    const SizedBox(height: 10),
                    RichText(
                      text: TextSpan(
                        style: TextStyle(fontSize: 28, fontWeight: FontWeight.w900, color: textDark, fontFamily: 'sans-serif'),
                        children: [
                          const TextSpan(text: 'Dashboard '),
                          TextSpan(text: 'Hotel SIG', style: TextStyle(color: primaryMaroon)),
                        ],
                      ),
                    ),
                    const SizedBox(height: 25),
                    // Widget Jam (Tetap Sama)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
                      decoration: BoxDecoration(color: pinkBg, borderRadius: BorderRadius.circular(20)),
                      child: Row(
                        children: [
                          Container(padding: const EdgeInsets.all(12), decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle), child: Icon(Icons.sensors, color: pinkText, size: 24)),
                          const SizedBox(width: 20),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(DateFormat('HH:mm:ss').format(_currentTime), style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: textDark, letterSpacing: 1)),
                              Text(DateFormat('EEEE, dd MMM yyyy', 'id_ID').format(_currentTime).toUpperCase(), style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: primaryMaroon, letterSpacing: 1)),
                            ],
                          )
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              // 2. KARTU UNIT KAMAR
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                child: Column(
                  children: [
                    _buildRoomCard(stats['Standard'].toString(), 'STANDARD'),
                    const SizedBox(height: 15),
                    _buildRoomCard(stats['Deluxe'].toString(), 'DELUXE'),
                    const SizedBox(height: 15),
                    _buildRoomCard(stats['Suite'].toString(), 'SUITE'),
                  ],
                ),
              ),

              // 3. SEARCH BAR & JUDUL TABEL
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 15),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Kamar Terisi &\nMaintenance', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: textDark)),
                    const SizedBox(height: 15),
                    // Widget Search Bar
                    TextField(
                      controller: _searchController,
                      onChanged: _filterRooms,
                      decoration: InputDecoration(
                        hintText: 'Cari nomor kamar...',
                        hintStyle: const TextStyle(fontSize: 13, color: Colors.grey),
                        prefixIcon: Icon(Icons.search, color: pinkText),
                        filled: true,
                        fillColor: Colors.white,
                        contentPadding: const EdgeInsets.symmetric(vertical: 0),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide.none),
                      ),
                    ),
                  ],
                ),
              ),

              // KOTAK TABEL
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(25),
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 15, offset: const Offset(0, 5))],
                ),
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  physics: const BouncingScrollPhysics(),
                  child: ConstrainedBox(
                    constraints: BoxConstraints(minWidth: MediaQuery.of(context).size.width - 40),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: const EdgeInsets.symmetric(vertical: 25, horizontal: 20),
                          child: Row(
                            children: [
                              _buildColText('NO. KAMAR', 90, isHeader: true),
                              _buildColText('STATUS', 100, isHeader: true),
                              _buildColText('PAYMENT', 110, isHeader: true),
                              _buildColText('STATUS BAYAR', 120, isHeader: true, alignCenter: true),
                              _buildColText('ACTION', 100, isHeader: true, alignCenter: true),
                            ],
                          ),
                        ),
                        // Data Kamar yang sudah di-filter
                        if (filteredRooms.isEmpty)
                          const Padding(padding: EdgeInsets.all(30), child: Text('Kamar tidak ditemukan.', style: TextStyle(color: Colors.grey)))
                        else
                          ...filteredRooms.map((room) {
                            bool isPaid = room['is_paid'] == true || room['is_paid'] == 1;
                            return Padding(
                              padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 20),
                              child: Row(
                                children: [
                                  _buildColText(room['no'].toString(), 90, isBold: true),
                                  _buildColText(room['left_status'].toString(), 100, isItalic: true),
                                  _buildColText(room['payment'].toString(), 110, isBold: true),
                                  SizedBox(width: 120, child: Center(child: Text(isPaid ? '✓' : '!', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: isPaid ? Colors.green : pinkText)))),
                                  SizedBox(width: 100, child: Center(child: Container(padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8), decoration: BoxDecoration(color: _getActionColor(room['action'].toString()), borderRadius: BorderRadius.circular(20)), child: Text(room['action'].toString(), style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5))))),
                                ],
                              ),
                            );
                          }),
                        const SizedBox(height: 10),
                      ],
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 120),
            ],
          ),
        ),
      ),
    );
  }

  // --- WIDGET BANTUAN UI (Tetap Sama) ---
  Widget _buildRoomCard(String count, String type) {
    return Container(
      height: 110,
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(25), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 15, offset: const Offset(0, 5))]),
      child: Stack(
        children: [
          Positioned(right: -30, top: -20, child: CircleAvatar(radius: 60, backgroundColor: const Color(0xFFF4F5F7))),
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text('UNIT KAMAR', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 1.5)),
                const SizedBox(height: 5),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(count, style: TextStyle(fontSize: 42, fontWeight: FontWeight.w900, color: textDark, height: 1.0)),
                    const SizedBox(width: 8),
                    Padding(padding: const EdgeInsets.only(bottom: 6.0), child: Text(type, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5))),
                  ],
                )
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildColText(String text, double width, {bool isHeader = false, bool isBold = false, bool isItalic = false, bool alignCenter = false}) {
    return SizedBox(
      width: width,
      child: Text(text, textAlign: alignCenter ? TextAlign.center : TextAlign.left, style: TextStyle(fontSize: isHeader ? 10 : 13, fontWeight: isHeader || isBold ? FontWeight.w900 : FontWeight.w600, fontStyle: isItalic ? FontStyle.italic : FontStyle.normal, color: isHeader ? pinkText : textDark, letterSpacing: isHeader ? 1.5 : 0)),
    );
  }
}