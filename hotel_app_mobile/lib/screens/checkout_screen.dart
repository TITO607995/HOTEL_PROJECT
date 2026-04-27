import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/reservation_service.dart';

class CheckoutScreen extends StatefulWidget {
  final dynamic reservation; 
  const CheckoutScreen({super.key, required this.reservation});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color darkNavy = const Color(0xFF1B212D);
  
  final TextEditingController _additionalChargesCtrl = TextEditingController();
  final TextEditingController _notesCtrl = TextEditingController();
  final TextEditingController _paidAmountCtrl = TextEditingController(); 
  
  int additionalCharges = 0;
  int paidAmount = 0;
  bool isLoading = false;

  final currencyFormat = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void dispose() {
    _additionalChargesCtrl.dispose();
    _notesCtrl.dispose();
    _paidAmountCtrl.dispose();
    super.dispose();
  }

  Future<void> _processCheckout(int grandTotal) async {
    if (paidAmount < grandTotal) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Uang Kurang! Sisa tagihan: ${currencyFormat.format(grandTotal - paidAmount)}'),
          backgroundColor: Colors.red,
          behavior: SnackBarBehavior.floating,
        )
      );
      return;
    }

    setState(() => isLoading = true);
    bool success = await ReservationService.checkOut(
      widget.reservation['id'],
      additionalCharges: additionalCharges,
      notes: _notesCtrl.text.isNotEmpty ? _notesCtrl.text : 'Checkout via App',
    );

    if (!mounted) return;

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Checkout Berhasil! 🎉'), backgroundColor: Colors.green));
      Navigator.pop(context, true);
    } else {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Checkout Gagal! Cek log backend.'), backgroundColor: Colors.red));
    }
  }

  @override
  Widget build(BuildContext context) {
    final res = widget.reservation;
    
    int subtotalKamar = res['total_price'] ?? 0;
    int grandTotal = subtotalKamar + additionalCharges;

    // Perhitungan Pajak
    double basePriceRaw = grandTotal / 1.221;
    int serviceCharge = (basePriceRaw * 0.10).round();
    int govTax = ((basePriceRaw + serviceCharge) * 0.11).round();
    
    int changeAmount = paidAmount - grandTotal;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('INVOICE CHECK-OUT', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 14, letterSpacing: 1.2, fontStyle: FontStyle.italic)),
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
            // INFORMASI TAMU
            _buildGuestHeader(res),
            const SizedBox(height: 30),

            // TABEL RINCIAN BIAYA
            Container(
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: darkNavy, width: 2)),
              child: Column(
                children: [
                  _buildTableHeader(),
                  
                  // Item 1: Kamar
                  _buildItemRow('Sewa Kamar (${res['room_type']})', '${res['nights']} Malam', subtotalKamar, subNote: '${currencyFormat.format(res['room_price'])}/mlm'),
                  const Divider(height: 1, color: Colors.black12),

                  // Item 2: Biaya Tambahan (Input)
                  Padding(
                    padding: const EdgeInsets.all(15),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Biaya Tambahan & Catatan', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey)),
                        const SizedBox(height: 10),
                        Row(
                          children: [
                            Expanded(
                              flex: 3, 
                              child: TextField(
                                controller: _notesCtrl, 
                                decoration: _inputStyle('Keterangan (Minibar, dll)'),
                                style: const TextStyle(fontSize: 12),
                              )
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              flex: 2, 
                              child: TextField(
                                controller: _additionalChargesCtrl, 
                                keyboardType: TextInputType.number, 
                                decoration: _inputStyle('Rp 0'),
                                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF800000)),
                                // 🔥 INI YANG BIKIN TOTAL OTOMATIS BERUBAH 🔥
                                onChanged: (val) {
                                  setState(() {
                                    additionalCharges = int.tryParse(val.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
                                  });
                                },
                              )
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  // PAJAK
                  _buildTaxRow('Termasuk Service (10%)', serviceCharge),
                  _buildTaxRow('Termasuk PPN (11%)', govTax),

                  // GRAND TOTAL
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(color: darkNavy, borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(12), bottomRight: Radius.circular(12))),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('TOTAL (NETT)', style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1)),
                        Text(currencyFormat.format(grandTotal), style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900)),
                      ],
                    ),
                  )
                ],
              ),
            ),
            const SizedBox(height: 25),

            // KASIR: INPUT UANG & KEMBALIAN
            _buildPaymentSection(grandTotal, changeAmount),
            const SizedBox(height: 40),

            // TOMBOL
            SizedBox(
              width: double.infinity, height: 55,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: primaryMaroon, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                onPressed: isLoading ? null : () => _processCheckout(grandTotal),
                child: isLoading 
                    ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 3)) 
                    : const Text('KONFIRMASI LUNAS & CHECK-OUT', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w900, letterSpacing: 1)),
              ),
            ),
            const SizedBox(height: 50),
          ],
        ),
      ),
    );
  }

  // --- WIDGET HELPER BAWAH ---

  Widget _buildGuestHeader(dynamic res) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        children: [
          _buildInfoRow('NOMOR KAMAR', res['room_number'].toString(), isBold: true),
          const Padding(padding: EdgeInsets.symmetric(vertical: 8), child: Divider(color: Colors.black12)),
          _buildInfoRow('NAMA TAMU', res['guest_name'].toString().toUpperCase()),
          const Padding(padding: EdgeInsets.symmetric(vertical: 8), child: Divider(color: Colors.black12)),
          _buildInfoRow('DURASI INAP', '${res['nights']} Malam (${res['arrival']} s/d ${res['departure']})'),
        ],
      ),
    );
  }

  // SOLUSI OVERFLOW 1: Bungkus Text Kanan pakai Expanded biar dia turun ke bawah kalau kepanjangan
  Widget _buildInfoRow(String label, String value, {bool isBold = false, Color? color}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
        const SizedBox(width: 15),
        Expanded(
          child: Text(
            value, 
            textAlign: TextAlign.right,
            style: TextStyle(fontSize: isBold ? 15 : 13, fontWeight: isBold ? FontWeight.w900 : FontWeight.bold, color: color ?? darkNavy)
          ),
        ),
      ],
    );
  }

  // SOLUSI OVERFLOW 2: Ubah rasio Flex biar durasi (Malam) gak sempit
  Widget _buildTableHeader() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 15),
      decoration: BoxDecoration(color: darkNavy, borderRadius: const BorderRadius.only(topLeft: Radius.circular(12), topRight: Radius.circular(12))),
      child: const Row(
        children: [
          Expanded(flex: 4, child: Text('DESKRIPSI', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w900))),
          Expanded(flex: 2, child: Text('DURASI', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w900))),
          Expanded(flex: 3, child: Text('SUBTOTAL', textAlign: TextAlign.right, style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w900))),
        ],
      ),
    );
  }

  // SOLUSI OVERFLOW 3: Samain rasio Flex dengan Header-nya
  Widget _buildItemRow(String title, String duration, int total, {String? subNote}) {
    return Padding(
      padding: const EdgeInsets.all(15),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(flex: 4, child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
            if (subNote != null) Text(subNote, style: const TextStyle(fontSize: 9, color: Colors.grey, fontStyle: FontStyle.italic)),
          ])),
          Expanded(flex: 2, child: Text(duration, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold))),
          Expanded(flex: 3, child: Text(currencyFormat.format(total), textAlign: TextAlign.right, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900))),
        ],
      ),
    );
  }

  Widget _buildTaxRow(String label, int amount) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 8),
      color: Colors.grey.shade50,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, fontStyle: FontStyle.italic)),
          Text(currencyFormat.format(amount), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
        ],
      ),
    );
  }

  Widget _buildPaymentSection(int grandTotal, int change) {
    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('UANG DIBAYARKAN', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey)),
                  const SizedBox(height: 10),
                  TextField(
                    controller: _paidAmountCtrl,
                    keyboardType: TextInputType.number,
                    style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.green),
                    // 🔥 INI YANG BIKIN KEMBALIAN OTOMATIS JALAN 🔥
                    onChanged: (val) {
                      setState(() {
                        paidAmount = int.tryParse(val.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
                      });
                    },
                    decoration: _inputStyle('Rp 0', isPaid: true),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 15),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('KEMBALIAN', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey)),
                  const SizedBox(height: 10),
                  Container(
                    height: 55, width: double.infinity,
                    alignment: Alignment.centerRight,
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade300)),
                    child: Text(
                      currencyFormat.format(change < 0 ? 0 : change),
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: change < 0 ? Colors.red : darkNavy),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ],
    );
  }

  InputDecoration _inputStyle(String hint, {bool isPaid = false}) {
    return InputDecoration(
      hintText: hint,
      filled: true, fillColor: isPaid ? Colors.green.withOpacity(0.05) : Colors.white,
      contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 15),
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade300)),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: isPaid ? Colors.green : primaryMaroon, width: 2)),
    );
  }
}