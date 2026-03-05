import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/user_service.dart';

class UserScreen extends StatefulWidget {
  const UserScreen({super.key});

  @override
  State<UserScreen> createState() => _UserScreenState();
}

class _UserScreenState extends State<UserScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFFAFBFC);
  
  List<dynamic> users = [];
  List<dynamic> dynamicRoles = []; 
  bool isLoading = true;

  // ==========================================
  // VARIABEL CRUD HAK AKSES 🔥
  // ==========================================
  bool canCreateUser = false;
  bool canEditUser = false;
  bool canDeleteUser = false;

  @override
  void initState() {
    super.initState();
    _checkPermissionsAndLoad();
  }

  Future<void> _checkPermissionsAndLoad() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    bool isSuper = prefs.getBool('is_superadmin') ?? false;
    List<String> allowedMenus = prefs.getStringList('allowed_menus') ?? [];
    
    setState(() {
      canCreateUser = isSuper || allowedMenus.contains('MANAJEMEN KARYAWAN - CREATE');
      canEditUser = isSuper || allowedMenus.contains('MANAJEMEN KARYAWAN - EDIT');
      canDeleteUser = isSuper || allowedMenus.contains('MANAJEMEN KARYAWAN - DELETE');
    });

    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => isLoading = true);
    final fetchedUsers = await UserService.fetchUsers();
    final fetchedRoles = await UserService.fetchRoles();
    if (mounted) {
      setState(() { 
        users = fetchedUsers; 
        dynamicRoles = fetchedRoles;
        isLoading = false; 
      });
    }
  }

  Color _getRoleColor(String roleName) {
    String role = roleName.toLowerCase();
    if (role == 'superadmin') return Colors.purple;
    if (role == 'admin') return Colors.blue;
    if (role == 'resepsionis') return Colors.green;
    return Colors.grey.shade700;
  }

  void _showAddUserDialog() {
    final nameCtrl = TextEditingController();
    final emailCtrl = TextEditingController();
    final passwordCtrl = TextEditingController();
    
    final availableRoles = dynamicRoles.where((r) => r['name'].toString().toUpperCase() != 'SUPERADMIN').toList();
    int? selectedRoleId = availableRoles.isNotEmpty ? availableRoles.first['id'] : null;

    showDialog(
      context: context,
      builder: (dialogContext) => StatefulBuilder(
        builder: (context, setStateLocal) => AlertDialog(
          title: const Text('Tambah Karyawan', style: TextStyle(fontWeight: FontWeight.w900)),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Nama Lengkap')),
                TextField(controller: emailCtrl, decoration: const InputDecoration(labelText: 'Email')),
                TextField(controller: passwordCtrl, decoration: const InputDecoration(labelText: 'Password'), obscureText: true),
                const SizedBox(height: 15),
                DropdownButtonFormField<int>(
                  value: selectedRoleId,
                  decoration: const InputDecoration(labelText: 'Pilih Role'),
                  items: availableRoles.map((r) => DropdownMenuItem<int>(
                    value: r['id'], 
                    child: Text(r['name'].toString().toUpperCase())
                  )).toList(),
                  onChanged: (val) => setStateLocal(() => selectedRoleId = val),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(dialogContext), child: const Text('Batal')),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: primaryMaroon),
              onPressed: () async {
                if (selectedRoleId == null) return;
                Navigator.pop(dialogContext);
                setState(() => isLoading = true);
                bool success = await UserService.createUser({
                  'name': nameCtrl.text, 'email': emailCtrl.text, 'password': passwordCtrl.text, 'role_id': selectedRoleId
                });
                if (!mounted) return;
                ScaffoldMessenger.of(this.context).showSnackBar(SnackBar(content: Text(success ? 'Karyawan ditambahkan!' : 'Gagal menambah karyawan!')));
                _loadData();
              },
              child: const Text('Simpan', style: TextStyle(color: Colors.white)),
            )
          ],
        )
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgGrey,
      appBar: AppBar(
        title: const Text('Manajemen Karyawan', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF1B212D))),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
        actions: [
          // MUNCUL HANYA JIKA PUNYA HAK CREATE
          if (canCreateUser)
            IconButton(icon: Icon(Icons.person_add, color: primaryMaroon), onPressed: _showAddUserDialog)
        ],
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: primaryMaroon))
          : RefreshIndicator(
              color: primaryMaroon,
              onRefresh: _loadData,
              child: ListView.builder(
                padding: const EdgeInsets.all(20),
                physics: const AlwaysScrollableScrollPhysics(),
                itemCount: users.length,
                itemBuilder: (context, index) {
                  final user = users[index];
                  final roleObj = user['role']; 
                  final String roleName = roleObj != null ? roleObj['name'].toString().toUpperCase() : 'NO ROLE';
                  final int? roleId = user['role_id'];

                  bool isSuperAdmin = roleName == 'SUPERADMIN' || user['email'] == 'admin@hotelsig.com';
                  final assignableRoles = dynamicRoles.where((r) => r['name'].toString().toUpperCase() != 'SUPERADMIN').toList();

                  return Container(
                    margin: const EdgeInsets.only(bottom: 15),
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(color: isSuperAdmin ? Colors.purple.shade50 : Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: isSuperAdmin ? Colors.purple.shade200 : Colors.grey.shade200)),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(user['name'] ?? 'Tanpa Nama', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                            
                            isSuperAdmin 
                                ? const SizedBox(height: 48) 
                                : canDeleteUser // MUNCUL HANYA JIKA PUNYA HAK DELETE
                                    ? IconButton(
                                        icon: const Icon(Icons.delete_outline, color: Colors.red, size: 20),
                                        onPressed: () async {
                                          bool confirm = await showDialog(context: context, builder: (c) => AlertDialog(
                                            title: const Text('Hapus Karyawan?'), actions: [
                                              TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Batal')),
                                              ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: Colors.red), onPressed: () => Navigator.pop(c, true), child: const Text('Hapus', style: TextStyle(color: Colors.white)))
                                          ]));
                                          if (confirm == true) {
                                            setState(() => isLoading = true);
                                            await UserService.deleteUser(user['id']);
                                            _loadData();
                                          }
                                        },
                                      )
                                    : const SizedBox(height: 48) // Jaga spasi kalau gak bisa delete
                          ],
                        ),
                        Text(user['email'] ?? '-', style: const TextStyle(color: Colors.grey, fontSize: 12)),
                        const Divider(height: 20),
                        
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text('Hak Akses (Role):', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                            
                            isSuperAdmin 
                                ? Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                    decoration: BoxDecoration(color: Colors.purple, borderRadius: BorderRadius.circular(10)),
                                    child: Row(
                                      children: [
                                        const Icon(Icons.lock, color: Colors.white, size: 12),
                                        const SizedBox(width: 5),
                                        Text(roleName, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 10, letterSpacing: 1)),
                                      ],
                                    ),
                                  )
                                : canEditUser // MUNCULKAN DROPDOWN JIKA PUNYA HAK EDIT
                                    ? Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 10),
                                        decoration: BoxDecoration(color: _getRoleColor(roleName).withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
                                        child: DropdownButton<int>(
                                          value: roleId,
                                          underline: const SizedBox(),
                                          icon: Icon(Icons.arrow_drop_down, color: _getRoleColor(roleName)),
                                          style: TextStyle(color: _getRoleColor(roleName), fontWeight: FontWeight.w900, fontSize: 11),
                                          items: assignableRoles.map((r) => DropdownMenuItem<int>(
                                            value: r['id'], 
                                            child: Text(r['name'].toString().toUpperCase())
                                          )).toList(),
                                          onChanged: (newRoleId) async {
                                            if (newRoleId != null && newRoleId != roleId) {
                                              setState(() => isLoading = true);
                                              await UserService.updateRole(user['id'], newRoleId);
                                              _loadData();
                                            }
                                          },
                                        ),
                                      )
                                    : Container( // JIKA READ ONLY
                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                        decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(10)),
                                        child: Row(
                                          children: [
                                            const Icon(Icons.lock_outline, size: 12, color: Colors.grey),
                                            const SizedBox(width: 5),
                                            Text(roleName, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                                          ],
                                        ),
                                      )
                          ],
                        )
                      ],
                    ),
                  );
                },
              ),
            ),
    );
  }
}