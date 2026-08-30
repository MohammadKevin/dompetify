import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../core/theme/app_theme.dart';
import '../core/utils/currency_formatter.dart';
import '../core/utils/date_formatter.dart';
import '../models/category.dart';
import '../models/receipt_scan_result.dart';
import '../models/transaction_item.dart';
import '../models/wallet.dart';
import '../providers/categories_provider.dart';
import '../providers/transactions_provider.dart';
import '../providers/wallets_provider.dart';
import '../widgets/custom_text_field.dart';

class ReviewReceiptScreen extends ConsumerStatefulWidget {
  final ReceiptScanResult scanResult;
  final File? imageFile;

  const ReviewReceiptScreen({
    super.key,
    required this.scanResult,
    this.imageFile,
  });

  @override
  ConsumerState<ReviewReceiptScreen> createState() => _ReviewReceiptScreenState();
}

class _ReviewReceiptScreenState extends ConsumerState<ReviewReceiptScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _merchantController;
  late TextEditingController _amountController;
  late DateTime _selectedDate;
  Wallet? _selectedWallet;
  Category? _selectedCategory;
  late List<TransactionItem> _items;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _merchantController = TextEditingController(text: widget.scanResult.merchantName);
    _amountController = TextEditingController(
      text: widget.scanResult.totalAmount > 0
          ? widget.scanResult.totalAmount.toStringAsFixed(0)
          : '',
    );

    _selectedDate = DateFormatter.parse(widget.scanResult.transactionDate) ?? DateTime.now();
    _items = List.from(widget.scanResult.items);
  }

  @override
  void dispose() {
    _merchantController.dispose();
    _amountController.dispose();
    super.dispose();
  }

  void _recalculateTotalFromItems() {
    if (_items.isEmpty) return;
    double sum = 0;
    for (var item in _items) {
      sum += item.totalPrice;
    }
    setState(() {
      _amountController.text = sum.toStringAsFixed(0);
    });
  }

  void _addItemRow() {
    setState(() {
      _items.add(const TransactionItem(itemName: 'Item Baru', quantity: 1, price: 0.0));
    });
  }

  void _removeItemRow(int index) {
    setState(() {
      _items.removeAt(index);
    });
    _recalculateTotalFromItems();
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

  Future<void> _submitTransaction() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedWallet == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Silakan pilih dompet pembayaran.')),
      );
      return;
    }

    setState(() {
      _isSaving = true;
    });

    try {
      final amount = CurrencyFormatter.parse(_amountController.text);
      final payload = {
        'wallet_id': _selectedWallet!.id,
        'category_id': _selectedCategory?.id,
        'type': 'EXPENSE',
        'amount': amount,
        'admin_fee': 0.00,
        'date': _selectedDate.toIso8601String(),
        'description': _merchantController.text.trim(),
        'receipt_image_path': widget.scanResult.receiptImagePath,
        'items': _items
            .where((i) => i.itemName.isNotEmpty)
            .map((i) => {
                  'item_name': i.itemName,
                  'quantity': i.quantity,
                  'price': i.price,
                })
            .toList(),
      };

      await ref.read(transactionsProvider.notifier).createTransaction(payload);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            backgroundColor: AppTheme.income,
            content: Text('Transaksi dari struk berhasil disimpan!'),
          ),
        );
        Navigator.popUntil(context, (route) => route.isFirst);
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isSaving = false;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.expense,
            content: Text('Gagal menyimpan transaksi: $e'),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final wallets = ref.watch(activeWalletsProvider);
    final expenseCategories = ref.watch(expenseCategoriesProvider);

    // Auto-select first wallet if none selected
    if (_selectedWallet == null && wallets.isNotEmpty) {
      _selectedWallet = wallets.first;
    }

    // Auto-select category matching AI suggestion
    if (_selectedCategory == null && expenseCategories.isNotEmpty) {
      final suggested = widget.scanResult.suggestedCategory.toLowerCase();
      _selectedCategory = expenseCategories.firstWhere(
        (c) => c.name.toLowerCase().contains(suggested) || suggested.contains(c.name.toLowerCase()),
        orElse: () => expenseCategories.first,
      );
    }

    return Scaffold(
      backgroundColor: AppTheme.scaffoldBackground,
      appBar: AppBar(
        title: const Text('Review Hasil Scan Struk'),
      ),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // AI Badge Header
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: const Color(0xFFF0FDF4),
                  borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                  border: Border.all(color: const Color(0xFFBBF7D0)),
                ),
                child: const Row(
                  children: [
                    Icon(Icons.check_circle_rounded, color: AppTheme.income, size: 20),
                    SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        'Data berhasil diekstraksi oleh Gemini AI. Anda dapat memeriksa dan mengedit kolom di bawah ini.',
                        style: TextStyle(color: Color(0xFF166534), fontSize: 12.5),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),

              // Merchant / Store Name
              CustomTextField(
                controller: _merchantController,
                label: 'Nama Toko / Merchant',
                hint: 'e.g. Indomaret, Starbucks',
                prefixIcon: Icons.storefront_rounded,
                validator: (val) => val == null || val.isEmpty ? 'Nama toko wajib diisi' : null,
              ),
              const SizedBox(height: 16),

              // Total Amount
              CustomTextField(
                controller: _amountController,
                label: 'Total Nominal (Rp)',
                hint: '0',
                prefixIcon: Icons.payments_rounded,
                keyboardType: TextInputType.number,
                inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                validator: (val) {
                  if (val == null || val.isEmpty) return 'Nominal wajib diisi';
                  if (CurrencyFormatter.parse(val) <= 0) return 'Nominal harus lebih besar dari 0';
                  return null;
                },
              ),
              const SizedBox(height: 16),

              // Date Picker Field
              CustomTextField(
                label: 'Tanggal Transaksi',
                controller: TextEditingController(text: DateFormatter.formatDateFull(_selectedDate)),
                readOnly: true,
                prefixIcon: Icons.calendar_today_rounded,
                onTap: _selectDate,
              ),
              const SizedBox(height: 20),

              // Wallet Selector
              const Text(
                'Pilih Dompet Sumber Pembayaran',
                style: TextStyle(
                  color: AppTheme.textPrimary,
                  fontSize: 13.5,
                  fontWeight: FontWeight.w600,
                ),
              ),
              const SizedBox(height: 8),
              if (wallets.isEmpty)
                const Text('Tidak ada dompet aktif.', style: TextStyle(color: AppTheme.textMuted))
              else
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: wallets.map((wallet) {
                      final isSelected = _selectedWallet?.id == wallet.id;
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
                          selectedColor: AppTheme.primary,
                          labelStyle: TextStyle(
                            color: isSelected ? Colors.white : AppTheme.textPrimary,
                            fontWeight: FontWeight.w600,
                          ),
                          onSelected: (selected) {
                            if (selected) {
                              setState(() {
                                _selectedWallet = wallet;
                              });
                            }
                          },
                        ),
                      );
                    }).toList(),
                  ),
                ),
              const SizedBox(height: 20),

              // Category Selector Dropdown
              const Text(
                'Kategori Pengeluaran',
                style: TextStyle(
                  color: AppTheme.textPrimary,
                  fontSize: 13.5,
                  fontWeight: FontWeight.w600,
                ),
              ),
              const SizedBox(height: 6),
              DropdownButtonFormField<Category>(
                value: _selectedCategory,
                decoration: const InputDecoration(
                  prefixIcon: Icon(Icons.category_rounded, color: AppTheme.primary),
                ),
                items: expenseCategories.map((cat) {
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
              const SizedBox(height: 26),

              // Itemized Breakdown Section
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Rincian Barang (${_items.length} Item)',
                    style: const TextStyle(
                      color: AppTheme.textPrimary,
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  TextButton.icon(
                    onPressed: _addItemRow,
                    icon: const Icon(Icons.add_rounded, size: 18),
                    label: const Text('Tambah Baris'),
                  ),
                ],
              ),
              const SizedBox(height: 8),

              if (_items.isEmpty)
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: AppTheme.cardDecoration(),
                  child: const Center(
                    child: Text(
                      'Tidak ada rincian item individual yang terdeteksi.',
                      style: TextStyle(color: AppTheme.textMuted, fontSize: 13),
                    ),
                  ),
                )
              else
                ListView.separated(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: _items.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 10),
                  itemBuilder: (context, index) {
                    final item = _items[index];
                    return Container(
                      padding: const EdgeInsets.all(12),
                      decoration: AppTheme.cardDecoration(),
                      child: Row(
                        children: [
                          Expanded(
                            flex: 3,
                            child: TextFormField(
                              initialValue: item.itemName,
                              decoration: const InputDecoration(
                                hintText: 'Nama barang',
                                isDense: true,
                                contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                              ),
                              style: const TextStyle(fontSize: 13.5),
                              onChanged: (val) {
                                _items[index] = item.copyWith(itemName: val);
                              },
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            flex: 1,
                            child: TextFormField(
                              initialValue: item.quantity.toString(),
                              keyboardType: TextInputType.number,
                              inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                              decoration: const InputDecoration(
                                hintText: 'Qty',
                                isDense: true,
                                contentPadding: EdgeInsets.symmetric(horizontal: 8, vertical: 10),
                              ),
                              style: const TextStyle(fontSize: 13.5),
                              onChanged: (val) {
                                final qty = int.tryParse(val) ?? 1;
                                _items[index] = item.copyWith(quantity: qty);
                                _recalculateTotalFromItems();
                              },
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            flex: 2,
                            child: TextFormField(
                              initialValue: item.price.toStringAsFixed(0),
                              keyboardType: TextInputType.number,
                              inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                              decoration: const InputDecoration(
                                hintText: 'Harga',
                                isDense: true,
                                contentPadding: EdgeInsets.symmetric(horizontal: 8, vertical: 10),
                              ),
                              style: const TextStyle(fontSize: 13.5),
                              onChanged: (val) {
                                final pr = double.tryParse(val) ?? 0.0;
                                _items[index] = item.copyWith(price: pr);
                                _recalculateTotalFromItems();
                              },
                            ),
                          ),
                          IconButton(
                            padding: EdgeInsets.zero,
                            constraints: const BoxConstraints(),
                            onPressed: () => _removeItemRow(index),
                            icon: const Icon(Icons.close_rounded, color: AppTheme.expense, size: 18),
                          ),
                        ],
                      ),
                    );
                  },
                ),
              const SizedBox(height: 32),

              // Save Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _isSaving ? null : _submitTransaction,
                  child: _isSaving
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                        )
                      : const Text('Simpan Transaksi'),
                ),
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }
}
