import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

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
      backgroundColor: JuventusTheme.grey100,
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: JuventusTheme.primaryBlack,
              ),
            )
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
                      labelColor: JuventusTheme.primaryBlack,
                      unselectedLabelColor: JuventusTheme.grey500,
                      indicatorColor: JuventusTheme.primaryBlack,
                      indicatorWeight: 3,
                      labelStyle: const TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 14,
                      ),
                      unselectedLabelStyle: const TextStyle(
                        fontWeight: FontWeight.normal,
                        fontSize: 14,
                      ),
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
        decoration: const BoxDecoration(
          gradient: JuventusTheme.blackGradient,
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
                      color: JuventusTheme.accentGold,
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
                      border: Border.all(color: JuventusTheme.primaryWhite, width: 2),
                    ),
                    child: Icon(
                      _getUserTypeIcon(userType),
                      color: JuventusTheme.primaryWhite,
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
                color: JuventusTheme.primaryWhite,
                letterSpacing: 0.5,
              ),
            ),

            const SizedBox(height: 4),

            // Email
            Text(
              email,
              style: const TextStyle(
                fontSize: 14,
                color: JuventusTheme.grey400,
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
                    color: JuventusTheme.primaryWhite,
                    size: 16,
                  ),
                  const SizedBox(width: 8),
                  Text(
                    _getUserTypeLabel(userType),
                    style: const TextStyle(
                      color: JuventusTheme.primaryWhite,
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
                      letterSpacing: 0.5,
                    ),
                  ),
                ],
              ),
            ),

            if (sociosNumber != null) ...[
              const SizedBox(height: 8),
              Text(
                'Carte N° $sociosNumber',
                style: const TextStyle(
                  fontSize: 12,
                  color: JuventusTheme.accentGold,
                  fontWeight: FontWeight.w600,
                  letterSpacing: 0.5,
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
      color: JuventusTheme.grey200,
      child: Center(
        child: Text(
          name.isNotEmpty ? name[0].toUpperCase() : '?',
          style: const TextStyle(
            fontSize: 40,
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
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
      borderRadius: BorderRadius.circular(8),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: JuventusTheme.primaryWhite.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: JuventusTheme.primaryWhite),
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: const TextStyle(
              fontSize: 12,
              color: JuventusTheme.primaryWhite,
              fontWeight: FontWeight.w500,
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
              color: JuventusTheme.primaryWhite,
              letterSpacing: 1,
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
                color: JuventusTheme.primaryWhite.withOpacity(0.3),
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
            backgroundColor: JuventusTheme.primaryWhite.withOpacity(0.3),
            valueColor: const AlwaysStoppedAnimation<Color>(JuventusTheme.primaryWhite),
            minHeight: 6,
            borderRadius: BorderRadius.circular(3),
          ),
          const SizedBox(height: 8),
          Text(
            _getNextLevelText(loyaltyLevel, loyaltyPoints),
            style: const TextStyle(
              fontSize: 12,
              color: JuventusTheme.primaryWhite,
              fontWeight: FontWeight.w500,
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
        Icon(icon, color: JuventusTheme.primaryWhite, size: 32),
        const SizedBox(height: 8),
        Text(
          value,
          style: const TextStyle(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryWhite,
          ),
        ),
        Text(
          label,
          style: TextStyle(
            fontSize: 12,
            color: JuventusTheme.primaryWhite.withOpacity(0.7),
            fontWeight: FontWeight.w500,
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

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: JuventusDecorations.whiteCard,
      child: ListTile(
        contentPadding: const EdgeInsets.all(12),
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: color, size: 24),
        ),
        title: Text(
          title,
          style: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 15,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 4),
            Text(
              subtitle,
              style: const TextStyle(
                fontSize: 13,
                color: JuventusTheme.grey700,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              dateFormat.format(date),
              style: const TextStyle(
                fontSize: 12,
                color: JuventusTheme.grey500,
              ),
            ),
          ],
        ),
        trailing: Text(
          amount,
          style: TextStyle(
            fontWeight: FontWeight.bold,
            fontSize: 15,
            color: color,
          ),
        ),
      ),
    );
  }

  Widget _buildPurchasesTab() {
    return Center(
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
            'Aucun achat récent',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: JuventusTheme.grey700,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRewardsTab() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.card_giftcard_outlined,
            size: 80,
            color: JuventusTheme.grey400,
          ),
          const SizedBox(height: 16),
          const Text(
            'Aucune récompense disponible',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: JuventusTheme.grey700,
            ),
          ),
        ],
      ),
    );
  }

  Color _getUserTypeColor(String userType) {
    switch (userType) {
      case 'socios':
        return JuventusTheme.accentGold;
      case 'premium':
        return JuventusTheme.grey400;
      default:
        return JuventusTheme.grey600;
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
        return JuventusTheme.primaryBlack;
      case 'gold':
        return JuventusTheme.accentGold;
      case 'silver':
        return JuventusTheme.grey400;
      default:
        return JuventusTheme.grey600; // bronze
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
        backgroundColor: JuventusTheme.primaryWhite,
        title: const Text(
          'Déconnexion',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        content: Text(
          'Voulez-vous vraiment vous déconnecter?',
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
      color: JuventusTheme.primaryWhite,
      child: tabBar,
    );
  }

  @override
  bool shouldRebuild(_SliverTabBarDelegate oldDelegate) => false;
}
