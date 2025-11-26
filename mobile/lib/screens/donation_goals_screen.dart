import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/donation_goal.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';
import 'donation_goal_details_screen.dart';

class DonationGoalsScreen extends StatefulWidget {
  const DonationGoalsScreen({super.key});

  @override
  State<DonationGoalsScreen> createState() => _DonationGoalsScreenState();
}

class _DonationGoalsScreenState extends State<DonationGoalsScreen> {
  final ApiService _apiService = ApiService();
  List<DonationGoal> _goals = [];
  bool _isLoading = true;
  String? _selectedCategory;

  @override
  void initState() {
    super.initState();
    _loadGoals();
  }

  Future<void> _loadGoals() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getDonationGoals(
        status: 'active',
        category: _selectedCategory,
      );
      final List data = response.data['data'];
      setState(() {
        _goals = data.map((json) => DonationGoal.fromJson(json)).toList();
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
        title: const Text('OBJECTIFS DE DONS'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: JuventusTheme.primaryBlack,
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadGoals,
              color: JuventusTheme.primaryBlack,
              child: _goals.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.volunteer_activism_outlined,
                            size: 80,
                            color: JuventusTheme.grey400,
                          ),
                          const SizedBox(height: 16),
                          const Text(
                            'Aucun objectif disponible',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w600,
                              color: JuventusTheme.grey700,
                            ),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      itemCount: _goals.length,
                      padding: const EdgeInsets.all(16),
                      itemBuilder: (context, index) {
                        return _buildGoalCard(_goals[index]);
                      },
                    ),
            ),
    );
  }

  Widget _buildGoalCard(DonationGoal goal) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');
    final progress = goal.progressPercentage / 100;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => DonationGoalDetailsScreen(slug: goal.slug),
            ),
          );
        },
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Priority Badge
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: _getPriorityColor(goal.priority),
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(
                      goal.priorityDisplay.toUpperCase(),
                      style: const TextStyle(
                        color: JuventusTheme.primaryWhite,
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: JuventusTheme.grey200,
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(
                      goal.categoryDisplay,
                      style: const TextStyle(
                        fontSize: 10,
                        color: JuventusTheme.grey700,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  const Spacer(),
                  if (goal.isFeatured)
                    const Icon(
                      Icons.star,
                      color: JuventusTheme.accentGold,
                      size: 20,
                    ),
                ],
              ),

              const SizedBox(height: 12),

              // Title
              Text(
                goal.title,
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: JuventusTheme.primaryBlack,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),

              const SizedBox(height: 8),

              // Description
              Text(
                goal.description,
                style: const TextStyle(
                  fontSize: 14,
                  color: JuventusTheme.grey600,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),

              const SizedBox(height: 16),

              // Progress Bar
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        '${goal.progressPercentage.toStringAsFixed(1)}%',
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: JuventusTheme.primaryBlack,
                        ),
                      ),
                      Text(
                        '${goal.donorsCount} donateurs',
                        style: const TextStyle(
                          fontSize: 12,
                          color: JuventusTheme.grey600,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  LinearProgressIndicator(
                    value: progress,
                    backgroundColor: JuventusTheme.grey200,
                    valueColor: AlwaysStoppedAnimation<Color>(
                      goal.isCompleted ? JuventusTheme.success : JuventusTheme.info,
                    ),
                    minHeight: 8,
                  ),
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Collecté',
                            style: TextStyle(
                              fontSize: 10,
                              color: JuventusTheme.grey600,
                            ),
                          ),
                          Text(
                            currencyFormat.format(goal.currentAmount),
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 14,
                              color: JuventusTheme.primaryBlack,
                            ),
                          ),
                        ],
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const Text(
                            'Objectif',
                            style: TextStyle(
                              fontSize: 10,
                              color: JuventusTheme.grey600,
                            ),
                          ),
                          Text(
                            currencyFormat.format(goal.targetAmount),
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 14,
                              color: JuventusTheme.primaryBlack,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ],
              ),

              if (goal.daysRemaining != null && goal.daysRemaining! > 0) ...[
                const SizedBox(height: 12),
                Row(
                  children: [
                    const Icon(
                      Icons.access_time,
                      size: 16,
                      color: JuventusTheme.grey600,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      '${goal.daysRemaining} jours restants',
                      style: const TextStyle(
                        fontSize: 12,
                        color: JuventusTheme.grey600,
                      ),
                    ),
                  ],
                ),
              ],

              if (goal.impactMetrics != null) ...[
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: JuventusTheme.grey100,
                    borderRadius: BorderRadius.circular(4),
                    border: Border.all(color: JuventusTheme.info, width: 1),
                  ),
                  child: Row(
                    children: [
                      const Icon(
                        Icons.trending_up,
                        size: 16,
                        color: JuventusTheme.info,
                      ),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          goal.impactMetrics!,
                          style: const TextStyle(
                            fontSize: 12,
                            color: JuventusTheme.info,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Color _getPriorityColor(String priority) {
    switch (priority) {
      case 'urgent':
        return JuventusTheme.error;
      case 'high':
        return JuventusTheme.warning;
      case 'medium':
        return JuventusTheme.info;
      case 'low':
        return JuventusTheme.grey600;
      default:
        return JuventusTheme.grey600;
    }
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: JuventusTheme.primaryWhite,
        title: const Text(
          'Filtrer par catégorie',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            _filterOption('Tous', null),
            _filterOption('Paiement de Litiges', 'litigation'),
            _filterOption('Achat de Joueurs', 'player_transfer'),
            _filterOption('Rénovation du Stade', 'stadium_renovation'),
            _filterOption('Académie des Jeunes', 'youth_academy'),
            _filterOption('Équipements', 'equipment'),
            _filterOption('Remboursement de Dettes', 'debt_payment'),
          ],
        ),
      ),
    );
  }

  Widget _filterOption(String label, String? category) {
    return RadioListTile<String?>(
      title: Text(
        label,
        style: const TextStyle(color: JuventusTheme.grey700),
      ),
      value: category,
      groupValue: _selectedCategory,
      activeColor: JuventusTheme.primaryBlack,
      onChanged: (value) {
        setState(() => _selectedCategory = value);
        Navigator.pop(context);
        _loadGoals();
      },
    );
  }
}
