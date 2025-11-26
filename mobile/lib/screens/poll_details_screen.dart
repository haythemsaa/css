import 'package:flutter/material.dart';
import 'dart:async';
import '../models/poll.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class PollDetailsScreen extends StatefulWidget {
  final int pollId;

  const PollDetailsScreen({super.key, required this.pollId});

  @override
  State<PollDetailsScreen> createState() => _PollDetailsScreenState();
}

class _PollDetailsScreenState extends State<PollDetailsScreen> {
  final ApiService _apiService = ApiService();
  final TextEditingController _textController = TextEditingController();
  Poll? _poll;
  bool _isLoading = true;
  bool _isSubmitting = false;
  Timer? _countdownTimer;

  // Voting state
  int? _selectedOptionId;
  List<int> _selectedOptionIds = [];
  double _ratingValue = 5.0;

  @override
  void initState() {
    super.initState();
    _loadPoll();
  }

  Future<void> _loadPoll() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getPollDetails(widget.pollId);
      setState(() {
        _poll = Poll.fromJson(response.data);
        _isLoading = false;
      });
      if (_poll!.isActive) {
        _startCountdown();
      }
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

  void _startCountdown() {
    _countdownTimer?.cancel();
    _countdownTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_poll != null && _poll!.isActive && mounted) {
        setState(() {}); // Rebuild to update countdown
      } else {
        timer.cancel();
      }
    });
  }

  Future<void> _submitVote() async {
    if (_poll == null) return;

    Map<String, dynamic> voteData = {};

    // Validate and prepare vote data based on poll type
    switch (_poll!.type) {
      case 'single':
        if (_selectedOptionId == null) {
          _showError('Veuillez sélectionner une option');
          return;
        }
        voteData['option_id'] = _selectedOptionId;
        break;

      case 'multiple':
        if (_selectedOptionIds.isEmpty) {
          _showError('Veuillez sélectionner au moins une option');
          return;
        }
        if (_selectedOptionIds.length > _poll!.maxVotesPerUser) {
          _showError('Vous ne pouvez sélectionner que ${_poll!.maxVotesPerUser} option(s) maximum');
          return;
        }
        voteData['option_ids'] = _selectedOptionIds;
        break;

      case 'rating':
        voteData['rating'] = _ratingValue.round();
        break;

      case 'text':
        if (_textController.text.trim().isEmpty) {
          _showError('Veuillez entrer une réponse');
          return;
        }
        voteData['text_response'] = _textController.text.trim();
        break;
    }

    setState(() => _isSubmitting = true);
    try {
      await _apiService.votePoll(widget.pollId, voteData);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Vote enregistré avec succès!'),
            backgroundColor: JuventusTheme.success,
          ),
        );
        _loadPoll(); // Reload to show results
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
    } finally {
      setState(() => _isSubmitting = false);
    }
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: JuventusTheme.error,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('DÉTAILS DU SONDAGE'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: JuventusTheme.primaryBlack,
              ),
            )
          : _poll == null
              ? const Center(child: Text('Sondage non trouvé'))
              : RefreshIndicator(
                  onRefresh: _loadPoll,
                  color: JuventusTheme.primaryBlack,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildHeader(),
                        const SizedBox(height: 16),
                        _buildMetaInfo(),
                        const SizedBox(height: 24),
                        if (_poll!.userHasVoted == true ||
                            _poll!.isClosed ||
                            _poll!.showResultsBeforeVote)
                          _buildResults()
                        else if (_poll!.userCanVote == true && _poll!.isActive)
                          _buildVotingInterface()
                        else
                          _buildNoAccessMessage(),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  _poll!.title,
                  style: const TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    color: JuventusTheme.primaryBlack,
                  ),
                ),
              ),
              if (_poll!.isFeatured)
                Container(
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
                      fontSize: 11,
                      letterSpacing: 0.5,
                    ),
                  ),
                ),
            ],
          ),
          if (_poll!.description != null) ...[
            const SizedBox(height: 12),
            Text(
              _poll!.description!,
              style: const TextStyle(
                fontSize: 15,
                color: JuventusTheme.grey700,
                height: 1.5,
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildMetaInfo() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: _buildInfoItem(
                  icon: Icons.category_outlined,
                  label: 'Catégorie',
                  value: _poll!.categoryDisplay,
                ),
              ),
              Container(width: 1, height: 40, color: JuventusTheme.grey300),
              const SizedBox(width: 12),
              Expanded(
                child: _buildInfoItem(
                  icon: Icons.format_list_bulleted,
                  label: 'Type',
                  value: _poll!.typeDisplay,
                ),
              ),
            ],
          ),
          const Divider(height: 24, color: JuventusTheme.grey300),
          Row(
            children: [
              Expanded(
                child: _buildInfoItem(
                  icon: Icons.how_to_vote,
                  label: 'Total votes',
                  value: _poll!.totalVotes.toString(),
                  valueColor: JuventusTheme.success,
                ),
              ),
              Container(width: 1, height: 40, color: JuventusTheme.grey300),
              const SizedBox(width: 12),
              Expanded(
                child: _buildInfoItem(
                  icon: Icons.people_outline,
                  label: 'Votants',
                  value: _poll!.totalVoters.toString(),
                  valueColor: JuventusTheme.info,
                ),
              ),
            ],
          ),
          if (_poll!.timeRemaining != null && _poll!.isActive) ...[
            const Divider(height: 24, color: JuventusTheme.grey300),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: JuventusTheme.primaryBlack.withOpacity(0.05),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.timer_outlined,
                    color: JuventusTheme.primaryBlack,
                    size: 20,
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    'Temps restant: ',
                    style: TextStyle(
                      fontSize: 14,
                      color: JuventusTheme.grey700,
                    ),
                  ),
                  Text(
                    _formatTimeRemaining(_poll!.timeRemaining!),
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: JuventusTheme.primaryBlack,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildInfoItem({
    required IconData icon,
    required String label,
    required String value,
    Color? valueColor,
  }) {
    return Column(
      children: [
        Icon(icon, color: JuventusTheme.grey600, size: 24),
        const SizedBox(height: 6),
        Text(
          label,
          style: const TextStyle(
            fontSize: 12,
            color: JuventusTheme.grey600,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: valueColor ?? JuventusTheme.primaryBlack,
          ),
        ),
      ],
    );
  }

  Widget _buildVotingInterface() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'VOTRE VOTE',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: JuventusTheme.primaryBlack,
              letterSpacing: 0.5,
            ),
          ),
          const SizedBox(height: 20),
          if (_poll!.type == 'single')
            _buildSingleChoice()
          else if (_poll!.type == 'multiple')
            _buildMultipleChoice()
          else if (_poll!.type == 'rating')
            _buildRatingInput()
          else if (_poll!.type == 'text')
            _buildTextInput(),
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton(
              onPressed: _isSubmitting ? null : _submitVote,
              style: ElevatedButton.styleFrom(
                backgroundColor: JuventusTheme.primaryBlack,
                disabledBackgroundColor: JuventusTheme.grey400,
                elevation: 0,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              child: _isSubmitting
                  ? const SizedBox(
                      height: 20,
                      width: 20,
                      child: CircularProgressIndicator(
                        color: JuventusTheme.primaryWhite,
                        strokeWidth: 2,
                      ),
                    )
                  : const Text(
                      'VOTER',
                      style: TextStyle(
                        color: JuventusTheme.primaryWhite,
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1,
                      ),
                    ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSingleChoice() {
    return Column(
      children: _poll!.options.map((option) {
        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          decoration: BoxDecoration(
            border: Border.all(
              color: _selectedOptionId == option.id
                  ? JuventusTheme.primaryBlack
                  : JuventusTheme.grey300,
              width: 2,
            ),
            borderRadius: BorderRadius.circular(8),
          ),
          child: RadioListTile<int>(
            value: option.id,
            groupValue: _selectedOptionId,
            onChanged: (value) {
              setState(() => _selectedOptionId = value);
            },
            title: Text(
              option.text,
              style: TextStyle(
                fontWeight: _selectedOptionId == option.id
                    ? FontWeight.bold
                    : FontWeight.normal,
                color: JuventusTheme.primaryBlack,
              ),
            ),
            subtitle: option.description != null
                ? Text(
                    option.description!,
                    style: const TextStyle(
                      color: JuventusTheme.grey600,
                      fontSize: 13,
                    ),
                  )
                : null,
            activeColor: JuventusTheme.primaryBlack,
          ),
        );
      }).toList(),
    );
  }

  Widget _buildMultipleChoice() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Sélectionnez jusqu\'à ${_poll!.maxVotesPerUser} option(s)',
          style: const TextStyle(
            fontSize: 13,
            color: JuventusTheme.grey600,
            fontStyle: FontStyle.italic,
          ),
        ),
        const SizedBox(height: 12),
        ..._poll!.options.map((option) {
          return Container(
            margin: const EdgeInsets.only(bottom: 12),
            decoration: BoxDecoration(
              border: Border.all(
                color: _selectedOptionIds.contains(option.id)
                    ? JuventusTheme.primaryBlack
                    : JuventusTheme.grey300,
                width: 2,
              ),
              borderRadius: BorderRadius.circular(8),
            ),
            child: CheckboxListTile(
              value: _selectedOptionIds.contains(option.id),
              onChanged: (checked) {
                setState(() {
                  if (checked == true) {
                    if (_selectedOptionIds.length < _poll!.maxVotesPerUser) {
                      _selectedOptionIds.add(option.id);
                    } else {
                      _showError('Maximum ${_poll!.maxVotesPerUser} options');
                    }
                  } else {
                    _selectedOptionIds.remove(option.id);
                  }
                });
              },
              title: Text(
                option.text,
                style: TextStyle(
                  fontWeight: _selectedOptionIds.contains(option.id)
                      ? FontWeight.bold
                      : FontWeight.normal,
                  color: JuventusTheme.primaryBlack,
                ),
              ),
              subtitle: option.description != null
                  ? Text(
                      option.description!,
                      style: const TextStyle(
                        color: JuventusTheme.grey600,
                        fontSize: 13,
                      ),
                    )
                  : null,
              activeColor: JuventusTheme.primaryBlack,
              checkColor: JuventusTheme.primaryWhite,
            ),
          );
        }).toList(),
      ],
    );
  }

  Widget _buildRatingInput() {
    return Column(
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Text(
              '1',
              style: TextStyle(
                fontSize: 14,
                color: JuventusTheme.grey600,
              ),
            ),
            Expanded(
              child: Slider(
                value: _ratingValue,
                min: 1,
                max: 10,
                divisions: 9,
                label: _ratingValue.round().toString(),
                activeColor: JuventusTheme.primaryBlack,
                inactiveColor: JuventusTheme.grey300,
                onChanged: (value) {
                  setState(() => _ratingValue = value);
                },
              ),
            ),
            const Text(
              '10',
              style: TextStyle(
                fontSize: 14,
                color: JuventusTheme.grey600,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: JuventusTheme.primaryBlack.withOpacity(0.05),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Text(
                'Votre note: ',
                style: TextStyle(
                  fontSize: 16,
                  color: JuventusTheme.grey700,
                ),
              ),
              Text(
                '${_ratingValue.round()}/10',
                style: const TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  color: JuventusTheme.primaryBlack,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildTextInput() {
    return TextField(
      controller: _textController,
      maxLines: 5,
      maxLength: 1000,
      decoration: InputDecoration(
        hintText: 'Entrez votre réponse...',
        hintStyle: const TextStyle(color: JuventusTheme.grey400),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: JuventusTheme.grey300, width: 2),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: JuventusTheme.grey300, width: 2),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: JuventusTheme.primaryBlack, width: 2),
        ),
        filled: true,
        fillColor: JuventusTheme.primaryWhite,
      ),
      style: const TextStyle(
        fontSize: 15,
        color: JuventusTheme.primaryBlack,
      ),
    );
  }

  Widget _buildResults() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Text(
                'RÉSULTATS',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: JuventusTheme.primaryBlack,
                  letterSpacing: 0.5,
                ),
              ),
              const Spacer(),
              if (_poll!.userHasVoted == true)
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: JuventusTheme.success.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: const Text(
                    '✓ Vous avez voté',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: JuventusTheme.success,
                    ),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 20),
          if (_poll!.type == 'rating' && _poll!.statistics?.averageRating != null)
            _buildAverageRating()
          else if (_poll!.type != 'text')
            _buildOptionResults()
          else
            const Text(
              'Les réponses textuelles sont privées',
              style: TextStyle(
                fontSize: 14,
                color: JuventusTheme.grey600,
                fontStyle: FontStyle.italic,
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildAverageRating() {
    final avgRating = _poll!.statistics!.averageRating!;
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: JuventusTheme.primaryBlack.withOpacity(0.05),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        children: [
          const Text(
            'Note moyenne',
            style: TextStyle(
              fontSize: 14,
              color: JuventusTheme.grey700,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            avgRating.toStringAsFixed(1),
            style: const TextStyle(
              fontSize: 48,
              fontWeight: FontWeight.bold,
              color: JuventusTheme.primaryBlack,
            ),
          ),
          const Text(
            'sur 10',
            style: TextStyle(
              fontSize: 16,
              color: JuventusTheme.grey600,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOptionResults() {
    return Column(
      children: _poll!.options.map((option) {
        return Container(
          margin: const EdgeInsets.only(bottom: 16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(
                      option.text,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w600,
                        color: JuventusTheme.primaryBlack,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Text(
                    '${option.votePercentage.toStringAsFixed(1)}%',
                    style: const TextStyle(
                      fontSize: 16,
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
                  value: option.votePercentage / 100,
                  minHeight: 10,
                  backgroundColor: JuventusTheme.grey200,
                  valueColor: const AlwaysStoppedAnimation<Color>(
                    JuventusTheme.primaryBlack,
                  ),
                ),
              ),
              const SizedBox(height: 4),
              Text(
                '${option.voteCount} vote${option.voteCount != 1 ? 's' : ''}',
                style: const TextStyle(
                  fontSize: 12,
                  color: JuventusTheme.grey600,
                ),
              ),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildNoAccessMessage() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: JuventusDecorations.whiteCard,
      child: Column(
        children: [
          Icon(
            _poll!.isClosed ? Icons.lock_outline : Icons.info_outline,
            size: 60,
            color: JuventusTheme.grey400,
          ),
          const SizedBox(height: 16),
          Text(
            _poll!.isClosed
                ? 'Ce sondage est fermé'
                : 'Vous ne pouvez pas participer à ce sondage',
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w600,
              color: JuventusTheme.grey700,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            _poll!.visibility == 'socios_only'
                ? 'Réservé aux membres Socios'
                : 'Accès restreint',
            style: const TextStyle(
              fontSize: 14,
              color: JuventusTheme.grey600,
            ),
          ),
        ],
      ),
    );
  }

  String _formatTimeRemaining(int seconds) {
    final duration = Duration(seconds: seconds);
    if (duration.inDays > 0) {
      return '${duration.inDays}j ${duration.inHours % 24}h';
    } else if (duration.inHours > 0) {
      return '${duration.inHours}h ${duration.inMinutes % 60}min';
    } else if (duration.inMinutes > 0) {
      return '${duration.inMinutes}min ${duration.inSeconds % 60}s';
    } else {
      return '${duration.inSeconds}s';
    }
  }

  @override
  void dispose() {
    _countdownTimer?.cancel();
    _textController.dispose();
    super.dispose();
  }
}
