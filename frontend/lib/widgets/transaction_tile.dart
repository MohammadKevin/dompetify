import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../core/theme/app_theme.dart';
import '../core/utils/currency_formatter.dart';
import '../core/utils/date_formatter.dart';
import '../models/transaction.dart';
import '../providers/wallets_provider.dart';

class TransactionTile extends ConsumerWidget {
  final Transaction transaction;
  final VoidCallback? onTap;
  final VoidCallback? onDelete;

  const TransactionTile({
    super.key,
    required this.transaction,
    this.onTap,
    this.onDelete,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final isPrivacy = ref.watch(privacyModeProvider);
    final typeColor = transaction.typeColor;
    final category = transaction.category;
    final wallet = transaction.wallet;
    final targetWallet = transaction.targetWallet;

    String subtitleText = '';
    if (transaction.type == TransactionType.transfer) {
      subtitleText = '${wallet?.name ?? "Dompet"} → ${targetWallet?.name ?? "Dompet"}';
    } else {
      subtitleText = wallet?.name ?? 'Dompet';
    }

    final dateStr = DateFormatter.formatDateTime(transaction.date);

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: AppTheme.cardDecoration(),
      child: ListTile(
        onTap: onTap,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
        leading: Container(
          width: 44,
          height: 44,
          decoration: BoxDecoration(
            color: typeColor.withOpacity(0.1),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(
            transaction.type == TransactionType.transfer
                ? Icons.swap_horiz_rounded
                : (category?.iconData ?? Icons.receipt_rounded),
            color: typeColor,
            size: 22,
          ),
        ),
        title: Row(
          children: [
            Expanded(
              child: Text(
                transaction.description?.isNotEmpty == true
                    ? transaction.description!
                    : (category?.name ?? (transaction.type == TransactionType.transfer ? 'Transfer Saldo' : 'Transaksi')),
                style: const TextStyle(
                  color: AppTheme.textPrimary,
                  fontSize: 14.5,
                  fontWeight: FontWeight.w600,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            if (transaction.items.isNotEmpty) ...[
              const SizedBox(width: 6),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: AppTheme.primaryContainer,
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  '${transaction.items.length} item',
                  style: const TextStyle(
                    color: AppTheme.primaryDark,
                    fontSize: 10.5,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ],
        ),
        subtitle: Padding(
          padding: const EdgeInsets.only(top: 4),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: AppTheme.scaffoldBackground,
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  subtitleText,
                  style: const TextStyle(
                    color: AppTheme.textSecondary,
                    fontSize: 11,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ),
              const SizedBox(width: 8),
              Text(
                dateStr,
                style: const TextStyle(
                  color: AppTheme.textMuted,
                  fontSize: 11,
                ),
              ),
            ],
          ),
        ),
        trailing: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            Text(
              isPrivacy
                  ? '••••••'
                  : '${transaction.amountPrefix}${CurrencyFormatter.format(transaction.amount)}',
              style: TextStyle(
                color: typeColor,
                fontSize: 14.5,
                fontWeight: FontWeight.w700,
              ),
            ),
            if (transaction.adminFee > 0)
              Text(
                'Biaya: ${CurrencyFormatter.format(transaction.adminFee)}',
                style: const TextStyle(
                  color: AppTheme.textMuted,
                  fontSize: 10.5,
                ),
              ),
          ],
        ),
      ),
    );
  }
}
