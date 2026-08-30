import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../core/theme/app_theme.dart';
import '../core/utils/currency_formatter.dart';
import '../models/wallet.dart';
import '../providers/wallets_provider.dart';
import '../widgets/custom_text_field.dart';
import 'manual_transaction_screen.dart';

class WalletsScreen extends ConsumerWidget {
  const WalletsScreen({super.key});

  void _showAddWalletDialog(BuildContext context, WidgetRef ref) {
    final formKey = GlobalKey<FormState>();
    final nameController = TextEditingController();
    final accountNumberController = TextEditingController();
    final balanceController = TextEditingController(text: '0');
    WalletType selectedType = WalletType.bank;
    String selectedColor = '#00529C';

    final presetColors = [
      '#00529C', // BCA Blue
      '#005596', // BRI Blue
      '#00AED6', // GoPay Cyan
      '#4C3494', // OVO Purple
      '#118EEA', // DANA Blue
      '#EE4D2D', // Shopee Orange
      '#10B981', // Emerald Green
      '#8B5CF6', // Purple
      '#0F172A', // Slate
    ];

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(AppTheme.radiusLarge)),
          title: const Text(
            'Tambah Dompet Baru',
            style: TextStyle(fontSize: 17, fontWeight: FontWeight.w700),
          ),
          content: Form(
            key: formKey,
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CustomTextField(
                    controller: nameController,
                    label: 'Nama Dompet / Bank',
                    hint: 'e.g. BCA, GoPay, Tunai',
                    validator: (v) => v == null || v.isEmpty ? 'Nama wajib diisi' : null,
                  ),
                  const SizedBox(height: 14),
                  const Text(
                    'Tipe Akun',
                    style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 6),
                  DropdownButtonFormField<WalletType>(
                    value: selectedType,
                    items: WalletType.values.map((type) {
                      return DropdownMenuItem(
                        value: type,
                        child: Text(type.label),
                      );
                    }).toList(),
                    onChanged: (val) {
                      if (val != null) {
                        setStateDialog(() => selectedType = val);
                      }
                    },
                  ),
                  const SizedBox(height: 14),
                  CustomTextField(
                    controller: accountNumberController,
                    label: 'Nomor Rekening / HP (Opsional)',
                    hint: '1234567890',
                  ),
                  const SizedBox(height: 14),
                  CustomTextField(
                    controller: balanceController,
                    label: 'Saldo Awal (Rp)',
                    keyboardType: TextInputType.number,
                    inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                    validator: (v) => v == null || v.isEmpty ? 'Saldo wajib diisi' : null,
                  ),
                  const SizedBox(height: 14),
                  const Text(
                    'Warna Tema',
                    style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: presetColors.map((hex) {
                      final c = Color(int.parse('FF${hex.replaceAll('#', '')}', radix: 16));
                      final isSelected = selectedColor == hex;
                      return InkWell(
                        onTap: () {
                          setStateDialog(() => selectedColor = hex);
                        },
                        child: Container(
                          width: 28,
                          height: 28,
                          decoration: BoxDecoration(
                            color: c,
                            shape: BoxShape.circle,
                            border: Border.all(
                              color: isSelected ? AppTheme.textPrimary : Colors.transparent,
                              width: 2.5,
                            ),
                          ),
                          child: isSelected
                              ? const Icon(Icons.check, color: Colors.white, size: 14)
                              : null,
                        ),
                      );
                    }).toList(),
                  ),
                ],
              ),
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Batal'),
            ),
            ElevatedButton(
              onPressed: () async {
                if (!formKey.currentState!.validate()) return;
                Navigator.pop(ctx);

                final payload = {
                  'name': nameController.text.trim(),
                  'type': selectedType.value,
                  'account_number': accountNumberController.text.trim().isNotEmpty
                      ? accountNumberController.text.trim()
                      : null,
                  'balance': CurrencyFormatter.parse(balanceController.text),
                  'color_hex': selectedColor,
                  'is_active': true,
                };

                await ref.read(walletsProvider.notifier).createWallet(payload);
              },
              child: const Text('Simpan'),
            ),
          ],
        ),
      ),
    );
  }

  void _showWalletOptions(BuildContext context, Wallet wallet, WidgetRef ref) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(20),
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: AppTheme.cardBorder,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: wallet.color.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(wallet.iconData, color: wallet.color, size: 24),
                ),
                const SizedBox(width: 14),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      wallet.name,
                      style: const TextStyle(
                        color: AppTheme.textPrimary,
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    Text(
                      CurrencyFormatter.format(wallet.balance),
                      style: TextStyle(
                        color: wallet.color,
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 20),
            ListTile(
              leading: const Icon(Icons.swap_horiz_rounded, color: AppTheme.transfer),
              title: const Text('Transfer dari Dompet Ini'),
              onTap: () {
                Navigator.pop(ctx);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => const ManualTransactionScreen(initialType: 'TRANSFER'),
                  ),
                );
              },
            ),
            ListTile(
              leading: const Icon(Icons.archive_outlined, color: AppTheme.textMuted),
              title: Text(wallet.isActive ? 'Nonaktifkan / Arsipkan' : 'Aktifkan Kembali'),
              onTap: () async {
                Navigator.pop(ctx);
                await ref.read(walletsProvider.notifier).updateWallet(
                  wallet.id,
                  {'is_active': !wallet.isActive},
                );
              },
            ),
            ListTile(
              leading: const Icon(Icons.delete_outline_rounded, color: AppTheme.expense),
              title: const Text('Hapus Permanen', style: TextStyle(color: AppTheme.expense)),
              onTap: () async {
                Navigator.pop(ctx);
                final confirm = await showDialog<bool>(
                  context: context,
                  builder: (dCtx) => AlertDialog(
                    title: const Text('Hapus Dompet?'),
                    content: Text('Apakah Anda yakin ingin menghapus "${wallet.name}"?'),
                    actions: [
                      TextButton(onPressed: () => Navigator.pop(dCtx, false), child: const Text('Batal')),
                      ElevatedButton(
                        style: ElevatedButton.styleFrom(backgroundColor: AppTheme.expense),
                        onPressed: () => Navigator.pop(dCtx, true),
                        child: const Text('Hapus'),
                      ),
                    ],
                  ),
                );

                if (confirm == true) {
                  await ref.read(walletsProvider.notifier).deleteWallet(wallet.id, force: true);
                }
              },
            ),
            const SizedBox(height: 12),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final walletsState = ref.watch(walletsProvider);
    final isPrivacy = ref.watch(privacyModeProvider);

    return Scaffold(
      backgroundColor: AppTheme.scaffoldBackground,
      appBar: AppBar(
        title: const Text('Manajemen Dompet'),
        actions: [
          IconButton(
            onPressed: () => _showAddWalletDialog(context, ref),
            icon: const Icon(Icons.add_circle_outline_rounded, color: AppTheme.primary, size: 26),
            tooltip: 'Tambah Dompet',
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await ref.read(walletsProvider.notifier).fetchWallets();
        },
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Net Worth Info Box
              Container(
                padding: const EdgeInsets.all(18),
                decoration: AppTheme.cardDecoration(),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Total Saldo Semua Dompet',
                          style: TextStyle(color: AppTheme.textMuted, fontSize: 12.5),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          isPrivacy ? 'Rp •••••••••' : CurrencyFormatter.format(walletsState.totalNetWorth),
                          style: const TextStyle(
                            color: AppTheme.textPrimary,
                            fontSize: 22,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                    ElevatedButton.icon(
                      onPressed: () => _showAddWalletDialog(context, ref),
                      icon: const Icon(Icons.add, size: 16),
                      label: const Text('Tambah'),
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // Wallets List Grouped
              const Text(
                'Daftar Dompet Aktif',
                style: TextStyle(
                  color: AppTheme.textPrimary,
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 12),

              if (walletsState.isLoading && walletsState.wallets.isEmpty)
                const Center(
                  child: Padding(
                    padding: EdgeInsets.all(40),
                    child: CircularProgressIndicator(),
                  ),
                )
              else if (walletsState.wallets.isEmpty)
                Container(
                  padding: const EdgeInsets.all(32),
                  decoration: AppTheme.cardDecoration(),
                  child: const Center(
                    child: Text('Belum ada dompet. Tambahkan dompet pertama Anda!'),
                  ),
                )
              else
                ListView.separated(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: walletsState.wallets.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 10),
                  itemBuilder: (context, index) {
                    final wallet = walletsState.wallets[index];
                    return Container(
                      decoration: AppTheme.cardDecoration(),
                      child: ListTile(
                        onTap: () => _showWalletOptions(context, wallet, ref),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                        leading: Container(
                          width: 44,
                          height: 44,
                          decoration: BoxDecoration(
                            color: wallet.color.withOpacity(0.12),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Icon(wallet.iconData, color: wallet.color, size: 22),
                        ),
                        title: Row(
                          children: [
                            Text(
                              wallet.name,
                              style: const TextStyle(
                                color: AppTheme.textPrimary,
                                fontSize: 15,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            if (!wallet.isActive) ...[
                              const SizedBox(width: 8),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: AppTheme.scaffoldBackground,
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: const Text(
                                  'Nonaktif',
                                  style: TextStyle(color: AppTheme.textMuted, fontSize: 10),
                                ),
                              ),
                            ],
                          ],
                        ),
                        subtitle: Text(
                          wallet.accountNumber?.isNotEmpty == true
                              ? '${wallet.type.label} • ${wallet.accountNumber}'
                              : wallet.type.label,
                          style: const TextStyle(color: AppTheme.textMuted, fontSize: 12),
                        ),
                        trailing: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text(
                              isPrivacy ? '••••••••' : CurrencyFormatter.format(wallet.balance),
                              style: TextStyle(
                                color: wallet.color,
                                fontSize: 15,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                            const SizedBox(width: 4),
                            const Icon(Icons.more_vert_rounded, color: AppTheme.textMuted, size: 20),
                          ],
                        ),
                      ),
                    );
                  },
                ),
            ],
          ),
        ),
      ),
    );
  }
}
