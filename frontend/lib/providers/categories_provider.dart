import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/category.dart';
import '../repositories/category_repository.dart';
import 'dio_provider.dart';

final categoryRepositoryProvider = Provider<CategoryRepository>((ref) {
  final client = ref.watch(dioClientProvider);
  return CategoryRepository(client);
});

class CategoriesState {
  final List<Category> categories;
  final bool isLoading;
  final String? errorMessage;

  const CategoriesState({
    this.categories = const [],
    this.isLoading = false,
    this.errorMessage,
  });

  CategoriesState copyWith({
    List<Category>? categories,
    bool? isLoading,
    String? errorMessage,
  }) {
    return CategoriesState(
      categories: categories ?? this.categories,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage,
    );
  }
}

class CategoriesNotifier extends StateNotifier<CategoriesState> {
  final CategoryRepository _repository;

  CategoriesNotifier(this._repository) : super(const CategoriesState()) {
    fetchCategories();
  }

  Future<void> fetchCategories() async {
    state = state.copyWith(isLoading: true, errorMessage: null);
    try {
      final list = await _repository.getCategories();
      state = state.copyWith(categories: list, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
    }
  }

  Future<Category?> createCategory(Map<String, dynamic> data) async {
    try {
      final category = await _repository.createCategory(data);
      await fetchCategories();
      return category;
    } catch (e) {
      state = state.copyWith(errorMessage: e.toString());
      rethrow;
    }
  }

  Future<void> deleteCategory(int id) async {
    try {
      await _repository.deleteCategory(id);
      await fetchCategories();
    } catch (e) {
      state = state.copyWith(errorMessage: e.toString());
      rethrow;
    }
  }
}

final categoriesProvider = StateNotifierProvider<CategoriesNotifier, CategoriesState>((ref) {
  final repository = ref.watch(categoryRepositoryProvider);
  return CategoriesNotifier(repository);
});

final expenseCategoriesProvider = Provider<List<Category>>((ref) {
  final state = ref.watch(categoriesProvider);
  return state.categories.where((c) => c.type == CategoryType.expense).toList();
});

final incomeCategoriesProvider = Provider<List<Category>>((ref) {
  final state = ref.watch(categoriesProvider);
  return state.categories.where((c) => c.type == CategoryType.income).toList();
});
