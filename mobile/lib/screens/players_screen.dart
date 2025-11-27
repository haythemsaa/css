import 'package:flutter/material.dart';
import '../models/additional_models.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class PlayersScreen extends StatefulWidget {
  const PlayersScreen({super.key});

  @override
  State<PlayersScreen> createState() => _PlayersScreenState();
}

class _PlayersScreenState extends State<PlayersScreen> {
  final ApiService _apiService = ApiService();
  List<Player> _players = [];
  String _selectedPosition = 'all';
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadPlayers();
  }

  Future<void> _loadPlayers() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getPlayers();
      setState(() {
        _players = (response.data['data'] as List)
            .map((json) => Player.fromJson(json))
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

  List<Player> get _filteredPlayers {
    if (_selectedPosition == 'all') return _players;
    return _players.where((p) => p.position == _selectedPosition).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('EFFECTIF'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: JuventusTheme.primaryBlack))
          : Column(
              children: [
                _buildPositionFilter(),
                Expanded(child: _buildPlayersList()),
              ],
            ),
    );
  }

  Widget _buildPositionFilter() {
    return Container(
      padding: const EdgeInsets.all(16),
      color: JuventusTheme.primaryWhite,
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          children: [
            _buildFilterChip('Tous', 'all'),
            const SizedBox(width: 8),
            _buildFilterChip('Gardiens', 'GK'),
            const SizedBox(width: 8),
            _buildFilterChip('Défenseurs', 'DEF'),
            const SizedBox(width: 8),
            _buildFilterChip('Milieux', 'MID'),
            const SizedBox(width: 8),
            _buildFilterChip('Attaquants', 'FWD'),
          ],
        ),
      ),
    );
  }

  Widget _buildFilterChip(String label, String position) {
    final isSelected = _selectedPosition == position;
    return InkWell(
      onTap: () => setState(() => _selectedPosition = position),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? JuventusTheme.primaryBlack : JuventusTheme.grey200,
          borderRadius: BorderRadius.circular(20),
        ),
        child: Text(
          label,
          style: TextStyle(
            color: isSelected ? JuventusTheme.primaryWhite : JuventusTheme.grey700,
            fontWeight: FontWeight.w600,
            fontSize: 13,
          ),
        ),
      ),
    );
  }

  Widget _buildPlayersList() {
    if (_filteredPlayers.isEmpty) {
      return const Center(child: Text('Aucun joueur trouvé'));
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _filteredPlayers.length,
      itemBuilder: (context, index) => _buildPlayerCard(_filteredPlayers[index]),
    );
  }

  Widget _buildPlayerCard(Player player) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: JuventusDecorations.whiteCard,
      child: ListTile(
        contentPadding: const EdgeInsets.all(16),
        leading: Stack(
          children: [
            CircleAvatar(
              radius: 30,
              backgroundColor: JuventusTheme.grey200,
              backgroundImage: player.photoUrl != null ? NetworkImage(player.photoUrl!) : null,
              child: player.photoUrl == null
                  ? const Icon(Icons.person, size: 30, color: JuventusTheme.grey500)
                  : null,
            ),
            if (player.jerseyNumber != null)
              Positioned(
                bottom: 0,
                right: 0,
                child: Container(
                  padding: const EdgeInsets.all(4),
                  decoration: const BoxDecoration(
                    color: JuventusTheme.primaryBlack,
                    shape: BoxShape.circle,
                  ),
                  child: Text(
                    '${player.jerseyNumber}',
                    style: const TextStyle(
                      color: JuventusTheme.primaryWhite,
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
          ],
        ),
        title: Text(
          player.displayName,
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
        ),
        subtitle: Text(
          '${player.position}${player.nationality != null ? " • ${player.nationality}" : ""}',
          style: const TextStyle(color: JuventusTheme.grey600, fontSize: 13),
        ),
        trailing: const Icon(Icons.chevron_right, color: JuventusTheme.grey400),
        onTap: () => _showPlayerDetails(player),
      ),
    );
  }

  void _showPlayerDetails(Player player) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: MediaQuery.of(context).size.height * 0.7,
        decoration: const BoxDecoration(
          color: JuventusTheme.primaryWhite,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: JuventusTheme.grey300,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 24),
            Row(
              children: [
                CircleAvatar(
                  radius: 40,
                  backgroundColor: JuventusTheme.grey200,
                  backgroundImage: player.photoUrl != null ? NetworkImage(player.photoUrl!) : null,
                  child: player.photoUrl == null
                      ? const Icon(Icons.person, size: 40, color: JuventusTheme.grey500)
                      : null,
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (player.jerseyNumber != null)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: JuventusTheme.primaryBlack,
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Text(
                            '#${player.jerseyNumber}',
                            style: const TextStyle(
                              color: JuventusTheme.primaryWhite,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      const SizedBox(height: 8),
                      Text(
                        player.fullName,
                        style: const TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Text(
                        player.position,
                        style: const TextStyle(
                          color: JuventusTheme.grey600,
                          fontSize: 14,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            if (player.bio != null) ...[
              const SizedBox(height: 24),
              const Text(
                'BIOGRAPHIE',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 1,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                player.bio!,
                style: const TextStyle(color: JuventusTheme.grey700, height: 1.5),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
