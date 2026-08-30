import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../core/theme/app_theme.dart';
import '../core/utils/currency_formatter.dart';
import '../core/utils/date_formatter.dart';
import '../models/category.dart';
import '../models/transaction.dart';
import '../models/wallet.dart';
import '../providers/categories_provider.dart';
import '../providers/transactions_provider.dart';
import '../providers/wallets_provider.dart';
import '../widgets/custom_text_field.dart';

class ManualTransactionScreen extends ConsumerStatefulWidget {
  final String? initialType;

  const ManualTransactionScreen({super.key, this.initialType});

  @override
  ConsumerState<ManualTransactionScreen> createState() => _ManualTransactionScreenState();
}

class _ManualTransactionScreenState extends ConsumerState<ManualTransactionScreen> {
  final _formKey = GlobalKey<FormState>();
  late TransactionType _selectedType;
  final TextEditingController _amountController = TextEditingController();
  final TextEditingController _adminFeeController = TextEditingController(text: '0');
  final TextEditingController _descriptionController = TextEditingController();
  DateTime _selectedDate = DateTime.now();

  Wallet? _sourceWallet;
  Wallet? _targetWallet;
  Category? _selectedCategory;
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _selectedType = widget.initialType != null
        ? TransactionType.fromString(widget.initialType)
        : TransactionType.expense;
  }

  @override
  void dispose() {
    _amountController.dispose();
    _adminFeeController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  Future<void> _selectDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (picked != null) {
      setState(() {
        _selectedDate = DateTime(
          picked.year,
          picked.month,
          picked.day,
          _selectedDate.hour,
          _selectedDate.minute,
        );
      });
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_sourceWallet == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih dompet sumber.')),
      );
      return;
    }

    if (_selectedType == TransactionType.transfer) {
      if (_targetWallet == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Pilih dompet tujuan transfer.')),
        );
        return;
      }
      if (_sourceWallet!.id == _targetWallet!.id) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Dompet asal dan tujuan tidak boleh sama.')),
        );
        return;
      }
    }

    setState(() {
      _isSubmitting = true;
    });

    try {
      final amount = CurrencyFormatter.parse(_amountController.text);
      final adminFee = CurrencyFormatter.parse(_adminFeeController.text);

      final payload = {
        'wallet_id': _sourceWallet!.id,
        'category_id': _selectedType != TransactionType.transfer ? _selectedCategory?.id : null,
        'target_wallet_id': _selectedType == TransactionType.transfer ? _targetWallet?.id : null,
        'type': _selectedType.value,
        'amount': amount,
        'admin_fee': adminFee,
        'date': _selectedDate.toIso8601String(),
        'description': _descriptionController.text.trim().isNotEmpty
            ? _descriptionController.text.trim()
            : (_selectedType == TransactionType.transfer
                ? 'Transfer ke ${_targetWallet?.name}'
                : (_selectedCategory?.name ?? _selectedType.label)),
      };

      await ref.read(transactionsProvider.notifier).createTransaction(payload);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.income,
            content: Text('${_selectedType.label} berhasil dicatat & saldo diperbarui!'),
          ),
        );
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isSubmitting = false;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.expense,
            content: Text('Gagal mencatat transaksi: $e'),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final wallets = ref.watch(activeWalletsProvider);
    final expenseCategories = ref.watch(expenseCategoriesProvider);
    final incomeCategories = ref.watch(incomeCategoriesProvider);

    final currentCategories = _selectedType == TransactionType.income
        ? incomeCategories
        : expenseCategories;

    // Default source wallet
    if (_sourceWallet == null && wallets.isNotEmpty) {
      _sourceWallet = wallets.first;
    }

    // Default target wallet for transfer
    if (_targetWallet == null && wallets.length > 1) {
      _targetWallet = wallets.firstWhere((w) => w.id != _sourceWallet?.id, orElse: () => wallets.last);
    }

    // Default category
    if (_selectedCategory == null && currentCategories.isNotEmpty) {
      _selectedCategory = currentCategories.first;
    }

    return Scaffold(
      backgroundColor: AppTheme.scaffoldBackground,
      appBar: AppBar(
        title: const Text('Catat Transaksi Manual'),
      ),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. Transaction Type Segmented Toggle
              Container(
                padding: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                  border: Border.all(color: AppTheme.cardBorder),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: _buildTypeSegment(
                        type: TransactionType.expense,
                        label: 'Pengeluaran',
                        color: AppTheme.expense,
                      ),
                    ),
                    Expanded(
                      child: _buildTypeSegment(
                        type: TransactionType.income,
                        label: 'Pemasukan',
                        color: AppTheme.income,
                      ),
                    ),
                    Expanded(
                      child: _buildTypeSegment(
                        type: TransactionType.transfer,
                        label: 'Transfer',
                        color: AppTheme.transfer,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // 2. Large Amount Card Input
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                decoration: AppTheme.cardDecoration(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Nominal Transaksi',
                      style: TextStyle(
                        color: AppTheme.textSecondary,
                        fontSize: 12.5,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Text(
                          'Rp',
                          style: TextStyle(
                            color: _selectedType == TransactionType.income
                                ? AppTheme.income
                                : (_selectedType == TransactionType.transfer
                                    ? AppTheme.transfer
                                    : AppTheme.expense),
                            fontSize: 26,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: TextFormField(
                            controller: _amountController,
                            keyboardType: TextInputType.number,
                            inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                            autofocus: true,
                            style: const TextStyle(
                              color: AppTheme.textPrimary,
                              fontSize: 26,
                              fontWeight: FontWeight.w800,
                            ),
                            decoration: const InputDecoration(
                              hintText: '0',
                              border: InputBorder.none,
                              enabledBorder: InputBorder.none,
                              focusedBorder: InputBorder.none,
                              contentPadding: EdgeInsets.zero,
                            ),
                            validator: (val) {
                              if (val == null || val.isEmpty) return 'Nominal wajib diisi';
                              if (CurrencyFormatter.parse(val) <= 0) return 'Nominal harus lebih dari 0';
                              return null;
                            },
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),

              // 3. Source Wallet Selector
              Text(
                _selectedType == TransactionType.transfer
                    ? 'Dompet Asal (Pengirim)'
                    : 'Pilih Dompet',
                style: const TextStyle(
                  color: AppTheme.textPrimary,
                  fontSize: 13.5,
                  fontWeight: FontWeight.w600,
                ),
              ),
              const SizedBox(height: 8),
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: wallets.map((wallet) {
                    final isSelected = _sourceWallet?.id == wallet.id;
                    return Padding(
                      padding: const EdgeInsets.only(right: 8.0),
                      child: ChoiceChip(
                        avatar: Icon(
                          wallet.iconData,
                          size: 16,
                          color: isSelected ? Colors.white : wallet.color,
                        ),
                        label: Text('${wallet.name} (${CurrencyFormatter.formatCompact(wallet.balance)})'),
                        selected: isSelected,
                        selectedColor: AppTheme.primary,
                        labelStyle: TextStyle(
                          color: isSelected ? Colors.white : AppTheme.textPrimary,
                          fontWeight: FontWeight.w600,
                          fontSize: 12.5,
                        ),
                        onSelected: (selected) {
                          if (selected) {
                            setState(() {
                              _sourceWallet = wallet;
                            });
                          }
                        },
                      ),
                    );
                  }).toList(),
                ),
              ),
              const SizedBox(height: 20),

              // 4. Target Wallet Selector (Only for TRANSFER)
              if (_selectedType == TransactionType.transfer) ...[
                const Text(
                  'Dompet Tujuan (Penerima)',
                  style: TextStyle(
                    color: AppTheme.textPrimary,
                    fontSize: 13.5,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 8),
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: wallets.where((w) => w.id != _sourceWallet?.id).map((wallet) {
                      final isSelected = _targetWallet?.id == wallet.id;
                      return Padding(
                        padding: const EdgeInsets.only(right: 8.0),
                        child: ChoiceChip(
                          avatar: Icon(
                            wallet.iconData,
                            size: 16,
                            color: isSelected ? Colors.white : wallet.color,
                          ),
                          label: Text(wallet.name),
                          selected: isSelected,
                          selectedColor: AppTheme.transfer,
                          labelStyle: TextStyle(
                            color: isSelected ? Colors.white : AppTheme.textPrimary,
                            fontWeight: FontWeight.w600,
                            fontSize: 12.5,
                          ),
                          onSelected: (selected) {
                            if (selected) {
                              setState(() {
                                _targetWallet = wallet;
                              });
                            }
                          },
                        ),
                      );
                    }).toList(),
                  ),
                ),
                const SizedBox(height: 16),

                // Admin fee field
                CustomTextField(
                  controller: _adminFeeController,
                  label: 'Biaya Admin Transfer (Rp)',
                  hint: '0',
                  prefixIcon: Icons.receipt_rounded,
                  keyboardType: TextInputType.number,
                  inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                ),
                const SizedBox(height: 20),
              ],

              // 5. Category Dropdown (Only for EXPENSE and INCOME)
              if (_selectedType != TransactionType.transfer) ...[
                const Text(
                  'Kategori',
                  style: TextStyle(
                    color: AppTheme.textPrimary,
                    fontSize: 13.5,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 6),
                DropdownButtonFormField<Category>(
                  value: _selectedCategory != null && currentCategories.contains(_selectedCategory)
                      ? _selectedCategory
                      : (currentCategories.isNotEmpty ? currentCategories.first : null),
                  decoration: const InputDecoration(
                    prefixIcon: Icon(Icons.category_rounded, color: AppTheme.primary),
                  ),
                  items: currentCategories.map((cat) {
                    return DropdownMenuItem<Category>(
                      value: cat,
                      child: Row(
                        children: [
                          Icon(cat.iconData, size: 18, color: AppTheme.textSecondary),
                          const SizedBox(width: 10),
                          Text(cat.name),
                        ],
                      ),
                    );
                  }).toList(),
                  onChanged: (cat) {
                    setState(() {
                      _selectedCategory = cat;
                    });
                  },
                ),
                const SizedBox(height: 20),
              ],

              // 6. Date & Time Picker
              CustomTextField(
                label: 'Tanggal & Waktu',
                controller: TextEditingController(text: DateFormatter.formatDateTime(_selectedDate)),
                readOnly: true,
                prefixIcon: Icons.calendar_today_rounded,
                onTap: _selectDate,
              ),
              const SizedBox(height: 16),

              // 7. Description / Notes
              CustomTextField(
                controller: _descriptionController,
                label: 'Catatan / Deskripsi',
                hint: 'e.g. Makan malam, Gaji bulanan, dsb.',
                prefixIcon: Icons.notes_rounded,
                maxLines: 2,
              ),
              const SizedBox(height: 32),

              // Submit Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _selectedType == TransactionType.income
                        ? AppTheme.income
                        : (_selectedType == TransactionType.transfer
                            ? AppTheme.transfer
                            : AppTheme.primary),
                  ),
                  onPressed: _isSubmitting ? null : _submit,
                  child: _isSubmitting
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                        )
                      : Text('Catat ${_selectedType.label}'),
                ),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTypeSegment({
    required TransactionType type,
    required String label,
    required Color color,
  }) {
    final isSelected = _selectedType == type;

    return InkWell(
      onTap: () {
        setState(() {
          _selectedType = type;
        });
      },
      borderRadius: BorderRadius.circular(AppTheme.radiusSmall),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: isSelected ? color : Colors.transparent,
          borderRadius: BorderRadius.circular(AppTheme.radiusSmall),
        ),
        child: Center(
          child: Text(
            label,
            style: TextStyle(
              color: isSelected ? Colors.white : AppTheme.textMuted,
              fontSize: 13,
              fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
            ),
          ),
        ),
      ),
    );
  }
}
