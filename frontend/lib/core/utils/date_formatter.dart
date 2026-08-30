import 'package:intl/intl.dart';

class DateFormatter {
  /// e.g. "30 Agu 2026, 14:30"
  static String formatDateTime(DateTime? dateTime) {
    if (dateTime == null) return '-';
    return DateFormat('dd MMM yyyy, HH:mm', 'id_ID').format(dateTime);
  }

  /// e.g. "30 Agustus 2026"
  static String formatDateFull(DateTime? dateTime) {
    if (dateTime == null) return '-';
    return DateFormat('dd MMMM yyyy', 'id_ID').format(dateTime);
  }

  /// e.g. "30 Agu 2026"
  static String formatDateShort(DateTime? dateTime) {
    if (dateTime == null) return '-';
    return DateFormat('dd MMM yyyy', 'id_ID').format(dateTime);
  }

  /// e.g. "14:30"
  static String formatTimeOnly(DateTime? dateTime) {
    if (dateTime == null) return '-';
    return DateFormat('HH:mm', 'id_ID').format(dateTime);
  }

  /// e.g. "2026-08-30" (for API query filters)
  static String formatApiDate(DateTime dateTime) {
    return DateFormat('yyyy-MM-dd').format(dateTime);
  }

  /// Parse string from API ISO8601 or Y-m-d H:i:s
  static DateTime? parse(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return null;
    return DateTime.tryParse(dateStr);
  }
}
