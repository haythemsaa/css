import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'dart:async';
import '../models/auction.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class AuctionDetailsScreen extends StatefulWidget {
  final int auctionId;

  const AuctionDetailsScreen({super.key, required this.auctionId});

  @override
  State<AuctionDetailsScreen> createState() => _AuctionDetailsScreenState();
}

class _AuctionDetailsScreenState extends State<AuctionDetailsScreen> {
  final ApiService _apiService = ApiService();
  final TextEditingController _bidController = TextEditingController();
  AuctionProduct? _auction;
  bool _isLoading = true;
  Timer? _countdownTimer;

  @override
  void initState() {
    super.initState();
    _loadAuction();
  }

  Future<void> _loadAuction() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getAuctionDetails(widget.auctionId);
      setState(() {
        _auction = AuctionProduct.fromJson(response.data);
        _isLoading = false;
        _bidController.text = _auction!.minimumBid.toStringAsFixed(2);
      });
      _startCountdown();
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Erreur: $e'),
            backgroundColor: JuventusTheme.error,
          ),
        );
      }
    }
  }

  void _startCountdown() {
    _countdownTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_auction != null && _auction!.isActive && mounted) {
        setState(() {}); // Rebuild to update countdown
      } else {
        timer.cancel();
      }
    });
  }

  Future<void> _placeBid() async {
    final bidAmount = double.tryParse(_bidController.text);
    if (bidAmount == null || bidAmount < _auction!.minimumBid) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('L\'enchère minimum est de ${_auction!.minimumBid} TND'),
          backgroundColor: JuventusTheme.error,
        ),
      );
      return;
    }

    try {
      await _apiService.placeBid(widget.auctionId, {'bid_amount': bidAmount});
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Enchère placée avec succès!'),
            backgroundColor: JuventusTheme.success,
          ),
        );
        _loadAuction(); // Reload to get updated bid
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Erreur: $e'),
            backgroundColor: JuventusTheme.error,
          ),
        );
      }
    }
  }

  Future<void> _buyNow() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: JuventusTheme.primaryWhite,
        title: const Text(
          'Achat immédiat',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        content: Text(
          'Confirmer l\'achat immédiat pour ${_auction!.buyNowPrice} TND?',
          style: const TextStyle(color: JuventusTheme.grey700),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text(
              'Annuler',
              style: TextStyle(color: JuventusTheme.grey600),
            ),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: JuventusTheme.info,
              elevation: 0,
            ),
            child: const Text(
              'Confirmer',
              style: TextStyle(
                color: JuventusTheme.primaryWhite,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );

    if (confirm == true) {
      try {
        await _apiService.buyNow(widget.auctionId);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Achat réussi! Vous avez remporté cet article.'),
              backgroundColor: JuventusTheme.success,
            ),
          );
          Navigator.pop(context);
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Erreur: $e'),
              backgroundColor: JuventusTheme.error,
            ),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');

    if (_isLoading || _auction == null) {
      return Scaffold(
        backgroundColor: JuventusTheme.grey100,
        appBar: AppBar(
          title: const Text('DÉTAILS ENCHÈRE'),
          backgroundColor: JuventusTheme.primaryBlack,
          elevation: 0,
        ),
        body: const Center(
          child: CircularProgressIndicator(
            color: JuventusTheme.primaryBlack,
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('DÉTAILS ENCHÈRE'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Images carousel
            SizedBox(
              height: 300,
              child: PageView.builder(
                itemCount: _auction!.images.length,
                itemBuilder: (context, index) {
                  return Image.network(
                    _auction!.images[index],
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) {
                      return Container(
                        color: JuventusTheme.grey200,
                        child: const Icon(
                          Icons.image_outlined,
                          size: 100,
                          color: JuventusTheme.grey400,
                        ),
                      );
                    },
                  );
                },
              ),
            ),

            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Title & Category
                  Text(
                    _auction!.title,
                    style: const TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: JuventusTheme.primaryBlack,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Chip(
                    label: Text(
                      _auction!.categoryDisplay,
                      style: const TextStyle(
                        fontWeight: FontWeight.w600,
                        color: JuventusTheme.grey700,
                      ),
                    ),
                    backgroundColor: JuventusTheme.grey200,
                  ),

                  const SizedBox(height: 16),

                  // Time Remaining
                  if (_auction!.isActive && _auction!.timeRemaining != null)
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: JuventusTheme.primaryWhite,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: JuventusTheme.error, width: 2),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.timer, color: JuventusTheme.error),
                          const SizedBox(width: 8),
                          Text(
                            'Temps restant: ${_formatTimeRemaining(_auction!.timeRemaining!)}',
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: JuventusTheme.error,
                            ),
                          ),
                        ],
                      ),
                    ),

                  const SizedBox(height: 16),

                  // Current Bid
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: JuventusTheme.primaryWhite,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: JuventusTheme.success, width: 2),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Enchère actuelle',
                          style: TextStyle(
                            fontSize: 14,
                            color: JuventusTheme.grey600,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          currencyFormat.format(_auction!.currentBid),
                          style: const TextStyle(
                            fontSize: 32,
                            fontWeight: FontWeight.bold,
                            color: JuventusTheme.success,
                          ),
                        ),
                        Text(
                          '${_auction!.totalBids} enchères',
                          style: const TextStyle(
                            fontSize: 12,
                            color: JuventusTheme.grey600,
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 16),

                  // Description
                  const Text(
                    'Description',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: JuventusTheme.primaryBlack,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _auction!.description,
                    style: const TextStyle(
                      fontSize: 16,
                      color: JuventusTheme.grey700,
                      height: 1.5,
                    ),
                  ),

                  if (_auction!.metadata != null) ...[
                    const SizedBox(height: 16),
                    const Text(
                      'Informations supplémentaires',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: JuventusTheme.primaryBlack,
                      ),
                    ),
                    const SizedBox(height: 8),
                    ..._auction!.metadata!.entries.map((e) => Padding(
                      padding: const EdgeInsets.only(bottom: 4),
                      child: Row(
                        children: [
                          Text(
                            '${e.key}: ',
                            style: const TextStyle(
                              fontWeight: FontWeight.w600,
                              color: JuventusTheme.primaryBlack,
                            ),
                          ),
                          Text(
                            e.value.toString(),
                            style: const TextStyle(
                              color: JuventusTheme.grey700,
                            ),
                          ),
                        ],
                      ),
                    )),
                  ],

                  const SizedBox(height: 24),

                  // Bid Form
                  if (_auction!.isActive) ...[
                    const Text(
                      'Placer une enchère',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: JuventusTheme.primaryBlack,
                      ),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _bidController,
                      keyboardType: TextInputType.number,
                      decoration: InputDecoration(
                        labelText: 'Montant (TND)',
                        hintText: 'Minimum: ${_auction!.minimumBid} TND',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: const BorderSide(
                            color: JuventusTheme.grey300,
                          ),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: const BorderSide(
                            color: JuventusTheme.primaryBlack,
                            width: 2,
                          ),
                        ),
                        prefixIcon: const Icon(
                          Icons.attach_money,
                          color: JuventusTheme.grey600,
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _placeBid,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: JuventusTheme.success,
                          padding: const EdgeInsets.all(16),
                          elevation: 0,
                        ),
                        child: const Text(
                          'ENCHÉRIR',
                          style: TextStyle(
                            fontSize: 18,
                            color: JuventusTheme.primaryWhite,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 0.5,
                          ),
                        ),
                      ),
                    ),
                  ],

                  if (_auction!.buyNowPrice != null && _auction!.isActive) ...[
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton(
                        onPressed: _buyNow,
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.all(16),
                          side: const BorderSide(
                            color: JuventusTheme.info,
                            width: 2,
                          ),
                        ),
                        child: Text(
                          'ACHETER MAINTENANT - ${currencyFormat.format(_auction!.buyNowPrice!)}',
                          style: const TextStyle(
                            fontSize: 16,
                            color: JuventusTheme.info,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 0.5,
                          ),
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _formatTimeRemaining(int seconds) {
    final duration = Duration(seconds: seconds);
    if (duration.inDays > 0) {
      return '${duration.inDays}j ${duration.inHours % 24}h';
    } else if (duration.inHours > 0) {
      return '${duration.inHours}h ${duration.inMinutes % 60}min';
    } else {
      return '${duration.inMinutes}min ${duration.inSeconds % 60}s';
    }
  }

  @override
  void dispose() {
    _countdownTimer?.cancel();
    _bidController.dispose();
    super.dispose();
  }
}
