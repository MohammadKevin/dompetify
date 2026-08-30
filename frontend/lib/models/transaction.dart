import 'package:flutter/material.dart';
import 'category.dart';
import 'transaction_item.dart';
import 'wallet.dart';

enum TransactionType {
  expense('EXPENSE', 'Pengeluaran'),
  income('INCOME', 'Pemasukan'),
  transfer('TRANSFER', 'Transfer');

  final String value;
  final String label;
  const TransactionType(this.value, this.label);

  static TransactionType fromString(String? type) {
    switch (type?.toUpperCase()) {
      case 'INCOME':
        return TransactionType.income;
      case 'TRANSFER':
        return TransactionType.transfer;
      default:
        return TransactionType.expense;
    }
  }
}

class Transaction {
  final int id;
  final int walletId;
  final int? categoryId;
  final int? targetWalletId;
  final TransactionType type;
  final double amount;
  final double adminFee;
  final DateTime date;
  final String? description;
  final String? receiptImagePath;
  final String? receiptImageUrl;
  final Wallet? wallet;
  final Wallet? targetWallet;
  final Category? category;
  final List<TransactionItem> items;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const Transaction({
    required this.id,
    required this.walletId,
    this.categoryId,
    this.targetWalletId,
    required this.type,
    required this.amount,
    this.adminFee = 0.0,
    required this.date,
    this.description,
    this.receiptImagePath,
    this.receiptImageUrl,
    this.wallet,
    this.targetWallet,
    this.category,
    this.items = const [],
    this.createdAt,
    this.updatedAt,
  });

  factory Transaction.fromJson(Map<String, dynamic> json) {
    return Transaction(
      id: json['id'] as int,
      walletId: json['wallet_id'] as int,
      categoryId: json['category_id'] as int?,
      targetWalletId: json['target_wallet_id'] as int?,
      type: TransactionType.fromString(json['type']?.toString()),
      amount: (json['amount'] as num?)?.toDouble() ?? 0.0,
      adminFee: (json['admin_fee'] as num?)?.toDouble() ?? 0.0,
      date: json['date'] != null
          ? (DateTime.tryParse(json['date'].toString()) ?? DateTime.now())
          : DateTime.now(),
      description: json['description'] as String?,
      receiptImagePath: json['receipt_image_path'] as String?,
      receiptImageUrl: json['receipt_image_url'] as String?,
      wallet: json['wallet'] != null && json['wallet'] is Map<String, dynamic>
          ? Wallet.fromJson(json['wallet'] as Map<String, dynamic>)
          : null,
      targetWallet: json['target_wallet'] != null && json['target_wallet'] is Map<String, dynamic>
          ? Wallet.fromJson(json['target_wallet'] as Map<String, dynamic>)
          : null,
      category: json['category'] != null && json['category'] is Map<String, dynamic>
          ? Category.fromJson(json['category'] as Map<String, dynamic>)
          : null,
      items: json['items'] != null && json['items'] is List
          ? (json['items'] as List)
              .map((e) => TransactionItem.fromJson(e as Map<String, dynamic>))
              .toList()
          : const [],
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at'].toString()) : null,
      updatedAt: json['updated_at'] != null ? DateTime.tryParse(json['updated_at'].toString()) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'wallet_id': walletId,
      'category_id': categoryId,
      'target_wallet_id': targetWalletId,
      'type': type.value,
      'amount': amount,
      'admin_fee': adminFee,
      'date': date.toIso8601String(),
      'description': description,
      'receipt_image_path': receiptImagePath,
      'items': items.map((e) => e.toJson()).toList(),
    };
  }

  Color get typeColor {
    switch (type) {
      case TransactionType.income:
        return const Color(0xFF10B981);
      case TransactionType.expense:
        return const Color(0xFFEF4444);
      case TransactionType.transfer:
        return const Color(0xFF6366F1);
    }
  }

  String get amountPrefix {
    switch (type) {
      case TransactionType.income:
        return '+';
      case TransactionType.expense:
        return '-';
      case TransactionType.transfer:
        return '';
    }
  }
}
