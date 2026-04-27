import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/role_service.dart';

class RoleScreen extends StatefulWidget {
  const RoleScreen({super.key});

  @override
  State<RoleScreen> createState() => _RoleScreenState();
}

class _RoleScreenState extends State<RoleScreen> {
  final Color primaryMaroon = const Color(0xFF800000);
  final Color bgGrey = const Color(0xFFFAFBFC);
  
  List<dynamic> roles = [];
  List<dynamic> allMenus = [];
  bool isLoading = true;

  bool canCreateRole = false;
  bool canEditRole = false;
  bool canDeleteRole = false;

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
      canCreateRole = isSuper || allowedMenus.contains('ROLE & AKSES - CREATE');
      canEditRole = isSuper || allowedMenus.contains('ROLE & AKSES - EDIT');
      canDeleteRole = isSuper || allowedMenus.contains('ROLE & AKSES - DELETE');
    });

    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => isLoading = true);
    final data = await RoleService.fetchRolesAndMenus();
    if (mounted && data != null) {
      setState(() { 
        roles = data['roles']; 
        allMenus = data['menus']; 
        isLoading = false; 
      });
    } else {
      setState(() => isLoading = false);
    }
  }

  void _showRoleDialog({dynamic existingRole}) {
    final nameCtrl = TextEditingController(text: existingRole != null ? existingRole['name'] : '');
    List<int> selectedMenuIds = [];
    
    if (existingRole != null && existingRole['menus'] != null) {
      selectedMenuIds = (existingRole['menus'] as List).map((m) => m['id'] as int).toList();
    }

    // ==========================================
    // LOGIKA PENGELOMPOKAN MENU YANG LEBIH AMAN
    // ==========================================
    Map<String, Map<String, dynamic>> groupedMenus = {};
    
    for (var menu in allMenus) {
      String name = menu['name'];
      if (name.contains(' - ')) {
        // Ini adalah sub-menu / CRUD action
        var parts = name.split(' - ');
        var baseName = parts[0].trim();
        groupedMenus.putIfAbsent(baseName, () => {'base': null, 'actions': []});
        groupedMenus[baseName]!['actions'].add(menu);
      } else {
        // Ini adalah menu utama
        var baseName = name.trim();
        groupedMenus.putIfAbsent(baseName, () => {'base': null, 'actions': []});
        groupedMenus[baseName]!['base'] = menu;
      }
    }

    // OTOMATIS CENTANG DASHBOARD & DATA TAMU (Jika baseMenu ada)
    for (var baseName in groupedMenus.keys) {
      if (baseName.toLowerCase() == 'dashboard' || baseName.toLowerCase() == 'data tamu') {
        var baseMenu = groupedMenus[baseName]!['base'];
        if (baseMenu != null && !selectedMenuIds.contains(baseMenu['id'])) {
          selectedMenuIds.add(baseMenu['id']);
        }
      }
    }

    showDialog(
      context: context,
      builder: (dialogContext) => StatefulBuilder(
        builder: (context, setStateLocal) => AlertDialog(
          title: Text(existingRole == null ? 'Tambah Role Baru' : 'Edit Role', style: const TextStyle(fontWeight: FontWeight.w900)),
          content: SizedBox(
            width: double.maxFinite,
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  TextField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Nama Role (Contoh: KASIR)')),
                  const SizedBox(height: 20),
                  const Text('Pengaturan Hak Akses:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.grey)),
                  const Divider(),
                  
                  // ==========================================
                  // RENDER MENU GROUPING
                  // ==========================================
                  ...groupedMenus.entries.map((entry) {
                    String groupName = entry.key;
                    var baseMenu = entry.value['base'];
                    List<dynamic> actions = entry.value['actions'];

                    // 🔥 FIX: Jangan skip! Kalau baseMenu null, kita akali dengan id bayangan biar minimal nama grupnya tetep muncul.
                    // Tapi pastinya database lu harusnya punya menu utamanya.
                    bool isMandatory = groupName.toLowerCase() == 'dashboard' || groupName.toLowerCase() == 'data tamu';
                    
                    // Cek apakah base dicentang ATAU (kalau base gak ada) ada action yang dicentang
                    bool isBaseChecked = false;
                    if (baseMenu != null) {
                       isBaseChecked = selectedMenuIds.contains(baseMenu['id']);
                    } else {
                       // Jika baseMenu hilang, kita anggap checked jika ada salah satu anak yang checked
                       isBaseChecked = actions.any((a) => selectedMenuIds.contains(a['id']));
                    }

                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // 1. BARIS MENU UTAMA
                        Container(
                          color: isBaseChecked ? primaryMaroon.withOpacity(0.02) : Colors.transparent,
                          child: CheckboxListTile(
                            title: Text(groupName, style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, color: isBaseChecked ? primaryMaroon : Colors.black87)),
                            subtitle: isMandatory ? const Text('Otomatis diaktifkan (Paten)', style: TextStyle(fontSize: 10, color: Colors.green)) : null,
                            value: isBaseChecked,
                            activeColor: primaryMaroon,
                            contentPadding: EdgeInsets.zero,
                            dense: true,
                            onChanged: isMandatory ? null : (val) {
                              setStateLocal(() {
                                if (val == true) {
                                  if(baseMenu != null) selectedMenuIds.add(baseMenu['id']);
                                  // Auto check semua anak (opsional)
                                  for(var act in actions) {
                                    if(!selectedMenuIds.contains(act['id'])) selectedMenuIds.add(act['id']);
                                  }
                                } else {
                                  // MATIKAN MENU UTAMA = MATIKAN SEMUA SUB MENU CRUD
                                  if(baseMenu != null) selectedMenuIds.remove(baseMenu['id']);
                                  for (var action in actions) {
                                    selectedMenuIds.remove(action['id']);
                                  }
                                }
                              });
                            },
                          ),
                        ),

                        // 2. MUNCULKAN OPSI CRUD
                        if (actions.isNotEmpty)
                          Padding(
                            padding: const EdgeInsets.only(left: 35, bottom: 10, right: 10),
                            child: Wrap(
                              spacing: 8,
                              runSpacing: 8,
                              children: actions.map((action) {
                                bool isActionChecked = selectedMenuIds.contains(action['id']);
                                String actionLabel = action['name'].split(' - ').last.toUpperCase(); // Ambil kata Edit, Create, dll
                                
                                return FilterChip(
                                  label: Text(actionLabel, style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: isActionChecked ? Colors.white : Colors.grey.shade700)),
                                  selected: isActionChecked,
                                  selectedColor: primaryMaroon,
                                  checkmarkColor: Colors.white,
                                  backgroundColor: Colors.grey.shade200,
                                  padding: EdgeInsets.zero,
                                  onSelected: (val) {
                                    setStateLocal(() {
                                      if (val) {
                                          selectedMenuIds.add(action['id']);
                                          // Auto nyalain parent kalau anaknya dicentang
                                          if (baseMenu != null && !selectedMenuIds.contains(baseMenu['id'])) {
                                              selectedMenuIds.add(baseMenu['id']);
                                          }
                                      } else {
                                          selectedMenuIds.remove(action['id']);
                                      }
                                    });
                                  },
                                );
                              }).toList(),
                            ),
                          ),
                        const Divider(height: 1, color: Colors.black12),
                      ],
                    );
                  }),
                ],
              ),
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(dialogContext), child: const Text('Batal')),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: primaryMaroon),
              onPressed: () async {
                if (nameCtrl.text.isEmpty) return;
                Navigator.pop(dialogContext);
                setState(() => isLoading = true);
                
                bool success;
                if (existingRole == null) {
                  success = await RoleService.createRole(nameCtrl.text, selectedMenuIds);
                } else {
                  success = await RoleService.updateRole(existingRole['id'], nameCtrl.text, selectedMenuIds);
                }
                
                if (!mounted) return;
                ScaffoldMessenger.of(this.context).showSnackBar(SnackBar(content: Text(success ? 'Role berhasil disimpan!' : 'Gagal menyimpan role!')));
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
        title: const Text('Role Management', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF1B212D))),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
        actions: [
          if (canCreateRole)
            IconButton(icon: Icon(Icons.add_moderator, color: primaryMaroon), onPressed: () => _showRoleDialog())
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
                itemCount: roles.length,
                itemBuilder: (context, index) {
                  final role = roles[index];
                  bool isSuperAdmin = role['name'].toString().toUpperCase() == 'SUPERADMIN';

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
                            Text(role['name'].toString().toUpperCase(), style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                            isSuperAdmin 
                                ? Container(padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4), decoration: BoxDecoration(color: Colors.purple, borderRadius: BorderRadius.circular(8)), child: const Text('PATEN', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold)))
                                : Row(
                                    children: [
                                      if (canEditRole)
                                        IconButton(icon: const Icon(Icons.edit, color: Colors.blue, size: 20), onPressed: () => _showRoleDialog(existingRole: role)),
                                      if (canDeleteRole)
                                        IconButton(icon: const Icon(Icons.delete_outline, color: Colors.red, size: 20), onPressed: () async {
                                          bool confirm = await showDialog(context: context, builder: (c) => AlertDialog(
                                            title: const Text('Hapus Role?'), actions: [
                                              TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Batal')),
                                              ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: Colors.red), onPressed: () => Navigator.pop(c, true), child: const Text('Hapus', style: TextStyle(color: Colors.white)))
                                          ]));
                                          if (confirm == true) {
                                            setState(() => isLoading = true);
                                            await RoleService.deleteRole(role['id']);
                                            _loadData();
                                          }
                                        }),
                                    ],
                                  )
                          ],
                        ),
                        const Divider(height: 20),
                        const Text('Akses Terdaftar:', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                        const SizedBox(height: 8),
                        
                        Wrap(
                          spacing: 5, runSpacing: 5,
                          children: isSuperAdmin 
                            ? [Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4), decoration: BoxDecoration(color: Colors.purple.shade100, borderRadius: BorderRadius.circular(5)), child: const Text('SEMUA MENU AKSES', style: TextStyle(fontSize: 9, color: Colors.purple, fontWeight: FontWeight.bold)))]
                            : (role['menus'] as List).isEmpty 
                                ? [const Text('Belum ada akses menu', style: TextStyle(fontSize: 11, color: Colors.red, fontStyle: FontStyle.italic))]
                                : (role['menus'] as List).map((m) {
                                    bool isAction = m['name'].toString().contains(' - ');
                                    String label = isAction ? m['name'].split(' - ').last.toUpperCase() : m['name'];
                                    
                                    return Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: isAction ? primaryMaroon.withOpacity(0.1) : Colors.grey.shade100, 
                                        borderRadius: BorderRadius.circular(5), 
                                        border: Border.all(color: isAction ? primaryMaroon.withOpacity(0.3) : Colors.grey.shade300)
                                      ),
                                      child: Text(label, style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: isAction ? primaryMaroon : Colors.black87)),
                                    );
                                  }).toList(),
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