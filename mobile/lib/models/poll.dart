class Poll {
  final int id;
  final String title;
  final String? description;
  final String type; // single, multiple, rating, text
  final String status; // draft, active, closed
  final String visibility; // public, socios_only, admin_only
  final bool allowAnonymous;
  final bool showResultsBeforeVote;
  final bool isFeatured;
  final int maxVotesPerUser;
  final DateTime? startsAt;
  final DateTime? endsAt;
  final int createdBy;
  final String? category;
  final Map<String, dynamic>? metadata;
  final DateTime createdAt;
  final DateTime updatedAt;

  // Computed properties
  final bool isActive;
  final bool isClosed;
  final int? timeRemaining;
  final int totalVotes;
  final int totalVoters;

  // Relations
  final List<PollOption> options;
  final PollStatistics? statistics;
  final Map<String, dynamic>? creator;

  // User-specific
  final bool? userHasVoted;
  final bool? userCanVote;
  final PollVote? userVote;

  Poll({
    required this.id,
    required this.title,
    this.description,
    required this.type,
    required this.status,
    required this.visibility,
    required this.allowAnonymous,
    required this.showResultsBeforeVote,
    required this.isFeatured,
    required this.maxVotesPerUser,
    this.startsAt,
    this.endsAt,
    required this.createdBy,
    this.category,
    this.metadata,
    required this.createdAt,
    required this.updatedAt,
    required this.isActive,
    required this.isClosed,
    this.timeRemaining,
    required this.totalVotes,
    required this.totalVoters,
    this.options = const [],
    this.statistics,
    this.creator,
    this.userHasVoted,
    this.userCanVote,
    this.userVote,
  });

  factory Poll.fromJson(Map<String, dynamic> json) {
    return Poll(
      id: json['id'],
      title: json['title'],
      description: json['description'],
      type: json['type'],
      status: json['status'],
      visibility: json['visibility'],
      allowAnonymous: json['allow_anonymous'] ?? false,
      showResultsBeforeVote: json['show_results_before_vote'] ?? false,
      isFeatured: json['is_featured'] ?? false,
      maxVotesPerUser: json['max_votes_per_user'] ?? 1,
      startsAt: json['starts_at'] != null ? DateTime.parse(json['starts_at']) : null,
      endsAt: json['ends_at'] != null ? DateTime.parse(json['ends_at']) : null,
      createdBy: json['created_by'],
      category: json['category'],
      metadata: json['metadata'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      isActive: json['is_active'] ?? false,
      isClosed: json['is_closed'] ?? false,
      timeRemaining: json['time_remaining'],
      totalVotes: json['total_votes'] ?? 0,
      totalVoters: json['total_voters'] ?? 0,
      options: json['options'] != null
          ? (json['options'] as List).map((o) => PollOption.fromJson(o)).toList()
          : [],
      statistics: json['statistics'] != null
          ? PollStatistics.fromJson(json['statistics'])
          : null,
      creator: json['creator'],
      userHasVoted: json['user_has_voted'],
      userCanVote: json['user_can_vote'],
      userVote: json['user_vote'] != null ? PollVote.fromJson(json['user_vote']) : null,
    );
  }

  String get typeDisplay {
    switch (type) {
      case 'single':
        return 'Choix unique';
      case 'multiple':
        return 'Choix multiple';
      case 'rating':
        return 'Notation';
      case 'text':
        return 'Question ouverte';
      default:
        return type;
    }
  }

  String get statusDisplay {
    switch (status) {
      case 'draft':
        return 'Brouillon';
      case 'active':
        return 'Actif';
      case 'closed':
        return 'Fermé';
      default:
        return status;
    }
  }

  String get categoryDisplay {
    switch (category) {
      case 'match':
        return 'Match';
      case 'transfer':
        return 'Transfert';
      case 'club_decision':
        return 'Décision du club';
      case 'community':
        return 'Communauté';
      default:
        return category ?? 'Général';
    }
  }
}

class PollOption {
  final int id;
  final int pollId;
  final String text;
  final String? description;
  final String? imageUrl;
  final int displayOrder;
  final DateTime createdAt;
  final DateTime updatedAt;

  // Computed from statistics
  final int voteCount;
  final double votePercentage;

  PollOption({
    required this.id,
    required this.pollId,
    required this.text,
    this.description,
    this.imageUrl,
    required this.displayOrder,
    required this.createdAt,
    required this.updatedAt,
    this.voteCount = 0,
    this.votePercentage = 0.0,
  });

  factory PollOption.fromJson(Map<String, dynamic> json) {
    return PollOption(
      id: json['id'],
      pollId: json['poll_id'],
      text: json['text'],
      description: json['description'],
      imageUrl: json['image_url'],
      displayOrder: json['display_order'] ?? 0,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      voteCount: json['vote_count'] ?? json['votes_count'] ?? 0,
      votePercentage: (json['vote_percentage'] ?? 0).toDouble(),
    );
  }
}

class PollVote {
  final int id;
  final int pollId;
  final int? pollOptionId;
  final int? userId;
  final String? textResponse;
  final int? ratingValue;
  final bool isAnonymous;
  final String? ipAddress;
  final DateTime createdAt;
  final DateTime updatedAt;

  PollVote({
    required this.id,
    required this.pollId,
    this.pollOptionId,
    this.userId,
    this.textResponse,
    this.ratingValue,
    required this.isAnonymous,
    this.ipAddress,
    required this.createdAt,
    required this.updatedAt,
  });

  factory PollVote.fromJson(Map<String, dynamic> json) {
    return PollVote(
      id: json['id'],
      pollId: json['poll_id'],
      pollOptionId: json['poll_option_id'],
      userId: json['user_id'],
      textResponse: json['text_response'],
      ratingValue: json['rating_value'],
      isAnonymous: json['is_anonymous'] ?? false,
      ipAddress: json['ip_address'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
}

class PollStatistics {
  final int id;
  final int pollId;
  final int totalVotes;
  final int totalVoters;
  final Map<String, dynamic>? optionResults;
  final double? averageRating;
  final DateTime? lastCalculatedAt;

  PollStatistics({
    required this.id,
    required this.pollId,
    required this.totalVotes,
    required this.totalVoters,
    this.optionResults,
    this.averageRating,
    this.lastCalculatedAt,
  });

  factory PollStatistics.fromJson(Map<String, dynamic> json) {
    return PollStatistics(
      id: json['id'],
      pollId: json['poll_id'],
      totalVotes: json['total_votes'] ?? 0,
      totalVoters: json['total_voters'] ?? 0,
      optionResults: json['option_results'],
      averageRating: json['average_rating'] != null
          ? double.parse(json['average_rating'].toString())
          : null,
      lastCalculatedAt: json['last_calculated_at'] != null
          ? DateTime.parse(json['last_calculated_at'])
          : null,
    );
  }
}
