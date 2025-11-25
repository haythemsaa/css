import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/donation_goal.dart';
import '../services/api_service.dart';
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
          SnackBar(content: Text('Erreur: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Objectifs de Dons'),
        backgroundColor: Colors.black,
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadGoals,
              child: _goals.isEmpty
                  ? const Center(child: Text('Aucun objectif disponible'))
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

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
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
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(
                      goal.categoryDisplay,
                      style: TextStyle(fontSize: 10, color: Colors.grey[700]),
                    ),
                  ),
                  const Spacer(),
                  if (goal.isFeatured)
                    Icon(Icons.star, color: Colors.yellow[700], size: 20),
                ],
              ),

              const SizedBox(height: 12),

              // Title
              Text(
                goal.title,
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),

              const SizedBox(height: 8),

              // Description
              Text(
                goal.description,
                style: TextStyle(fontSize: 14, color: Colors.grey[600]),
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
                        ),
                      ),
                      Text(
                        '${goal.donorsCount} donateurs',
                        style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  LinearProgressIndicator(
                    value: progress,
                    backgroundColor: Colors.grey[200],
                    valueColor: AlwaysStoppedAnimation<Color>(
                      goal.isCompleted ? Colors.green : Colors.blue,
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
                          Text(
                            'Collecté',
                            style: TextStyle(fontSize: 10, color: Colors.grey[600]),
                          ),
                          Text(
                            currencyFormat.format(goal.currentAmount),
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 14,
                            ),
                          ),
                        ],
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(
                            'Objectif',
                            style: TextStyle(fontSize: 10, color: Colors.grey[600]),
                          ),
                          Text(
                            currencyFormat.format(goal.targetAmount),
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 14,
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
                    Icon(Icons.access_time, size: 16, color: Colors.grey[600]),
                    const SizedBox(width: 4),
                    Text(
                      '${goal.daysRemaining} jours restants',
                      style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    ),
                  ],
                ),
              ],

              if (goal.impactMetrics != null) ...[
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.blue[50],
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.trending_up, size: 16, color: Colors.blue),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          goal.impactMetrics!,
                          style: const TextStyle(fontSize: 12, color: Colors.blue),
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
        return Colors.red;
      case 'high':
        return Colors.orange;
      case 'medium':
        return Colors.blue;
      case 'low':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Filtrer par catégorie'),
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
      title: Text(label),
      value: category,
      groupValue: _selectedCategory,
      onChanged: (value) {
        setState(() => _selectedCategory = value);
        Navigator.pop(context);
        _loadGoals();
      },
    );
  }
}
