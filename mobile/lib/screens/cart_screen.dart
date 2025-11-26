import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  final ApiService _apiService = ApiService();

  Map<String, dynamic>? _cart;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadCart();
  }

  Future<void> _loadCart() async {
    try {
      setState(() => _isLoading = true);

      final response = await _apiService.getCart();

      if (mounted) {
        setState(() {
          _cart = response.data['cart'];
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  Future<void> _updateQuantity(int productId, int newQuantity) async {
    try {
      await _apiService.updateCartItem(productId, newQuantity);
      await _loadCart();

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Panier mis à jour'),
            backgroundColor: JuventusTheme.success,
          ),
        );
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

  Future<void> _removeItem(int productId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: JuventusTheme.primaryWhite,
        title: const Text(
          'Retirer du panier',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        content: Text(
          'Voulez-vous retirer ce produit du panier?',
          style: TextStyle(color: JuventusTheme.grey700),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text(
              'Annuler',
              style: TextStyle(color: JuventusTheme.primaryBlack),
            ),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: JuventusTheme.error,
              foregroundColor: JuventusTheme.primaryWhite,
            ),
            child: const Text('Retirer'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      try {
        await _apiService.removeCartItem(productId);
        await _loadCart();

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Produit retiré du panier'),
              backgroundColor: JuventusTheme.primaryBlack,
            ),
          );
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

  Future<void> _clearCart() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: JuventusTheme.primaryWhite,
        title: const Text(
          'Vider le panier',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        content: Text(
          'Voulez-vous vider complètement votre panier?',
          style: TextStyle(color: JuventusTheme.grey700),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text(
              'Annuler',
              style: TextStyle(color: JuventusTheme.primaryBlack),
            ),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: JuventusTheme.error,
              foregroundColor: JuventusTheme.primaryWhite,
            ),
            child: const Text('Vider'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      try {
        await _apiService.clearCart();
        await _loadCart();

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Panier vidé'),
              backgroundColor: JuventusTheme.primaryBlack,
            ),
          );
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
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('MON PANIER'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        actions: [
          if (_cart != null && (_cart!['items'] as List).isNotEmpty)
            IconButton(
              icon: const Icon(Icons.delete_outline),
              onPressed: _clearCart,
              tooltip: 'Vider le panier',
            ),
        ],
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: JuventusTheme.primaryBlack,
              ),
            )
          : _buildCartContent(),
    );
  }

  Widget _buildCartContent() {
    if (_cart == null || (_cart!['items'] as List).isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.shopping_cart_outlined,
              size: 80,
              color: JuventusTheme.grey400,
            ),
            const SizedBox(height: 16),
            const Text(
              'Votre panier est vide',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: JuventusTheme.grey700,
              ),
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: () => Navigator.pop(context),
              icon: const Icon(Icons.shopping_bag),
              label: const Text('Continuer vos achats'),
              style: ElevatedButton.styleFrom(
                backgroundColor: JuventusTheme.primaryBlack,
                foregroundColor: JuventusTheme.primaryWhite,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
              ),
            ),
          ],
        ),
      );
    }

    final items = _cart!['items'] as List;
    final total = _cart!['total'] ?? 0.0;

    return Column(
      children: [
        // Cart Items
        Expanded(
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: items.length,
            itemBuilder: (context, index) {
              final item = items[index];
              return _buildCartItem(item);
            },
          ),
        ),

        // Summary Section
        _buildSummarySection(total),
      ],
    );
  }

  Widget _buildCartItem(dynamic item) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');
    final product = item['product'];
    final quantity = item['quantity'];
    final subtotal = item['subtotal'];

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Product Image
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(8),
                color: JuventusTheme.grey200,
              ),
              child: product['images'] != null && (product['images'] as List).isNotEmpty
                  ? ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: Image.network(
                        product['images'][0],
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) => const Icon(
                          Icons.image,
                          size: 32,
                          color: JuventusTheme.grey400,
                        ),
                      ),
                    )
                  : const Icon(Icons.image, size: 32, color: JuventusTheme.grey400),
            ),

            const SizedBox(width: 12),

            // Product Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    product['name'] ?? '',
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),

                  const SizedBox(height: 8),

                  Text(
                    currencyFormat.format(product['price']),
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                    ),
                  ),

                  const SizedBox(height: 12),

                  // Quantity Controls
                  Row(
                    children: [
                      _buildQuantityButton(
                        icon: Icons.remove,
                        onPressed: quantity > 1
                            ? () => _updateQuantity(product['id'], quantity - 1)
                            : null,
                      ),

                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                        child: Text(
                          quantity.toString(),
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),

                      _buildQuantityButton(
                        icon: Icons.add,
                        onPressed: () => _updateQuantity(product['id'], quantity + 1),
                      ),

                      const Spacer(),

                      IconButton(
                        icon: const Icon(Icons.delete_outline, color: JuventusTheme.error),
                        onPressed: () => _removeItem(product['id']),
                        tooltip: 'Retirer',
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

  Widget _buildQuantityButton({required IconData icon, VoidCallback? onPressed}) {
    return Container(
      decoration: BoxDecoration(
        border: Border.all(
          color: onPressed != null ? JuventusTheme.grey300 : JuventusTheme.grey200,
        ),
        borderRadius: BorderRadius.circular(4),
      ),
      child: IconButton(
        icon: Icon(
          icon,
          size: 18,
          color: onPressed != null ? JuventusTheme.primaryBlack : JuventusTheme.grey400,
        ),
        onPressed: onPressed,
        padding: EdgeInsets.zero,
        constraints: const BoxConstraints(minWidth: 32, minHeight: 32),
      ),
    );
  }

  Widget _buildSummarySection(double total) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');
    final itemsCount = _cart!['items_count'] ?? 0;
    final subtotal = total / 1.19; // Remove 19% TVA for display
    final taxes = subtotal * 0.19;
    final shipping = subtotal >= 200 ? 0.0 : 7.0;

    return Container(
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
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                _buildSummaryRow('Sous-total', currencyFormat.format(subtotal)),
                const SizedBox(height: 8),
                _buildSummaryRow('TVA (19%)', currencyFormat.format(taxes)),
                const SizedBox(height: 8),
                _buildSummaryRow(
                  'Livraison',
                  shipping == 0 ? 'GRATUITE' : currencyFormat.format(shipping),
                  highlight: shipping == 0,
                ),
                if (subtotal < 200) ...[
                  const SizedBox(height: 4),
                  Text(
                    'Livraison gratuite dès ${currencyFormat.format(200)}',
                    style: const TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                  ),
                ],
                const Divider(height: 24),
                _buildSummaryRow(
                  'Total',
                  currencyFormat.format(total),
                  isTotal: true,
                ),
              ],
            ),
          ),

          // Checkout Button
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => _proceedToCheckout(),
                style: ElevatedButton.styleFrom(
                  backgroundColor: JuventusTheme.primaryBlack,
                  foregroundColor: JuventusTheme.primaryWhite,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: Text(
                  'COMMANDER ($itemsCount article${itemsCount > 1 ? "s" : ""})',
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 1,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryRow(String label, String value, {bool isTotal = false, bool highlight = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: isTotal ? 18 : 14,
            fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
            color: highlight ? JuventusTheme.success : JuventusTheme.primaryBlack,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            fontSize: isTotal ? 18 : 14,
            fontWeight: isTotal ? FontWeight.bold : FontWeight.w600,
            color: highlight ? JuventusTheme.success : JuventusTheme.primaryBlack,
          ),
        ),
      ],
    );
  }

  void _proceedToCheckout() {
    // Navigate to checkout screen
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Passage à la commande - À implémenter'),
        backgroundColor: JuventusTheme.warning,
      ),
    );
  }
}
