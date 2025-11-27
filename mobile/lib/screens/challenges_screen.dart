import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/additional_models.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class ChallengesScreen extends StatefulWidget {
  const ChallengesScreen({super.key});

  @override
  State<ChallengesScreen> createState() => _ChallengesScreenState();
}

class _ChallengesScreenState extends State<ChallengesScreen> with SingleTickerProviderStateMixin {
  final ApiService _apiService = ApiService();
  late TabController _tabController;
  List<Challenge> _challenges = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadChallenges();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadChallenges() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getChallenges();
      setState(() {
        _challenges = (response.data['data'] as List)
            .map((json) => Challenge.fromJson(json))
            .toList();
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur: $e'), backgroundColor: JuventusTheme.error),
        );
      }
    }
  }

  List<Challenge> get _filteredChallenges {
    switch (_tabController.index) {
      case 1: // En cours
        return _challenges.where((c) => c.userProgress != null && !c.userProgress!.isCompleted).toList();
      case 2: // Complétés
        return _challenges.where((c) => c.userProgress?.isCompleted ?? false).toList();
      default: // Tous
        return _challenges;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('DÉFIS'),
        backgroundColor: JuventusTheme.primaryBlack,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: JuventusTheme.accentGold,
          labelColor: JuventusTheme.accentGold,
          unselectedLabelColor: JuventusTheme.grey400,
          onTap: (_) => setState(() {}),
          tabs: const [
            Tab(text: 'TOUS'),
            Tab(text: 'EN COURS'),
            Tab(text: 'COMPLÉTÉS'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: JuventusTheme.primaryBlack))
          : RefreshIndicator(
              onRefresh: _loadChallenges,
              color: JuventusTheme.primaryBlack,
              child: _buildChallengesList(),
            ),
    );
  }

  Widget _buildChallengesList() {
    if (_filteredChallenges.isEmpty) {
      return const Center(child: Text('Aucun défi trouvé'));
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _filteredChallenges.length,
      itemBuilder: (context, index) => _buildChallengeCard(_filteredChallenges[index]),
    );
  }

  Widget _buildChallengeCard(Challenge challenge) {
    final userProgress = challenge.userProgress;
    final isCompleted = userProgress?.isCompleted ?? false;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: isCompleted ? JuventusTheme.success.withOpacity(0.1) : JuventusTheme.accentGold.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Icon(
                    isCompleted ? Icons.check_circle : Icons.flag,
                    color: isCompleted ? JuventusTheme.success : JuventusTheme.accentGold,
                    size: 24,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        challenge.name,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Text(
                        challenge.type.toUpperCase(),
                        style: const TextStyle(
                          fontSize: 11,
                          color: JuventusTheme.grey600,
                        ),
                      ),
                    ],
                  ),
                ),
                if (isCompleted)
                  const Icon(Icons.check_circle, color: JuventusTheme.success, size: 28),
              ],
            ),
            const SizedBox(height: 12),
            Text(
              challenge.description,
              style: const TextStyle(color: JuventusTheme.grey700, fontSize: 14),
            ),
            if (userProgress != null && !isCompleted) ...[
              const SizedBox(height: 16),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Progression',
                        style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                      ),
                      Text(
                        '${userProgress.currentProgress}/${userProgress.targetValue}',
                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  ClipRRect(
                    borderRadius: BorderRadius.circular(4),
                    child: LinearProgressIndicator(
                      value: userProgress.progressPercentage / 100,
                      minHeight: 8,
                      backgroundColor: JuventusTheme.grey200,
                      valueColor: const AlwaysStoppedAnimation<Color>(JuventusTheme.accentGold),
                    ),
                  ),
                ],
              ),
            ],
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    const Icon(Icons.stars, size: 16, color: JuventusTheme.accentGold),
                    const SizedBox(width: 4),
                    Text(
                      '+${challenge.rewardTokens} tokens',
                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                    ),
                    const SizedBox(width: 12),
                    const Icon(Icons.trending_up, size: 16, color: JuventusTheme.info),
                    const SizedBox(width: 4),
                    Text(
                      '+${challenge.rewardXp} XP',
                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
                Text(
                  'Expire: ${DateFormat('dd/MM').format(challenge.endsAt)}',
                  style: const TextStyle(fontSize: 11, color: JuventusTheme.grey600),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
