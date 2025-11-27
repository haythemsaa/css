import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/prediction_models.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class PredictionsScreen extends StatefulWidget {
  const PredictionsScreen({super.key});

  @override
  State<PredictionsScreen> createState() => _PredictionsScreenState();
}

class _PredictionsScreenState extends State<PredictionsScreen> with SingleTickerProviderStateMixin {
  final ApiService _apiService = ApiService();

  late TabController _tabController;

  List<Map<String, dynamic>> _matches = [];
  PredictionLeaderboardEntry? _myStats;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _loadData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    try {
      final matchesResponse = await _apiService.getAvailableMatchesForPrediction();
      final statsResponse = await _apiService.getMyPredictionStats();

      setState(() {
        _matches = List<Map<String, dynamic>>.from(matchesResponse.data['matches']);
        _myStats = statsResponse.data['stats'] != null
            ? PredictionLeaderboardEntry.fromJson(statsResponse.data['stats'])
            : null;
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('PRONOSTICS'),
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
            Tab(text: 'MATCHS À VENIR'),
            Tab(text: 'MES STATS'),
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
              child: TabBarView(
                controller: _tabController,
                children: [
                  _buildMatchesList(),
                  _buildStatsTab(),
                ],
              ),
            ),
    );
  }

  Widget _buildMatchesList() {
    if (_matches.isEmpty) {
      return Center(
        child: Container(
          margin: const EdgeInsets.all(16),
          padding: const EdgeInsets.all(48),
          decoration: JuventusDecorations.whiteCard,
          child: const Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(Icons.sports_soccer, size: 48, color: JuventusTheme.grey400),
              SizedBox(height: 12),
              Text(
                'Aucun match disponible',
                style: TextStyle(color: JuventusTheme.grey600, fontSize: 14),
              ),
            ],
          ),
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _matches.length,
      itemBuilder: (context, index) {
        return _buildMatchCard(_matches[index]);
      },
    );
  }

  Widget _buildMatchCard(Map<String, dynamic> matchData) {
    final match = Match.fromJson(matchData);
    final prediction = matchData['user_prediction'] != null
        ? MatchPrediction.fromJson(matchData['user_prediction'])
        : null;
    final canPredict = matchData['can_predict'] ?? true;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        children: [
          // Match header
          Container(
            padding: const EdgeInsets.all(16),
            decoration: const BoxDecoration(
              color: JuventusTheme.primaryBlack,
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(12),
                topRight: Radius.circular(12),
              ),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  match.competition ?? 'Serie A',
                  style: const TextStyle(
                    color: JuventusTheme.primaryWhite,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                if (match.matchDate != null)
                  Text(
                    DateFormat('dd MMM, HH:mm').format(match.matchDate!),
                    style: const TextStyle(
                      color: JuventusTheme.accentGold,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
              ],
            ),
          ),
          // Match teams
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    children: [
                      const Icon(Icons.shield, size: 40, color: JuventusTheme.primaryBlack),
                      const SizedBox(height: 8),
                      Text(
                        match.homeTeamName ?? 'Juventus',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: JuventusTheme.primaryBlack,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ],
                  ),
                ),
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16),
                  child: Text(
                    'VS',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: JuventusTheme.grey400,
                    ),
                  ),
                ),
                Expanded(
                  child: Column(
                    children: [
                      const Icon(Icons.shield_outlined, size: 40, color: JuventusTheme.grey500),
                      const SizedBox(height: 8),
                      Text(
                        match.awayTeamName ?? 'Adversaire',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: JuventusTheme.primaryBlack,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          // Prediction section
          if (prediction != null)
            Container(
              margin: const EdgeInsets.fromLTRB(16, 0, 16, 16),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: JuventusTheme.accentGold.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: JuventusTheme.accentGold.withOpacity(0.3)),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const Icon(Icons.check_circle, size: 16, color: JuventusTheme.success),
                      const SizedBox(width: 8),
                      const Text(
                        'Votre pronostic:',
                        style: TextStyle(
                          fontSize: 12,
                          color: JuventusTheme.grey700,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        prediction.predictedScoreDisplay,
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: JuventusTheme.primaryBlack,
                        ),
                      ),
                    ],
                  ),
                  if (prediction.isProcessed)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: JuventusTheme.accentGold,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        '+${prediction.pointsEarned} pts',
                        style: const TextStyle(
                          color: JuventusTheme.primaryWhite,
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                ],
              ),
            )
          else if (canPredict)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
              child: SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => _showPredictionDialog(match, matchData),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: JuventusTheme.primaryBlack,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  child: const Text(
                    'FAIRE MON PRONOSTIC',
                    style: TextStyle(
                      color: JuventusTheme.primaryWhite,
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildStatsTab() {
    if (_myStats == null) {
      return const Center(
        child: Text('Aucune statistique disponible'),
      );
    }

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        // Points card
        Container(
          padding: const EdgeInsets.all(20),
          decoration: JuventusDecorations.blackCard,
          child: Column(
            children: [
              Text(
                '${_myStats!.totalPoints}',
                style: const TextStyle(
                  color: JuventusTheme.primaryWhite,
                  fontSize: 48,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              const Text(
                'POINTS TOTAUX',
                style: TextStyle(
                  color: JuventusTheme.grey400,
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  letterSpacing: 1,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),
        // Stats grid
        Row(
          children: [
            Expanded(
              child: _buildStatCard(
                'Pronostics',
                _myStats!.totalPredictions.toString(),
                Icons.sports_soccer,
                JuventusTheme.info,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _buildStatCard(
                'Précision',
                '${_myStats!.accuracyPercentage.toStringAsFixed(1)}%',
                Icons.trending_up,
                JuventusTheme.success,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: _buildStatCard(
                'Série actuelle',
                _myStats!.currentStreak.toString(),
                Icons.local_fire_department,
                JuventusTheme.warning,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _buildStatCard(
                'Meilleure série',
                _myStats!.bestStreak.toString(),
                Icons.emoji_events,
                JuventusTheme.accentGold,
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildStatCard(String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        children: [
          Icon(icon, size: 32, color: color),
          const SizedBox(height: 8),
          Text(
            value,
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: const TextStyle(
              fontSize: 11,
              color: JuventusTheme.grey600,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  void _showPredictionDialog(Match match, Map<String, dynamic> matchData) {
    int homeScore = 0;
    int awayScore = 0;

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: const Text(
            'VOTRE PRONOSTIC',
            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                '${match.homeTeamName} vs ${match.awayTeamName}',
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: JuventusTheme.grey700,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 24),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  _buildScorePicker(
                    match.homeTeamName ?? 'Domicile',
                    homeScore,
                    (value) => setDialogState(() => homeScore = value),
                  ),
                  const Text(
                    '-',
                    style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                  ),
                  _buildScorePicker(
                    match.awayTeamName ?? 'Extérieur',
                    awayScore,
                    (value) => setDialogState(() => awayScore = value),
                  ),
                ],
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('ANNULER'),
            ),
            ElevatedButton(
              onPressed: () async {
                Navigator.pop(context);
                await _submitPrediction(match.id, homeScore, awayScore);
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: JuventusTheme.primaryBlack,
              ),
              child: const Text('VALIDER'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildScorePicker(String team, int score, Function(int) onChanged) {
    return Column(
      children: [
        Text(
          team,
          style: const TextStyle(fontSize: 12, color: JuventusTheme.grey600),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 8),
        Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            IconButton(
              icon: const Icon(Icons.remove_circle_outline),
              onPressed: score > 0 ? () => onChanged(score - 1) : null,
            ),
            Container(
              width: 50,
              padding: const EdgeInsets.symmetric(vertical: 8),
              decoration: BoxDecoration(
                border: Border.all(color: JuventusTheme.grey300),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                score.toString(),
                style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                textAlign: TextAlign.center,
              ),
            ),
            IconButton(
              icon: const Icon(Icons.add_circle_outline),
              onPressed: score < 10 ? () => onChanged(score + 1) : null,
            ),
          ],
        ),
      ],
    );
  }

  Future<void> _submitPrediction(int matchId, int homeScore, int awayScore) async {
    try {
      await _apiService.submitPrediction(matchId, homeScore, awayScore);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Pronostic enregistré avec succès!'),
            backgroundColor: JuventusTheme.success,
          ),
        );
      }

      _loadData();
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

  void _showLeaderboard() {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Classement des pronostiqueurs - Disponible prochainement')),
    );
  }
}
