import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();

  Map<String, dynamic>? _userData;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadUserData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadUserData() async {
    try {
      setState(() => _isLoading = true);

      final response = await _apiService.getUserProfile();

      if (mounted) {
        setState(() {
          _userData = response.data;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : CustomScrollView(
              slivers: [
                // Profile Header
                _buildProfileHeader(),

                // Loyalty Stats
                SliverToBoxAdapter(child: _buildLoyaltyStats()),

                // Tabs
                SliverPersistentHeader(
                  pinned: true,
                  delegate: _SliverTabBarDelegate(
                    TabBar(
                      controller: _tabController,
                      labelColor: Colors.black,
                      unselectedLabelColor: Colors.grey,
                      indicatorColor: Colors.yellow[700],
                      tabs: const [
                        Tab(text: 'Activité'),
                        Tab(text: 'Achats'),
                        Tab(text: 'Récompenses'),
                      ],
                    ),
                  ),
                ),

                // Tab Content
                SliverFillRemaining(
                  child: TabBarView(
                    controller: _tabController,
                    children: [
                      _buildActivityTab(),
                      _buildPurchasesTab(),
                      _buildRewardsTab(),
                    ],
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildProfileHeader() {
    final firstName = _userData?['first_name'] ?? '';
    final lastName = _userData?['last_name'] ?? '';
    final email = _userData?['email'] ?? '';
    final userType = _userData?['user_type'] ?? 'free';
    final sociosNumber = _userData?['socios_number'];
    final profilePhoto = _userData?['profile_photo'];

    return SliverToBoxAdapter(
      child: Container(
        padding: const EdgeInsets.fromLTRB(20, 60, 20, 20),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [Colors.black, Colors.grey[900]!],
          ),
        ),
        child: Column(
          children: [
            // Profile Photo
            Stack(
              children: [
                Container(
                  width: 100,
                  height: 100,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: Colors.yellow[700]!,
                      width: 3,
                    ),
                  ),
                  child: ClipOval(
                    child: profilePhoto != null
                        ? Image.network(
                            profilePhoto,
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) =>
                                _buildDefaultAvatar(firstName),
                          )
                        : _buildDefaultAvatar(firstName),
                  ),
                ),
                Positioned(
                  bottom: 0,
                  right: 0,
                  child: Container(
                    padding: const EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      color: _getUserTypeColor(userType),
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.white, width: 2),
                    ),
                    child: Icon(
                      _getUserTypeIcon(userType),
                      color: Colors.white,
                      size: 16,
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 16),

            // Name
            Text(
              '$firstName $lastName',
              style: const TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),

            const SizedBox(height: 4),

            // Email
            Text(
              email,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[400],
              ),
            ),

            const SizedBox(height: 12),

            // Status Badge
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(
                color: _getUserTypeColor(userType),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    _getUserTypeIcon(userType),
                    color: Colors.white,
                    size: 16,
                  ),
                  const SizedBox(width: 8),
                  Text(
                    _getUserTypeLabel(userType),
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),

            if (sociosNumber != null) ...[
              const SizedBox(height: 8),
              Text(
                'Carte N° $sociosNumber',
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.yellow[700],
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],

            const SizedBox(height: 16),

            // Quick Actions
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildQuickAction(
                  icon: Icons.edit,
                  label: 'Modifier',
                  onTap: () {
                    // Navigate to edit profile
                  },
                ),
                _buildQuickAction(
                  icon: Icons.settings,
                  label: 'Paramètres',
                  onTap: () {
                    // Navigate to settings
                  },
                ),
                _buildQuickAction(
                  icon: Icons.logout,
                  label: 'Déconnexion',
                  onTap: _logout,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDefaultAvatar(String name) {
    return Container(
      color: Colors.grey[300],
      child: Center(
        child: Text(
          name.isNotEmpty ? name[0].toUpperCase() : '?',
          style: const TextStyle(
            fontSize: 40,
            fontWeight: FontWeight.bold,
            color: Colors.black,
          ),
        ),
      ),
    );
  }

  Widget _buildQuickAction({
    required IconData icon,
    required String label,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: Colors.white),
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: const TextStyle(
              fontSize: 12,
              color: Colors.white,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLoyaltyStats() {
    final loyaltyPoints = _userData?['loyalty_points'] ?? 0;
    final loyaltyLevel = _userData?['loyalty_level'] ?? 'bronze';
    final referralCount = _userData?['referral_count'] ?? 0;

    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            _getLoyaltyColor(loyaltyLevel),
            _getLoyaltyColor(loyaltyLevel).withOpacity(0.7),
          ],
        ),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        children: [
          Text(
            'Niveau ${loyaltyLevel.toUpperCase()}',
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildStatItem(
                icon: Icons.stars,
                value: loyaltyPoints.toString(),
                label: 'Points',
              ),
              Container(
                width: 1,
                height: 40,
                color: Colors.white.withOpacity(0.3),
              ),
              _buildStatItem(
                icon: Icons.people,
                value: referralCount.toString(),
                label: 'Parrainages',
              ),
            ],
          ),
          const SizedBox(height: 16),
          LinearProgressIndicator(
            value: _getLevelProgress(loyaltyLevel, loyaltyPoints),
            backgroundColor: Colors.white.withOpacity(0.3),
            valueColor: const AlwaysStoppedAnimation<Color>(Colors.white),
          ),
          const SizedBox(height: 8),
          Text(
            _getNextLevelText(loyaltyLevel, loyaltyPoints),
            style: const TextStyle(
              fontSize: 12,
              color: Colors.white,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatItem({
    required IconData icon,
    required String value,
    required String label,
  }) {
    return Column(
      children: [
        Icon(icon, color: Colors.white, size: 32),
        const SizedBox(height: 8),
        Text(
          value,
          style: const TextStyle(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
        Text(
          label,
          style: const TextStyle(
            fontSize: 12,
            color: Colors.white70,
          ),
        ),
      ],
    );
  }

  Widget _buildActivityTab() {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        _buildActivityItem(
          icon: Icons.favorite,
          title: 'Don effectué',
          subtitle: 'Objectif: Paiement litiges FIFA',
          amount: '100 TND',
          date: DateTime.now().subtract(const Duration(days: 2)),
          color: Colors.red,
        ),
        _buildActivityItem(
          icon: Icons.gavel,
          title: 'Enchère gagnée',
          subtitle: 'Maillot CSS 2007',
          amount: '850 TND',
          date: DateTime.now().subtract(const Duration(days: 5)),
          color: Colors.orange,
        ),
        _buildActivityItem(
          icon: Icons.shopping_bag,
          title: 'Achat boutique',
          subtitle: 'Écharpe CSS Officielle',
          amount: '45 TND',
          date: DateTime.now().subtract(const Duration(days: 7)),
          color: Colors.green,
        ),
      ],
    );
  }

  Widget _buildActivityItem({
    required IconData icon,
    required String title,
    required String subtitle,
    required String amount,
    required DateTime date,
    required Color color,
  }) {
    final dateFormat = DateFormat('dd MMM yyyy', 'fr_FR');

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: ListTile(
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: color),
        ),
        title: Text(
          title,
          style: const TextStyle(fontWeight: FontWeight.w600),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(subtitle),
            const SizedBox(height: 4),
            Text(
              dateFormat.format(date),
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
          ],
        ),
        trailing: Text(
          amount,
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
      ),
    );
  }

  Widget _buildPurchasesTab() {
    return const Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.shopping_bag, size: 64, color: Colors.grey),
          SizedBox(height: 16),
          Text('Aucun achat récent'),
        ],
      ),
    );
  }

  Widget _buildRewardsTab() {
    return const Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.card_giftcard, size: 64, color: Colors.grey),
          SizedBox(height: 16),
          Text('Aucune récompense disponible'),
        ],
      ),
    );
  }

  Color _getUserTypeColor(String userType) {
    switch (userType) {
      case 'socios':
        return Colors.yellow[700]!;
      case 'premium':
        return Colors.blue;
      default:
        return Colors.grey;
    }
  }

  IconData _getUserTypeIcon(String userType) {
    switch (userType) {
      case 'socios':
        return Icons.verified;
      case 'premium':
        return Icons.star;
      default:
        return Icons.person;
    }
  }

  String _getUserTypeLabel(String userType) {
    switch (userType) {
      case 'socios':
        return 'MEMBRE SOCIOS';
      case 'premium':
        return 'PREMIUM';
      default:
        return 'SUPPORTER';
    }
  }

  Color _getLoyaltyColor(String level) {
    switch (level) {
      case 'platinum':
        return Colors.purple;
      case 'gold':
        return Colors.amber[700]!;
      case 'silver':
        return Colors.grey[400]!;
      default:
        return Colors.brown;
    }
  }

  double _getLevelProgress(String level, int points) {
    const thresholds = {
      'bronze': 500,
      'silver': 1500,
      'gold': 5000,
      'platinum': 10000,
    };

    int currentThreshold = thresholds[level] ?? 500;
    int previousThreshold = 0;

    if (level == 'silver') previousThreshold = 500;
    if (level == 'gold') previousThreshold = 1500;
    if (level == 'platinum') previousThreshold = 5000;

    if (points >= currentThreshold) return 1.0;

    return (points - previousThreshold) / (currentThreshold - previousThreshold);
  }

  String _getNextLevelText(String level, int points) {
    const thresholds = {
      'bronze': 500,
      'silver': 1500,
      'gold': 5000,
      'platinum': 10000,
    };

    final nextLevels = {
      'bronze': 'Silver',
      'silver': 'Gold',
      'gold': 'Platinum',
      'platinum': 'Maximum',
    };

    if (level == 'platinum' && points >= 10000) {
      return 'Niveau maximum atteint!';
    }

    int needed = (thresholds[level] ?? 500) - points;
    String nextLevel = nextLevels[level] ?? '';

    return '$needed points pour atteindre $nextLevel';
  }

  Future<void> _logout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Déconnexion'),
        content: const Text('Voulez-vous vraiment vous déconnecter?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Annuler'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Déconnexion'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      await _apiService.logout();
      if (mounted) {
        Navigator.pushReplacementNamed(context, '/login');
      }
    }
  }
}

class _SliverTabBarDelegate extends SliverPersistentHeaderDelegate {
  final TabBar tabBar;

  _SliverTabBarDelegate(this.tabBar);

  @override
  double get minExtent => tabBar.preferredSize.height;
  @override
  double get maxExtent => tabBar.preferredSize.height;

  @override
  Widget build(BuildContext context, double shrinkOffset, bool overlapsContent) {
    return Container(
      color: Colors.white,
      child: tabBar,
    );
  }

  @override
  bool shouldRebuild(_SliverTabBarDelegate oldDelegate) => false;
}
