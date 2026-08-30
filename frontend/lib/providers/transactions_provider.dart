import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/transaction.dart';
import '../repositories/transaction_repository.dart';
import 'dio_provider.dart';
import 'wallets_provider.dart';

final transactionRepositoryProvider = Provider<TransactionRepository>((ref) {
  final client = ref.watch(dioClientProvider);
  return TransactionRepository(client);
});

class TransactionFilter {
  final int? walletId;
  final int? categoryId;
  final String? type;
  final String? startDate;
  final String? endDate;
  final String? search;

  const TransactionFilter({
    this.walletId,
    this.categoryId,
    this.type,
    this.startDate,
    this.endDate,
    this.search,
  });

  TransactionFilter copyWith({
    int? walletId,
    int? categoryId,
    String? type,
    String? startDate,
    String? endDate,
    String? search,
    bool clearWallet = false,
    bool clearCategory = false,
    bool clearType = false,
    bool clearDates = false,
  }) {
    return TransactionFilter(
      walletId: clearWallet ? null : (walletId ?? this.walletId),
      categoryId: clearCategory ? null : (categoryId ?? this.categoryId),
      type: clearType ? null : (type ?? this.type),
      startDate: clearDates ? null : (startDate ?? this.startDate),
      endDate: clearDates ? null : (endDate ?? this.endDate),
      search: search ?? this.search,
    );
  }
}

class TransactionsState {
  final List<Transaction> transactions;
  final double totalExpense;
  final double totalIncome;
  final double totalTransfer;
  final double netFlow;
  final int currentPage;
  final int lastPage;
  final int totalCount;
  final bool isLoading;
  final String? errorMessage;
  final TransactionFilter filter;

  const TransactionsState({
    this.transactions = const [],
    this.totalExpense = 0.0,
    this.totalIncome = 0.0,
    this.totalTransfer = 0.0,
    this.netFlow = 0.0,
    this.currentPage = 1,
    this.lastPage = 1,
    this.totalCount = 0,
    this.isLoading = false,
    this.errorMessage,
    this.filter = const TransactionFilter(),
  });

  TransactionsState copyWith({
    List<Transaction>? transactions,
    double? totalExpense,
    double? totalIncome,
    double? totalTransfer,
    double? netFlow,
    int? currentPage,
    int? lastPage,
    int? totalCount,
    bool? isLoading,
    String? errorMessage,
    TransactionFilter? filter,
  }) {
    return TransactionsState(
      transactions: transactions ?? this.transactions,
      totalExpense: totalExpense ?? this.totalExpense,
      totalIncome: totalIncome ?? this.totalIncome,
      totalTransfer: totalTransfer ?? this.totalTransfer,
      netFlow: netFlow ?? this.netFlow,
      currentPage: currentPage ?? this.currentPage,
      lastPage: lastPage ?? this.lastPage,
      totalCount: totalCount ?? this.totalCount,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage,
      filter: filter ?? this.filter,
    );
  }
}

class TransactionsNotifier extends StateNotifier<TransactionsState> {
  final TransactionRepository _repository;
  final Ref _ref;

  TransactionsNotifier(this._repository, this._ref) : super(const TransactionsState()) {
    fetchTransactions();
  }

  Future<void> fetchTransactions({int page = 1}) async {
    state = state.copyWith(isLoading: true, errorMessage: null);
    try {
      final f = state.filter;
      final res = await _repository.getTransactions(
        walletId: f.walletId,
        categoryId: f.categoryId,
        type: f.type,
        startDate: f.startDate,
        endDate: f.endDate,
        search: f.search,
        page: page,
      );

      final list = res['transactions'] as List<Transaction>;
      final summary = res['summary'] as Map<String, dynamic>;
      final pagination = res['pagination'] as Map<String, dynamic>;

      state = state.copyWith(
        transactions: list,
        totalExpense: summary['total_expense'] as double,
        totalIncome: summary['total_income'] as double,
        totalTransfer: summary['total_transfer'] as double,
        netFlow: summary['net_flow'] as double,
        currentPage: (pagination['current_page'] as num?)?.toInt() ?? 1,
        lastPage: (pagination['last_page'] as num?)?.toInt() ?? 1,
        totalCount: (pagination['total'] as num?)?.toInt() ?? list.length,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        errorMessage: e.toString(),
      );
    }
  }

  void setFilter(TransactionFilter newFilter) {
    state = state.copyWith(filter: newFilter);
    fetchTransactions(page: 1);
  }

  void resetFilter() {
    state = state.copyWith(filter: const TransactionFilter());
    fetchTransactions(page: 1);
  }

  Future<Transaction?> createTransaction(Map<String, dynamic> data) async {
    try {
      final tx = await _repository.createTransaction(data);
      // Refresh transactions and also refresh wallet balances!
      await fetchTransactions();
      await _ref.read(walletsProvider.notifier).fetchWallets();
      return tx;
    } catch (e) {
      state = state.copyWith(errorMessage: e.toString());
      rethrow;
    }
  }

  Future<void> deleteTransaction(int id) async {
    try {
      await _repository.deleteTransaction(id);
      await fetchTransactions();
      await _ref.read(walletsProvider.notifier).fetchWallets();
    } catch (e) {
      state = state.copyWith(errorMessage: e.toString());
      rethrow;
    }
  }
}

final transactionsProvider = StateNotifierProvider<TransactionsNotifier, TransactionsState>((ref) {
  final repository = ref.watch(transactionRepositoryProvider);
  return TransactionsNotifier(repository, ref);
});
