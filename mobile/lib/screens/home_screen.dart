import 'package:flutter/material.dart';
import '../theme/juventus_theme.dart';
import '../services/api_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const HomeTabScreen(),
    const MatchesTabScreen(),
    const EngagementTabScreen(),
    const ProfileTabScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _screens[_selectedIndex],
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          boxShadow: [
            BoxShadow(
              color: JuventusTheme.primaryBlack.withOpacity(0.1),
              blurRadius: 8,
              offset: const Offset(0, -2),
            ),
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: _selectedIndex,
          onTap: (index) {
            setState(() {
              _selectedIndex = index;
            });
          },
          backgroundColor: JuventusTheme.primaryWhite,
          selectedItemColor: JuventusTheme.primaryBlack,
          unselectedItemColor: JuventusTheme.grey500,
          selectedLabelStyle: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 12,
          ),
          unselectedLabelStyle: const TextStyle(
            fontWeight: FontWeight.normal,
            fontSize: 11,
          ),
          type: BottomNavigationBarType.fixed,
          elevation: 0,
          items: const [
            BottomNavigationBarItem(
              icon: Icon(Icons.home_outlined),
              activeIcon: Icon(Icons.home),
              label: 'Accueil',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.sports_soccer_outlined),
              activeIcon: Icon(Icons.sports_soccer),
              label: 'Matchs',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.favorite_border),
              activeIcon: Icon(Icons.favorite),
              label: 'Soutien',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.person_outline),
              activeIcon: Icon(Icons.person),
              label: 'Profil',
            ),
          ],
        ),
      ),
    );
  }
}

// =====================================================
// HOME TAB - Juventus Style
// =====================================================

class HomeTabScreen extends StatefulWidget {
  const HomeTabScreen({super.key});

  @override
  State<HomeTabScreen> createState() => _HomeTabScreenState();
}

class _HomeTabScreenState extends State<HomeTabScreen> {
  final ApiService _apiService = ApiService();
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    // Simulate API call
    await Future.delayed(const Duration(seconds: 1));
    if (mounted) {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      body: CustomScrollView(
        slivers: [
          _buildAppBar(),
          SliverToBoxAdapter(child: _buildHeroSection()),
          SliverToBoxAdapter(child: _buildQuickActions()),
          SliverToBoxAdapter(child: _buildFeaturedContent()),
          SliverToBoxAdapter(child: _buildLatestNews()),
          const SliverToBoxAdapter(child: SizedBox(height: 100)),
        ],
      ),
    );
  }

  Widget _buildAppBar() {
    return SliverAppBar(
      floating: true,
      snap: true,
      backgroundColor: JuventusTheme.primaryBlack,
      elevation: 0,
      title: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: const BoxDecoration(
              color: JuventusTheme.primaryWhite,
              shape: BoxShape.circle,
            ),
            child: const Center(
              child: Text(
                'CSS',
                style: TextStyle(
                  color: JuventusTheme.primaryBlack,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
          const SizedBox(width: 12),
          const Text(
            'CSS SOCIOS',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              letterSpacing: 1.5,
            ),
          ),
        ],
      ),
      actions: [
        IconButton(
          icon: const Icon(Icons.notifications_outlined),
          onPressed: () {},
        ),
        IconButton(
          icon: const Icon(Icons.search),
          onPressed: () {},
        ),
      ],
    );
  }

  Widget _buildHeroSection() {
    return Container(
      margin: const EdgeInsets.all(16),
      height: 200,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        gradient: JuventusTheme.blackGradient,
        boxShadow: JuventusTheme.elevatedShadow,
      ),
      child: Stack(
        children: [
          // Background pattern
          Positioned.fill(
            child: Container(
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                image: DecorationImage(
                  image: const NetworkImage('https://via.placeholder.com/800x400/000000/FFFFFF?text=CSS+SOCIOS'),
                  fit: BoxFit.cover,
                  colorFilter: ColorFilter.mode(
                    JuventusTheme.primaryBlack.withOpacity(0.7),
                    BlendMode.darken,
                  ),
                ),
              ),
            ),
          ),
          // Content
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: JuventusTheme.error,
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: const Text(
                    'EN DIRECT',
                    style: TextStyle(
                      color: JuventusTheme.primaryWhite,
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1,
                    ),
                  ),
                ),
                const Spacer(),
                const Text(
                  'CSS vs EST',
                  style: TextStyle(
                    color: JuventusTheme.primaryWhite,
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(
                      Icons.stadium,
                      size: 14,
                      color: JuventusTheme.primaryWhite,
                    ),
                    const SizedBox(width: 6),
                    Text(
                      'Stade olympique de Sousse • 20:00',
                      style: TextStyle(
                        color: JuventusTheme.primaryWhite.withOpacity(0.8),
                        fontSize: 12,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActions() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(
        children: [
          Expanded(
            child: _buildActionCard(
              'Boutique',
              Icons.shopping_bag_outlined,
              () {},
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: _buildActionCard(
              'Billets',
              Icons.confirmation_number_outlined,
              () {},
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: _buildActionCard(
              'Soutenir',
              Icons.favorite_border,
              () {},
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionCard(String label, IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: JuventusDecorations.whiteCard,
        child: Column(
          children: [
            Icon(icon, size: 28, color: JuventusTheme.primaryBlack),
            const SizedBox(height: 8),
            Text(
              label,
              style: const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFeaturedContent() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 24, 16, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('À LA UNE', style: JuventusTheme.overline),
              TextButton(
                onPressed: () {},
                child: const Text(
                  'Voir tout',
                  style: TextStyle(color: JuventusTheme.primaryBlack),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          SizedBox(
            height: 220,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: 5,
              itemBuilder: (context, index) {
                return Container(
                  width: 280,
                  margin: const EdgeInsets.only(right: 12),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    color: JuventusTheme.grey300,
                  ),
                  child: Stack(
                    children: [
                      // Image placeholder
                      Positioned.fill(
                        child: Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            image: const DecorationImage(
                              image: NetworkImage('https://via.placeholder.com/280x220/212121/FFFFFF?text=CSS'),
                              fit: BoxFit.cover,
                            ),
                          ),
                        ),
                      ),
                      // Gradient overlay
                      Positioned.fill(
                        child: Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            gradient: LinearGradient(
                              begin: Alignment.topCenter,
                              end: Alignment.bottomCenter,
                              colors: [
                                Colors.transparent,
                                JuventusTheme.primaryBlack.withOpacity(0.8),
                              ],
                            ),
                          ),
                        ),
                      ),
                      // Content
                      Positioned(
                        left: 16,
                        right: 16,
                        bottom: 16,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: JuventusTheme.error,
                                borderRadius: BorderRadius.circular(4),
                              ),
                              child: const Text(
                                'NOUVEAU',
                                style: TextStyle(
                                  color: JuventusTheme.primaryWhite,
                                  fontSize: 9,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              'Titre de l\'article en vedette',
                              style: TextStyle(
                                color: JuventusTheme.primaryWhite,
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                              maxLines: 2,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLatestNews() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 24, 16, 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('DERNIÈRES ACTUS', style: JuventusTheme.overline),
              TextButton(
                onPressed: () {},
                child: const Text(
                  'Voir tout',
                  style: TextStyle(color: JuventusTheme.primaryBlack),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          ...List.generate(3, (index) => _buildNewsItem()),
        ],
      ),
    );
  }

  Widget _buildNewsItem() {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: JuventusDecorations.whiteCard,
      child: Row(
        children: [
          Container(
            width: 100,
            height: 100,
            decoration: BoxDecoration(
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(12),
                bottomLeft: Radius.circular(12),
              ),
              color: JuventusTheme.grey300,
            ),
            child: const Center(
              child: Icon(
                Icons.image,
                size: 32,
                color: JuventusTheme.grey500,
              ),
            ),
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Titre de l\'article',
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Il y a 2 heures',
                    style: TextStyle(
                      fontSize: 11,
                      color: JuventusTheme.grey600,
                    ),
                  ),
                ],
              ),
            ),
          ),
          const Padding(
            padding: EdgeInsets.only(right: 12),
            child: Icon(
              Icons.arrow_forward_ios,
              size: 16,
              color: JuventusTheme.grey500,
            ),
          ),
        ],
      ),
    );
  }
}

// =====================================================
// MATCHES TAB - Placeholder
// =====================================================

class MatchesTabScreen extends StatelessWidget {
  const MatchesTabScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('Matchs'),
        backgroundColor: JuventusTheme.primaryBlack,
      ),
      body: const Center(
        child: Text('Matchs Screen - Coming Soon'),
      ),
    );
  }
}

// =====================================================
// ENGAGEMENT TAB - Placeholder
// =====================================================

class EngagementTabScreen extends StatelessWidget {
  const EngagementTabScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('Soutenir le Club'),
        backgroundColor: JuventusTheme.primaryBlack,
      ),
      body: const Center(
        child: Text('Engagement Screen - Coming Soon'),
      ),
    );
  }
}

// =====================================================
// PROFILE TAB - Placeholder
// =====================================================

class ProfileTabScreen extends StatelessWidget {
  const ProfileTabScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('Profil'),
        backgroundColor: JuventusTheme.primaryBlack,
      ),
      body: const Center(
        child: Text('Profile Screen - Coming Soon'),
      ),
    );
  }
}
