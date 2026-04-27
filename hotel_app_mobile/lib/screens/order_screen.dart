import 'package:flutter/material.dart';
import 'package:hotel_app_mobile/screens/checkout_screen.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart'; // 🔥 Tambahan Import Brankas
import '../services/reservation_service.dart';
import 'add_reservation_screen.dart';

class OrderScreen extends StatefulWidget {
  final String mode; 
  const OrderScreen({super.key, this.mode = 'all'});

  @override
  State<OrderScreen> createState() => _OrderScreenState();
}

class _OrderScreenState extends State<OrderScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFFAFBFC);
  
  List<dynamic> reservations = [];
  bool isLoading = true;
  bool isSuperadmin = false; // 🔥 Variabel penentu hak akses

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    // 1. Buka brankas buat ngecek status Superadmin
    SharedPreferences prefs = await SharedPreferences.getInstance();
    bool superadminStatus = prefs.getBool('is_superadmin') ?? false;

    final data = await ReservationService.fetchReservations();
    if (mounted) {
      setState(() {
        isSuperadmin = superadminStatus; // Simpan statusnya
        reservations = data;
        isLoading = false;
      });
    }
  }

  Color _getStatusColor(String status) {
    if (status == 'BOOKED') return Colors.blue.shade600;
    if (status == 'CHECKED-IN' || status == 'IN-HOUSE') return Colors.green.shade600;
    if (status == 'CHECKED-OUT') return Colors.grey.shade600;
    return primaryMaroon;
  }

  void _showConfirmDialog(int id, String action, String guestName, String resType) {
    String selectedPayment = 'Cash'; 
    final messenger = ScaffoldMessenger.of(context);

    showDialog(
      context: context,
      builder: (dialogContext) => StatefulBuilder(
        builder: (statefulContext, setStateLocal) {
          bool isUnpaid = (action == 'Check-In' && resType == 'non-guaranteed');

          return AlertDialog(
            title: Text('Konfirmasi $action', style: const TextStyle(fontWeight: FontWeight.bold)),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
            content: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Nama tamu di sini juga bakal tersensor kalau dia incognito
                Text('Apakah Anda yakin ingin melakukan $action untuk tamu $guestName?'),
                
                if (isUnpaid) ...[
                  const SizedBox(height: 15),
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.red.shade200)),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('TAGIHAN:', style: TextStyle(color: Colors.red, fontSize: 12, fontWeight: FontWeight.bold)),
                        Text(
                          NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(widget.mode == 'checkin' ? reservations.firstWhere((e) => e['id'] == id)['total_price'] : 0), 
                          style: const TextStyle(color: Colors.red, fontSize: 16, fontWeight: FontWeight.w900)
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 15),
                  const Text('Metode Pembayaran Saat Ini:', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 5),
                  DropdownButtonFormField<String>(
                    value: selectedPayment,
                    decoration: InputDecoration(
                      contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                      filled: true, fillColor: Colors.grey.shade50,
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: Colors.grey.shade300)),
                    ),
                    items: const [
                      DropdownMenuItem(value: 'Cash', child: Text('Tunai (Cash)', style: TextStyle(fontSize: 13))),
                      DropdownMenuItem(value: 'Transfer', child: Text('Transfer Bank', style: TextStyle(fontSize: 13))),
                      DropdownMenuItem(value: 'Credit Card', child: Text('Kartu Kredit', style: TextStyle(fontSize: 13))),
                    ],
                    onChanged: (val) => setStateLocal(() => selectedPayment = val!),
                  ),
                ]
              ],
            ),
            actions: [
              TextButton(onPressed: () => Navigator.pop(dialogContext), child: const Text('Batal', style: TextStyle(color: Colors.grey))),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: action == 'Check-In' ? Colors.green : Colors.orange.shade700,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))
                ),
                onPressed: () async {
                  Navigator.pop(dialogContext); 
                  setState(() => isLoading = true); 
                  
                  bool success = action == 'Check-In' 
                      ? await ReservationService.checkIn(id, paymentMethod: isUnpaid ? selectedPayment : null) 
                      : await ReservationService.checkOut(id);

                  if (!mounted) return;

                  if (success) {
                    messenger.showSnackBar(SnackBar(content: Text('$action Berhasil! 🎉'), backgroundColor: Colors.green));
                    _loadData(); 
                  } else {
                    messenger.showSnackBar(SnackBar(content: Text('$action Gagal!'), backgroundColor: Colors.red));
                    setState(() => isLoading = false);
                  }
                },
                child: Text(isUnpaid ? 'Bayar & Check-In' : 'Ya, $action', style: const TextStyle(color: Colors.white)),
              ),
            ],
          );
        }
      ),
    );
  }

  void _showCancelDialog(int id, String guestName) {
    final messenger = ScaffoldMessenger.of(context);
    showDialog(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: const Text('Batalkan Reservasi?', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.red)),
        content: Text('Apakah Anda yakin ingin membatalkan reservasi untuk $guestName?'),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        actions: [
          TextButton(onPressed: () => Navigator.pop(dialogContext), child: const Text('Tutup', style: TextStyle(color: Colors.grey))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))
            ),
            onPressed: () async {
              Navigator.pop(dialogContext);
              setState(() => isLoading = true); 
              
              bool success = await ReservationService.cancelReservation(id);

              if (!mounted) return;

              if (success) {
                messenger.showSnackBar(const SnackBar(content: Text('Reservasi Dibatalkan! Masuk ke History.'), backgroundColor: Colors.green));
                _loadData(); 
              } else {
                messenger.showSnackBar(const SnackBar(content: Text('Gagal membatalkan reservasi!'), backgroundColor: Colors.red));
                setState(() => isLoading = false);
              }
            },
            child: const Text('Ya, Batalkan', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final displayList = reservations.where((res) {
      if (widget.mode == 'checkin') return res['status'] == 'BOOKED';
      if (widget.mode == 'checkout' || widget.mode == 'extend') return res['status'] == 'CHECKED-IN' || res['status'] == 'IN-HOUSE';
      return true; 
    }).toList();

    return Scaffold(
      backgroundColor: bgGrey,
      appBar: AppBar(
        backgroundColor: bgGrey,
        elevation: 0,
        title: Text(
          widget.mode == 'checkin' ? 'Pilih Tamu Check-In' :
          widget.mode == 'checkout' ? 'Pilih Tamu Check-Out' : 
          widget.mode == 'extend' ? 'Perpanjang Menginap' : 'Daftar Reservasi',
          style: const TextStyle(color: Color(0xFF1B212D), fontWeight: FontWeight.w900, fontSize: 18)
        ),
        iconTheme: const IconThemeData(color: Colors.black),
        actions: [
          if (widget.mode == 'all' || widget.mode == 'checkin')
            Container(
              margin: const EdgeInsets.only(right: 20, top: 8, bottom: 8),
              decoration: BoxDecoration(color: primaryMaroon, borderRadius: BorderRadius.circular(12)),
              child: IconButton(
                icon: const Icon(Icons.add, color: Colors.white, size: 20),
                onPressed: () async {
                  final result = await Navigator.push(context, MaterialPageRoute(builder: (context) => const AddReservationScreen()));
                  if (result == true) {
                    setState(() => isLoading = true);
                    _loadData();
                  }
                },
              ),
            )
        ],
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : displayList.isEmpty
              ? Center(child: Text(
                  widget.mode == 'checkin' ? 'Tidak ada tamu yang siap Check-In.' :
                  widget.mode == 'checkout' ? 'Tidak ada tamu yang siap Check-Out.' :
                  'Belum ada data reservasi.', 
                  style: const TextStyle(color: Colors.grey)
                ))
              : RefreshIndicator(
                  color: primaryMaroon,
                  backgroundColor: Colors.white,
                  onRefresh: _loadData,
                  child: ListView.builder(
                    physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
                    padding: const EdgeInsets.only(left: 20, right: 20, bottom: 100, top: 10), 
                    itemCount: displayList.length,
                    itemBuilder: (context, index) {
                      final res = displayList[index];
                      Color statusColor = _getStatusColor(res['status']);
                      String resType = res['reservation_type'] ?? 'non-guaranteed';

                      // ========================================================
                      // 🔥 LOGIKA SENSOR NAMA (INCOGNITO MODE) 🔥
                      // ========================================================
                      // Anggap dari backend API mengirimkan flag 'incognito' bernilai 1 atau true
                      bool isIncognito = res['incognito'] == 1 || res['incognito'] == true || res['is_incognito'] == 1 || res['is_incognito'] == true;
                      
                      String displayGuestName = res['guest_name'] ?? 'Tamu';
                      // Jika dia Incognito DAN yang login BUKAN Superadmin -> Sensor!
                      if (isIncognito && !isSuperadmin) {
                        displayGuestName = '*** (Rahasia/Incognito)';
                      }

                      return Container(
                        margin: const EdgeInsets.only(bottom: 15),
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 5))],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text('Kamar ${res['room_number']} • ${res['room_type']}'.toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 1)),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
                                  child: Text(res['status'], style: TextStyle(color: statusColor, fontSize: 9, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                            
                            // 🔥 PANGGIL NAMA YANG SUDAH DI-FILTER DI SINI
                            Text(displayGuestName, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: (isIncognito && !isSuperadmin) ? primaryMaroon : const Color(0xFF1B212D))),
                            
                            const SizedBox(height: 12),
                            Row(
                              children: [
                                const Icon(Icons.calendar_month_outlined, size: 14, color: Colors.grey),
                                const SizedBox(width: 5),
                                Text('${res['arrival']} - ${res['departure']}', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.black87)),
                              ],
                            ),
                            const SizedBox(height: 6),
                            Row(
                              children: [
                                const Icon(Icons.account_balance_wallet_outlined, size: 14, color: Colors.grey),
                                const SizedBox(width: 5),
                                Text('${res['payment_method']} (${resType == 'guaranteed' ? 'Lunas' : 'Belum Lunas'})', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: resType == 'guaranteed' ? Colors.green : Colors.red)),
                              ],
                            ),
                            
                            if (widget.mode == 'checkin') ...[
                              const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Divider(color: Colors.black12)),
                              Row(
                                children: [
                                  Expanded(
                                    flex: 1,
                                    child: SizedBox(
                                      height: 40,
                                      child: OutlinedButton(
                                        style: OutlinedButton.styleFrom(
                                          side: const BorderSide(color: Colors.red),
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                        ),
                                        // Lempar displayGuestName biar pas dialog konfirmasi batal namanya tetep disensor
                                        onPressed: () => _showCancelDialog(res['id'], displayGuestName),
                                        child: const Text('BATAL', style: TextStyle(color: Colors.red, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1)),
                                      ),
                                    ),
                                  ),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    flex: 2,
                                    child: SizedBox(
                                      height: 40,
                                      child: ElevatedButton(
                                        style: ElevatedButton.styleFrom(backgroundColor: Colors.green.shade600, elevation: 0, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
                                        // Lempar displayGuestName biar pas dialog checkin namanya tetep disensor
                                        onPressed: () => _showConfirmDialog(res['id'], 'Check-In', displayGuestName, resType),
                                        child: const Text('PROSES CHECK-IN', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1)),
                                      ),
                                    ),
                                  ),
                                ],
                              )
                            ],
                            if (widget.mode == 'checkout') ...[
                            const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Divider(color: Colors.black12)),
                            SizedBox(
                              width: double.infinity, height: 40,
                              child: ElevatedButton(
                                style: ElevatedButton.styleFrom(backgroundColor: Colors.orange.shade700, elevation: 0, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
                                onPressed: () async {
                                  // Lempar objek reservasi utuh (nanti di layar checkout lu bisa sensor juga kalau mau)
                                  final result = await Navigator.push(
                                    context, 
                                    MaterialPageRoute(builder: (context) => CheckoutScreen(reservation: res))
                                  );
                                  
                                  if (result == true) {
                                    setState(() => isLoading = true);
                                    _loadData();
                                  }
                                },
                                child: const Text('PROSES CHECK-OUT', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1)),
                              ),
                            ),
                          ],
                          if (widget.mode == 'extend') ...[
                            const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Divider(color: Colors.black12)),
                            SizedBox(
                              width: double.infinity, height: 40,
                              child: ElevatedButton(
                                style: ElevatedButton.styleFrom(backgroundColor: Colors.blueAccent, elevation: 0, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
                                onPressed: () async {
                                  final messenger = ScaffoldMessenger.of(context);
                                  DateTime currentCheckout;
                                  try {
                                    currentCheckout = DateTime.parse(res['raw_departure'].toString());
                                  } catch (e) {
                                    currentCheckout = DateTime.now();
                                  }
                                  
                                  DateTime minExtendDate = currentCheckout.add(const Duration(days: 1));

                                  final picked = await showDatePicker(
                                    context: context,
                                    initialDate: minExtendDate,
                                    firstDate: minExtendDate, 
                                    lastDate: DateTime.now().add(const Duration(days: 365)),
                                    builder: (pickerContext, child) => Theme(
                                      data: ThemeData.light().copyWith(colorScheme: ColorScheme.light(primary: primaryMaroon)),
                                      child: child!,
                                    ),
                                  );

                                  if (picked != null) {
                                    setState(() => isLoading = true);
                                    String newDate = DateFormat('yyyy-MM-dd').format(picked);
                                    bool success = await ReservationService.extendReservation(res['id'], newDate);

                                    if (!mounted) return;
                                    
                                    if (success) {
                                      messenger.showSnackBar(const SnackBar(content: Text('Perpanjangan Berhasil! 🎉'), backgroundColor: Colors.green));
                                      _loadData();
                                    } else {
                                      messenger.showSnackBar(const SnackBar(content: Text('Perpanjangan Gagal!'), backgroundColor: Colors.red));
                                      setState(() => isLoading = false);
                                    }
                                  }
                                },
                                child: const Text('PILIH TANGGAL BARU (EXTEND)', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1)),
                              ),
                            ),
                          ],
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}