import 'package:flutter/material.dart';
import '../models/additional_models.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class PartnersScreen extends StatefulWidget {
  const PartnersScreen({super.key});

  @override
  State<PartnersScreen> createState() => _PartnersScreenState();
}

class _PartnersScreenState extends State<PartnersScreen> {
  final ApiService _apiService = ApiService();
  List<Partner> _partners = [];
  List<Offer> _flashOffers = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    try {
      final partnersResponse = await _apiService.getPartners();
      final offersResponse = await _apiService.getFlashOffers();

      setState(() {
        _partners = (partnersResponse.data['data'] as List)
            .map((json) => Partner.fromJson(json))
            .toList();
        _flashOffers = (offersResponse.data['data'] as List)
            .map((json) => Offer.fromJson(json))
            .toList();
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur: $e'), backgroundColor: JuventusTheme.error),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('PARTENAIRES & OFFRES'),
        backgroundColor: JuventusTheme.primaryBlack,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: JuventusTheme.primaryBlack))
          : RefreshIndicator(
              onRefresh: _loadData,
              color: JuventusTheme.primaryBlack,
              child: CustomScrollView(
                slivers: [
                  if (_flashOffers.isNotEmpty) ...[
                    SliverToBoxAdapter(child: _buildFlashOffersSection()),
                  ],
                  SliverToBoxAdapter(child: _buildSectionHeader('TOUS LES PARTENAIRES')),
                  SliverPadding(
                    padding: const EdgeInsets.all(16),
                    sliver: SliverGrid(
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        childAspectRatio: 0.85,
                        crossAxisSpacing: 12,
                        mainAxisSpacing: 12,
                      ),
                      delegate: SliverChildBuilderDelegate(
                        (context, index) => _buildPartnerCard(_partners[index]),
                        childCount: _partners.length,
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildFlashOffersSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.fromLTRB(16, 16, 16, 12),
          child: Row(
            children: [
              Icon(Icons.flash_on, color: JuventusTheme.warning, size: 20),
              SizedBox(width: 8),
              Text(
                'OFFRES FLASH',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 1,
                ),
              ),
            ],
          ),
        ),
        SizedBox(
          height: 160,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 12),
            itemCount: _flashOffers.length,
            itemBuilder: (context, index) => _buildFlashOfferCard(_flashOffers[index]),
          ),
        ),
      ],
    );
  }

  Widget _buildFlashOfferCard(Offer offer) {
    return Container(
      width: 280,
      margin: const EdgeInsets.symmetric(horizontal: 4),
      decoration: JuventusDecorations.whiteCard,
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    offer.title,
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: JuventusTheme.error,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    offer.discountType == 'percentage'
                        ? '-${offer.discountValue?.toInt()}%'
                        : '${offer.discountValue?.toInt()}€',
                    style: const TextStyle(
                      color: JuventusTheme.primaryWhite,
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Expanded(
              child: Text(
                offer.description,
                style: const TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                maxLines: 3,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const SizedBox(height: 8),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {},
                style: ElevatedButton.styleFrom(
                  backgroundColor: JuventusTheme.primaryBlack,
                  padding: const EdgeInsets.symmetric(vertical: 8),
                ),
                child: const Text('VOIR L\'OFFRE', style: TextStyle(fontSize: 11)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 24, 16, 0),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.bold,
          letterSpacing: 1,
        ),
      ),
    );
  }

  Widget _buildPartnerCard(Partner partner) {
    return InkWell(
      onTap: () {},
      child: Container(
        decoration: JuventusDecorations.whiteCard,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (partner.logoUrl != null)
              Container(
                height: 80,
                decoration: BoxDecoration(
                  color: JuventusTheme.grey100,
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
                  image: DecorationImage(
                    image: NetworkImage(partner.logoUrl!),
                    fit: BoxFit.cover,
                  ),
                ),
              )
            else
              Container(
                height: 80,
                decoration: const BoxDecoration(
                  color: JuventusTheme.grey200,
                  borderRadius: BorderRadius.vertical(top: Radius.circular(12)),
                ),
                child: const Center(
                  child: Icon(Icons.store, size: 40, color: JuventusTheme.grey400),
                ),
              ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      partner.name,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      partner.category.toUpperCase(),
                      style: const TextStyle(
                        fontSize: 10,
                        color: JuventusTheme.grey600,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
