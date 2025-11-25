import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class MatchesScreen extends StatefulWidget {
  const MatchesScreen({super.key});

  @override
  State<MatchesScreen> createState() => _MatchesScreenState();
}

class _MatchesScreenState extends State<MatchesScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();

  List<dynamic> _upcomingMatches = [];
  List<dynamic> _liveMatches = [];
  List<dynamic> _pastMatches = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadMatches();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadMatches() async {
    try {
      setState(() => _isLoading = true);

      final response = await _apiService.get('/matches');
      final matches = response.data['data'] as List;

      if (mounted) {
        setState(() {
          _upcomingMatches = matches.where((m) => m['status'] == 'scheduled').toList();
          _liveMatches = matches.where((m) => m['status'] == 'live' || m['status'] == 'halftime').toList();
          _pastMatches = matches.where((m) => m['status'] == 'finished').toList();
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
      appBar: AppBar(
        title: const Text('MATCHS'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: JuventusTheme.primaryWhite,
          indicatorWeight: 3,
          labelColor: JuventusTheme.primaryWhite,
          unselectedLabelColor: JuventusTheme.grey500,
          labelStyle: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            letterSpacing: 0.5,
          ),
          tabs: const [
            Tab(text: 'À VENIR'),
            Tab(text: 'EN DIRECT'),
            Tab(text: 'TERMINÉS'),
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
              onRefresh: _loadMatches,
              color: JuventusTheme.primaryBlack,
              child: TabBarView(
                controller: _tabController,
                children: [
                  _buildUpcomingMatches(),
                  _buildLiveMatches(),
                  _buildPastMatches(),
                ],
              ),
            ),
    );
  }

  Widget _buildUpcomingMatches() {
    if (_upcomingMatches.isEmpty) {
      return _buildEmptyState(
        icon: Icons.calendar_today_outlined,
        message: 'Aucun match à venir',
        subtitle: 'Les prochains matchs apparaîtront ici',
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _upcomingMatches.length,
      itemBuilder: (context, index) {
        return _buildMatchCard(_upcomingMatches[index], isUpcoming: true);
      },
    );
  }

  Widget _buildLiveMatches() {
    if (_liveMatches.isEmpty) {
      return _buildEmptyState(
        icon: Icons.sports_soccer_outlined,
        message: 'Aucun match en direct',
        subtitle: 'Les matchs en cours apparaîtront ici',
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _liveMatches.length,
      itemBuilder: (context, index) {
        return _buildMatchCard(_liveMatches[index], isLive: true);
      },
    );
  }

  Widget _buildPastMatches() {
    if (_pastMatches.isEmpty) {
      return _buildEmptyState(
        icon: Icons.history,
        message: 'Aucun match terminé',
        subtitle: 'L\'historique des matchs apparaîtra ici',
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _pastMatches.length,
      itemBuilder: (context, index) {
        return _buildMatchCard(_pastMatches[index], isPast: true);
      },
    );
  }

  Widget _buildEmptyState({
    required IconData icon,
    required String message,
    required String subtitle,
  }) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            icon,
            size: 80,
            color: JuventusTheme.grey400,
          ),
          const SizedBox(height: 16),
          Text(
            message,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: JuventusTheme.grey700,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            subtitle,
            style: TextStyle(
              fontSize: 14,
              color: JuventusTheme.grey500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMatchCard(
    dynamic match, {
    bool isUpcoming = false,
    bool isLive = false,
    bool isPast = false,
  }) {
    final dateFormat = DateFormat('EEE dd MMM', 'fr_FR');
    final timeFormat = DateFormat('HH:mm');
    final matchDate = DateTime.parse(match['match_date']);

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        children: [
          // Header avec compétition et date
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: JuventusTheme.grey100,
              borderRadius: const BorderRadius.vertical(
                top: Radius.circular(12),
              ),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.sports_soccer,
                  size: 16,
                  color: JuventusTheme.grey700,
                ),
                const SizedBox(width: 8),
                Text(
                  match['competition'] ?? 'Ligue 1',
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: JuventusTheme.grey700,
                  ),
                ),
                const Spacer(),
                if (isLive)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: JuventusTheme.error,
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 6,
                          height: 6,
                          decoration: const BoxDecoration(
                            color: JuventusTheme.primaryWhite,
                            shape: BoxShape.circle,
                          ),
                        ),
                        const SizedBox(width: 4),
                        const Text(
                          'EN DIRECT',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: JuventusTheme.primaryWhite,
                            letterSpacing: 0.5,
                          ),
                        ),
                      ],
                    ),
                  )
                else
                  Text(
                    '${dateFormat.format(matchDate)} • ${timeFormat.format(matchDate)}',
                    style: TextStyle(
                      fontSize: 12,
                      color: JuventusTheme.grey600,
                    ),
                  ),
              ],
            ),
          ),

          // Match info
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                // Home team
                Expanded(
                  child: Column(
                    children: [
                      Container(
                        width: 56,
                        height: 56,
                        decoration: const BoxDecoration(
                          color: JuventusTheme.primaryBlack,
                          shape: BoxShape.circle,
                        ),
                        child: Center(
                          child: Text(
                            match['home_team'].toString().substring(0, 3).toUpperCase(),
                            style: const TextStyle(
                              color: JuventusTheme.primaryWhite,
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Text(
                        match['home_team'] ?? 'CSS',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                        textAlign: TextAlign.center,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),

                // Score or VS
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Column(
                    children: [
                      if (isPast || isLive)
                        Row(
                          children: [
                            Text(
                              '${match['home_score'] ?? 0}',
                              style: const TextStyle(
                                fontSize: 32,
                                fontWeight: FontWeight.bold,
                                color: JuventusTheme.primaryBlack,
                              ),
                            ),
                            const Padding(
                              padding: EdgeInsets.symmetric(horizontal: 8),
                              child: Text(
                                '-',
                                style: TextStyle(
                                  fontSize: 24,
                                  fontWeight: FontWeight.w300,
                                  color: JuventusTheme.grey500,
                                ),
                              ),
                            ),
                            Text(
                              '${match['away_score'] ?? 0}',
                              style: const TextStyle(
                                fontSize: 32,
                                fontWeight: FontWeight.bold,
                                color: JuventusTheme.primaryBlack,
                              ),
                            ),
                          ],
                        )
                      else
                        const Text(
                          'VS',
                          style: TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                            color: JuventusTheme.grey500,
                            letterSpacing: 2,
                          ),
                        ),
                      if (isLive) ...[
                        const SizedBox(height: 8),
                        Text(
                          '${match['minute'] ?? 0}\'',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: JuventusTheme.error,
                          ),
                        ),
                      ],
                    ],
                  ),
                ),

                // Away team
                Expanded(
                  child: Column(
                    children: [
                      Container(
                        width: 56,
                        height: 56,
                        decoration: BoxDecoration(
                          color: JuventusTheme.grey200,
                          shape: BoxShape.circle,
                          border: Border.all(
                            color: JuventusTheme.grey300,
                            width: 2,
                          ),
                        ),
                        child: Center(
                          child: Text(
                            match['away_team'].toString().substring(0, 3).toUpperCase(),
                            style: const TextStyle(
                              color: JuventusTheme.primaryBlack,
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Text(
                        match['away_team'] ?? 'Adversaire',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                        textAlign: TextAlign.center,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // Footer avec stade
          if (match['stadium'] != null)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: JuventusTheme.grey100,
                borderRadius: const BorderRadius.vertical(
                  bottom: Radius.circular(12),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.stadium,
                    size: 14,
                    color: JuventusTheme.grey600,
                  ),
                  const SizedBox(width: 6),
                  Text(
                    match['stadium'],
                    style: TextStyle(
                      fontSize: 12,
                      color: JuventusTheme.grey600,
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
