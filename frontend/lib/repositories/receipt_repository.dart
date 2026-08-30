import 'dart:io';
import 'package:dio/dio.dart';
import '../core/constants/api_endpoints.dart';
import '../core/network/dio_client.dart';
import '../models/receipt_scan_result.dart';

class ReceiptRepository {
  final DioClient _client;

  ReceiptRepository(this._client);

  Future<ReceiptScanResult> scanReceipt(File imageFile) async {
    try {
      final fileName = imageFile.path.split(Platform.pathSeparator).last;
      final formData = FormData.fromMap({
        'image': await MultipartFile.fromFile(
          imageFile.path,
          filename: fileName,
        ),
      });

      final response = await _client.dio.post(
        ApiEndpoints.scanReceipt,
        data: formData,
        options: Options(
          contentType: 'multipart/form-data',
          sendTimeout: const Duration(seconds: 45),
          receiveTimeout: const Duration(seconds: 45),
        ),
      );

      final data = response.data['data'] as Map<String, dynamic>;
      return ReceiptScanResult.fromJson(data);
    } catch (e) {
      throw _client.handleError(e);
    }
  }
}
