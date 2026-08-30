import '../core/constants/api_endpoints.dart';
import '../core/network/dio_client.dart';
import '../models/wallet.dart';

class WalletRepository {
  final DioClient _client;

  WalletRepository(this._client);

  Future<Map<String, dynamic>> getWalletsWithMeta({bool? isActive, String? type}) async {
    try {
      final queryParams = <String, dynamic>{};
      if (isActive != null) queryParams['is_active'] = isActive;
      if (type != null) queryParams['type'] = type;

      final response = await _client.dio.get(
        ApiEndpoints.wallets,
        queryParameters: queryParams,
      );

      final data = response.data['data'] as List? ?? [];
      final meta = response.data['meta'] as Map<String, dynamic>? ?? {};

      final wallets = data.map((json) => Wallet.fromJson(json as Map<String, dynamic>)).toList();

      return {
        'wallets': wallets,
        'total_net_worth': (meta['total_net_worth'] as num?)?.toDouble() ?? 0.0,
        'active_count': (meta['active_wallets_count'] as num?)?.toInt() ?? wallets.length,
        'by_type_summary': meta['summary_by_type'] as Map<String, dynamic>? ?? {},
      };
    } catch (e) {
      throw _client.handleError(e);
    }
  }

  Future<Wallet> createWallet(Map<String, dynamic> data) async {
    try {
      final response = await _client.dio.post(
        ApiEndpoints.wallets,
        data: data,
      );
      return Wallet.fromJson(response.data['data'] as Map<String, dynamic>);
    } catch (e) {
      throw _client.handleError(e);
    }
  }

  Future<Wallet> updateWallet(int id, Map<String, dynamic> data) async {
    try {
      final response = await _client.dio.put(
        '${ApiEndpoints.wallets}/$id',
        data: data,
      );
      return Wallet.fromJson(response.data['data'] as Map<String, dynamic>);
    } catch (e) {
      throw _client.handleError(e);
    }
  }

  Future<void> deleteWallet(int id, {bool force = false}) async {
    try {
      await _client.dio.delete(
        '${ApiEndpoints.wallets}/$id',
        queryParameters: {'force': force},
      );
    } catch (e) {
      throw _client.handleError(e);
    }
  }
}
