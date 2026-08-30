import 'package:flutter/material.dart';

enum CategoryType {
  expense('EXPENSE', 'Pengeluaran'),
  income('INCOME', 'Pemasukan');

  final String value;
  final String label;
  const CategoryType(this.value, this.label);

  static CategoryType fromString(String? type) {
    if (type?.toUpperCase() == 'INCOME') {
      return CategoryType.income;
    }
    return CategoryType.expense;
  }
}

class Category {
  final int id;
  final String name;
  final CategoryType type;
  final String? icon;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const Category({
    required this.id,
    required this.name,
    required this.type,
    this.icon,
    this.createdAt,
    this.updatedAt,
  });

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'] as int,
      name: json['name'] as String? ?? 'Kategori',
      type: CategoryType.fromString(json['type']?.toString()),
      icon: json['icon'] as String?,
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at'].toString()) : null,
      updatedAt: json['updated_at'] != null ? DateTime.tryParse(json['updated_at'].toString()) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'type': type.value,
      'icon': icon,
    };
  }

  IconData get iconData {
    final lower = (icon ?? name).toLowerCase();
    if (lower.contains('makan') || lower.contains('restoran') || lower.contains('food') || lower.contains('cafe')) {
      return Icons.restaurant_rounded;
    }
    if (lower.contains('transport') || lower.contains('bensin') || lower.contains('car') || lower.contains('motor')) {
      return Icons.directions_car_rounded;
    }
    if (lower.contains('belanja') || lower.contains('shopping') || lower.contains('supermarket')) {
      return Icons.shopping_cart_rounded;
    }
    if (lower.contains('tagihan') || lower.contains('listrik') || lower.contains('air') || lower.contains('bill')) {
      return Icons.receipt_long_rounded;
    }
    if (lower.contains('hiburan') || lower.contains('game') || lower.contains('nonton')) {
      return Icons.sports_esports_rounded;
    }
    if (lower.contains('kesehatan') || lower.contains('medis') || lower.contains('obat')) {
      return Icons.medical_services_rounded;
    }
    if (lower.contains('pendidikan') || lower.contains('sekolah') || lower.contains('kursus')) {
      return Icons.school_rounded;
    }
    if (lower.contains('gaji') || lower.contains('salary')) {
      return Icons.payments_rounded;
    }
    if (lower.contains('bonus') || lower.contains('thr') || lower.contains('hadiah')) {
      return Icons.card_giftcard_rounded;
    }
    if (lower.contains('investasi') || lower.contains('saham') || lower.contains('reksadana')) {
      return Icons.trending_up_rounded;
    }
    if (lower.contains('freelance') || lower.contains('bisnis')) {
      return Icons.business_center_rounded;
    }
    return type == CategoryType.income ? Icons.add_circle_outline_rounded : Icons.category_rounded;
  }
}
