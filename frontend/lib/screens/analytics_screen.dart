import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../core/theme/app_theme.dart';
import '../core/utils/currency_formatter.dart';
import '../models/transaction.dart';
import '../providers/transactions_provider.dart';

class AnalyticsScreen extends ConsumerStatefulWidget {
  const AnalyticsScreen({super.key});

  @override
  ConsumerState<AnalyticsScreen> createState() => _AnalyticsScreenState();
}

class _AnalyticsScreenState extends ConsumerState<AnalyticsScreen> {
  int _touchedIndex = -1;

  @override
  Widget build(BuildContext context) {
    final transactionsState = ref.watch(transactionsProvider);
    final transactions = transactionsState.transactions;

    final totalIncome = transactionsState.totalIncome;
    final totalExpense = transactionsState.totalExpense;
    final netFlow = transactionsState.netFlow;

    // Group expense by category
    final Map<String, double> categoryExpenses = {};
    for (var tx in transactions.where((t) => t.type == TransactionType.expense)) {
      final catName = tx.category?.name ?? 'Lain-lain';
      categoryExpenses[catName] = (categoryExpenses[catName] ?? 0.0) + tx.amount;
    }

    final categoryColors = [
      const Color(0xFF0EA5E9),
      const Color(0xFFEF4444),
      const Color(0xFFF59E0B),
      const Color(0xFF10B981),
      const Color(0xFF8B5CF6),
      const Color(0xFFEC4899),
      const Color(0xFF6366F1),
      const Color(0xFF14B8A6),
    ];

    return Scaffold(
      backgroundColor: AppTheme.scaffoldBackground,
      appBar: AppBar(
        title: const Text('Analisis & Laporan'),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await ref.read(transactionsProvider.notifier).fetchTransactions();
        },
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. Overview Summary Metric Cards
              Row(
                children: [
                  Expanded(
                    child: Container(
                      padding: const EdgeInsets.all(16),
                      decoration: AppTheme.cardDecoration(),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(6),
                                decoration: BoxDecoration(
                                  color: AppTheme.income.withOpacity(0.12),
                                  shape: BoxShape.circle,
                                ),
                                child: const Icon(Icons.arrow_downward, color: AppTheme.income, size: 14),
                              ),
                              const SizedBox(width: 8),
                              const Text('Pemasukan', style: TextStyle(color: AppTheme.textMuted, fontSize: 12)),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Text(
                            CurrencyFormatter.format(totalIncome),
                            style: const TextStyle(
                              color: AppTheme.textPrimary,
                              fontSize: 16,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Container(
                      padding: const EdgeInsets.all(16),
                      decoration: AppTheme.cardDecoration(),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(6),
                                decoration: BoxDecoration(
                                  color: AppTheme.expense.withOpacity(0.12),
                                  shape: BoxShape.circle,
                                ),
                                child: const Icon(Icons.arrow_upward, color: AppTheme.expense, size: 14),
                              ),
                              const SizedBox(width: 8),
                              const Text('Pengeluaran', style: TextStyle(color: AppTheme.textMuted, fontSize: 12)),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Text(
                            CurrencyFormatter.format(totalExpense),
                            style: const TextStyle(
                              color: AppTheme.textPrimary,
                              fontSize: 16,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Net Flow Banner
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: netFlow >= 0 ? const Color(0xFFF0FDF4) : const Color(0xFFFEF2F2),
                  borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
                  border: Border.all(
                    color: netFlow >= 0 ? const Color(0xFFBBF7D0) : const Color(0xFFFECACA),
                  ),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Sisa Arus Kas (Net Cashflow)',
                          style: TextStyle(
                            color: netFlow >= 0 ? const Color(0xFF166534) : const Color(0xFF991B1B),
                            fontSize: 12.5,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          CurrencyFormatter.format(netFlow),
                          style: TextStyle(
                            color: netFlow >= 0 ? AppTheme.income : AppTheme.expense,
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                    Icon(
                      netFlow >= 0 ? Icons.trending_up_rounded : Icons.trending_down_rounded,
                      color: netFlow >= 0 ? AppTheme.income : AppTheme.expense,
                      size: 32,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 28),

              // 2. Breakdown by Category Section
              const Text(
                'Distribusi Pengeluaran',
                style: TextStyle(
                  color: AppTheme.textPrimary,
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 14),

              if (categoryExpenses.isEmpty)
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(32),
                  decoration: AppTheme.cardDecoration(),
                  child: const Center(
                    child: Text(
                      'Belum ada data pengeluaran untuk dianalisis.',
                      style: TextStyle(color: AppTheme.textMuted),
                    ),
                  ),
                )
              else ...[
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: AppTheme.cardDecoration(),
                  child: Column(
                    children: [
                      SizedBox(
                        height: 180,
                        child: PieChart(
                          PieChartData(
                            pieTouchData: PieTouchData(
                              touchCallback: (FlTouchEvent event, pieTouchResponse) {
                                setState(() {
                                  if (!event.isInterestedForInteractions ||
                                      pieTouchResponse == null ||
                                      pieTouchResponse.touchedSection == null) {
                                    _touchedIndex = -1;
                                    return;
                                  }
                                  _touchedIndex = pieTouchResponse.touchedSection!.touchedSectionIndex;
                                });
                              },
                            ),
                            borderData: FlBorderData(show: false),
                            sectionsSpace: 3,
                            centerSpaceRadius: 40,
                            sections: categoryExpenses.entries.toList().asMap().entries.map((entry) {
                              final idx = entry.key;
                              final cat = entry.value;
                              final isTouched = idx == _touchedIndex;
                              final fontSize = isTouched ? 14.0 : 11.0;
                              final radius = isTouched ? 50.0 : 42.0;
                              final color = categoryColors[idx % categoryColors.length];
                              final percent = totalExpense > 0 ? (cat.value / totalExpense * 100) : 0;

                              return PieChartSectionData(
                                color: color,
                                value: cat.value,
                                title: '${percent.toStringAsFixed(0)}%',
                                radius: radius,
                                titleStyle: TextStyle(
                                  fontSize: fontSize,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white,
                                ),
                              );
                            }).toList(),
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),
                      const Divider(),
                      const SizedBox(height: 12),

                      // Category Legend & Nominal List
                      ListView.separated(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: categoryExpenses.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 8),
                        itemBuilder: (context, idx) {
                          final entry = categoryExpenses.entries.elementAt(idx);
                          final color = categoryColors[idx % categoryColors.length];
                          final percent = totalExpense > 0 ? (entry.value / totalExpense * 100) : 0;

                          return Row(
                            children: [
                              Container(
                                width: 12,
                                height: 12,
                                decoration: BoxDecoration(
                                  color: color,
                                  shape: BoxShape.circle,
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  entry.key,
                                  style: const TextStyle(
                                    color: AppTheme.textPrimary,
                                    fontSize: 13.5,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                              Text(
                                '${percent.toStringAsFixed(1)}% • ${CurrencyFormatter.format(entry.value)}',
                                style: const TextStyle(
                                  color: AppTheme.textSecondary,
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          );
                        },
                      ),
                    ],
                  ),
                ),
              ],
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }
}
