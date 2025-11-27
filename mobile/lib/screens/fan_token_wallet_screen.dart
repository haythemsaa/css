import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/fan_token_models.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class FanTokenWalletScreen extends StatefulWidget {
  const FanTokenWalletScreen({super.key});

  @override
  State<FanTokenWalletScreen> createState() => _FanTokenWalletScreenState();
}

class _FanTokenWalletScreenState extends State<FanTokenWalletScreen> {
  final ApiService _apiService = ApiService();
  FanTokenWallet? _wallet;
  List<FanTokenTransaction> _transactions = [];
  bool _isLoading = true;
  bool _isClaimingBonus = false;

  @override
  void initState() {
    super.initState();
    _loadWallet();
  }

  Future<void> _loadWallet() async {
    setState(() => _isLoading = true);
    try {
      final walletResponse = await _apiService.getTokenWallet();
      final transactionsResponse = await _apiService.getTokenTransactions(page: 1);

      setState(() {
        _wallet = FanTokenWallet.fromJson(walletResponse.data['wallet']);
        final List data = transactionsResponse.data['data'];
        _transactions = data.map((json) => FanTokenTransaction.fromJson(json)).toList();
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

  Future<void> _claimDailyBonus() async {
    if (_wallet == null || !_wallet!.canClaimDailyBonus) return;

    setState(() => _isClaimingBonus = true);
    try {
      final response = await _apiService.claimDailyBonus();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Bonus quotidien réclamé! +${response.data['tokens_earned']} tokens'),
            backgroundColor: JuventusTheme.success,
          ),
        );
        _loadWallet();
      }
    } catch (e) {
      setState(() => _isClaimingBonus = false);
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
        title: const Text('MON PORTEFEUILLE'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.leaderboard),
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Classement - Disponible prochainement')),
              );
            },
            tooltip: 'Classement',
          ),
        ],
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(color: JuventusTheme.primaryBlack),
            )
          : RefreshIndicator(
              onRefresh: _loadWallet,
              color: JuventusTheme.primaryBlack,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildBalanceCard(),
                    const SizedBox(height: 16),
                    _buildLevelCard(),
                    const SizedBox(height: 16),
                    _buildQuickActions(),
                    const SizedBox(height: 24),
                    _buildTransactionsSection(),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildBalanceCard() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: JuventusDecorations.blackCard,
      child: Column(
        children: [
          const Text(
            'SOLDE DISPONIBLE',
            style: TextStyle(
              color: JuventusTheme.grey400,
              fontSize: 12,
              fontWeight: FontWeight.w600,
              letterSpacing: 1,
            ),
          ),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                _wallet!.balance.toStringAsFixed(0),
                style: const TextStyle(
                  color: JuventusTheme.primaryWhite,
                  fontSize: 48,
                  fontWeight: FontWeight.bold,
                  height: 1,
                ),
              ),
              const SizedBox(width: 8),
              Container(
                margin: const EdgeInsets.only(top: 8),
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: JuventusTheme.accentGold,
                  borderRadius: BorderRadius.circular(4),
                ),
                child: const Text(
                  'TOKENS',
                  style: TextStyle(
                    color: JuventusTheme.primaryWhite,
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 0.5,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          Row(
            children: [
              Expanded(
                child: _buildStatColumn(
                  'Gagnés',
                  _wallet!.lifetimeEarned.toStringAsFixed(0),
                  JuventusTheme.success,
                ),
              ),
              Container(width: 1, height: 40, color: JuventusTheme.grey700),
              Expanded(
                child: _buildStatColumn(
                  'Dépensés',
                  _wallet!.lifetimeSpent.toStringAsFixed(0),
                  JuventusTheme.error,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatColumn(String label, String value, Color color) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(
            color: color,
            fontSize: 24,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          label,
          style: const TextStyle(
            color: JuventusTheme.grey400,
            fontSize: 12,
          ),
        ),
      ],
    );
  }

  Widget _buildLevelCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  gradient: JuventusTheme.goldGradient,
                  shape: BoxShape.circle,
                ),
                child: Center(
                  child: Text(
                    '${_wallet!.level}',
                    style: const TextStyle(
                      color: JuventusTheme.primaryWhite,
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _wallet!.levelName,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: JuventusTheme.primaryBlack,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Niveau ${_wallet!.level}',
                      style: const TextStyle(
                        fontSize: 13,
                        color: JuventusTheme.grey600,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Progression',
                    style: TextStyle(
                      fontSize: 12,
                      color: JuventusTheme.grey600,
                    ),
                  ),
                  Text(
                    '${_wallet!.progressToNextLevel.toStringAsFixed(1)}%',
                    style: const TextStyle(
                      fontSize: 12,
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
                  value: _wallet!.progressToNextLevel / 100,
                  minHeight: 8,
                  backgroundColor: JuventusTheme.grey200,
                  valueColor: const AlwaysStoppedAnimation<Color>(JuventusTheme.accentGold),
                ),
              ),
              const SizedBox(height: 8),
              Text(
                '${_wallet!.experiencePoints} XP',
                style: const TextStyle(
                  fontSize: 11,
                  color: JuventusTheme.grey600,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActions() {
    return Row(
      children: [
        Expanded(
          child: _buildActionButton(
            label: 'Bonus Quotidien',
            icon: Icons.card_giftcard,
            color: _wallet!.canClaimDailyBonus ? JuventusTheme.success : JuventusTheme.grey400,
            enabled: _wallet!.canClaimDailyBonus && !_isClaimingBonus,
            onTap: _claimDailyBonus,
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _buildActionButton(
            label: 'Boutique',
            icon: Icons.storefront,
            color: JuventusTheme.info,
            onTap: () {
              Navigator.pushNamed(context, '/rewards');
            },
          ),
        ),
      ],
    );
  }

  Widget _buildActionButton({
    required String label,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
    bool enabled = true,
  }) {
    return InkWell(
      onTap: enabled ? onTap : null,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(
          color: enabled ? color.withOpacity(0.1) : JuventusTheme.grey200,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: enabled ? color.withOpacity(0.3) : JuventusTheme.grey300,
            width: 2,
          ),
        ),
        child: Column(
          children: [
            Icon(icon, color: enabled ? color : JuventusTheme.grey400, size: 28),
            const SizedBox(height: 8),
            Text(
              label,
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: enabled ? color : JuventusTheme.grey400,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTransactionsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'HISTORIQUE',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
                color: JuventusTheme.primaryBlack,
                letterSpacing: 0.5,
              ),
            ),
            TextButton(
              onPressed: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Historique complet - Disponible prochainement')),
                );
              },
              child: const Text(
                'Voir tout',
                style: TextStyle(color: JuventusTheme.info),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        if (_transactions.isEmpty)
          Container(
            padding: const EdgeInsets.all(32),
            decoration: JuventusDecorations.whiteCard,
            child: Center(
              child: Column(
                children: [
                  Icon(
                    Icons.receipt_long_outlined,
                    size: 48,
                    color: JuventusTheme.grey400,
                  ),
                  const SizedBox(height: 12),
                  const Text(
                    'Aucune transaction',
                    style: TextStyle(
                      color: JuventusTheme.grey600,
                      fontSize: 14,
                    ),
                  ),
                ],
              ),
            ),
          )
        else
          ..._transactions.take(5).map((transaction) => _buildTransactionItem(transaction)),
      ],
    );
  }

  Widget _buildTransactionItem(FanTokenTransaction transaction) {
    final dateFormat = DateFormat('dd/MM/yyyy • HH:mm');

    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(16),
      decoration: JuventusDecorations.whiteCard,
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: (transaction.isPositive ? JuventusTheme.success : JuventusTheme.error)
                  .withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(
              transaction.isPositive ? Icons.add : Icons.remove,
              color: transaction.isPositive ? JuventusTheme.success : JuventusTheme.error,
              size: 20,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  transaction.description ?? transaction.typeDisplay,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: JuventusTheme.primaryBlack,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  dateFormat.format(transaction.createdAt),
                  style: const TextStyle(
                    fontSize: 12,
                    color: JuventusTheme.grey600,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 12),
          Text(
            '${transaction.isPositive ? '+' : ''}${transaction.amount.toStringAsFixed(0)}',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: transaction.isPositive ? JuventusTheme.success : JuventusTheme.error,
            ),
          ),
        ],
      ),
    );
  }
}
