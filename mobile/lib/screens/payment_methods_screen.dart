import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/payment_method.dart';
import '../services/api_service.dart';

import '../theme/juventus_theme.dart';
class PaymentMethodsScreen extends StatefulWidget {
  final String context; // 'donations', 'products', 'tickets', 'auctions'
  final double amount;
  final Function(PaymentMethod) onMethodSelected;

  const PaymentMethodsScreen({
    super.key,
    required this.context,
    required this.amount,
    required this.onMethodSelected,
  });

  @override
  State<PaymentMethodsScreen> createState() => _PaymentMethodsScreenState();
}

class _PaymentMethodsScreenState extends State<PaymentMethodsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();
  List<PaymentMethod> _allMethods = [];
  List<PaymentMethod> _filteredMethods = [];
  bool _isLoading = true;
  String? _selectedType;

  final Map<String, String> _typeLabels = {
    'all': 'Tous',
    'mobile_wallet': 'Portefeuilles',
    'bank_card': 'Cartes',
    'bank_transfer': 'Virement',
    'cash': 'Espèces',
    'international': 'International',
  };

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _typeLabels.length, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {
          _selectedType = _typeLabels.keys.elementAt(_tabController.index);
          _filterMethods();
        });
      }
    });
    _loadPaymentMethods();
  }

  Future<void> _loadPaymentMethods() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getPaymentMethods(context: widget.context);
      final List data = response.data['payment_methods'];
      setState(() {
        _allMethods = data.map((json) => PaymentMethod.fromJson(json)).toList();
        _selectedType = 'all';
        _filterMethods();
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

  void _filterMethods() {
    if (_selectedType == null || _selectedType == 'all') {
      _filteredMethods = _allMethods;
    } else {
      _filteredMethods = _allMethods
          .where((method) => method.type == _selectedType)
          .toList();
    }
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');

    return Scaffold(
      appBar: AppBar(
        title: const Text('MOYEN DE PAIEMENT'),
        backgroundColor: JuventusTheme.primaryBlack,
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          indicatorColor: JuventusTheme.primaryWhite,
          tabs: _typeLabels.values.map((label) => Tab(text: label)).toList(),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: JuventusTheme.primaryBlack))
          : Column(
              children: [
                // Amount Display
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(16),
                  color: JuventusTheme.grey100,
                  child: Column(
                    children: [
                      const Text(
                        'Montant à payer',
                        style: TextStyle(fontSize: 14, color: JuventusTheme.grey600),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        currencyFormat.format(widget.amount),
                        style: const TextStyle(
                          fontSize: 28,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),

                // Payment Methods List
                Expanded(
                  child: _filteredMethods.isEmpty
                      ? const Center(child: Text('Aucune méthode disponible'))
                      : ListView.builder(
                          itemCount: _filteredMethods.length,
                          padding: const EdgeInsets.all(16),
                          itemBuilder: (context, index) {
                            return _buildPaymentMethodContainer(decoration: JuventusDecorations.whiteCard,_filteredMethods[index]);
                          },
                        ),
                ),
              ],
            ),
    );
  }

  Widget _buildPaymentMethodContainer(decoration: JuventusDecorations.whiteCard,PaymentMethod method) {
    final canProcess = method.canProcessAmount(widget.amount);
    final fees = method.calculateFees(widget.amount);
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');

    return Container(decoration: JuventusDecorations.whiteCard,
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        onTap: canProcess
            ? () {
                widget.onMethodSelected(method);
                Navigator.pop(context, method);
              }
            : null,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  // Logo placeholder
                  Container(
                    width: 50,
                    height: 50,
                    decoration: BoxDecoration(
                      color: JuventusTheme.grey200,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: method.logoUrl != null
                        ? Image.network(method.logoUrl!, fit: BoxFit.contain)
                        : Icon(
                            _getMethodIcon(method.type),
                            size: 30,
                            color: JuventusTheme.grey600,
                          ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          method.name,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          method.typeDisplay,
                          style: TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                        ),
                        if (method.provider != null)
                          Text(
                            method.provider!,
                            style: TextStyle(fontSize: 10, color: JuventusTheme.grey500),
                          ),
                      ],
                    ),
                  ),
                  if (method.isDefault)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: JuventusTheme.success,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: const Text(
                        'RECOMMANDÉ',
                        style: TextStyle(
                          color: JuventusTheme.primaryWhite,
                          fontSize: 8,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                ],
              ),

              if (!canProcess) ...[
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: JuventusTheme.primaryWhite,
                    borderRadius: BorderRadius.circular(4),
                    border: Border.all(color: JuventusTheme.error, width: 2),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.error_outline, size: 16, color: JuventusTheme.error),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'Montant incompatible (min: ${method.minAmount ?? 0} TND, max: ${method.maxAmount ?? "illimité"} TND)',
                          style: const TextStyle(fontSize: 12, color: JuventusTheme.error),
                        ),
                      ),
                    ],
                  ),
                ),
              ],

              if (canProcess) ...[
                const SizedBox(height: 12),
                const Divider(),
                const SizedBox(height: 8),

                // Fees Breakdown
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Montant:', style: TextStyle(fontSize: 12)),
                    Text(
                      currencyFormat.format(fees['amount']),
                      style: const TextStyle(fontSize: 12),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Frais (${method.transactionFeePercentage}%):',
                      style: TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                    ),
                    Text(
                      '- ${currencyFormat.format(fees['total_fee'])}',
                      style: TextStyle(fontSize: 12, color: JuventusTheme.error[700]),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Vous recevrez:',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
                    Text(
                      currencyFormat.format(fees['net_amount']),
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                        color: JuventusTheme.success,
                      ),
                    ),
                  ],
                ),

                if (method.processingTime != null) ...[
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Icon(Icons.access_time, size: 14, color: JuventusTheme.grey600),
                      const SizedBox(width: 4),
                      Text(
                        'Traitement: ${method.processingTime}',
                        style: TextStyle(fontSize: 11, color: JuventusTheme.grey600),
                      ),
                    ],
                  ),
                ],
              ],
            ],
          ),
        ),
      ),
    );
  }

  IconData _getMethodIcon(String type) {
    switch (type) {
      case 'mobile_wallet':
        return Icons.phone_android;
      case 'bank_card':
        return Icons.credit_card;
      case 'bank_transfer':
        return Icons.account_balance;
      case 'cash':
        return Icons.money;
      case 'international':
        return Icons.public;
      default:
        return Icons.payment;
    }
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }
}
