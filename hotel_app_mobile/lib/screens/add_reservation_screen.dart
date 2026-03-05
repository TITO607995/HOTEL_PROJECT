import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/reservation_service.dart';
import '../services/room_service.dart';

class AddReservationScreen extends StatefulWidget {
  const AddReservationScreen({super.key});

  @override
  State<AddReservationScreen> createState() => _AddReservationScreenState();
}

class _AddReservationScreenState extends State<AddReservationScreen> {
  final _formKey = GlobalKey<FormState>();
  final Color primaryMaroon = const Color(0xFF800000);

  // Controller Text
  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  
  DateTime? _arrivalDate;
  DateTime? _departureDate;
  
  List<dynamic> _availableRooms = [];
  String? _selectedRoomId;
  
  // Variabel Baru Untuk Dropdown
  String _selectedReservationType = 'non-guaranteed'; // Default
  String _selectedPaymentMethod = 'Cash';             // Default

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _loadAvailableRooms();
  }

  Future<void> _loadAvailableRooms() async {
    final rooms = await RoomService.fetchRooms();
    setState(() {
      // Pastikan ambil status kamar yang available saja
      _availableRooms = rooms!.where((r) => r['status_label'] == 'AVAILABLE').toList();
    });
  }

  Future<void> _pickDate(bool isArrival) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) => Theme(
        data: ThemeData.light().copyWith(colorScheme: ColorScheme.light(primary: primaryMaroon)),
        child: child!,
      ),
    );

    if (picked != null) {
      setState(() {
        if (isArrival) _arrivalDate = picked;
        else _departureDate = picked;
      });
    }
  }

  Future<void> _submitForm() async {
    if (!_formKey.currentState!.validate() || _arrivalDate == null || _departureDate == null || _selectedRoomId == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Lengkapi semua data & tanggal!'), backgroundColor: Colors.red));
      return;
    }

    setState(() => _isLoading = true);

    // Data yang akan dikirim ke API
    final data = {
      'room_id': _selectedRoomId,
      'guest_name': _nameCtrl.text,
      'email': _emailCtrl.text,
      'phone': _phoneCtrl.text,
      'arrival_date': DateFormat('yyyy-MM-dd').format(_arrivalDate!),
      'departure_date': DateFormat('yyyy-MM-dd').format(_departureDate!),
      'payment_method': _selectedPaymentMethod,       // Ambil dari Dropdown
      'reservation_type': _selectedReservationType,   // Ambil dari Dropdown
    };

    bool success = await ReservationService.createReservation(data);

    setState(() => _isLoading = false);

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Reservasi Berhasil! 🎉'), backgroundColor: Colors.green));
      Navigator.pop(context, true); 
    } else {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal menyimpan reservasi'), backgroundColor: Colors.red));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Buat Reservasi', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        physics: const BouncingScrollPhysics(),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildLabel('PILIH KAMAR AVAILABLE'),
              DropdownButtonFormField<String>(
                decoration: _inputStyle(),
                hint: const Text('Pilih Kamar...'),
                value: _selectedRoomId,
                items: _availableRooms.map((r) {
                  return DropdownMenuItem<String>(
                    value: r['id'].toString(), 
                    child: Text('Kamar ${r['room_number']} - ${r['type']}'),
                  );
                }).toList(),
                onChanged: (val) => setState(() => _selectedRoomId = val),
              ),
              const SizedBox(height: 20),

              _buildLabel('NAMA TAMU'),
              TextFormField(controller: _nameCtrl, decoration: _inputStyle(hint: 'Masukkan nama lengkap'), validator: (v) => v!.isEmpty ? 'Wajib diisi' : null),
              const SizedBox(height: 20),

              _buildLabel('EMAIL'),
              TextFormField(controller: _emailCtrl, keyboardType: TextInputType.emailAddress, decoration: _inputStyle(hint: 'email@contoh.com'), validator: (v) => v!.isEmpty ? 'Wajib diisi' : null),
              const SizedBox(height: 20),

              _buildLabel('NOMOR TELEPON'),
              TextFormField(controller: _phoneCtrl, keyboardType: TextInputType.phone, decoration: _inputStyle(hint: '0812xxxxxx'), validator: (v) => v!.isEmpty ? 'Wajib diisi' : null),
              const SizedBox(height: 20),

              // TANGGAL CHECK-IN & CHECK-OUT SEJAJAR
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildLabel('CHECK-IN'),
                        InkWell(
                          onTap: () => _pickDate(true),
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 15),
                            decoration: BoxDecoration(color: Colors.grey.shade50, border: Border.all(color: Colors.grey.shade200), borderRadius: BorderRadius.circular(15)),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(_arrivalDate == null ? 'Pilih Tanggal' : DateFormat('dd MMM yyyy').format(_arrivalDate!), style: TextStyle(color: _arrivalDate == null ? Colors.grey : Colors.black, fontSize: 13, fontWeight: FontWeight.bold)),
                                const Icon(Icons.calendar_today, size: 16, color: Colors.grey),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildLabel('CHECK-OUT'),
                        InkWell(
                          onTap: () => _pickDate(false),
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 15),
                            decoration: BoxDecoration(color: Colors.grey.shade50, border: Border.all(color: Colors.grey.shade200), borderRadius: BorderRadius.circular(15)),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(_departureDate == null ? 'Pilih Tanggal' : DateFormat('dd MMM yyyy').format(_departureDate!), style: TextStyle(color: _departureDate == null ? Colors.grey : Colors.black, fontSize: 13, fontWeight: FontWeight.bold)),
                                const Icon(Icons.calendar_today, size: 16, color: Colors.grey),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),

              // ==========================================
              // DROPDOWN BARU: TIPE RESERVASI & PEMBAYARAN
              // ==========================================
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildLabel('TIPE RESERVASI'),
                        DropdownButtonFormField<String>(
                          decoration: _inputStyle(),
                          icon: Icon(Icons.keyboard_arrow_down, color: primaryMaroon), // Panah merah ala foto
                          value: _selectedReservationType,
                          items: const [
                            DropdownMenuItem(value: 'non-guaranteed', child: Text('Non-Guaranteed', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold))),
                            DropdownMenuItem(value: 'guaranteed', child: Text('Guaranteed', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold))),
                          ],
                          onChanged: (val) => setState(() => _selectedReservationType = val!),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildLabel('METODE PEMBAYARAN'),
                        DropdownButtonFormField<String>(
                          decoration: _inputStyle(),
                          icon: Icon(Icons.keyboard_arrow_down, color: primaryMaroon), // Panah merah ala foto
                          value: _selectedPaymentMethod,
                          items: const [
                            DropdownMenuItem(value: 'Cash', child: Text('Tunai (Cash)', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold))),
                            DropdownMenuItem(value: 'Transfer', child: Text('Transfer Bank', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold))),
                            DropdownMenuItem(value: 'Credit Card', child: Text('Kartu Kredit', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold))),
                          ],
                          onChanged: (val) => setState(() => _selectedPaymentMethod = val!),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 40),

              // TOMBOL SIMPAN
              SizedBox(
                width: double.infinity,
                height: 55,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: primaryMaroon, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                  onPressed: _isLoading ? null : _submitForm,
                  child: _isLoading 
                      ? const CircularProgressIndicator(color: Colors.white) 
                      : const Text('Simpan Reservasi', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              )
            ],
          ),
        ),
      ),
    );
  }

  // Desain Label (kecil, abu-abu, bold)
  Widget _buildLabel(String text) => Padding(
    padding: const EdgeInsets.only(bottom: 8), 
    child: Text(text, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 10, color: Colors.grey, letterSpacing: 1.0))
  );

  // Desain Kotak Input (Rounded soft grey seperti gambar)
  InputDecoration _inputStyle({String? hint}) => InputDecoration(
    hintText: hint,
    hintStyle: const TextStyle(color: Colors.grey, fontSize: 13),
    contentPadding: const EdgeInsets.symmetric(horizontal: 15, vertical: 15),
    filled: true,
    fillColor: Colors.grey.shade50, // Latar abu-abu sangat muda
    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Colors.grey.shade200)),
    focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: primaryMaroon, width: 2)),
    errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: const BorderSide(color: Colors.red)),
    focusedErrorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: const BorderSide(color: Colors.red)),
  );
}