import '../core/constants/api_endpoints.dart';
import '../core/network/dio_client.dart';
import '../models/transaction.dart';

class TransactionRepository {
  final DioClient _client;

  TransactionRepository(this._client);

  Future<Map<String, dynamic>> getTransactions({
    int? walletId,
    int? categoryId,
    String? type,
    String? startDate,
    String? endDate,
    String? search,
    int page = 1,
    int perPage = 15,
  }) async {
    try {
      final queryParams = <String, dynamic>{
        'page': page,
        'per_page': perPage,
      };

      if (walletId != null) queryParams['wallet_id'] = walletId;
      if (categoryId != null) queryParams['category_id'] = categoryId;
      if (type != null && type.isNotEmpty) queryParams['type'] = type;
      if (startDate != null && startDate.isNotEmpty) queryParams['start_date'] = startDate;
      if (endDate != null && endDate.isNotEmpty) queryParams['end_date'] = endDate;
      if (search != null && search.isNotEmpty) queryParams['search'] = search;

      final response = await _client.dio.get(
        ApiEndpoints.transactions,
        queryParameters: queryParams,
      );

      final data = response.data['data'] as List? ?? [];
      final pagination = response.data['pagination'] as Map<String, dynamic>? ?? {};
      final summary = response.data['summary'] as Map<String, dynamic>? ?? {};

      final transactions = data.map((json) => Transaction.fromJson(json as Map<String, dynamic>)).toList();

      return {
        'transactions': transactions,
        'pagination': pagination,
        'summary': {
          'total_expense': (summary['total_expense'] as num?)?.toDouble() ?? 0.0,
          'total_income': (summary['total_income'] as num?)?.toDouble() ?? 0.0,
          'total_transfer': (summary['total_transfer'] as num?)?.toDouble() ?? 0.0,
          'net_flow': (summary['net_flow'] as num?)?.toDouble() ?? 0.0,
        },
      };
    } catch (e) {
      throw _client.handleError(e);
    }
  }

  Future<Transaction> createTransaction(Map<String, dynamic> data) async {
    try {
      final response = await _client.dio.post(
        ApiEndpoints.transactions,
        data: data,
      );
      return Transaction.fromJson(response.data['data'] as Map<String, dynamic>);
    } catch (e) {
      throw _client.handleError(e);
    }
  }

  Future<Transaction> getTransaction(int id) async {
    try {
      final response = await _client.dio.get('${ApiEndpoints.transactions}/$id');
      return Transaction.fromJson(response.data['data'] as Map<String, dynamic>);
    } catch (e) {
      throw _client.handleError(e);
    }
  }

  Future<void> deleteTransaction(int id) async {
    try {
      await _client.dio.delete('${ApiEndpoints.transactions}/$id');
    } catch (e) {
      throw _client.handleError(e);
    }
  }
}
