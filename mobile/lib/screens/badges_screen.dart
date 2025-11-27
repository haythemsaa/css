import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/badge_models.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class BadgesScreen extends StatefulWidget {
  const BadgesScreen({super.key});

  @override
  State<BadgesScreen> createState() => _BadgesScreenState();
}

class _BadgesScreenState extends State<BadgesScreen> with SingleTickerProviderStateMixin {
  final ApiService _apiService = ApiService();

  late TabController _tabController;

  List<Badge> _badges = [];
  UserBadgeSummary? _summary;
  List<UserBadge> _recentUnlocks = [];
  List<UserBadge> _inProgress = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _tabController.addListener(_onTabChanged);
    _loadData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  void _onTabChanged() {
    if (_tabController.indexIsChanging) {
      setState(() {}); // Rebuild to apply filters
    }
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    try {
      final badgesResponse = await _apiService.getBadges();
      final summaryResponse = await _apiService.getBadgeSummary();

      setState(() {
        final List data = badgesResponse.data['data'];
        _badges = data.map((json) => Badge.fromJson(json)).toList();

        _summary = UserBadgeSummary.fromJson(summaryResponse.data['summary']);

        final List recentData = summaryResponse.data['recent_unlocks'];
        _recentUnlocks = recentData.map((json) => UserBadge.fromJson(json)).toList();

        final List progressData = summaryResponse.data['in_progress'];
        _inProgress = progressData.map((json) => UserBadge.fromJson(json)).toList();

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

  List<Badge> get _filteredBadges {
    switch (_tabController.index) {
      case 1: // Débloqués
        return _badges.where((b) => b.userProgress?.isUnlocked ?? false).toList();
      case 2: // Verrouillés
        return _badges.where((b) => !(b.userProgress?.isUnlocked ?? false)).toList();
      default: // Tous
        return _badges;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('MES BADGES'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.leaderboard),
            onPressed: _showLeaderboard,
            tooltip: 'Classement',
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: JuventusTheme.accentGold,
          labelColor: JuventusTheme.accentGold,
          unselectedLabelColor: JuventusTheme.grey400,
          labelStyle: const TextStyle(fontWeight: FontWeight.bold),
          tabs: const [
            Tab(text: 'TOUS'),
            Tab(text: 'DÉBLOQUÉS'),
            Tab(text: 'VERROUILLÉS'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(color: JuventusTheme.primaryBlack),
            )
          : RefreshIndicator(
              onRefresh: _loadData,
              color: JuventusTheme.primaryBlack,
              child: CustomScrollView(
                slivers: [
                  SliverToBoxAdapter(child: _buildSummarySection()),
                  if (_recentUnlocks.isNotEmpty) ...[
                    SliverToBoxAdapter(child: _buildRecentUnlocksSection()),
                  ],
                  if (_inProgress.isNotEmpty && _tabController.index != 1) ...[
                    SliverToBoxAdapter(child: _buildInProgressSection()),
                  ],
                  SliverToBoxAdapter(child: _buildSectionHeader()),
                  _buildBadgesGrid(),
                ],
              ),
            ),
    );
  }

  Widget _buildSummarySection() {
    if (_summary == null) return const SizedBox.shrink();

    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(20),
      decoration: JuventusDecorations.blackCard,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  gradient: JuventusTheme.goldGradient,
                  shape: BoxShape.circle,
                ),
                child: Center(
                  child: Text(
                    '${_summary!.totalBadges}',
                    style: const TextStyle(
                      color: JuventusTheme.primaryWhite,
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'BADGES DÉBLOQUÉS',
                      style: TextStyle(
                        color: JuventusTheme.grey400,
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        letterSpacing: 1,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      '${_summary!.completionPercentage.toStringAsFixed(1)}% complété',
                      style: const TextStyle(
                        color: JuventusTheme.primaryWhite,
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: _summary!.completionPercentage / 100,
              minHeight: 8,
              backgroundColor: JuventusTheme.grey700,
              valueColor: const AlwaysStoppedAnimation<Color>(JuventusTheme.accentGold),
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              _buildRarityChip('Légendaires', _summary!.legendaryBadges, JuventusTheme.warning),
              const SizedBox(width: 8),
              _buildRarityChip('Épiques', _summary!.epicBadges, const Color(0xFFA855F7)),
              const SizedBox(width: 8),
              _buildRarityChip('Rares', _summary!.rareBadges, JuventusTheme.info),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildRarityChip(String label, int count, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 6,
            height: 6,
            decoration: BoxDecoration(
              color: color,
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 6),
          Text(
            '$count',
            style: TextStyle(
              color: color,
              fontSize: 13,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRecentUnlocksSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.fromLTRB(16, 16, 16, 12),
          child: Text(
            'RÉCEMMENT DÉBLOQUÉS',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: JuventusTheme.primaryBlack,
              letterSpacing: 1,
            ),
          ),
        ),
        SizedBox(
          height: 100,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 12),
            itemCount: _recentUnlocks.length,
            itemBuilder: (context, index) {
              final userBadge = _recentUnlocks[index];
              return _buildRecentBadgeCard(userBadge);
            },
          ),
        ),
      ],
    );
  }

  Widget _buildRecentBadgeCard(UserBadge userBadge) {
    final badge = userBadge.badge;
    if (badge == null) return const SizedBox.shrink();

    return Container(
      width: 80,
      margin: const EdgeInsets.symmetric(horizontal: 4),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            badge.icon ?? '🏆',
            style: const TextStyle(fontSize: 32),
          ),
          const SizedBox(height: 4),
          Text(
            badge.name,
            style: const TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w600,
              color: JuventusTheme.primaryBlack,
            ),
            textAlign: TextAlign.center,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  Widget _buildInProgressSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.fromLTRB(16, 16, 16, 12),
          child: Text(
            'EN COURS',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: JuventusTheme.primaryBlack,
              letterSpacing: 1,
            ),
          ),
        ),
        ..._inProgress.take(3).map((userBadge) {
          final badge = userBadge.badge;
          if (badge == null) return const SizedBox.shrink();

          return Container(
            margin: const EdgeInsets.fromLTRB(16, 0, 16, 8),
            padding: const EdgeInsets.all(12),
            decoration: JuventusDecorations.whiteCard,
            child: Row(
              children: [
                Text(
                  badge.icon ?? '🎯',
                  style: const TextStyle(fontSize: 28),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        badge.name,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: JuventusTheme.primaryBlack,
                        ),
                      ),
                      const SizedBox(height: 6),
                      ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: LinearProgressIndicator(
                          value: userBadge.progressPercentage / 100,
                          minHeight: 6,
                          backgroundColor: JuventusTheme.grey200,
                          valueColor: const AlwaysStoppedAnimation<Color>(JuventusTheme.accentGold),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 8),
                Text(
                  '${userBadge.progress}/${userBadge.progressMax}',
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: JuventusTheme.grey600,
                  ),
                ),
              ],
            ),
          );
        }),
      ],
    );
  }

  Widget _buildSectionHeader() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 24, 16, 12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          const Text(
            'COLLECTION',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: JuventusTheme.primaryBlack,
              letterSpacing: 1,
            ),
          ),
          Text(
            '${_filteredBadges.length} badges',
            style: const TextStyle(
              fontSize: 12,
              color: JuventusTheme.grey600,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBadgesGrid() {
    if (_filteredBadges.isEmpty) {
      return SliverToBoxAdapter(
        child: Container(
          margin: const EdgeInsets.all(16),
          padding: const EdgeInsets.all(48),
          decoration: JuventusDecorations.whiteCard,
          child: Center(
            child: Column(
              children: [
                Icon(
                  _tabController.index == 1 ? Icons.lock_outline : Icons.search_off,
                  size: 48,
                  color: JuventusTheme.grey400,
                ),
                const SizedBox(height: 12),
                Text(
                  _tabController.index == 1
                      ? 'Aucun badge débloqué'
                      : 'Aucun badge disponible',
                  style: const TextStyle(
                    color: JuventusTheme.grey600,
                    fontSize: 14,
                  ),
                ),
              ],
            ),
          ),
        ),
      );
    }

    return SliverPadding(
      padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
      sliver: SliverGrid(
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 3,
          childAspectRatio: 0.8,
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
        ),
        delegate: SliverChildBuilderDelegate(
          (context, index) {
            return _buildBadgeCard(_filteredBadges[index]);
          },
          childCount: _filteredBadges.length,
        ),
      ),
    );
  }

  Widget _buildBadgeCard(Badge badge) {
    final isUnlocked = badge.userProgress?.isUnlocked ?? false;
    final progress = badge.userProgress?.progressPercentage ?? 0;

    Color rarityColor = _getRarityColor(badge.rarity);

    return InkWell(
      onTap: () => _showBadgeDetails(badge),
      borderRadius: BorderRadius.circular(12),
      child: Container(
        decoration: BoxDecoration(
          color: JuventusTheme.primaryWhite,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isUnlocked ? rarityColor : JuventusTheme.grey300,
            width: isUnlocked ? 2 : 1,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Stack(
          children: [
            // Rarity indicator
            Positioned(
              top: 0,
              left: 0,
              right: 0,
              child: Container(
                height: 3,
                decoration: BoxDecoration(
                  color: rarityColor,
                  borderRadius: const BorderRadius.only(
                    topLeft: Radius.circular(11),
                    topRight: Radius.circular(11),
                  ),
                ),
              ),
            ),
            // Content
            Padding(
              padding: const EdgeInsets.all(8),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  if (!isUnlocked)
                    Opacity(
                      opacity: 0.3,
                      child: Text(
                        badge.isSecret ? '🔒' : (badge.icon ?? '🏆'),
                        style: const TextStyle(fontSize: 40),
                      ),
                    )
                  else
                    Text(
                      badge.icon ?? '🏆',
                      style: const TextStyle(fontSize: 40),
                    ),
                  const SizedBox(height: 6),
                  Text(
                    badge.name,
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: isUnlocked ? JuventusTheme.primaryBlack : JuventusTheme.grey500,
                    ),
                    textAlign: TextAlign.center,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  if (!isUnlocked && progress > 0) ...[
                    const SizedBox(height: 6),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(2),
                      child: LinearProgressIndicator(
                        value: progress / 100,
                        minHeight: 3,
                        backgroundColor: JuventusTheme.grey200,
                        valueColor: AlwaysStoppedAnimation<Color>(rarityColor),
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getRarityColor(String rarity) {
    switch (rarity) {
      case 'legendary':
        return JuventusTheme.warning;
      case 'epic':
        return const Color(0xFFA855F7);
      case 'rare':
        return JuventusTheme.info;
      default:
        return JuventusTheme.grey500;
    }
  }

  void _showBadgeDetails(Badge badge) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => _buildBadgeDetailsSheet(badge),
    );
  }

  Widget _buildBadgeDetailsSheet(Badge badge) {
    final isUnlocked = badge.userProgress?.isUnlocked ?? false;
    final progress = badge.userProgress;
    final rarityColor = _getRarityColor(badge.rarity);

    return Container(
      decoration: const BoxDecoration(
        color: JuventusTheme.primaryWhite,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: SingleChildScrollView(
        padding: EdgeInsets.only(
          bottom: MediaQuery.of(context).viewInsets.bottom,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Handle bar
            Container(
              margin: const EdgeInsets.only(top: 12),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: JuventusTheme.grey300,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                children: [
                  // Badge icon
                  Container(
                    width: 80,
                    height: 80,
                    decoration: BoxDecoration(
                      color: rarityColor.withOpacity(0.1),
                      shape: BoxShape.circle,
                      border: Border.all(color: rarityColor, width: 3),
                    ),
                    child: Center(
                      child: Text(
                        isUnlocked ? (badge.icon ?? '🏆') : (badge.isSecret ? '🔒' : (badge.icon ?? '🏆')),
                        style: const TextStyle(fontSize: 48),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  // Badge name
                  Text(
                    badge.name,
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                      color: JuventusTheme.primaryBlack,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 8),
                  // Rarity
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: rarityColor.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: rarityColor.withOpacity(0.3)),
                    ),
                    child: Text(
                      badge.rarityDisplay,
                      style: TextStyle(
                        color: rarityColor,
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  // Description
                  if (badge.description != null && badge.description!.isNotEmpty)
                    Text(
                      badge.description!,
                      style: const TextStyle(
                        fontSize: 14,
                        color: JuventusTheme.grey600,
                        height: 1.5,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  const SizedBox(height: 20),
                  // Rewards
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      if (badge.tokenReward > 0) ...[
                        _buildRewardChip(
                          '${badge.tokenReward} Tokens',
                          Icons.stars,
                          JuventusTheme.accentGold,
                        ),
                        const SizedBox(width: 12),
                      ],
                      if (badge.xpReward > 0)
                        _buildRewardChip(
                          '${badge.xpReward} XP',
                          Icons.trending_up,
                          JuventusTheme.info,
                        ),
                    ],
                  ),
                  // Progress
                  if (progress != null && !isUnlocked) ...[
                    const SizedBox(height: 20),
                    const Divider(),
                    const SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Progression',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: JuventusTheme.primaryBlack,
                          ),
                        ),
                        Text(
                          '${progress.progress}/${progress.progressMax}',
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                            color: JuventusTheme.primaryBlack,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(4),
                      child: LinearProgressIndicator(
                        value: progress.progressPercentage / 100,
                        minHeight: 8,
                        backgroundColor: JuventusTheme.grey200,
                        valueColor: AlwaysStoppedAnimation<Color>(rarityColor),
                      ),
                    ),
                  ],
                  // Unlocked date
                  if (isUnlocked && progress?.unlockedAt != null) ...[
                    const SizedBox(height: 20),
                    const Divider(),
                    const SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(
                          Icons.check_circle,
                          size: 16,
                          color: JuventusTheme.success,
                        ),
                        const SizedBox(width: 8),
                        Text(
                          'Débloqué le ${DateFormat('dd/MM/yyyy').format(progress!.unlockedAt!)}',
                          style: const TextStyle(
                            fontSize: 13,
                            color: JuventusTheme.success,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRewardChip(String label, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 16, color: color),
          const SizedBox(width: 6),
          Text(
            label,
            style: TextStyle(
              color: color,
              fontSize: 13,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  void _showLeaderboard() {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Classement des collectionneurs - Disponible prochainement')),
    );
  }
}
