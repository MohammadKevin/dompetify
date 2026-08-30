import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../constants/api_endpoints.dart';

class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final dynamic errors;

  ApiException({
    required this.message,
    this.statusCode,
    this.errors,
  });

  @override
  String toString() => message;
}

class DioClient {
  late final Dio _dio;

  DioClient({String? customBaseUrl}) {
    _dio = Dio(
      BaseOptions(
        baseUrl: customBaseUrl ?? ApiEndpoints.baseUrl,
        connectTimeout: const Duration(seconds: 15),
        receiveTimeout: const Duration(seconds: 35),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      ),
    );

    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) {
          if (kDebugMode) {
            debugPrint('[HTTP Request] ${options.method} ${options.uri}');
          }
          return handler.next(options);
        },
        onResponse: (response, handler) {
          if (kDebugMode) {
            debugPrint('[HTTP Response] ${response.statusCode} from ${response.requestOptions.uri}');
          }
          return handler.next(response);
        },
        onError: (DioException e, handler) {
          if (kDebugMode) {
            debugPrint('[HTTP Error] ${e.response?.statusCode} on ${e.requestOptions.uri}: ${e.message}');
          }
          return handler.next(e);
        },
      ),
    );
  }

  Dio get dio => _dio;

  void updateBaseUrl(String newBaseUrl) {
    _dio.options.baseUrl = newBaseUrl;
    ApiEndpoints.baseUrl = newBaseUrl;
  }

  ApiException handleError(dynamic error) {
    if (error is DioException) {
      switch (error.type) {
        case DioExceptionType.connectionTimeout:
        case DioExceptionType.sendTimeout:
        case DioExceptionType.receiveTimeout:
          return ApiException(
            message: 'Koneksi ke server timeout. Periksa jaringan Anda.',
            statusCode: 408,
          );
        case DioExceptionType.badResponse:
          final response = error.response;
          final data = response?.data;
          String message = 'Terjadi kesalahan pada server.';
          dynamic errors;

          if (data is Map<String, dynamic>) {
            message = data['message']?.toString() ?? message;
            errors = data['errors'];
          }

          return ApiException(
            message: message,
            statusCode: response?.statusCode,
            errors: errors,
          );
        case DioExceptionType.connectionError:
          return ApiException(
            message: 'Gagal terhubung ke backend API (${_dio.options.baseUrl}). Pastikan server Laravel sedang berjalan.',
            statusCode: 503,
          );
        default:
          return ApiException(
            message: error.message ?? 'Terjadi kesalahan tidak terduga.',
          );
      }
    }
    return ApiException(message: error.toString());
  }
}
