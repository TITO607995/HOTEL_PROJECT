import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/reservation_service.dart';

class CheckoutScreen extends StatefulWidget {
  final dynamic reservation; // Nangkep data tamu dari Order Screen
  const CheckoutScreen({super.key, required this.reservation});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color darkNavy = const Color(0xFF1B212D);
  
  final TextEditingController _additionalChargesCtrl = TextEditingController();
  final TextEditingController _notesCtrl = TextEditingController();
  
  int additionalCharges = 0;
  bool isLoading = false;

  final currencyFormat = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    // Tiap kali input biaya tambahan diketik, langsung update Grand Total di bawah
    _additionalChargesCtrl.addListener(() {
      setState(() {
        additionalCharges = int.tryParse(_additionalChargesCtrl.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
      });
    });
  }

  Future<void> _processCheckout() async {
    setState(() => isLoading = true);
    bool success = await ReservationService.checkOut(
      widget.reservation['id'],
      additionalCharges: additionalCharges,
      notes: _notesCtrl.text.isNotEmpty ? _notesCtrl.text : 'Checkout via App',
    );

    if (!mounted) return;

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Checkout Berhasil! 🎉'), backgroundColor: Colors.green));
      Navigator.pop(context, true); // Kembali dan beri sinyal sukses
    } else {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Checkout Gagal!'), backgroundColor: Colors.red));
    }
  }

  @override
  Widget build(BuildContext context) {
    final res = widget.reservation;
    int subtotal = res['total_price'] ?? 0;
    int grandTotal = subtotal + additionalCharges;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('INVOICE CHECK-OUT', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, letterSpacing: 1.5, fontStyle: FontStyle.italic)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // INFORMASI TAMU & KAMAR
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
              child: Column(
                children: [
                  _buildInfoRow('NOMOR KAMAR', res['room_number'].toString(), isBold: true),
                  const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Divider(color: Colors.black12)),
                  _buildInfoRow('NAMA TAMU', res['guest_name']),
                  const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Divider(color: Colors.black12)),
                  _buildInfoRow('CHECK-IN', res['raw_arrival'].toString().split(' ')[0]), // Ambil YYYY-MM-DD
                  const SizedBox(height: 10),
                  _buildInfoRow('CHECK-OUT', res['raw_departure'].toString().split(' ')[0], color: primaryMaroon),
                ],
              ),
            ),
            const SizedBox(height: 30),

            // TABEL RINCIAN ALA WEB LU
            Container(
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: darkNavy, width: 2)),
              child: Column(
                children: [
                  // Header Hitam
                  Container(
                    padding: const EdgeInsets.symmetric(vertical: 15, horizontal: 15),
                    decoration: BoxDecoration(color: darkNavy, borderRadius: const BorderRadius.only(topLeft: Radius.circular(12), topRight: Radius.circular(12))),
                    child: const Row(
                      children: [
                        Expanded(flex: 3, child: Text('DESKRIPSI', style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w900))),
                        Expanded(flex: 1, child: Text('DURASI', style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w900))),
                        Expanded(flex: 2, child: Text('SUBTOTAL', textAlign: TextAlign.right, style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w900))),
                      ],
                    ),
                  ),
                  
                  // Item 1: Sewa Kamar
                  Padding(
                    padding: const EdgeInsets.all(15),
                    child: Row(
                      children: [
                        Expanded(flex: 3, child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Sewa Kamar (${res['room_type']})', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                            Text('${currencyFormat.format(res['room_price'])} / malam', style: const TextStyle(fontSize: 10, color: Colors.grey)),
                          ],
                        )),
                        Expanded(flex: 1, child: Text('${res['nights']} Malam', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold))),
                        Expanded(flex: 2, child: Text(currencyFormat.format(subtotal), textAlign: TextAlign.right, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold))),
                      ],
                    ),
                  ),
                  const Divider(height: 1, color: Colors.black12),

                  // Item 2: Biaya Tambahan (Bisa Diinput)
                  Padding(
                    padding: const EdgeInsets.all(15),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Biaya Tambahan (Jika Ada)', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey)),
                        const SizedBox(height: 10),
                        Row(
                          children: [
                            Expanded(
                              flex: 2,
                              child: TextField(
                                controller: _notesCtrl,
                                decoration: InputDecoration(hintText: 'Keterangan (Minimarket, dll)', hintStyle: const TextStyle(fontSize: 12), contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 0), border: OutlineInputBorder(borderRadius: BorderRadius.circular(8))),
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              flex: 1,
                              child: TextField(
                                controller: _additionalChargesCtrl,
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(hintText: 'Rp 0', hintStyle: const TextStyle(fontSize: 12), contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 0), border: OutlineInputBorder(borderRadius: BorderRadius.circular(8))),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  // Grand Total
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(color: darkNavy, borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(12), bottomRight: Radius.circular(12))),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('TOTAL PEMBAYARAN AKHIR', style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1)),
                        Text(currencyFormat.format(grandTotal), style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900)),
                      ],
                    ),
                  )
                ],
              ),
            ),
            const SizedBox(height: 40),

            // Tombol Proses
            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: primaryMaroon, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                onPressed: isLoading ? null : _processCheckout,
                child: isLoading 
                    ? const CircularProgressIndicator(color: Colors.white) 
                    : const Text('PROSES CHECK-OUT', style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.w900, letterSpacing: 1)),
              ),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isBold = false, Color? color}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
        Text(value, style: TextStyle(fontSize: isBold ? 16 : 14, fontWeight: isBold ? FontWeight.w900 : FontWeight.bold, color: color ?? darkNavy)),
      ],
    );
  }
}