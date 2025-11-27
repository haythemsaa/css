import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/poll.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';
import 'poll_details_screen.dart';

class PollsScreen extends StatefulWidget {
  const PollsScreen({super.key});

  @override
  State<PollsScreen> createState() => _PollsScreenState();
}

class _PollsScreenState extends State<PollsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();
  List<Poll> _polls = [];
  bool _isLoading = true;
  String? _selectedStatus;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {
          switch (_tabController.index) {
            case 0:
              _selectedStatus = 'active';
              break;
            case 1:
              _selectedStatus = 'closed';
              break;
            case 2:
              _selectedStatus = null; // All
              break;
          }
          _loadPolls();
        });
      }
    });
    _selectedStatus = 'active';
    _loadPolls();
  }

  Future<void> _loadPolls() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getPolls(status: _selectedStatus);
      final List data = response.data['polls'];
      setState(() {
        _polls = data.map((json) => Poll.fromJson(json)).toList();
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
        title: const Text('SONDAGES'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: JuventusTheme.primaryWhite,
          indicatorWeight: 3,
          labelColor: JuventusTheme.primaryWhite,
          unselectedLabelColor: JuventusTheme.grey500,
          labelStyle: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 13,
          ),
          tabs: const [
            Tab(text: 'ACTIFS'),
            Tab(text: 'FERMÉS'),
            Tab(text: 'TOUS'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: JuventusTheme.primaryBlack,
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadPolls,
              color: JuventusTheme.primaryBlack,
              child: _polls.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.poll_outlined,
                            size: 80,
                            color: JuventusTheme.grey400,
                          ),
                          const SizedBox(height: 16),
                          const Text(
                            'Aucun sondage disponible',
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
                      itemCount: _polls.length,
                      padding: const EdgeInsets.all(16),
                      itemBuilder: (context, index) {
                        return _buildPollCard(_polls[index]);
                      },
                    ),
            ),
    );
  }

  Widget _buildPollCard(Poll poll) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => PollDetailsScreen(pollId: poll.id),
            ),
          );
        },
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header
              Row(
                children: [
                  Expanded(
                    child: Text(
                      poll.title,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: JuventusTheme.primaryBlack,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  if (poll.isFeatured)
                    Container(
                      margin: const EdgeInsets.only(left: 8),
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: JuventusTheme.accentGold,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: const Text(
                        'VEDETTE',
                        style: TextStyle(
                          color: JuventusTheme.primaryWhite,
                          fontWeight: FontWeight.bold,
                          fontSize: 10,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ),
                ],
              ),

              if (poll.description != null) ...[
                const SizedBox(height: 8),
                Text(
                  poll.description!,
                  style: const TextStyle(
                    fontSize: 14,
                    color: JuventusTheme.grey600,
                    height: 1.4,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],

              const SizedBox(height: 12),

              // Meta info
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: [
                  _buildChip(
                    icon: Icons.category_outlined,
                    label: poll.categoryDisplay,
                  ),
                  _buildChip(
                    icon: Icons.format_list_bulleted,
                    label: poll.typeDisplay,
                  ),
                  if (poll.isActive)
                    _buildChip(
                      icon: Icons.check_circle_outline,
                      label: 'ACTIF',
                      color: JuventusTheme.success,
                    )
                  else if (poll.isClosed)
                    _buildChip(
                      icon: Icons.lock_outline,
                      label: 'FERMÉ',
                      color: JuventusTheme.grey600,
                    ),
                ],
              ),

              const SizedBox(height: 12),
              const Divider(color: JuventusTheme.grey300, height: 1),
              const SizedBox(height: 12),

              // Stats
              Row(
                children: [
                  _buildStatItem(
                    icon: Icons.how_to_vote,
                    value: poll.totalVotes.toString(),
                    label: 'Votes',
                  ),
                  const SizedBox(width: 24),
                  _buildStatItem(
                    icon: Icons.people_outline,
                    value: poll.totalVoters.toString(),
                    label: 'Votants',
                  ),
                  const Spacer(),
                  if (poll.timeRemaining != null && poll.isActive)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: JuventusTheme.primaryBlack.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Row(
                        children: [
                          const Icon(
                            Icons.timer_outlined,
                            size: 16,
                            color: JuventusTheme.primaryBlack,
                          ),
                          const SizedBox(width: 4),
                          Text(
                            _formatTimeRemaining(poll.timeRemaining!),
                            style: const TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: JuventusTheme.primaryBlack,
                            ),
                          ),
                        ],
                      ),
                    ),
                ],
              ),

              // User status
              if (poll.userHasVoted == true) ...[
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  decoration: BoxDecoration(
                    color: JuventusTheme.success.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(4),
                    border: Border.all(color: JuventusTheme.success.withOpacity(0.3)),
                  ),
                  child: Row(
                    children: const [
                      Icon(
                        Icons.check_circle,
                        size: 16,
                        color: JuventusTheme.success,
                      ),
                      SizedBox(width: 8),
                      Text(
                        'Vous avez déjà voté',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: JuventusTheme.success,
                        ),
                      ),
                    ],
                  ),
                ),
              ] else if (poll.userCanVote == false) ...[
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  decoration: BoxDecoration(
                    color: JuventusTheme.grey200,
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: Row(
                    children: const [
                      Icon(
                        Icons.lock_outline,
                        size: 16,
                        color: JuventusTheme.grey600,
                      ),
                      SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'Vous ne pouvez pas participer',
                          style: TextStyle(
                            fontSize: 12,
                            color: JuventusTheme.grey600,
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

  Widget _buildChip({
    required IconData icon,
    required String label,
    Color? color,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color?.withOpacity(0.1) ?? JuventusTheme.grey200,
        borderRadius: BorderRadius.circular(4),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            icon,
            size: 14,
            color: color ?? JuventusTheme.grey700,
          ),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              color: color ?? JuventusTheme.grey700,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatItem({
    required IconData icon,
    required String value,
    required String label,
  }) {
    return Row(
      children: [
        Icon(
          icon,
          size: 16,
          color: JuventusTheme.grey600,
        ),
        const SizedBox(width: 4),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              value,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: JuventusTheme.primaryBlack,
              ),
            ),
            Text(
              label,
              style: const TextStyle(
                fontSize: 10,
                color: JuventusTheme.grey600,
              ),
            ),
          ],
        ),
      ],
    );
  }

  String _formatTimeRemaining(int seconds) {
    final duration = Duration(seconds: seconds);
    if (duration.inDays > 0) {
      return '${duration.inDays}j ${duration.inHours % 24}h';
    } else if (duration.inHours > 0) {
      return '${duration.inHours}h ${duration.inMinutes % 60}min';
    } else if (duration.inMinutes > 0) {
      return '${duration.inMinutes}min';
    } else {
      return '${duration.inSeconds}s';
    }
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }
}
