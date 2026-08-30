import '../core/constants/api_endpoints.dart';
import '../core/network/dio_client.dart';
import '../models/category.dart';

class CategoryRepository {
  final DioClient _client;

  CategoryRepository(this._client);

  Future<List<Category>> getCategories({String? type}) async {
    try {
      final queryParams = <String, dynamic>{};
      if (type != null) queryParams['type'] = type;

      final response = await _client.dio.get(
        ApiEndpoints.categories,
        queryParameters: queryParams,
      );

      final data = response.data['data'] as List? ?? [];
      return data.map((json) => Category.fromJson(json as Map<String, dynamic>)).toList();
    } catch (e) {
      throw _client.handleError(e);
    }
  }

  Future<Category> createCategory(Map<String, dynamic> data) async {
    try {
      final response = await _client.dio.post(
        ApiEndpoints.categories,
        data: data,
      );
      return Category.fromJson(response.data['data'] as Map<String, dynamic>);
    } catch (e) {
      throw _client.handleError(e);
    }
  }

  Future<void> deleteCategory(int id) async {
    try {
      await _client.dio.delete('${ApiEndpoints.categories}/$id');
    } catch (e) {
      throw _client.handleError(e);
    }
  }
}
