class ApiEndpoints {
  // In Android emulator use 10.0.2.2; on real device / desktop use local machine IP or 127.0.0.1
  static const String defaultBaseUrl = 'http://10.0.2.2:8000/api';
  static const String desktopBaseUrl = 'http://127.0.0.1:8000/api';

  // Configurable base URL
  static String baseUrl = desktopBaseUrl;

  // Wallets
  static const String wallets = '/wallets';

  // Categories
  static const String categories = '/categories';

  // Transactions
  static const String transactions = '/transactions';

  // AI Receipt Vision Scan
  static const String scanReceipt = '/receipts/scan';

  // Android Push Notification Webhook
  static const String notificationWebhook = '/webhook/notification';
}
