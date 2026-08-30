import 'package:intl/intl.dart';

class CurrencyFormatter {
  static final NumberFormat _formatter = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  static final NumberFormat _compactFormatter = NumberFormat.compactCurrency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 1,
  );

  /// Format double/num to standard Indonesian Rupiah (e.g. "Rp 150.000")
  static String format(num? amount, {bool showSymbol = true}) {
    if (amount == null) return showSymbol ? 'Rp 0' : '0';
    if (!showSymbol) {
      return NumberFormat('#,###', 'id_ID').format(amount);
    }
    return _formatter.format(amount);
  }

  /// Format double/num to compact Indonesian Rupiah (e.g. "Rp 1,5 jt")
  static String formatCompact(num? amount) {
    if (amount == null) return 'Rp 0';
    return _compactFormatter.format(amount);
  }

  /// Clean numeric string input (e.g. "150.000" or "Rp 150.000" -> 150000.0)
  static double parse(String text) {
    final cleaned = text.replaceAll(RegExp(r'[^0-9]'), '');
    return double.tryParse(cleaned) ?? 0.0;
  }
}
