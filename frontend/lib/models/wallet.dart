import 'package:flutter/material.dart';

enum WalletType {
  bank('BANK', 'Bank'),
  eWallet('E_WALLET', 'E-Wallet'),
  cash('CASH', 'Tunai'),
  savings('SAVINGS', 'Tabungan'),
  other('OTHER', 'Lainnya');

  final String value;
  final String label;
  const WalletType(this.value, this.label);

  static WalletType fromString(String? type) {
    switch (type?.toUpperCase()) {
      case 'BANK':
        return WalletType.bank;
      case 'E_WALLET':
        return WalletType.eWallet;
      case 'CASH':
        return WalletType.cash;
      case 'SAVINGS':
        return WalletType.savings;
      default:
        return WalletType.other;
    }
  }
}

class Wallet {
  final int id;
  final String name;
  final WalletType type;
  final String? accountNumber;
  final double balance;
  final String? colorHex;
  final String? icon;
  final bool isActive;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const Wallet({
    required this.id,
    required this.name,
    required this.type,
    this.accountNumber,
    required this.balance,
    this.colorHex,
    this.icon,
    this.isActive = true,
    this.createdAt,
    this.updatedAt,
  });

  factory Wallet.fromJson(Map<String, dynamic> json) {
    return Wallet(
      id: json['id'] as int,
      name: json['name'] as String? ?? 'Dompet',
      type: WalletType.fromString(json['type']?.toString()),
      accountNumber: json['account_number'] as String?,
      balance: (json['balance'] as num?)?.toDouble() ?? 0.0,
      colorHex: json['color_hex'] as String?,
      icon: json['icon'] as String?,
      isActive: json['is_active'] as bool? ?? true,
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at'].toString()) : null,
      updatedAt: json['updated_at'] != null ? DateTime.tryParse(json['updated_at'].toString()) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'type': type.value,
      'account_number': accountNumber,
      'balance': balance,
      'color_hex': colorHex,
      'icon': icon,
      'is_active': isActive,
    };
  }

  Color get color {
    if (colorHex != null && colorHex!.isNotEmpty) {
      try {
        final hex = colorHex!.replaceAll('#', '');
        return Color(int.parse('FF$hex', radix: 16));
      } catch (_) {}
    }
    switch (type) {
      case WalletType.bank:
        return const Color(0xFF00529C);
      case WalletType.eWallet:
        return const Color(0xFF00AED6);
      case WalletType.cash:
        return const Color(0xFF10B981);
      case WalletType.savings:
        return const Color(0xFF8B5CF6);
      case WalletType.other:
        return const Color(0xFF64748B);
    }
  }

  IconData get iconData {
    switch (type) {
      case WalletType.bank:
        return Icons.account_balance_rounded;
      case WalletType.eWallet:
        return Icons.account_balance_wallet_rounded;
      case WalletType.cash:
        return Icons.payments_rounded;
      case WalletType.savings:
        return Icons.savings_rounded;
      case WalletType.other:
        return Icons.credit_card_rounded;
    }
  }

  Wallet copyWith({
    int? id,
    String? name,
    WalletType? type,
    String? accountNumber,
    double? balance,
    String? colorHex,
    String? icon,
    bool? isActive,
  }) {
    return Wallet(
      id: id ?? this.id,
      name: name ?? this.name,
      type: type ?? this.type,
      accountNumber: accountNumber ?? this.accountNumber,
      balance: balance ?? this.balance,
      colorHex: colorHex ?? this.colorHex,
      icon: icon ?? this.icon,
      isActive: isActive ?? this.isActive,
      createdAt: createdAt,
      updatedAt: updatedAt,
    );
  }
}
