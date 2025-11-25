import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/auction.dart';
import '../services/api_service.dart';
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
          SnackBar(content: Text('Erreur: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Enchères Exclusives'),
        backgroundColor: Colors.black,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.yellow[700],
          tabs: const [
            Tab(text: 'En Cours'),
            Tab(text: 'À Venir'),
            Tab(text: 'Terminées'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadAuctions,
              child: _auctions.isEmpty
                  ? const Center(child: Text('Aucune enchère disponible'))
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

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
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
                      color: Colors.grey[300],
                      child: const Icon(Icons.image, size: 50),
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
                        color: Colors.yellow[700],
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: const Text(
                        'VEDETTE',
                        style: TextStyle(
                          color: Colors.black,
                          fontWeight: FontWeight.bold,
                          fontSize: 12,
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
                        color: Colors.black87,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.timer, color: Colors.white, size: 16),
                          const SizedBox(width: 4),
                          Text(
                            _formatTimeRemaining(auction.timeRemaining!),
                            style: const TextStyle(color: Colors.white, fontSize: 12),
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
                          color: Colors.grey[200],
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          auction.categoryDisplay,
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[700],
                          ),
                        ),
                      ),
                      const Spacer(),
                      Text(
                        '${auction.totalBids} enchères',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[600],
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
                          Text(
                            'Enchère actuelle',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            currencyFormat.format(auction.currentBid),
                            style: const TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                              color: Colors.green,
                            ),
                          ),
                        ],
                      ),
                      if (auction.buyNowPrice != null)
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text(
                              'Achat immédiat',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[600],
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              currencyFormat.format(auction.buyNowPrice!),
                              style: TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                color: Colors.blue[700],
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
