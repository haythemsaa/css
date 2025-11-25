import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/donation_goal.dart';
import '../models/payment_method.dart';
import '../services/api_service.dart';
import 'payment_methods_screen.dart';

class DonationGoalDetailsScreen extends StatefulWidget {
  final String slug;

  const DonationGoalDetailsScreen({super.key, required this.slug});

  @override
  State<DonationGoalDetailsScreen> createState() => _DonationGoalDetailsScreenState();
}

class _DonationGoalDetailsScreenState extends State<DonationGoalDetailsScreen> {
  final ApiService _apiService = ApiService();
  final TextEditingController _amountController = TextEditingController();
  final TextEditingController _messageController = TextEditingController();
  DonationGoal? _goal;
  bool _isLoading = true;
  bool _isAnonymous = false;
  PaymentMethod? _selectedPaymentMethod;

  @override
  void initState() {
    super.initState();
    _loadGoalDetails();
  }

  Future<void> _loadGoalDetails() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getDonationGoalDetails(widget.slug);
      setState(() {
        _goal = DonationGoal.fromJson(response.data);
        _amountController.text = _goal!.minDonation.toStringAsFixed(2);
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

  Future<void> _selectPaymentMethod() async {
    final result = await Navigator.push<PaymentMethod>(
      context,
      MaterialPageRoute(
        builder: (context) => PaymentMethodsScreen(
          context: 'donations',
          amount: double.tryParse(_amountController.text) ?? _goal!.minDonation,
          onMethodSelected: (method) {},
        ),
      ),
    );

    if (result != null) {
      setState(() => _selectedPaymentMethod = result);
    }
  }

  Future<void> _makeDonation() async {
    final amount = double.tryParse(_amountController.text);

    if (amount == null || amount < _goal!.minDonation) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Le don minimum est de ${_goal!.minDonation} TND'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (_selectedPaymentMethod == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Veuillez sélectionner une méthode de paiement'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    try {
      await _apiService.donateToGoal(_goal!.id, {
        'amount': amount,
        'payment_method_id': _selectedPaymentMethod!.id,
        'is_anonymous': _isAnonymous,
        'donor_message': _messageController.text.isNotEmpty ? _messageController.text : null,
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Merci pour votre don!'),
            backgroundColor: Colors.green,
          ),
        );
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading || _goal == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Détails objectif')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');
    final progress = _goal!.progressPercentage / 100;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Objectif de don'),
        backgroundColor: Colors.black,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Featured Image
            if (_goal!.featuredImage != null)
              Image.network(
                _goal!.featuredImage!,
                height: 200,
                width: double.infinity,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) {
                  return Container(
                    height: 200,
                    color: Colors.grey[300],
                    child: const Icon(Icons.image, size: 100),
                  );
                },
              ),

            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Priority & Category
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: _getPriorityColor(_goal!.priority),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          _goal!.priorityDisplay.toUpperCase(),
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Chip(
                        label: Text(_goal!.categoryDisplay),
                        backgroundColor: Colors.grey[200],
                      ),
                    ],
                  ),

                  const SizedBox(height: 16),

                  // Title
                  Text(
                    _goal!.title,
                    style: const TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                    ),
                  ),

                  const SizedBox(height: 12),

                  // Progress
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.blue[50],
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.blue),
                    ),
                    child: Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Collecté',
                                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                                ),
                                Text(
                                  currencyFormat.format(_goal!.currentAmount),
                                  style: const TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.blue,
                                  ),
                                ),
                              ],
                            ),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.end,
                              children: [
                                Text(
                                  'Objectif',
                                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                                ),
                                Text(
                                  currencyFormat.format(_goal!.targetAmount),
                                  style: const TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        LinearProgressIndicator(
                          value: progress,
                          backgroundColor: Colors.grey[200],
                          valueColor: AlwaysStoppedAnimation<Color>(
                            _goal!.isCompleted ? Colors.green : Colors.blue,
                          ),
                          minHeight: 10,
                        ),
                        const SizedBox(height: 8),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              '${_goal!.progressPercentage.toStringAsFixed(1)}% atteint',
                              style: const TextStyle(fontWeight: FontWeight.bold),
                            ),
                            Text(
                              '${_goal!.donorsCount} donateurs',
                              style: TextStyle(color: Colors.grey[600]),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 16),

                  // Description
                  const Text(
                    'Description',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _goal!.fullDetails ?? _goal!.description,
                    style: const TextStyle(fontSize: 16),
                  ),

                  if (_goal!.impactMetrics != null) ...[
                    const SizedBox(height: 16),
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.green[50],
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.trending_up, color: Colors.green),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              _goal!.impactMetrics!,
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],

                  // Milestones
                  if (_goal!.milestones != null && _goal!.milestones!.isNotEmpty) ...[
                    const SizedBox(height: 24),
                    const Text(
                      'Jalons de progression',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 12),
                    ..._goal!.milestones!.map((milestone) => _buildMilestoneCard(milestone)),
                  ],

                  const SizedBox(height: 24),

                  // Donation Form
                  if (!_goal!.isCompleted) ...[
                    const Text(
                      'Faire un don',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 16),

                    TextField(
                      controller: _amountController,
                      keyboardType: TextInputType.number,
                      decoration: InputDecoration(
                        labelText: 'Montant (TND)',
                        hintText: 'Minimum: ${_goal!.minDonation} TND',
                        border: const OutlineInputBorder(),
                        prefixIcon: const Icon(Icons.attach_money),
                      ),
                    ),

                    const SizedBox(height: 16),

                    TextField(
                      controller: _messageController,
                      maxLines: 3,
                      decoration: const InputDecoration(
                        labelText: 'Message (optionnel)',
                        hintText: 'Laissez un message de soutien...',
                        border: OutlineInputBorder(),
                      ),
                    ),

                    const SizedBox(height: 12),

                    CheckboxListTile(
                      title: const Text('Don anonyme'),
                      value: _isAnonymous,
                      onChanged: (value) => setState(() => _isAnonymous = value!),
                      controlAffinity: ListTileControlAffinity.leading,
                    ),

                    const SizedBox(height: 16),

                    OutlinedButton(
                      onPressed: _selectPaymentMethod,
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.all(16),
                        minimumSize: const Size(double.infinity, 50),
                      ),
                      child: Text(
                        _selectedPaymentMethod == null
                            ? 'Sélectionner une méthode de paiement'
                            : _selectedPaymentMethod!.name,
                      ),
                    ),

                    const SizedBox(height: 12),

                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _makeDonation,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green,
                          padding: const EdgeInsets.all(16),
                        ),
                        child: const Text(
                          'Confirmer le don',
                          style: TextStyle(fontSize: 18, color: Colors.white),
                        ),
                      ),
                    ),
                  ] else ...[
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.green[50],
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: Colors.green),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.check_circle, color: Colors.green),
                          SizedBox(width: 12),
                          Expanded(
                            child: Text(
                              'Objectif atteint! Merci à tous les donateurs.',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: Colors.green,
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
          ],
        ),
      ),
    );
  }

  Widget _buildMilestoneCard(DonationGoalMilestone milestone) {
    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: ListTile(
        leading: Icon(
          milestone.isAchieved ? Icons.check_circle : Icons.circle_outlined,
          color: milestone.isAchieved ? Colors.green : Colors.grey,
        ),
        title: Text(
          milestone.title,
          style: TextStyle(
            fontWeight: FontWeight.bold,
            decoration: milestone.isAchieved ? TextDecoration.lineThrough : null,
          ),
        ),
        subtitle: Text('${milestone.percentage}%'),
        trailing: milestone.isAchieved
            ? const Icon(Icons.done, color: Colors.green)
            : null,
      ),
    );
  }

  Color _getPriorityColor(String priority) {
    switch (priority) {
      case 'urgent': return Colors.red;
      case 'high': return Colors.orange;
      case 'medium': return Colors.blue;
      case 'low': return Colors.grey;
      default: return Colors.grey;
    }
  }

  @override
  void dispose() {
    _amountController.dispose();
    _messageController.dispose();
    super.dispose();
  }
}
