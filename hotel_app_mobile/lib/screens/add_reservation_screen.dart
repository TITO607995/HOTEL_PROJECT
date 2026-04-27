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
  final Color bgGrey = const Color(0xFFFAFBFC);

  // --- CONTROLLER LAMA ---
  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController(); 
  
  // --- CONTROLLER BARU ---
  final _flightCtrl = TextEditingController();
  final _idCardCtrl = TextEditingController();
  final _numGuestsCtrl = TextEditingController(text: '1');
  final _countryCtrl = TextEditingController(text: 'Indonesia');
  final _cityCtrl = TextEditingController();
  final _pobCtrl = TextEditingController();
  final _remarksCtrl = TextEditingController();

  DateTime? _arrivalDate;
  DateTime? _departureDate;
  
  List<dynamic> _availableRooms = [];
  String? _selectedRoomId;
  
  String _selectedReservationType = 'non-guaranteed';
  String _selectedPaymentMethod = 'Cash';
  String _selectedPickup = 'None';
  String _selectedStatus = 'TENTATIVE'; 

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _loadAvailableRooms();
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _phoneCtrl.dispose();
    _flightCtrl.dispose();
    _idCardCtrl.dispose();
    _numGuestsCtrl.dispose();
    _countryCtrl.dispose();
    _cityCtrl.dispose();
    _pobCtrl.dispose();
    _remarksCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadAvailableRooms() async {
    final rooms = await RoomService.fetchRooms();
    setState(() {
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
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Cek kembali data wajib & tanggal!'), backgroundColor: Colors.red));
      return;
    }

    setState(() => _isLoading = true);

    final data = {
      'room_id': _selectedRoomId,
      'arrival_date': DateFormat('yyyy-MM-dd').format(_arrivalDate!),
      'departure_date': DateFormat('yyyy-MM-dd').format(_departureDate!),
      'reservation_type': _selectedReservationType,
      'payment_method': _selectedPaymentMethod,
      'flight_detail': _flightCtrl.text,
      'pickup_service': _selectedPickup,
      'reservation_status': _selectedStatus,
      'guest_name': _nameCtrl.text,
      'id_card': _idCardCtrl.text,
      'num_guests': _numGuestsCtrl.text,
      'phone': _phoneCtrl.text,
      'email': _emailCtrl.text,
      'country': _countryCtrl.text,
      'city': _cityCtrl.text,
      'place_of_birth': _pobCtrl.text,
      'remarks': _remarksCtrl.text,
    };

    bool success = await ReservationService.createReservation(data);

    setState(() => _isLoading = false);

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Reservasi Berhasil Dibuat! 🎉'), backgroundColor: Colors.green));
      Navigator.pop(context, true); 
    } else {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal menyimpan reservasi. Cek API.'), backgroundColor: Colors.red));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgGrey,
      appBar: AppBar(
        title: const Text('New Reservation', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: Color(0xFF1B212D))),
        backgroundColor: bgGrey,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
        physics: const BouncingScrollPhysics(),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildDatesCard(),
              const SizedBox(height: 20),

              _buildSectionCard(
                title: 'Room Configuration',
                icon: Icons.bed,
                children: [
                  _buildLabel('SELECT AVAILABLE ROOM *'),
                  DropdownButtonFormField<String>(
                    isExpanded: true, // 🔥 FIX OVERFLOW DI SINI
                    decoration: _inputStyle(),
                    hint: const Text('— Search Room Number —', style: TextStyle(fontSize: 12)),
                    value: _selectedRoomId,
                    items: _availableRooms.map((r) {
                      return DropdownMenuItem<String>(
                        value: r['id'].toString(), 
                        child: Text('Kamar ${r['room_number']} - ${r['type']}', style: const TextStyle(fontSize: 12), overflow: TextOverflow.ellipsis),
                      );
                    }).toList(),
                    onChanged: (val) => setState(() => _selectedRoomId = val),
                    validator: (v) => v == null ? 'Pilih kamar' : null,
                  ),
                  const SizedBox(height: 15),
                  Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildLabel('RESERVATION TYPE'),
                            DropdownButtonFormField<String>(
                              isExpanded: true, // 🔥 FIX OVERFLOW DI SINI
                              decoration: _inputStyle(),
                              value: _selectedReservationType,
                              items: const [
                                DropdownMenuItem(value: 'non-guaranteed', child: Text('Non-Guaranteed', style: TextStyle(fontSize: 11), overflow: TextOverflow.ellipsis)),
                                DropdownMenuItem(value: 'guaranteed', child: Text('Guaranteed', style: TextStyle(fontSize: 11), overflow: TextOverflow.ellipsis)),
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
                            _buildLabel('PAYMENT METHOD'),
                            DropdownButtonFormField<String>(
                              isExpanded: true, // 🔥 FIX OVERFLOW DI SINI
                              decoration: _inputStyle(),
                              value: _selectedPaymentMethod,
                              items: const [
                                DropdownMenuItem(value: 'Cash', child: Text('Cash', style: TextStyle(fontSize: 11), overflow: TextOverflow.ellipsis)),
                                DropdownMenuItem(value: 'Transfer', child: Text('Transfer', style: TextStyle(fontSize: 11), overflow: TextOverflow.ellipsis)),
                                DropdownMenuItem(value: 'Credit Card', child: Text('Credit Card', style: TextStyle(fontSize: 11), overflow: TextOverflow.ellipsis)),
                              ],
                              onChanged: (val) => setState(() => _selectedPaymentMethod = val!),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 20),

              _buildSectionCard(
                title: 'Arrival Information',
                icon: Icons.flight_land,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildLabel('FLIGHT DETAIL & TIME'),
                            TextFormField(controller: _flightCtrl, style: const TextStyle(fontSize: 12), decoration: _inputStyle(hint: 'e.g.: GA-123 / 14:00')),
                          ],
                        ),
                      ),
                      const SizedBox(width: 15),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildLabel('PICKUP SERVICE'),
                            DropdownButtonFormField<String>(
                              isExpanded: true, // 🔥 FIX OVERFLOW DI SINI
                              decoration: _inputStyle(),
                              value: _selectedPickup,
                              items: const [
                                DropdownMenuItem(value: 'None', child: Text('None', style: TextStyle(fontSize: 11), overflow: TextOverflow.ellipsis)),
                                DropdownMenuItem(value: 'Airport', child: Text('Airport', style: TextStyle(fontSize: 11), overflow: TextOverflow.ellipsis)),
                                DropdownMenuItem(value: 'Train Station', child: Text('Train Station', style: TextStyle(fontSize: 11), overflow: TextOverflow.ellipsis)),
                              ],
                              onChanged: (val) => setState(() => _selectedPickup = val!),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 20),

              _buildSectionCard(
                title: 'Guest Data',
                icon: Icons.person,
                children: [
                  _buildLabel('RESERVATION STATUS'),
                  _buildStatusToggle(), 
                  const SizedBox(height: 20),

                  _buildLabel('GUEST FULL NAME *'),
                  TextFormField(controller: _nameCtrl, style: const TextStyle(fontSize: 12), decoration: _inputStyle(hint: 'Input name as per ID/Passport...'), validator: (v) => v!.isEmpty ? 'Wajib diisi' : null),
                  const SizedBox(height: 15),

                  _buildLabel('ID CARD / PASSPORT NUMBER'),
                  TextFormField(controller: _idCardCtrl, style: const TextStyle(fontSize: 12), decoration: _inputStyle(hint: '3171xxxxxxxxxxxx')),
                  const SizedBox(height: 15),

                  Row(
                    children: [
                      Expanded(flex: 1, child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        _buildLabel('GUESTS'),
                        TextFormField(controller: _numGuestsCtrl, style: const TextStyle(fontSize: 12), keyboardType: TextInputType.number, decoration: _inputStyle()),
                      ])),
                      const SizedBox(width: 15),
                      Expanded(flex: 2, child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        _buildLabel('WHATSAPP NUMBER *'),
                        TextFormField(controller: _phoneCtrl, style: const TextStyle(fontSize: 12), keyboardType: TextInputType.phone, decoration: _inputStyle(hint: '08...'), validator: (v) => v!.isEmpty ? 'Wajib diisi' : null),
                      ])),
                    ],
                  ),
                  const SizedBox(height: 15),

                  _buildLabel('EMAIL ADDRESS'),
                  TextFormField(controller: _emailCtrl, style: const TextStyle(fontSize: 12), keyboardType: TextInputType.emailAddress, decoration: _inputStyle(hint: 'guest@example.com')),
                  const SizedBox(height: 15),

                  Row(
                    children: [
                      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        _buildLabel('COUNTRY'),
                        TextFormField(controller: _countryCtrl, style: const TextStyle(fontSize: 12), decoration: _inputStyle()),
                      ])),
                      const SizedBox(width: 15),
                      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        _buildLabel('CITY'),
                        TextFormField(controller: _cityCtrl, style: const TextStyle(fontSize: 12), decoration: _inputStyle(hint: 'Jakarta')),
                      ])),
                    ],
                  ),
                  const SizedBox(height: 15),

                  _buildLabel('PLACE OF BIRTH'),
                  TextFormField(controller: _pobCtrl, style: const TextStyle(fontSize: 12), decoration: _inputStyle(hint: 'City of Birth')),
                  const SizedBox(height: 15),

                  _buildLabel('REMARKS (NOTES)'),
                  TextFormField(controller: _remarksCtrl, maxLines: 3, style: const TextStyle(fontSize: 12), decoration: _inputStyle(hint: 'e.g.: High floor, twin bed requested...')),
                ],
              ),
              const SizedBox(height: 30),

              SizedBox(
                width: double.infinity, height: 55,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: primaryMaroon, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                  onPressed: _isLoading ? null : _submitForm,
                  child: _isLoading 
                      ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 3)) 
                      : const Text('SAVE RESERVATION', style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.w900, letterSpacing: 1)),
                ),
              ),
              const SizedBox(height: 50),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDatesCard() {
    return Row(
      children: [
        Expanded(
          child: Container(
            padding: const EdgeInsets.all(15),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildLabel('CHECK IN *', color: primaryMaroon),
                InkWell(
                  onTap: () => _pickDate(true),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(_arrivalDate == null ? 'DD/MM/YYYY' : DateFormat('dd/MM/yyyy').format(_arrivalDate!), style: TextStyle(color: _arrivalDate == null ? Colors.grey : Colors.black, fontSize: 12, fontWeight: FontWeight.bold)),
                      const Icon(Icons.calendar_today, size: 14, color: Colors.grey),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(width: 15),
        Expanded(
          child: Container(
            padding: const EdgeInsets.all(15),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildLabel('CHECK OUT *', color: primaryMaroon),
                InkWell(
                  onTap: () => _pickDate(false),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(_departureDate == null ? 'DD/MM/YYYY' : DateFormat('dd/MM/yyyy').format(_departureDate!), style: TextStyle(color: _departureDate == null ? Colors.grey : Colors.black, fontSize: 12, fontWeight: FontWeight.bold)),
                      const Icon(Icons.calendar_today, size: 14, color: Colors.grey),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildSectionCard({required String title, required IconData icon, required List<Widget> children}) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: primaryMaroon.withOpacity(0.1), shape: BoxShape.circle), child: Icon(icon, color: primaryMaroon, size: 18)),
              const SizedBox(width: 10),
              Text(title, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: Color(0xFF1B212D))),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 15), child: Divider(color: Colors.black12, height: 1)),
          ...children,
        ],
      ),
    );
  }

  Widget _buildStatusToggle() {
    return Container(
      height: 40,
      decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(20)),
      child: Row(
        children: [
          Expanded(
            child: GestureDetector(
              onTap: () => setState(() => _selectedStatus = 'TENTATIVE'),
              child: Container(
                decoration: BoxDecoration(color: _selectedStatus == 'TENTATIVE' ? primaryMaroon : Colors.transparent, borderRadius: BorderRadius.circular(20)),
                alignment: Alignment.center,
                child: Text('TENTATIVE', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: _selectedStatus == 'TENTATIVE' ? Colors.white : Colors.grey)),
              ),
            ),
          ),
          Expanded(
            child: GestureDetector(
              onTap: () => setState(() => _selectedStatus = 'CONFIRMED'),
              child: Container(
                decoration: BoxDecoration(color: _selectedStatus == 'CONFIRMED' ? Colors.green.shade700 : Colors.transparent, borderRadius: BorderRadius.circular(20)),
                alignment: Alignment.center,
                child: Text('CONFIRMED', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: _selectedStatus == 'CONFIRMED' ? Colors.white : Colors.grey)),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLabel(String text, {Color? color}) => Padding(
    padding: const EdgeInsets.only(bottom: 6), 
    child: Text(text, style: TextStyle(fontWeight: FontWeight.w900, fontSize: 8, color: color ?? Colors.grey, letterSpacing: 1.0))
  );

  InputDecoration _inputStyle({String? hint}) => InputDecoration(
    hintText: hint,
    hintStyle: const TextStyle(color: Colors.grey, fontSize: 12, fontStyle: FontStyle.italic),
    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
    filled: true, fillColor: Colors.transparent, 
    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: Colors.grey.shade300)),
    focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: primaryMaroon, width: 2)),
    errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: const BorderSide(color: Colors.red)),
    focusedErrorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: const BorderSide(color: Colors.red)),
  );
}