import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class ProductsScreen extends StatefulWidget {
  const ProductsScreen({super.key});

  @override
  State<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  final ApiService _apiService = ApiService();
  final TextEditingController _searchController = TextEditingController();

  List<dynamic> _products = [];
  List<dynamic> _filteredProducts = [];
  bool _isLoading = true;
  String? _selectedCategory;

  final List<Map<String, dynamic>> _categories = [
    {'key': null, 'name': 'Tous', 'icon': Icons.apps},
    {'key': 'jerseys', 'name': 'Maillots', 'icon': Icons.checkroom},
    {'key': 'accessories', 'name': 'Accessoires', 'icon': Icons.watch},
    {'key': 'merchandise', 'name': 'Souvenirs', 'icon': Icons.card_giftcard},
    {'key': 'collectibles', 'name': 'Collection', 'icon': Icons.sports},
  ];

  @override
  void initState() {
    super.initState();
    _loadProducts();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadProducts() async {
    try {
      setState(() => _isLoading = true);

      final response = await _apiService.get('/products');
      final products = response.data['data'] as List;

      if (mounted) {
        setState(() {
          _products = products;
          _filteredProducts = products;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  void _filterProducts(String query) {
    setState(() {
      _filteredProducts = _products.where((product) {
        final matchesSearch = product['name']
            .toString()
            .toLowerCase()
            .contains(query.toLowerCase());
        final matchesCategory = _selectedCategory == null ||
            product['category'] == _selectedCategory;
        return matchesSearch && matchesCategory;
      }).toList();
    });
  }

  void _selectCategory(String? category) {
    setState(() {
      _selectedCategory = category;
      _filterProducts(_searchController.text);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('BOUTIQUE'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.shopping_cart_outlined),
            onPressed: () => Navigator.pushNamed(context, '/cart'),
          ),
        ],
      ),
      body: Column(
        children: [
          // Search Bar
          Container(
            color: JuventusTheme.primaryBlack,
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            child: TextField(
              controller: _searchController,
              onChanged: _filterProducts,
              style: const TextStyle(color: JuventusTheme.primaryWhite),
              decoration: InputDecoration(
                hintText: 'Rechercher un produit...',
                hintStyle: TextStyle(color: JuventusTheme.grey500),
                prefixIcon: const Icon(Icons.search, color: JuventusTheme.grey500),
                filled: true,
                fillColor: JuventusTheme.grey900,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide.none,
                ),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              ),
            ),
          ),

          // Categories
          Container(
            height: 56,
            padding: const EdgeInsets.symmetric(vertical: 8),
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              itemCount: _categories.length,
              itemBuilder: (context, index) {
                final category = _categories[index];
                final isSelected = _selectedCategory == category['key'];
                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: FilterChip(
                    selected: isSelected,
                    label: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          category['icon'],
                          size: 16,
                          color: isSelected ? JuventusTheme.primaryWhite : JuventusTheme.primaryBlack,
                        ),
                        const SizedBox(width: 6),
                        Text(category['name']),
                      ],
                    ),
                    onSelected: (_) => _selectCategory(category['key']),
                    backgroundColor: JuventusTheme.primaryWhite,
                    selectedColor: JuventusTheme.primaryBlack,
                    labelStyle: TextStyle(
                      color: isSelected ? JuventusTheme.primaryWhite : JuventusTheme.primaryBlack,
                      fontWeight: FontWeight.w600,
                      fontSize: 13,
                    ),
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                  ),
                );
              },
            ),
          ),

          // Products Grid
          Expanded(
            child: _isLoading
                ? const Center(
                    child: CircularProgressIndicator(
                      color: JuventusTheme.primaryBlack,
                    ),
                  )
                : _filteredProducts.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              Icons.shopping_bag_outlined,
                              size: 80,
                              color: JuventusTheme.grey400,
                            ),
                            const SizedBox(height: 16),
                            const Text(
                              'Aucun produit trouvé',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w600,
                                color: JuventusTheme.grey700,
                              ),
                            ),
                          ],
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: _loadProducts,
                        color: JuventusTheme.primaryBlack,
                        child: GridView.builder(
                          padding: const EdgeInsets.all(16),
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            childAspectRatio: 0.7,
                            crossAxisSpacing: 12,
                            mainAxisSpacing: 12,
                          ),
                          itemCount: _filteredProducts.length,
                          itemBuilder: (context, index) {
                            return _buildProductCard(_filteredProducts[index]);
                          },
                        ),
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildProductCard(dynamic product) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');
    final price = product['price'] ?? 0.0;
    final salePrice = product['sale_price'];
    final hasDiscount = salePrice != null && salePrice < price;
    final stock = product['stock_quantity'] ?? 0;
    final isOutOfStock = stock <= 0;

    return GestureDetector(
      onTap: () {
        // Navigate to product detail
      },
      child: Container(
        decoration: JuventusDecorations.whiteCard,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            Expanded(
              child: Stack(
                children: [
                  Container(
                    width: double.infinity,
                    decoration: BoxDecoration(
                      color: JuventusTheme.grey200,
                      borderRadius: const BorderRadius.vertical(
                        top: Radius.circular(12),
                      ),
                    ),
                    child: product['images'] != null && (product['images'] as List).isNotEmpty
                        ? ClipRRect(
                            borderRadius: const BorderRadius.vertical(
                              top: Radius.circular(12),
                            ),
                            child: Image.network(
                              product['images'][0],
                              fit: BoxFit.cover,
                              errorBuilder: (context, error, stackTrace) => const Center(
                                child: Icon(
                                  Icons.image_outlined,
                                  size: 48,
                                  color: JuventusTheme.grey400,
                                ),
                              ),
                            ),
                          )
                        : const Center(
                            child: Icon(
                              Icons.image_outlined,
                              size: 48,
                              color: JuventusTheme.grey400,
                            ),
                          ),
                  ),

                  // Discount Badge
                  if (hasDiscount)
                    Positioned(
                      top: 8,
                      right: 8,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: JuventusTheme.error,
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          '-${((1 - salePrice / price) * 100).toInt()}%',
                          style: const TextStyle(
                            color: JuventusTheme.primaryWhite,
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),

                  // Out of Stock Overlay
                  if (isOutOfStock)
                    Positioned.fill(
                      child: Container(
                        decoration: BoxDecoration(
                          color: JuventusTheme.primaryBlack.withOpacity(0.7),
                          borderRadius: const BorderRadius.vertical(
                            top: Radius.circular(12),
                          ),
                        ),
                        child: const Center(
                          child: Text(
                            'ÉPUISÉ',
                            style: TextStyle(
                              color: JuventusTheme.primaryWhite,
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 1,
                            ),
                          ),
                        ),
                      ),
                    ),

                  // Wishlist Button
                  Positioned(
                    top: 8,
                    left: 8,
                    child: GestureDetector(
                      onTap: () => _toggleWishlist(product),
                      child: Container(
                        width: 32,
                        height: 32,
                        decoration: BoxDecoration(
                          color: JuventusTheme.primaryWhite,
                          shape: BoxShape.circle,
                          boxShadow: [
                            BoxShadow(
                              color: JuventusTheme.primaryBlack.withOpacity(0.2),
                              blurRadius: 4,
                            ),
                          ],
                        ),
                        child: const Icon(
                          Icons.favorite_border,
                          size: 18,
                          color: JuventusTheme.primaryBlack,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Product Info
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    product['name'] ?? '',
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      height: 1.2,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      if (hasDiscount) ...[
                        Text(
                          currencyFormat.format(price),
                          style: TextStyle(
                            fontSize: 12,
                            color: JuventusTheme.grey500,
                            decoration: TextDecoration.lineThrough,
                          ),
                        ),
                        const SizedBox(width: 6),
                      ],
                      Text(
                        currencyFormat.format(hasDiscount ? salePrice : price),
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: hasDiscount ? JuventusTheme.error : JuventusTheme.primaryBlack,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // Add to Cart Button
            if (!isOutOfStock)
              Container(
                width: double.infinity,
                decoration: const BoxDecoration(
                  color: JuventusTheme.primaryBlack,
                  borderRadius: BorderRadius.vertical(
                    bottom: Radius.circular(12),
                  ),
                ),
                child: TextButton.icon(
                  onPressed: () => _addToCart(product),
                  icon: const Icon(Icons.add_shopping_cart, size: 16, color: JuventusTheme.primaryWhite),
                  label: const Text(
                    'Ajouter',
                    style: TextStyle(
                      color: JuventusTheme.primaryWhite,
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  style: TextButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 10),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Future<void> _addToCart(dynamic product) async {
    try {
      await _apiService.addToCart(product['id'], 1);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('${product['name']} ajouté au panier'),
            backgroundColor: JuventusTheme.success,
            action: SnackBarAction(
              label: 'Voir',
              textColor: JuventusTheme.primaryWhite,
              onPressed: () => Navigator.pushNamed(context, '/cart'),
            ),
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

  Future<void> _toggleWishlist(dynamic product) async {
    try {
      await _apiService.toggleWishlist(product['id']);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Favoris mis à jour'),
            backgroundColor: JuventusTheme.primaryBlack,
            duration: Duration(seconds: 1),
          ),
        );
      }
    } catch (e) {
      // Silently fail
    }
  }
}
