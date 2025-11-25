import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'dart:async';
import '../models/auction.dart';
import '../services/api_service.dart';

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
          SnackBar(content: Text('Erreur: $e')),
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
          backgroundColor: Colors.red,
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
            backgroundColor: Colors.green,
          ),
        );
        _loadAuction(); // Reload to get updated bid
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  Future<void> _buyNow() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Achat immédiat'),
        content: Text(
          'Confirmer l\'achat immédiat pour ${_auction!.buyNowPrice} TND?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Annuler'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Confirmer'),
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
              backgroundColor: Colors.green,
            ),
          );
          Navigator.pop(context);
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Erreur: $e'), backgroundColor: Colors.red),
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
        appBar: AppBar(title: const Text('Détails enchère')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Détails enchère'),
        backgroundColor: Colors.black,
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
                        color: Colors.grey[300],
                        child: const Icon(Icons.image, size: 100),
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
                    ),
                  ),
                  const SizedBox(height: 8),
                  Chip(
                    label: Text(_auction!.categoryDisplay),
                    backgroundColor: Colors.grey[200],
                  ),

                  const SizedBox(height: 16),

                  // Time Remaining
                  if (_auction!.isActive && _auction!.timeRemaining != null)
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.red[50],
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.timer, color: Colors.red),
                          const SizedBox(width: 8),
                          Text(
                            'Temps restant: ${_formatTimeRemaining(_auction!.timeRemaining!)}',
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: Colors.red,
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
                      color: Colors.green[50],
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.green),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Enchère actuelle',
                          style: TextStyle(fontSize: 14, color: Colors.grey),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          currencyFormat.format(_auction!.currentBid),
                          style: const TextStyle(
                            fontSize: 32,
                            fontWeight: FontWeight.bold,
                            color: Colors.green,
                          ),
                        ),
                        Text(
                          '${_auction!.totalBids} enchères',
                          style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 16),

                  // Description
                  const Text(
                    'Description',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _auction!.description,
                    style: const TextStyle(fontSize: 16),
                  ),

                  if (_auction!.metadata != null) ...[
                    const SizedBox(height: 16),
                    const Text(
                      'Informations supplémentaires',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    ..._auction!.metadata!.entries.map((e) => Padding(
                      padding: const EdgeInsets.only(bottom: 4),
                      child: Row(
                        children: [
                          Text(
                            '${e.key}: ',
                            style: const TextStyle(fontWeight: FontWeight.w600),
                          ),
                          Text(e.value.toString()),
                        ],
                      ),
                    )),
                  ],

                  const SizedBox(height: 24),

                  // Bid Form
                  if (_auction!.isActive) ...[
                    const Text(
                      'Placer une enchère',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _bidController,
                      keyboardType: TextInputType.number,
                      decoration: InputDecoration(
                        labelText: 'Montant (TND)',
                        hintText: 'Minimum: ${_auction!.minimumBid} TND',
                        border: const OutlineInputBorder(),
                        prefixIcon: const Icon(Icons.attach_money),
                      ),
                    ),
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _placeBid,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green,
                          padding: const EdgeInsets.all(16),
                        ),
                        child: const Text(
                          'Enchérir',
                          style: TextStyle(fontSize: 18, color: Colors.white),
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
                          side: const BorderSide(color: Colors.blue, width: 2),
                        ),
                        child: Text(
                          'Acheter maintenant - ${currencyFormat.format(_auction!.buyNowPrice!)}',
                          style: const TextStyle(fontSize: 16),
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
