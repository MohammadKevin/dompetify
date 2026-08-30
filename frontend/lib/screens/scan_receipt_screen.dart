import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import '../core/theme/app_theme.dart';
import '../providers/receipt_scan_provider.dart';
import 'review_receipt_screen.dart';

class ScanReceiptScreen extends ConsumerStatefulWidget {
  const ScanReceiptScreen({super.key});

  @override
  ConsumerState<ScanReceiptScreen> createState() => _ScanReceiptScreenState();
}

class _ScanReceiptScreenState extends ConsumerState<ScanReceiptScreen> with SingleTickerProviderStateMixin {
  File? _selectedImage;
  final ImagePicker _picker = ImagePicker();
  late AnimationController _pulseController;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _pulseController.dispose();
    super.dispose();
  }

  Future<void> _pickImage(ImageSource source) async {
    try {
      final picked = await _picker.pickImage(
        source: source,
        maxWidth: 1600,
        maxHeight: 2000,
        imageQuality: 88,
      );

      if (picked != null) {
        setState(() {
          _selectedImage = File(picked.path);
        });
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal memilih gambar: $e')),
        );
      }
    }
  }

  Future<void> _processScan() async {
    if (_selectedImage == null) return;

    try {
      final result = await ref.read(receiptScanProvider.notifier).scanReceipt(_selectedImage!);

      if (result != null && mounted) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) => ReviewReceiptScreen(
              scanResult: result,
              imageFile: _selectedImage,
            ),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.expense,
            content: Text('Gagal memindai struk: $e'),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final scanState = ref.watch(receiptScanProvider);
    final isScanning = scanState.status == ReceiptScanStatus.scanning;

    return Scaffold(
      backgroundColor: AppTheme.scaffoldBackground,
      appBar: AppBar(
        title: const Text('Scan Struk Belanja AI'),
        actions: [
          if (_selectedImage != null && !isScanning)
            TextButton(
              onPressed: () {
                setState(() {
                  _selectedImage = null;
                });
                ref.read(receiptScanProvider.notifier).reset();
              },
              child: const Text('Ganti'),
            ),
        ],
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Tips Banner
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: AppTheme.primaryContainer,
                  borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                  border: Border.all(color: const Color(0xFFBAE6FD)),
                ),
                child: const Row(
                  children: [
                    Icon(Icons.lightbulb_outline_rounded, color: AppTheme.primaryDark, size: 20),
                    SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        'Pastikan struk berada di tempat terang, tidak terlipat, dan teks terlihat jelas.',
                        style: TextStyle(
                          color: AppTheme.textPrimary,
                          fontSize: 12.5,
                          height: 1.3,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),

              // Image Viewport / Placeholder
              Expanded(
                child: Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
                    border: Border.all(
                      color: isScanning ? AppTheme.primary : AppTheme.cardBorder,
                      width: isScanning ? 2.0 : 1.0,
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFF0F172A).withOpacity(0.04),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  clipBehavior: Clip.antiAlias,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      if (_selectedImage != null)
                        Image.file(
                          _selectedImage!,
                          fit: BoxFit.contain,
                          width: double.infinity,
                          height: double.infinity,
                        )
                      else
                        Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(20),
                              decoration: BoxDecoration(
                                color: AppTheme.primaryContainer.withOpacity(0.5),
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.document_scanner_rounded,
                                color: AppTheme.primary,
                                size: 48,
                              ),
                            ),
                            const SizedBox(height: 16),
                            const Text(
                              'Pilih atau Foto Struk Anda',
                              style: TextStyle(
                                color: AppTheme.textPrimary,
                                fontSize: 16,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            const SizedBox(height: 6),
                            const Text(
                              'Gemini Vision AI akan mengekstrak toko, nominal, & rincian barang',
                              style: TextStyle(
                                color: AppTheme.textMuted,
                                fontSize: 12.5,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          ],
                        ),

                      // Scanning Overlay Animation
                      if (isScanning)
                        Container(
                          color: Colors.black.withOpacity(0.55),
                          child: Center(
                            child: Column(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                AnimatedBuilder(
                                  animation: _pulseController,
                                  builder: (context, child) {
                                    return Transform.scale(
                                      scale: 1.0 + (_pulseController.value * 0.15),
                                      child: Container(
                                        padding: const EdgeInsets.all(18),
                                        decoration: BoxDecoration(
                                          color: AppTheme.primary.withOpacity(0.25),
                                          shape: BoxShape.circle,
                                        ),
                                        child: const CircularProgressIndicator(
                                          color: Colors.white,
                                          strokeWidth: 3.5,
                                        ),
                                      ),
                                    );
                                  },
                                ),
                                const SizedBox(height: 24),
                                const Text(
                                  'Gemini Vision AI sedang menganalisis struk...',
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 14.5,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                                const SizedBox(height: 6),
                                Text(
                                  'Mendeteksi nama toko, tanggal, nominal, dan daftar barang',
                                  style: TextStyle(
                                    color: Colors.white.withOpacity(0.8),
                                    fontSize: 12,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Action Buttons
              if (_selectedImage == null) ...[
                Row(
                  children: [
                    Expanded(
                      child: ElevatedButton.icon(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.white,
                          foregroundColor: AppTheme.textPrimary,
                          side: const BorderSide(color: AppTheme.cardBorder),
                        ),
                        onPressed: () => _pickImage(ImageSource.gallery),
                        icon: const Icon(Icons.photo_library_outlined, color: AppTheme.primary),
                        label: const Text('Galeri'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ElevatedButton.icon(
                        onPressed: () => _pickImage(ImageSource.camera),
                        icon: const Icon(Icons.camera_alt_rounded),
                        label: const Text('Kamera'),
                      ),
                    ),
                  ],
                ),
              ] else ...[
                ElevatedButton.icon(
                  onPressed: isScanning ? null : _processScan,
                  icon: const Icon(Icons.auto_awesome_rounded),
                  label: const Text('Pindai & Ekstrak Data (AI)'),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
