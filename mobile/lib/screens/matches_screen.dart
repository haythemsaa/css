import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';

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

      final response = await _apiService.getMatches();
      final matches = response.data as List;

      if (mounted) {
        setState(() {
          _upcomingMatches = matches.where((m) => m['status'] == 'scheduled').toList();
          _liveMatches = matches.where((m) => m['status'] == 'live').toList();
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
      appBar: AppBar(
        title: const Text('Calendrier des Matchs'),
        backgroundColor: Colors.black,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.yellow[700],
          labelColor: Colors.yellow[700],
          unselectedLabelColor: Colors.white70,
          tabs: const [
            Tab(text: 'À Venir'),
            Tab(text: 'En Direct'),
            Tab(text: 'Terminés'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadMatches,
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
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.calendar_today, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Aucun match à venir'),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _upcomingMatches.length,
      itemBuilder: (context, index) {
        final match = _upcomingMatches[index];
        return _buildMatchCard(match, isUpcoming: true);
      },
    );
  }

  Widget _buildLiveMatches() {
    if (_liveMatches.isEmpty) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.sports_soccer, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Aucun match en direct'),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _liveMatches.length,
      itemBuilder: (context, index) {
        final match = _liveMatches[index];
        return _buildMatchCard(match, isLive: true);
      },
    );
  }

  Widget _buildPastMatches() {
    if (_pastMatches.isEmpty) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.history, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Aucun résultat'),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _pastMatches.length,
      itemBuilder: (context, index) {
        final match = _pastMatches[index];
        return _buildMatchCard(match);
      },
    );
  }

  Widget _buildMatchCard(dynamic match, {bool isLive = false, bool isUpcoming = false}) {
    final dateFormat = DateFormat('EEE dd MMM yyyy - HH:mm', 'fr_FR');
    final matchDate = DateTime.parse(match['match_date'] ?? DateTime.now().toString());

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      child: InkWell(
        onTap: () {
          // Navigate to match details
        },
        child: Column(
          children: [
            // Competition and Date Header
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey[100],
                borderRadius: const BorderRadius.vertical(top: Radius.circular(4)),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Icon(Icons.emoji_events, size: 16, color: Colors.grey[600]),
                      const SizedBox(width: 4),
                      Text(
                        match['competition'] ?? 'Championnat',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[600],
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                  if (isLive)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.circle, color: Colors.white, size: 8),
                          SizedBox(width: 4),
                          Text(
                            'EN DIRECT',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    )
                  else
                    Text(
                      dateFormat.format(matchDate),
                      style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    ),
                ],
              ),
            ),

            // Teams and Score
            Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  // Home Team
                  Expanded(
                    child: Column(
                      children: [
                        Container(
                          width: 60,
                          height: 60,
                          decoration: BoxDecoration(
                            color: match['home_team'] == 'CSS'
                                ? Colors.yellow[700]
                                : Colors.grey[200],
                            shape: BoxShape.circle,
                          ),
                          child: Center(
                            child: Text(
                              _getTeamAbbr(match['home_team']),
                              style: TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                                color: match['home_team'] == 'CSS'
                                    ? Colors.black
                                    : Colors.grey[700],
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          match['home_team'] ?? 'CSS',
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontWeight: FontWeight.w600,
                            fontSize: 14,
                          ),
                        ),
                      ],
                    ),
                  ),

                  // Score or VS
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                    child: Column(
                      children: [
                        if (isUpcoming)
                          Text(
                            'VS',
                            style: TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                              color: Colors.grey[400],
                            ),
                          )
                        else
                          Row(
                            children: [
                              Text(
                                (match['home_score'] ?? 0).toString(),
                                style: const TextStyle(
                                  fontSize: 36,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              Padding(
                                padding: const EdgeInsets.symmetric(horizontal: 8),
                                child: Text(
                                  '-',
                                  style: TextStyle(
                                    fontSize: 36,
                                    color: Colors.grey[400],
                                  ),
                                ),
                              ),
                              Text(
                                (match['away_score'] ?? 0).toString(),
                                style: const TextStyle(
                                  fontSize: 36,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        if (isLive) ...[
                          const SizedBox(height: 4),
                          Text(
                            '${match['minute'] ?? 0}\'',
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                              color: Colors.red,
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),

                  // Away Team
                  Expanded(
                    child: Column(
                      children: [
                        Container(
                          width: 60,
                          height: 60,
                          decoration: BoxDecoration(
                            color: Colors.grey[200],
                            shape: BoxShape.circle,
                          ),
                          child: Center(
                            child: Text(
                              _getTeamAbbr(match['away_team']),
                              style: TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                                color: Colors.grey[700],
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          match['away_team'] ?? 'Adversaire',
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontWeight: FontWeight.w600,
                            fontSize: 14,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            // Stadium Info
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey[50],
                borderRadius: const BorderRadius.vertical(bottom: Radius.circular(4)),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.stadium, size: 14, color: Colors.grey[600]),
                  const SizedBox(width: 4),
                  Text(
                    match['stadium'] ?? 'Stade Taïeb Mhiri',
                    style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _getTeamAbbr(String? teamName) {
    if (teamName == null || teamName.isEmpty) return '???';
    if (teamName.length <= 3) return teamName.toUpperCase();

    // Extract abbreviation from team name
    final words = teamName.split(' ');
    if (words.length >= 2) {
      return words.take(2).map((w) => w[0]).join().toUpperCase();
    }
    return teamName.substring(0, 3).toUpperCase();
  }
}
