import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../core/theme/app_theme.dart';
import '../providers/transactions_provider.dart';
import '../providers/wallets_provider.dart';
import '../widgets/empty_state_view.dart';
import '../widgets/net_worth_card.dart';
import '../widgets/quick_action_button.dart';
import '../widgets/transaction_tile.dart';
import '../widgets/wallet_card.dart';
import 'manual_transaction_screen.dart';
import 'scan_receipt_screen.dart';
import 'wallets_screen.dart';

class DashboardScreen extends ConsumerWidget {
  const DashboardScreen({super.key});

  void _showFilterBottomSheet(BuildContext context, WidgetRef ref) {
    final currentFilter = ref.read(transactionsProvider).filter;
    String? selectedType = currentFilter.type;

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setStateModal) => Container(
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
              const Text(
                'Filter Transaksi',
                style: TextStyle(
                  color: AppTheme.textPrimary,
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 14),
              Wrap(
                spacing: 8,
                children: [
                  ChoiceChip(
                    label: const Text('Semua'),
                    selected: selectedType == null || selectedType!.isEmpty,
                    onSelected: (val) {
                      setStateModal(() => selectedType = null);
                    },
                  ),
                  ChoiceChip(
                    label: const Text('Pengeluaran'),
                    selected: selectedType == 'EXPENSE',
                    onSelected: (val) {
                      setStateModal(() => selectedType = 'EXPENSE');
                    },
                  ),
                  ChoiceChip(
                    label: const Text('Pemasukan'),
                    selected: selectedType == 'INCOME',
                    onSelected: (val) {
                      setStateModal(() => selectedType = 'INCOME');
                    },
                  ),
                  ChoiceChip(
                    label: const Text('Transfer'),
                    selected: selectedType == 'TRANSFER',
                    onSelected: (val) {
                      setStateModal(() => selectedType = 'TRANSFER');
                    },
                  ),
                ],
              ),
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () {
                        ref.read(transactionsProvider.notifier).resetFilter();
                        Navigator.pop(ctx);
                      },
                      child: const Text('Reset'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        ref.read(transactionsProvider.notifier).setFilter(
                          currentFilter.copyWith(
                            type: selectedType,
                            clearType: selectedType == null,
                          ),
                        );
                        Navigator.pop(ctx);
                      },
                      child: const Text('Terapkan'),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
            ],
          ),
        ),
      ),
    );
  }

  void _showTransactionDetailsModal(BuildContext context, dynamic transaction, WidgetRef ref) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(24),
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
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  transaction.type.label,
                  style: TextStyle(
                    color: transaction.typeColor,
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                IconButton(
                  onPressed: () async {
                    final confirm = await showDialog<bool>(
                      context: context,
                      builder: (dCtx) => AlertDialog(
                        title: const Text('Hapus Transaksi?'),
                        content: const Text('Saldo dompet akan disesuaikan kembali.'),
                        actions: [
                          TextButton(
                            onPressed: () => Navigator.pop(dCtx, false),
                            child: const Text('Batal'),
                          ),
                          ElevatedButton(
                            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.expense),
                            onPressed: () => Navigator.pop(dCtx, true),
                            child: const Text('Hapus'),
                          ),
                        ],
                      ),
                    );

                    if (confirm == true) {
                      Navigator.pop(ctx);
                      await ref.read(transactionsProvider.notifier).deleteTransaction(transaction.id);
                    }
                  },
                  icon: const Icon(Icons.delete_outline_rounded, color: AppTheme.expense),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Text(
              transaction.description ?? 'Tanpa Keterangan',
              style: const TextStyle(
                color: AppTheme.textPrimary,
                fontSize: 18,
                fontWeight: FontWeight.w800,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              '${transaction.amountPrefix}${transaction.amount}',
              style: TextStyle(
                color: transaction.typeColor,
                fontSize: 22,
                fontWeight: FontWeight.w800,
              ),
            ),
            if (transaction.items.isNotEmpty) ...[
              const Divider(height: 28),
              const Text(
                'Rincian Item Struk:',
                style: TextStyle(
                  color: AppTheme.textPrimary,
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 8),
              ...transaction.items.map<Widget>(
                (item) => Padding(
                  padding: const EdgeInsets.symmetric(vertical: 4),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        '${item.quantity}x ${item.itemName}',
                        style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13.5),
                      ),
                      Text(
                        'Rp ${(item.price * item.quantity).toStringAsFixed(0)}',
                        style: const TextStyle(
                          color: AppTheme.textPrimary,
                          fontSize: 13.5,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
            const SizedBox(height: 16),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final walletsState = ref.watch(walletsProvider);
    final transactionsState = ref.watch(transactionsProvider);

    return Scaffold(
      backgroundColor: AppTheme.scaffoldBackground,
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppTheme.primaryContainer,
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(
                Icons.account_balance_wallet_rounded,
                color: AppTheme.primary,
                size: 20,
              ),
            ),
            const SizedBox(width: 10),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'FinanceApp',
                  style: TextStyle(
                    color: AppTheme.textPrimary,
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                Text(
                  'Kelola Keuangan Pribadi',
                  style: TextStyle(
                    color: AppTheme.textMuted,
                    fontSize: 11,
                  ),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            onPressed: () {
              ref.read(walletsProvider.notifier).fetchWallets();
              ref.read(transactionsProvider.notifier).fetchTransactions();
            },
            icon: const Icon(Icons.refresh_rounded),
            tooltip: 'Segarkan',
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await ref.read(walletsProvider.notifier).fetchWallets();
          await ref.read(transactionsProvider.notifier).fetchTransactions();
        },
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. Net Worth Hero Card
              NetWorthCard(
                netWorth: walletsState.totalNetWorth,
                totalIncome: transactionsState.totalIncome,
                totalExpense: transactionsState.totalExpense,
              ),
              const SizedBox(height: 18),

              // 2. Quick Actions
              Row(
                children: [
                  Expanded(
                    child: QuickActionButton(
                      icon: Icons.document_scanner_rounded,
                      label: 'Scan Struk',
                      color: AppTheme.primary,
                      backgroundColor: AppTheme.primaryContainer,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (_) => const ScanReceiptScreen()),
                        );
                      },
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: QuickActionButton(
                      icon: Icons.swap_horiz_rounded,
                      label: 'Transfer',
                      color: AppTheme.transfer,
                      backgroundColor: AppTheme.transferLight,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const ManualTransactionScreen(initialType: 'TRANSFER'),
                          ),
                        );
                      },
                    ),
                  ),
                  const SizedBox(width: 10),
                  QuickActionButton(
                    icon: Icons.filter_list_rounded,
                    label: 'Filter',
                    color: AppTheme.textSecondary,
                    backgroundColor: AppTheme.scaffoldBackground,
                    onTap: () => _showFilterBottomSheet(context, ref),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              // 3. Wallets Section
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Dompet & Rekening',
                    style: TextStyle(
                      color: AppTheme.textPrimary,
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  TextButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const WalletsScreen()),
                      );
                    },
                    child: const Text('Lihat Semua'),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              if (walletsState.isLoading && walletsState.wallets.isEmpty)
                const Center(
                  child: Padding(
                    padding: EdgeInsets.all(20),
                    child: CircularProgressIndicator(),
                  ),
                )
              else if (walletsState.wallets.isEmpty)
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: AppTheme.cardDecoration(),
                  child: const Center(
                    child: Text(
                      'Belum ada dompet terdaftar.',
                      style: TextStyle(color: AppTheme.textMuted),
                    ),
                  ),
                )
              else
                SizedBox(
                  height: 110,
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    itemCount: walletsState.wallets.length,
                    separatorBuilder: (_, __) => const SizedBox(width: 12),
                    itemBuilder: (context, index) {
                      final wallet = walletsState.wallets[index];
                      return WalletCard(
                        wallet: wallet,
                        onTap: () {
                          // Filter transactions by tapped wallet
                          ref.read(transactionsProvider.notifier).setFilter(
                            transactionsState.filter.copyWith(walletId: wallet.id),
                          );
                        },
                      );
                    },
                  ),
                ),
              const SizedBox(height: 26),

              // 4. Recent Transactions Section
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const Text(
                        'Riwayat Transaksi',
                        style: TextStyle(
                          color: AppTheme.textPrimary,
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      if (transactionsState.filter.type != null) ...[
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: AppTheme.primaryContainer,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            transactionsState.filter.type!,
                            style: const TextStyle(
                              color: AppTheme.primaryDark,
                              fontSize: 11,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ],
                    ],
                  ),
                  if (transactionsState.filter.type != null || transactionsState.filter.walletId != null)
                    TextButton(
                      onPressed: () {
                        ref.read(transactionsProvider.notifier).resetFilter();
                      },
                      child: const Text('Reset Filter'),
                    ),
                ],
              ),
              const SizedBox(height: 10),

              if (transactionsState.isLoading && transactionsState.transactions.isEmpty)
                const Center(
                  child: Padding(
                    padding: EdgeInsets.all(40),
                    child: CircularProgressIndicator(),
                  ),
                )
              else if (transactionsState.transactions.isEmpty)
                EmptyStateView(
                  icon: Icons.receipt_long_outlined,
                  title: 'Belum Ada Transaksi',
                  message: 'Catat pengeluaran harian atau scan struk belanja Anda untuk mulai mencatat keuangan.',
                  actionLabel: 'Tambah Transaksi',
                  onAction: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const ManualTransactionScreen()),
                    );
                  },
                )
              else
                ListView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: transactionsState.transactions.length,
                  itemBuilder: (context, index) {
                    final tx = transactionsState.transactions[index];
                    return TransactionTile(
                      transaction: tx,
                      onTap: () => _showTransactionDetailsModal(context, tx, ref),
                    );
                  },
                ),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }
}
