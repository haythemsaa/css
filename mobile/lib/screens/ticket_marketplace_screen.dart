import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/ticket_listing.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class TicketMarketplaceScreen extends StatefulWidget {
  const TicketMarketplaceScreen({super.key});

  @override
  State<TicketMarketplaceScreen> createState() => _TicketMarketplaceScreenState();
}

class _TicketMarketplaceScreenState extends State<TicketMarketplaceScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();
  List<TicketListing> _listings = [];
  bool _isLoading = true;
  String? _selectedCategory;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {
          switch (_tabController.index) {
            case 0:
              _selectedCategory = null; // All
              break;
            case 1:
              _selectedCategory = 'tribune';
              break;
            case 2:
              _selectedCategory = 'populaire';
              break;
            case 3:
              _selectedCategory = 'vip';
              break;
          }
          _loadListings();
        });
      }
    });
    _loadListings();
  }

  Future<void> _loadListings() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getMarketplaceListings(
        category: _selectedCategory,
      );
      final List data = response.data['listings'];
      setState(() {
        _listings = data.map((json) => TicketListing.fromJson(json)).toList();
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
        title: const Text('MARKETPLACE DE BILLETS'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.add_circle_outline),
            onPressed: () {
              // Navigate to create listing screen
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Créer une annonce - Disponible prochainement'),
                ),
              );
            },
            tooltip: 'Vendre un billet',
          ),
          IconButton(
            icon: const Icon(Icons.person_outline),
            onPressed: () {
              // Navigate to my listings/purchases
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Mes annonces - Disponible prochainement'),
                ),
              );
            },
            tooltip: 'Mes annonces',
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: JuventusTheme.primaryWhite,
          indicatorWeight: 3,
          labelColor: JuventusTheme.primaryWhite,
          unselectedLabelColor: JuventusTheme.grey500,
          labelStyle: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 12,
          ),
          isScrollable: true,
          tabs: const [
            Tab(text: 'TOUS'),
            Tab(text: 'TRIBUNE'),
            Tab(text: 'POPULAIRE'),
            Tab(text: 'VIP'),
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
              onRefresh: _loadListings,
              color: JuventusTheme.primaryBlack,
              child: _listings.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.confirmation_number_outlined,
                            size: 80,
                            color: JuventusTheme.grey400,
                          ),
                          const SizedBox(height: 16),
                          const Text(
                            'Aucun billet disponible',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w600,
                              color: JuventusTheme.grey700,
                            ),
                          ),
                          const SizedBox(height: 8),
                          const Text(
                            'Revenez plus tard',
                            style: TextStyle(
                              fontSize: 14,
                              color: JuventusTheme.grey600,
                            ),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      itemCount: _listings.length,
                      padding: const EdgeInsets.all(16),
                      itemBuilder: (context, index) {
                        return _buildListingCard(_listings[index]);
                      },
                    ),
            ),
    );
  }

  Widget _buildListingCard(TicketListing listing) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');

    Color categoryColor;
    switch (listing.category) {
      case 'vip':
        categoryColor = JuventusTheme.accentGold;
        break;
      case 'tribune':
        categoryColor = JuventusTheme.info;
        break;
      case 'populaire':
        categoryColor = JuventusTheme.success;
        break;
      case 'virage':
        categoryColor = JuventusTheme.error;
        break;
      default:
        categoryColor = JuventusTheme.grey600;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      child: InkWell(
        onTap: () {
          _showListingDetails(listing);
        },
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Match info header
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          listing.match?.homeTeam ?? 'Match',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: JuventusTheme.primaryBlack,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Row(
                          children: [
                            const Icon(
                              Icons.calendar_today,
                              size: 14,
                              color: JuventusTheme.grey600,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              listing.match != null
                                  ? DateFormat('dd/MM/yyyy • HH:mm').format(listing.match!.matchDate)
                                  : 'Date à confirmer',
                              style: const TextStyle(
                                fontSize: 13,
                                color: JuventusTheme.grey600,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  if (listing.isFeatured)
                    Container(
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
                          fontSize: 10,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ),
                ],
              ),

              const SizedBox(height: 12),
              const Divider(color: JuventusTheme.grey300, height: 1),
              const SizedBox(height: 12),

              // Ticket details
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: [
                  _buildDetailChip(
                    icon: Icons.category,
                    label: listing.categoryDisplay,
                    color: categoryColor,
                  ),
                  if (listing.section != null)
                    _buildDetailChip(
                      icon: Icons.location_on_outlined,
                      label: 'Section ${listing.section}',
                      color: JuventusTheme.grey700,
                    ),
                  if (listing.row != null)
                    _buildDetailChip(
                      icon: Icons.table_rows,
                      label: 'Rang ${listing.row}',
                      color: JuventusTheme.grey700,
                    ),
                  if (listing.seatNumber != null)
                    _buildDetailChip(
                      icon: Icons.event_seat,
                      label: 'Place ${listing.seatNumber}',
                      color: JuventusTheme.grey700,
                    ),
                  _buildDetailChip(
                    icon: Icons.confirmation_number,
                    label: '${listing.quantity} billet${listing.quantity > 1 ? 's' : ''}',
                    color: JuventusTheme.grey700,
                  ),
                ],
              ),

              const SizedBox(height: 16),

              // Price and seller info
              Row(
                children: [
                  // Price
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Prix',
                          style: TextStyle(
                            fontSize: 12,
                            color: JuventusTheme.grey600,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Row(
                          children: [
                            Text(
                              currencyFormat.format(listing.sellingPrice),
                              style: const TextStyle(
                                fontSize: 22,
                                fontWeight: FontWeight.bold,
                                color: JuventusTheme.success,
                              ),
                            ),
                            if (listing.sellingPrice < listing.originalPrice) ...[
                              const SizedBox(width: 8),
                              Text(
                                currencyFormat.format(listing.originalPrice),
                                style: const TextStyle(
                                  fontSize: 14,
                                  decoration: TextDecoration.lineThrough,
                                  color: JuventusTheme.grey500,
                                ),
                              ),
                            ],
                          ],
                        ),
                      ],
                    ),
                  ),

                  // Seller info
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: JuventusTheme.grey100,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      children: [
                        const CircleAvatar(
                          radius: 16,
                          backgroundColor: JuventusTheme.primaryBlack,
                          child: Icon(
                            Icons.person,
                            color: JuventusTheme.primaryWhite,
                            size: 18,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              listing.seller?.name ?? 'Vendeur',
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: JuventusTheme.primaryBlack,
                              ),
                            ),
                            Row(
                              children: [
                                const Icon(
                                  Icons.star,
                                  size: 12,
                                  color: JuventusTheme.accentGold,
                                ),
                                const SizedBox(width: 2),
                                const Text(
                                  '4.8',
                                  style: TextStyle(
                                    fontSize: 11,
                                    color: JuventusTheme.grey600,
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

              // Additional info
              if (listing.allowNegotiation || listing.isVerified) ...[
                const SizedBox(height: 12),
                Row(
                  children: [
                    if (listing.isVerified)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: JuventusTheme.success.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(4),
                          border: Border.all(color: JuventusTheme.success.withOpacity(0.3)),
                        ),
                        child: Row(
                          children: const [
                            Icon(
                              Icons.verified,
                              size: 14,
                              color: JuventusTheme.success,
                            ),
                            SizedBox(width: 4),
                            Text(
                              'Vérifié',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: JuventusTheme.success,
                              ),
                            ),
                          ],
                        ),
                      ),
                    if (listing.allowNegotiation && listing.isVerified)
                      const SizedBox(width: 8),
                    if (listing.allowNegotiation)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: JuventusTheme.info.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: const Text(
                          'Négociable',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: JuventusTheme.info,
                          ),
                        ),
                      ),
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDetailChip({
    required IconData icon,
    required String label,
    required Color color,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(4),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  void _showListingDetails(TicketListing listing) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (context) {
        return Container(
          height: MediaQuery.of(context).size.height * 0.85,
          decoration: const BoxDecoration(
            color: JuventusTheme.primaryWhite,
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(20),
              topRight: Radius.circular(20),
            ),
          ),
          child: Column(
            children: [
              // Handle bar
              Container(
                margin: const EdgeInsets.only(top: 12, bottom: 8),
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: JuventusTheme.grey300,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),

              // Header
              Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  children: [
                    const Expanded(
                      child: Text(
                        'Détails du billet',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                          color: JuventusTheme.primaryBlack,
                        ),
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ],
                ),
              ),

              // Content
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Match info
                      Text(
                        listing.match?.homeTeam ?? 'Match',
                        style: const TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                          color: JuventusTheme.primaryBlack,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'vs ${listing.match?.awayTeam ?? 'Équipe adverse'}',
                        style: const TextStyle(
                          fontSize: 16,
                          color: JuventusTheme.grey700,
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Price
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: JuventusTheme.success.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text(
                              'Prix total',
                              style: TextStyle(
                                fontSize: 16,
                                color: JuventusTheme.grey700,
                              ),
                            ),
                            Text(
                              currencyFormat.format(listing.sellingPrice),
                              style: const TextStyle(
                                fontSize: 28,
                                fontWeight: FontWeight.bold,
                                color: JuventusTheme.success,
                              ),
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 24),

                      // Description
                      if (listing.description != null) ...[
                        const Text(
                          'Description',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: JuventusTheme.primaryBlack,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          listing.description!,
                          style: const TextStyle(
                            fontSize: 14,
                            color: JuventusTheme.grey700,
                            height: 1.5,
                          ),
                        ),
                        const SizedBox(height: 24),
                      ],

                      // Action buttons
                      const SizedBox(height: 16),
                    ],
                  ),
                ),
              ),

              // Bottom buttons
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: JuventusTheme.primaryWhite,
                  boxShadow: [
                    BoxShadow(
                      color: JuventusTheme.primaryBlack.withOpacity(0.1),
                      blurRadius: 8,
                      offset: const Offset(0, -2),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () {
                          Navigator.pop(context);
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text('Réserver - Disponible prochainement'),
                              backgroundColor: JuventusTheme.info,
                            ),
                          );
                        },
                        style: OutlinedButton.styleFrom(
                          side: const BorderSide(color: JuventusTheme.primaryBlack, width: 2),
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: const Text(
                          'RÉSERVER',
                          style: TextStyle(
                            color: JuventusTheme.primaryBlack,
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ElevatedButton(
                        onPressed: () {
                          Navigator.pop(context);
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text('Acheter - Disponible prochainement'),
                              backgroundColor: JuventusTheme.success,
                            ),
                          );
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: JuventusTheme.primaryBlack,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          elevation: 0,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: const Text(
                          'ACHETER',
                          style: TextStyle(
                            color: JuventusTheme.primaryWhite,
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }
}
