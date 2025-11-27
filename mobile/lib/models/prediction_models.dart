class MatchPrediction {
  final int? id;
  final int userId;
  final int matchId;
  final int? predictedHomeScore;
  final int? predictedAwayScore;
  final String? predictedResult;
  final int? predictedFirstScorerId;
  final int pointsEarned;
  final bool isProcessed;
  final DateTime predictedAt;
  final Match? match;

  MatchPrediction({
    this.id,
    required this.userId,
    required this.matchId,
    this.predictedHomeScore,
    this.predictedAwayScore,
    this.predictedResult,
    this.predictedFirstScorerId,
    required this.pointsEarned,
    required this.isProcessed,
    required this.predictedAt,
    this.match,
  });

  String get predictedScoreDisplay {
    if (predictedHomeScore == null || predictedAwayScore == null) {
      return 'N/A';
    }
    return '$predictedHomeScore - $predictedAwayScore';
  }

  factory MatchPrediction.fromJson(Map<String, dynamic> json) {
    return MatchPrediction(
      id: json['id'],
      userId: json['user_id'],
      matchId: json['match_id'],
      predictedHomeScore: json['predicted_home_score'],
      predictedAwayScore: json['predicted_away_score'],
      predictedResult: json['predicted_result'],
      predictedFirstScorerId: json['predicted_first_scorer_id'],
      pointsEarned: json['points_earned'] ?? 0,
      isProcessed: json['is_processed'] ?? false,
      predictedAt: DateTime.parse(json['predicted_at']),
      match: json['match'] != null ? Match.fromJson(json['match']) : null,
    );
  }
}

class PredictionLeaderboardEntry {
  final int id;
  final int userId;
  final int totalPredictions;
  final int correctResults;
  final int correctScores;
  final int totalPoints;
  final int currentStreak;
  final int bestStreak;
  final double accuracyPercentage;
  final int rank;
  final User? user;

  PredictionLeaderboardEntry({
    required this.id,
    required this.userId,
    required this.totalPredictions,
    required this.correctResults,
    required this.correctScores,
    required this.totalPoints,
    required this.currentStreak,
    required this.bestStreak,
    required this.accuracyPercentage,
    required this.rank,
    this.user,
  });

  factory PredictionLeaderboardEntry.fromJson(Map<String, dynamic> json) {
    return PredictionLeaderboardEntry(
      id: json['id'],
      userId: json['user_id'],
      totalPredictions: json['total_predictions'] ?? 0,
      correctResults: json['correct_results'] ?? 0,
      correctScores: json['correct_scores'] ?? 0,
      totalPoints: json['total_points'] ?? 0,
      currentStreak: json['current_streak'] ?? 0,
      bestStreak: json['best_streak'] ?? 0,
      accuracyPercentage: json['accuracy_percentage'] != null
          ? double.parse(json['accuracy_percentage'].toString())
          : 0.0,
      rank: json['rank'] ?? 0,
      user: json['user'] != null ? User.fromJson(json['user']) : null,
    );
  }
}

class Match {
  final int id;
  final String? homeTeamName;
  final String? awayTeamName;
  final int? homeScore;
  final int? awayScore;
  final String status;
  final DateTime? matchDate;
  final String? competition;

  Match({
    required this.id,
    this.homeTeamName,
    this.awayTeamName,
    this.homeScore,
    this.awayScore,
    required this.status,
    this.matchDate,
    this.competition,
  });

  factory Match.fromJson(Map<String, dynamic> json) {
    return Match(
      id: json['id'],
      homeTeamName: json['home_team']?['name'] ?? json['home_team_name'],
      awayTeamName: json['away_team']?['name'] ?? json['away_team_name'],
      homeScore: json['home_score'],
      awayScore: json['away_score'],
      status: json['status'] ?? 'scheduled',
      matchDate: json['match_date'] != null ? DateTime.parse(json['match_date']) : null,
      competition: json['competition']?['name'] ?? json['competition'],
    );
  }
}

class User {
  final int id;
  final String firstName;
  final String lastName;
  final String? profilePhoto;

  User({
    required this.id,
    required this.firstName,
    required this.lastName,
    this.profilePhoto,
  });

  String get fullName => '$firstName $lastName';

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      firstName: json['first_name'] ?? '',
      lastName: json['last_name'] ?? '',
      profilePhoto: json['profile_photo'],
    );
  }
}
