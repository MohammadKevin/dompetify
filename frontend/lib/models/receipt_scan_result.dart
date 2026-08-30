import 'transaction_item.dart';

class ReceiptScanResult {
  final String merchantName;
  final String transactionDate;
  final double totalAmount;
  final String suggestedCategory;
  final List<TransactionItem> items;
  final String? receiptImagePath;

  const ReceiptScanResult({
    required this.merchantName,
    required this.transactionDate,
    required this.totalAmount,
    required this.suggestedCategory,
    this.items = const [],
    this.receiptImagePath,
  });

  factory ReceiptScanResult.fromJson(Map<String, dynamic> json) {
    return ReceiptScanResult(
      merchantName: json['merchant_name'] as String? ?? 'Toko / Merchant',
      transactionDate: json['transaction_date'] as String? ?? '',
      totalAmount: (json['total_amount'] as num?)?.toDouble() ?? 0.0,
      suggestedCategory: json['suggested_category'] as String? ?? 'Belanja',
      items: json['items'] != null && json['items'] is List
          ? (json['items'] as List)
              .map((e) => TransactionItem.fromJson(e as Map<String, dynamic>))
              .toList()
          : const [],
      receiptImagePath: json['receipt_image_path'] as String?,
    );
  }
}
