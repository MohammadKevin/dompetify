class TransactionItem {
  final int? id;
  final int? transactionId;
  final String itemName;
  final int quantity;
  final double price;

  const TransactionItem({
    this.id,
    this.transactionId,
    required this.itemName,
    this.quantity = 1,
    required this.price,
  });

  factory TransactionItem.fromJson(Map<String, dynamic> json) {
    return TransactionItem(
      id: json['id'] as int?,
      transactionId: json['transaction_id'] as int?,
      itemName: json['item_name'] as String? ?? '',
      quantity: (json['quantity'] as num?)?.toInt() ?? 1,
      price: (json['price'] as num?)?.toDouble() ?? 0.0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      if (id != null) 'id': id,
      if (transactionId != null) 'transaction_id': transactionId,
      'item_name': itemName,
      'quantity': quantity,
      'price': price,
    };
  }

  double get totalPrice => quantity * price;

  TransactionItem copyWith({
    int? id,
    int? transactionId,
    String? itemName,
    int? quantity,
    double? price,
  }) {
    return TransactionItem(
      id: id ?? this.id,
      transactionId: transactionId ?? this.transactionId,
      itemName: itemName ?? this.itemName,
      quantity: quantity ?? this.quantity,
      price: price ?? this.price,
    );
  }
}
