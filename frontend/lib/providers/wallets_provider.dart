import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/wallet.dart';
import '../repositories/wallet_repository.dart';
import 'dio_provider.dart';

final walletRepositoryProvider = Provider<WalletRepository>((ref) {
  final client = ref.watch(dioClientProvider);
  return WalletRepository(client);
});

final privacyModeProvider = StateProvider<bool>((ref) => false);

class WalletsState {
  final List<Wallet> wallets;
  final double totalNetWorth;
  final int activeCount;
  final Map<String, dynamic> summaryByType;
  final bool isLoading;
  final String? errorMessage;

  const WalletsState({
    this.wallets = const [],
    this.totalNetWorth = 0.0,
    this.activeCount = 0,
    this.summaryByType = const {},
    this.isLoading = false,
    this.errorMessage,
  });

  WalletsState copyWith({
    List<Wallet>? wallets,
    double? totalNetWorth,
    int? activeCount,
    Map<String, dynamic>? summaryByType,
    bool? isLoading,
    String? errorMessage,
  }) {
    return WalletsState(
      wallets: wallets ?? this.wallets,
      totalNetWorth: totalNetWorth ?? this.totalNetWorth,
      activeCount: activeCount ?? this.activeCount,
      summaryByType: summaryByType ?? this.summaryByType,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage,
    );
  }
}

class WalletsNotifier extends StateNotifier<WalletsState> {
  final WalletRepository _repository;

  WalletsNotifier(this._repository) : super(const WalletsState()) {
    fetchWallets();
  }

  Future<void> fetchWallets() async {
    state = state.copyWith(isLoading: true, errorMessage: null);
    try {
      final res = await _repository.getWalletsWithMeta();
      final wallets = res['wallets'] as List<Wallet>;
      final netWorth = res['total_net_worth'] as double;
      final activeCount = res['active_count'] as int;
      final summaryByType = res['by_type_summary'] as Map<String, dynamic>;

      state = state.copyWith(
        wallets: wallets,
        totalNetWorth: netWorth,
        activeCount: activeCount,
        summaryByType: summaryByType,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        errorMessage: e.toString(),
      );
    }
  }

  Future<Wallet?> createWallet(Map<String, dynamic> data) async {
    try {
      final wallet = await _repository.createWallet(data);
      await fetchWallets();
      return wallet;
    } catch (e) {
      state = state.copyWith(errorMessage: e.toString());
      rethrow;
    }
  }

  Future<Wallet?> updateWallet(int id, Map<String, dynamic> data) async {
    try {
      final wallet = await _repository.updateWallet(id, data);
      await fetchWallets();
      return wallet;
    } catch (e) {
      state = state.copyWith(errorMessage: e.toString());
      rethrow;
    }
  }

  Future<void> deleteWallet(int id, {bool force = false}) async {
    try {
      await _repository.deleteWallet(id, force: force);
      await fetchWallets();
    } catch (e) {
      state = state.copyWith(errorMessage: e.toString());
      rethrow;
    }
  }
}

final walletsProvider = StateNotifierProvider<WalletsNotifier, WalletsState>((ref) {
  final repository = ref.watch(walletRepositoryProvider);
  return WalletsNotifier(repository);
});

final activeWalletsProvider = Provider<List<Wallet>>((ref) {
  final walletsState = ref.watch(walletsProvider);
  return walletsState.wallets.where((w) => w.isActive).toList();
});
