import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../services/api_service.dart';

class FreeoiScreen extends StatefulWidget {
import '../theme/juventus_theme.dart';
  const FreeoiScreen({super.key});

  @override
  State<FreeoiScreen> createState() => _FreeoiScreenState();
}

class _FreeoiScreenState extends State<FreeoiScreen> {
  final ApiService _api = ApiService();
  List<dynamic> partners = [];
  bool isLoading = true;
  String selectedCity = '';

  @override
  void initState() {
    super.initState();
    _loadPartners();
  }

  Future<void> _loadPartners() async {
    setState(() => isLoading = true);
    try {
      final response = await _api.getPartners(city: selectedCity.isEmpty ? null : selectedCity);
      setState(() {
        partners = response.data['data'] ?? [];
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Erreur: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Freeoui - Partenaires'),
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: () {},
          ),
        ],
      ),
      body: Column(
        children: [
          // Filters
          Container(
            padding: const EdgeInsets.all(16),
            color: JuventusTheme.primaryWhite,
            child: Row(
              children: [
                Expanded(
                  child: DropdownButtonFormField<String>(
                    value: selectedCity.isEmpty ? null : selectedCity,
                    decoration: const InputDecoration(
                      labelText: 'Ville',
                      border: OutlineInputBorder(),
                    ),
                    items: const [
                      DropdownMenuItem(value: '', child: Text('Toutes')),
                      DropdownMenuItem(value: 'Sfax', child: Text('Sfax')),
                      DropdownMenuItem(value: 'Tunis', child: Text('Tunis')),
                      DropdownMenuItem(value: 'Sousse', child: Text('Sousse')),
                    ],
                    onChanged: (value) {
                      setState(() => selectedCity = value ?? '');
                      _loadPartners();
                    },
                  ),
                ),
                const SizedBox(width: 12),
                ElevatedButton.icon(
                  onPressed: () {},
                  icon: const Icon(Icons.location_on),
                  label: const Text('Près de moi'),
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 16,
                    ),
                  ),
                ),
              ],
            ),
          ),

          // Partners List
          Expanded(
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : RefreshIndicator(
                    onRefresh: _loadPartners,
                    child: ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: partners.length,
                      itemBuilder: (context, index) {
                        final partner = partners[index];
                        return _buildPartnerContainer(decoration: JuventusDecorations.whiteCard,partner);
                      },
                    ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildPartnerContainer(decoration: JuventusDecorations.whiteCard,dynamic partner) {
    final isFeatured = partner['is_featured'] ?? false;

    return Container(decoration: JuventusDecorations.whiteCard,
      margin: const EdgeInsets.only(bottom: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (isFeatured)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: const BoxDecoration(
                color: AppTheme.primaryGold,
                borderRadius: BorderRadius.vertical(top: Radius.circular(12)),
              ),
              child: const Text(
                '⭐ Partenaire Featured',
                style: TextStyle(
                  color: Colors.black,
                  fontWeight: FontWeight.bold,
                  fontSize: 12,
                ),
              ),
            ),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  partner['name'] ?? '',
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  '📍 ${partner['city']} - ${partner['address']}',
                  style: TextStyle(
                    color: JuventusTheme.grey600[600],
                    fontSize: 14,
                  ),
                ),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Premium:',
                          style: TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                        ),
                        Text(
                          '${partner['reduction_value_premium']}% réduction',
                          style: const TextStyle(
                            color: AppTheme.primaryGold,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Socios:',
                          style: TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                        ),
                        Text(
                          '${partner['reduction_value_socios']}% réduction',
                          style: const TextStyle(
                            color: AppTheme.primaryGold,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: () => _generateQRCode(partner['id']),
                    icon: const Icon(Icons.qr_code),
                    label: const Text('Générer QR Code'),
                    style: ElevatedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _generateQRCode(int partnerId) async {
    try {
      final response = await _api.generateReductionCode(partnerId);
      // TODO: Show QR code dialog
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Code QR généré avec succès!')),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Erreur: $e')),
      );
    }
  }
}
