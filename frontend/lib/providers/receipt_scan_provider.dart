import 'dart:io';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/receipt_scan_result.dart';
import '../repositories/receipt_repository.dart';
import 'dio_provider.dart';

final receiptRepositoryProvider = Provider<ReceiptRepository>((ref) {
  final client = ref.watch(dioClientProvider);
  return ReceiptRepository(client);
});

enum ReceiptScanStatus { idle, scanning, success, error }

class ReceiptScanState {
  final ReceiptScanStatus status;
  final ReceiptScanResult? result;
  final File? scannedFile;
  final String? errorMessage;

  const ReceiptScanState({
    this.status = ReceiptScanStatus.idle,
    this.result,
    this.scannedFile,
    this.errorMessage,
  });

  ReceiptScanState copyWith({
    ReceiptScanStatus? status,
    ReceiptScanResult? result,
    File? scannedFile,
    String? errorMessage,
  }) {
    return ReceiptScanState(
      status: status ?? this.status,
      result: result ?? this.result,
      scannedFile: scannedFile ?? this.scannedFile,
      errorMessage: errorMessage,
    );
  }
}

class ReceiptScanNotifier extends StateNotifier<ReceiptScanState> {
  final ReceiptRepository _repository;

  ReceiptScanNotifier(this._repository) : super(const ReceiptScanState());

  Future<ReceiptScanResult?> scanReceipt(File file) async {
    state = state.copyWith(
      status: ReceiptScanStatus.scanning,
      scannedFile: file,
      errorMessage: null,
    );

    try {
      final result = await _repository.scanReceipt(file);
      state = state.copyWith(
        status: ReceiptScanStatus.success,
        result: result,
      );
      return result;
    } catch (e) {
      state = state.copyWith(
        status: ReceiptScanStatus.error,
        errorMessage: e.toString(),
      );
      rethrow;
    }
  }

  void reset() {
    state = const ReceiptScanState();
  }
}

final receiptScanProvider = StateNotifierProvider<ReceiptScanNotifier, ReceiptScanState>((ref) {
  final repository = ref.watch(receiptRepositoryProvider);
  return ReceiptScanNotifier(repository);
});
