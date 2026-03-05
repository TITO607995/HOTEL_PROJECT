import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart'; // Wajib import ini buat RBAC
import '../services/maintenance_service.dart';

class MaintenanceScreen extends StatefulWidget {
  const MaintenanceScreen({super.key});

  @override
  State<MaintenanceScreen> createState() => _MaintenanceScreenState();
}

class _MaintenanceScreenState extends State<MaintenanceScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFFAFBFC);
  
  bool isLoading = true;
  List<dynamic> allRooms = [];     // Data asli dari server
  List<dynamic> filteredRooms = []; // Data yang ditampilin setelah di-search
  int ooCount = 0;
  int osCount = 0;

  // Controller untuk fitur Search
  final TextEditingController _searchCtrl = TextEditingController();

  // ==========================================
  // VARIABEL PENENTU HAK AKSES EDIT 🔥
  // ==========================================
  bool canEditOOOS = false;

  @override
  void initState() {
    super.initState();
    _checkPermissionsAndLoad(); // Panggil fungsi cek akses dulu
    
    // Tiap kali ngetik di kolom search, jalankan fungsi filter
    _searchCtrl.addListener(() {
      _filterRooms(_searchCtrl.text);
    });
  }

  // ==========================================
  // FUNGSI CEK HAK AKSES DARI BRANKAS HP
  // ==========================================
  Future<void> _checkPermissionsAndLoad() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    bool isSuper = prefs.getBool('is_superadmin') ?? false;
    List<String> allowedMenus = prefs.getStringList('allowed_menus') ?? [];
    
    setState(() {
      // Aktifkan edit JIKA dia Superadmin ATAU punya akses menu 'STATUS OO/OS - EDIT'
      canEditOOOS = isSuper || allowedMenus.contains('STATUS OO/OS - EDIT');
    });

    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => isLoading = true);
    final data = await MaintenanceService.fetchMaintenanceData();
    if (mounted && data != null) {
      setState(() {
        allRooms = data['rooms'];
        filteredRooms = allRooms; // Awalnya tampilkan semua
        ooCount = data['oo_count'];
        osCount = data['os_count'];
        isLoading = false;
      });
    } else {
      setState(() => isLoading = false);
    }
  }

  // Fungsi Search Cepat (Lokal)
  void _filterRooms(String query) {
    if (query.isEmpty) {
      setState(() => filteredRooms = allRooms);
    } else {
      setState(() {
        filteredRooms = allRooms.where((room) {
          final roomNumber = room['room_number'].toString().toLowerCase();
          return roomNumber.contains(query.toLowerCase());
        }).toList();
      });
    }
  }

  Future<void> _updateStatus(int id, String newStatus, String notes) async {
    setState(() => isLoading = true);
    bool success = await MaintenanceService.updateRoomStatus(id, newStatus, notes);
    
    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Status berhasil diperbarui! 🎉'), backgroundColor: Colors.green));
      _loadData(); // Refresh data
    } else {
      setState(() => isLoading = false);
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal memperbarui status!'), backgroundColor: Colors.red));
    }
  }

  Color _getStatusColor(String status) {
    if (status == 'available') return Colors.green;
    if (status == 'vacant dirty') return Colors.orange;
    if (status == 'oo') return Colors.black;
    if (status == 'os') return Colors.grey.shade700;
    if (status == 'occupied') return Colors.red;
    return primaryMaroon;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgGrey,
      appBar: AppBar(
        backgroundColor: bgGrey,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
        title: const Text('Maintenance & Blocking', style: TextStyle(color: Color(0xFF1B212D), fontWeight: FontWeight.w900, fontSize: 18)),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : Column(
              children: [
                // === STATISTIK KARTU KECIL ===
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                  child: Row(
                    children: [
                      Expanded(child: _buildStatCard('OUT OF ORDER', ooCount.toString(), Colors.black)),
                      const SizedBox(width: 15),
                      Expanded(child: _buildStatCard('OUT OF SERVICE', osCount.toString(), Colors.grey.shade700)),
                    ],
                  ),
                ),

                // === FITUR PENCARIAN (SEARCH BAR) ===
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                  child: TextField(
                    controller: _searchCtrl,
                    decoration: InputDecoration(
                      hintText: 'Cari Nomor Kamar...',
                      prefixIcon: const Icon(Icons.search, color: Colors.grey),
                      suffixIcon: _searchCtrl.text.isNotEmpty 
                          ? IconButton(icon: const Icon(Icons.clear, color: Colors.grey), onPressed: () => _searchCtrl.clear()) 
                          : null,
                      filled: true,
                      fillColor: Colors.white,
                      contentPadding: const EdgeInsets.symmetric(vertical: 0),
                      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Colors.grey.shade200)),
                      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: primaryMaroon)),
                    ),
                  ),
                ),

                // === LIST KAMAR ===
                Expanded(
                  child: filteredRooms.isEmpty
                    ? const Center(child: Text('Kamar tidak ditemukan.', style: TextStyle(color: Colors.grey)))
                    : ListView.builder(
                        physics: const BouncingScrollPhysics(),
                        padding: const EdgeInsets.all(20),
                        itemCount: filteredRooms.length,
                        itemBuilder: (context, index) {
                          final room = filteredRooms[index];
                          return _buildRoomItem(room);
                        },
                      ),
                ),
              ],
            ),
    );
  }

  Widget _buildStatCard(String title, String count, Color color) {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey.shade500, letterSpacing: 1)),
          const SizedBox(height: 5),
          Text(count, style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: color)),
        ],
      ),
    );
  }

  // Item List Kamar (Mirip UI Tabel di Web)
  Widget _buildRoomItem(dynamic room) {
    String currentStatus = room['status'] ?? 'available';
    Color statusColor = _getStatusColor(currentStatus);
    
    String selectedStatus = currentStatus;
    TextEditingController notesCtrl = TextEditingController();

    return Container(
      margin: const EdgeInsets.only(bottom: 15),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 5))]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('KAMAR ${room['room_number']}', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: Color(0xFF1B212D))),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
                child: Text(currentStatus.toUpperCase(), style: TextStyle(color: statusColor, fontSize: 9, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
              ),
            ],
          ),
          
          // ==========================================
          // LOGIKA RBAC JALAN DI SINI 🔥
          // ==========================================
          if (canEditOOOS) ...[
            const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Divider(color: Colors.black12)),
            const Text('Ubah Status:', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 5),
            
            // Dropdown Status
            StatefulBuilder(builder: (context, setStateLocal) {
              return DropdownButtonFormField<String>(
                value: ['available', 'vacant dirty', 'oo', 'os', 'occupied', 'booked'].contains(selectedStatus) ? selectedStatus : 'available',
                decoration: InputDecoration(
                  contentPadding: const EdgeInsets.symmetric(horizontal: 15, vertical: 10),
                  filled: true, fillColor: Colors.grey.shade50,
                  enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: Colors.grey.shade200)),
                  focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: primaryMaroon)),
                ),
                items: const [
                  DropdownMenuItem(value: 'available', child: Text('Available (Normal)')),
                  DropdownMenuItem(value: 'vacant dirty', child: Text('Vacant Dirty')),
                  DropdownMenuItem(value: 'oo', child: Text('Out of Order (OO)')),
                  DropdownMenuItem(value: 'os', child: Text('Out of Service (OS)')),
                ],
                onChanged: (val) => setStateLocal(() => selectedStatus = val!),
              );
            }),
            
            const SizedBox(height: 10),
            TextField(
              controller: notesCtrl,
              decoration: InputDecoration(
                hintText: 'Keterangan perbaikan...',
                hintStyle: const TextStyle(fontSize: 12, color: Colors.grey),
                prefixIcon: const Icon(Icons.edit, size: 16, color: Colors.grey),
                contentPadding: const EdgeInsets.symmetric(vertical: 0),
                filled: true, fillColor: Colors.grey.shade50,
                enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: Colors.grey.shade200)),
                focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: primaryMaroon)),
              ),
            ),
            const SizedBox(height: 15),
            
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: primaryMaroon, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
                onPressed: () => _updateStatus(room['id'], selectedStatus, notesCtrl.text),
                child: const Text('UPDATE STATUS', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w900)),
              ),
            )
          ] else ...[
            // JIKA READ ONLY (TIDAK ADA AKSES EDIT)
            const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Divider(color: Colors.black12)),
            Row(
              children: [
                const Icon(Icons.lock_outline, size: 16, color: Colors.grey),
                const SizedBox(width: 5),
                const Text('Read Only (Tidak ada hak akses ubah data)', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
              ],
            )
          ]
        ],
      ),
    );
  }
}