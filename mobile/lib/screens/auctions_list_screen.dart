import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/auction.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';
import 'auction_details_screen.dart';

class AuctionsListScreen extends StatefulWidget {
  const AuctionsListScreen({super.key});

  @override
  State<AuctionsListScreen> createState() => _AuctionsListScreenState();
}

class _AuctionsListScreenState extends State<AuctionsListScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();
  List<AuctionProduct> _auctions = [];
  bool _isLoading = true;
  String _selectedStatus = 'active';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {
          switch (_tabController.index) {
            case 0:
              _selectedStatus = 'active';
              break;
            case 1:
              _selectedStatus = 'upcoming';
              break;
            case 2:
              _selectedStatus = 'ended';
              break;
          }
          _loadAuctions();
        });
      }
    });
    _loadAuctions();
  }

  Future<void> _loadAuctions() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getAuctions(status: _selectedStatus);
      final List data = response.data['data'];
      setState(() {
        _auctions = data.map((json) => AuctionProduct.fromJson(json)).toList();
        _isLoading = false;
      });
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('ENCHÈRES EXCLUSIVES'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: JuventusTheme.primaryWhite,
          indicatorWeight: 3,
          labelColor: JuventusTheme.primaryWhite,
          unselectedLabelColor: JuventusTheme.grey500,
          labelStyle: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 13,
          ),
          tabs: const [
            Tab(text: 'EN COURS'),
            Tab(text: 'À VENIR'),
            Tab(text: 'TERMINÉES'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: JuventusTheme.primaryBlack,
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadAuctions,
              color: JuventusTheme.primaryBlack,
              child: _auctions.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.gavel_outlined,
                            size: 80,
                            color: JuventusTheme.grey400,
                          ),
                          const SizedBox(height: 16),
                          const Text(
                            'Aucune enchère disponible',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w600,
                              color: JuventusTheme.grey700,
                            ),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      itemCount: _auctions.length,
                      padding: const EdgeInsets.all(16),
                      itemBuilder: (context, index) {
                        return _buildAuctionCard(_auctions[index]);
                      },
                    ),
            ),
    );
  }

  Widget _buildAuctionCard(AuctionProduct auction) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => AuctionDetailsScreen(auctionId: auction.id),
            ),
          );
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            Stack(
              children: [
                Image.network(
                  auction.images.isNotEmpty
                      ? auction.images[0]
                      : 'https://via.placeholder.com/400x200',
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) {
                    return Container(
                      height: 200,
                      color: JuventusTheme.grey200,
                      child: const Icon(
                        Icons.image_outlined,
                        size: 50,
                        color: JuventusTheme.grey400,
                      ),
                    );
                  },
                ),
                if (auction.isFeatured)
                  Positioned(
                    top: 8,
                    right: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: JuventusTheme.accentGold,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: const Text(
                        'VEDETTE',
                        style: TextStyle(
                          color: JuventusTheme.primaryWhite,
                          fontWeight: FontWeight.bold,
                          fontSize: 12,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ),
                  ),
                if (auction.isActive && auction.timeRemaining != null)
                  Positioned(
                    bottom: 8,
                    left: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: JuventusTheme.primaryBlack.withOpacity(0.85),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Row(
                        children: [
                          const Icon(
                            Icons.timer,
                            color: JuventusTheme.primaryWhite,
                            size: 16,
                          ),
                          const SizedBox(width: 4),
                          Text(
                            _formatTimeRemaining(auction.timeRemaining!),
                            style: const TextStyle(
                              color: JuventusTheme.primaryWhite,
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
              ],
            ),

            // Info
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    auction.title,
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: JuventusTheme.primaryBlack,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: JuventusTheme.grey200,
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          auction.categoryDisplay,
                          style: const TextStyle(
                            fontSize: 12,
                            color: JuventusTheme.grey700,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                      const Spacer(),
                      Text(
                        '${auction.totalBids} enchères',
                        style: const TextStyle(
                          fontSize: 12,
                          color: JuventusTheme.grey600,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Enchère actuelle',
                            style: TextStyle(
                              fontSize: 12,
                              color: JuventusTheme.grey600,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            currencyFormat.format(auction.currentBid),
                            style: const TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                              color: JuventusTheme.success,
                            ),
                          ),
                        ],
                      ),
                      if (auction.buyNowPrice != null)
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            const Text(
                              'Achat immédiat',
                              style: TextStyle(
                                fontSize: 12,
                                color: JuventusTheme.grey600,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              currencyFormat.format(auction.buyNowPrice!),
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                color: JuventusTheme.info,
                              ),
                            ),
                          ],
                        ),
                    ],
                  ),
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
    _tabController.dispose();
    super.dispose();
  }
}
