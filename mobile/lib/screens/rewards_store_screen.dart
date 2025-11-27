import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/fan_token_models.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class RewardsStoreScreen extends StatefulWidget {
  const RewardsStoreScreen({super.key});

  @override
  State<RewardsStoreScreen> createState() => _RewardsStoreScreenState();
}

class _RewardsStoreScreenState extends State<RewardsStoreScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();
  List<Reward> _rewards = [];
  double _userBalance = 0;
  int _userLevel = 1;
  bool _isLoading = true;
  String? _selectedCategory;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 6, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {
          switch (_tabController.index) {
            case 0:
              _selectedCategory = null;
              break;
            case 1:
              _selectedCategory = 'merchandise';
              break;
            case 2:
              _selectedCategory = 'experience';
              break;
            case 3:
              _selectedCategory = 'discount';
              break;
            case 4:
              _selectedCategory = 'digital';
              break;
            case 5:
              _selectedCategory = 'exclusive';
              break;
          }
          _loadRewards();
        });
      }
    });
    _loadRewards();
  }

  Future<void> _loadRewards() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getRewards(category: _selectedCategory);
      setState(() {
        final List data = response.data['rewards'];
        _rewards = data.map((json) => Reward.fromJson(json)).toList();
        _userBalance = response.data['user_balance'] ?? 0;
        _userLevel = response.data['user_level'] ?? 1;
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
        title: const Text('BOUTIQUE DE RÉCOMPENSES'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(100),
          child: Column(
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                color: JuventusTheme.primaryBlack,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Text(
                      'Votre solde: ',
                      style: TextStyle(
                        color: JuventusTheme.grey300,
                        fontSize: 14,
                      ),
                    ),
                    Text(
                      '${_userBalance.toStringAsFixed(0)} ',
                      style: const TextStyle(
                        color: JuventusTheme.primaryWhite,
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const Text(
                      'tokens',
                      style: TextStyle(
                        color: JuventusTheme.accentGold,
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ),
              TabBar(
                controller: _tabController,
                isScrollable: true,
                indicatorColor: JuventusTheme.primaryWhite,
                indicatorWeight: 3,
                labelColor: JuventusTheme.primaryWhite,
                unselectedLabelColor: JuventusTheme.grey500,
                labelStyle: const TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 12,
                ),
                tabs: const [
                  Tab(text: 'TOUS'),
                  Tab(text: 'MARCHANDISE'),
                  Tab(text: 'EXPÉRIENCE'),
                  Tab(text: 'RÉDUCTION'),
                  Tab(text: 'DIGITAL'),
                  Tab(text: 'EXCLUSIF'),
                ],
              ),
            ],
          ),
        ),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(color: JuventusTheme.primaryBlack),
            )
          : RefreshIndicator(
              onRefresh: _loadRewards,
              color: JuventusTheme.primaryBlack,
              child: _rewards.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.card_giftcard_outlined,
                            size: 80,
                            color: JuventusTheme.grey400,
                          ),
                          const SizedBox(height: 16),
                          const Text(
                            'Aucune récompense disponible',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w600,
                              color: JuventusTheme.grey700,
                            ),
                          ),
                        ],
                      ),
                    )
                  : GridView.builder(
                      padding: const EdgeInsets.all(16),
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        childAspectRatio: 0.75,
                        crossAxisSpacing: 12,
                        mainAxisSpacing: 12,
                      ),
                      itemCount: _rewards.length,
                      itemBuilder: (context, index) {
                        return _buildRewardCard(_rewards[index]);
                      },
                    ),
            ),
    );
  }

  Widget _buildRewardCard(Reward reward) {
    final canAfford = _userBalance >= reward.tokenCost;
    final meetsLevel = _userLevel >= reward.minimumLevel;
    final canRedeem = reward.canRedeem;

    return Container(
      decoration: JuventusDecorations.whiteCard,
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => _showRewardDetails(reward),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            Stack(
              children: [
                Container(
                  height: 120,
                  width: double.infinity,
                  color: JuventusTheme.grey200,
                  child: reward.images.isNotEmpty
                      ? Image.network(
                          reward.images[0],
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) {
                            return const Icon(
                              Icons.card_giftcard,
                              size: 40,
                              color: JuventusTheme.grey400,
                            );
                          },
                        )
                      : const Icon(
                          Icons.card_giftcard,
                          size: 40,
                          color: JuventusTheme.grey400,
                        ),
                ),
                if (reward.isFeatured)
                  Positioned(
                    top: 8,
                    right: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
                      decoration: BoxDecoration(
                        color: JuventusTheme.accentGold,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: const Text(
                        'VEDETTE',
                        style: TextStyle(
                          color: JuventusTheme.primaryWhite,
                          fontWeight: FontWeight.bold,
                          fontSize: 9,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ),
                  ),
                if (reward.stockRemaining != null && reward.stockRemaining! <= 5)
                  Positioned(
                    bottom: 8,
                    left: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
                      decoration: BoxDecoration(
                        color: JuventusTheme.error,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(
                        '${reward.stockRemaining} restant${reward.stockRemaining! > 1 ? 's' : ''}',
                        style: const TextStyle(
                          color: JuventusTheme.primaryWhite,
                          fontWeight: FontWeight.bold,
                          fontSize: 9,
                        ),
                      ),
                    ),
                  ),
              ],
            ),

            // Info
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      reward.name,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: JuventusTheme.primaryBlack,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      reward.categoryDisplay,
                      style: const TextStyle(
                        fontSize: 11,
                        color: JuventusTheme.grey600,
                      ),
                    ),
                    const Spacer(),
                    Row(
                      children: [
                        const Icon(
                          Icons.stars,
                          size: 16,
                          color: JuventusTheme.accentGold,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          '${reward.tokenCost}',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: canAfford
                                ? JuventusTheme.primaryBlack
                                : JuventusTheme.grey400,
                          ),
                        ),
                      ],
                    ),
                    if (!meetsLevel)
                      Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(
                          'Niveau ${reward.minimumLevel} requis',
                          style: const TextStyle(
                            fontSize: 10,
                            color: JuventusTheme.error,
                          ),
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showRewardDetails(Reward reward) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (context) {
        return Container(
          height: MediaQuery.of(context).size.height * 0.85,
          decoration: const BoxDecoration(
            color: JuventusTheme.primaryWhite,
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(20),
              topRight: Radius.circular(20),
            ),
          ),
          child: Column(
            children: [
              Container(
                margin: const EdgeInsets.only(top: 12, bottom: 8),
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: JuventusTheme.grey300,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  children: [
                    const Expanded(
                      child: Text(
                        'Détails de la récompense',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                          color: JuventusTheme.primaryBlack,
                        ),
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (reward.images.isNotEmpty)
                        ClipRRect(
                          borderRadius: BorderRadius.circular(12),
                          child: Image.network(
                            reward.images[0],
                            height: 200,
                            width: double.infinity,
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) {
                              return Container(
                                height: 200,
                                color: JuventusTheme.grey200,
                                child: const Icon(
                                  Icons.card_giftcard,
                                  size: 60,
                                  color: JuventusTheme.grey400,
                                ),
                              );
                            },
                          ),
                        ),
                      const SizedBox(height: 20),
                      Text(
                        reward.name,
                        style: const TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: JuventusTheme.primaryBlack,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: JuventusTheme.info.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          reward.categoryDisplay,
                          style: const TextStyle(
                            fontSize: 12,
                            color: JuventusTheme.info,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: JuventusTheme.accentGold.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text(
                              'Coût',
                              style: TextStyle(
                                fontSize: 16,
                                color: JuventusTheme.grey700,
                              ),
                            ),
                            Row(
                              children: [
                                const Icon(
                                  Icons.stars,
                                  color: JuventusTheme.accentGold,
                                  size: 24,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  '${reward.tokenCost}',
                                  style: const TextStyle(
                                    fontSize: 28,
                                    fontWeight: FontWeight.bold,
                                    color: JuventusTheme.primaryBlack,
                                  ),
                                ),
                                const SizedBox(width: 4),
                                const Text(
                                  'tokens',
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: JuventusTheme.grey600,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      if (reward.description != null) ...[
                        const SizedBox(height: 24),
                        const Text(
                          'Description',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: JuventusTheme.primaryBlack,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          reward.description!,
                          style: const TextStyle(
                            fontSize: 14,
                            color: JuventusTheme.grey700,
                            height: 1.5,
                          ),
                        ),
                      ],
                      const SizedBox(height: 24),
                      _buildRewardInfo(reward),
                    ],
                  ),
                ),
              ),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: JuventusTheme.primaryWhite,
                  boxShadow: [
                    BoxShadow(
                      color: JuventusTheme.primaryBlack.withOpacity(0.1),
                      blurRadius: 8,
                      offset: const Offset(0, -2),
                    ),
                  ],
                ),
                child: SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton(
                    onPressed: reward.canRedeem
                        ? () {
                            Navigator.pop(context);
                            _redeemReward(reward);
                          }
                        : null,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: JuventusTheme.primaryBlack,
                      disabledBackgroundColor: JuventusTheme.grey400,
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child: Text(
                      reward.canRedeem ? 'ÉCHANGER' : 'NON DISPONIBLE',
                      style: const TextStyle(
                        color: JuventusTheme.primaryWhite,
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1,
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildRewardInfo(Reward reward) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Informations',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        const SizedBox(height: 12),
        _buildInfoRow('Niveau minimum', 'Niveau ${reward.minimumLevel}'),
        if (reward.stockRemaining != null)
          _buildInfoRow('Stock restant', '${reward.stockRemaining} unité${reward.stockRemaining! > 1 ? 's' : ''}'),
        if (reward.maxPerUser != null)
          _buildInfoRow('Limite par utilisateur', '${reward.maxPerUser} max'),
        _buildInfoRow('Échanges totaux', '${reward.totalRedeemed}'),
        if (reward.monetaryValue != null)
          _buildInfoRow(
            'Valeur',
            '${reward.monetaryValue!.toStringAsFixed(2)} TND',
          ),
      ],
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
              color: JuventusTheme.grey600,
            ),
          ),
          Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: JuventusTheme.primaryBlack,
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _redeemReward(Reward reward) async {
    try {
      await _apiService.redeemReward(reward.id, {});
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('${reward.name} échangée avec succès!'),
            backgroundColor: JuventusTheme.success,
          ),
        );
        _loadRewards();
      }
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

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }
}
