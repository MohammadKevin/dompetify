class ApiEndpoints {
  static const String productionBaseUrl = 'https://finance.corecraft.my.id/api';
  static const String defaultBaseUrl = 'http://10.0.2.2:8000/api';
  static const String desktopBaseUrl = 'http://127.0.0.1:8000/api';

  // Configurable base URL (defaulting to live production VPS)
  static String baseUrl = productionBaseUrl;

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
