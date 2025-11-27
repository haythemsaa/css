class Badge {
  final int id;
  final String name;
  final String? description;
  final String category;
  final String? icon;
  final String? imageUrl;
  final String rarity;
  final int tokenReward;
  final int xpReward;
  final bool isSecret;
  final bool isActive;
  final Map<String, dynamic>? unlockCriteria;
  final String unlockType;
  final int requiredCount;
  final int displayOrder;
  final String categoryDisplay;
  final String rarityDisplay;
  final String unlockTypeDisplay;
  final BadgeStatistic? statistics;
  final UserBadgeProgress? userProgress;

  Badge({
    required this.id,
    required this.name,
    this.description,
    required this.category,
    this.icon,
    this.imageUrl,
    required this.rarity,
    required this.tokenReward,
    required this.xpReward,
    required this.isSecret,
    required this.isActive,
    this.unlockCriteria,
    required this.unlockType,
    required this.requiredCount,
    required this.displayOrder,
    required this.categoryDisplay,
    required this.rarityDisplay,
    required this.unlockTypeDisplay,
    this.statistics,
    this.userProgress,
  });

  factory Badge.fromJson(Map<String, dynamic> json) {
    return Badge(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      category: json['category'],
      icon: json['icon'],
      imageUrl: json['image_url'],
      rarity: json['rarity'],
      tokenReward: json['token_reward'],
      xpReward: json['xp_reward'],
      isSecret: json['is_secret'] ?? false,
      isActive: json['is_active'] ?? true,
      unlockCriteria: json['unlock_criteria'],
      unlockType: json['unlock_type'],
      requiredCount: json['required_count'] ?? 1,
      displayOrder: json['display_order'] ?? 0,
      categoryDisplay: json['category_display'] ?? '',
      rarityDisplay: json['rarity_display'] ?? '',
      unlockTypeDisplay: json['unlock_type_display'] ?? '',
      statistics: json['statistics'] != null
          ? BadgeStatistic.fromJson(json['statistics'])
          : null,
      userProgress: json['user_progress'] != null
          ? UserBadgeProgress.fromJson(json['user_progress'])
          : null,
    );
  }
}

class BadgeStatistic {
  final int id;
  final int badgeId;
  final int totalUnlocked;
  final int totalInProgress;
  final double unlockPercentage;
  final DateTime? firstUnlockedAt;
  final DateTime? lastUnlockedAt;

  BadgeStatistic({
    required this.id,
    required this.badgeId,
    required this.totalUnlocked,
    required this.totalInProgress,
    required this.unlockPercentage,
    this.firstUnlockedAt,
    this.lastUnlockedAt,
  });

  factory BadgeStatistic.fromJson(Map<String, dynamic> json) {
    return BadgeStatistic(
      id: json['id'],
      badgeId: json['badge_id'],
      totalUnlocked: json['total_unlocked'] ?? 0,
      totalInProgress: json['total_in_progress'] ?? 0,
      unlockPercentage: json['unlock_percentage'] != null
          ? double.parse(json['unlock_percentage'].toString())
          : 0.0,
      firstUnlockedAt: json['first_unlocked_at'] != null
          ? DateTime.parse(json['first_unlocked_at'])
          : null,
      lastUnlockedAt: json['last_unlocked_at'] != null
          ? DateTime.parse(json['last_unlocked_at'])
          : null,
    );
  }
}

class UserBadgeProgress {
  final int progress;
  final int progressMax;
  final double progressPercentage;
  final bool isUnlocked;
  final DateTime? unlockedAt;
  final int? tokensEarned;
  final int? xpEarned;

  UserBadgeProgress({
    required this.progress,
    required this.progressMax,
    required this.progressPercentage,
    required this.isUnlocked,
    this.unlockedAt,
    this.tokensEarned,
    this.xpEarned,
  });

  factory UserBadgeProgress.fromJson(Map<String, dynamic> json) {
    return UserBadgeProgress(
      progress: json['progress'] ?? 0,
      progressMax: json['progress_max'] ?? 1,
      progressPercentage: json['progress_percentage'] != null
          ? double.parse(json['progress_percentage'].toString())
          : 0.0,
      isUnlocked: json['is_unlocked'] ?? false,
      unlockedAt: json['unlocked_at'] != null
          ? DateTime.parse(json['unlocked_at'])
          : null,
      tokensEarned: json['tokens_earned'],
      xpEarned: json['xp_earned'],
    );
  }
}

class UserBadgeSummary {
  final int id;
  final int userId;
  final int totalBadges;
  final int commonBadges;
  final int rareBadges;
  final int epicBadges;
  final int legendaryBadges;
  final int totalTokensFromBadges;
  final int totalXpFromBadges;
  final double completionPercentage;

  UserBadgeSummary({
    required this.id,
    required this.userId,
    required this.totalBadges,
    required this.commonBadges,
    required this.rareBadges,
    required this.epicBadges,
    required this.legendaryBadges,
    required this.totalTokensFromBadges,
    required this.totalXpFromBadges,
    required this.completionPercentage,
  });

  factory UserBadgeSummary.fromJson(Map<String, dynamic> json) {
    return UserBadgeSummary(
      id: json['id'],
      userId: json['user_id'],
      totalBadges: json['total_badges'] ?? 0,
      commonBadges: json['common_badges'] ?? 0,
      rareBadges: json['rare_badges'] ?? 0,
      epicBadges: json['epic_badges'] ?? 0,
      legendaryBadges: json['legendary_badges'] ?? 0,
      totalTokensFromBadges: json['total_tokens_from_badges'] ?? 0,
      totalXpFromBadges: json['total_xp_from_badges'] ?? 0,
      completionPercentage: json['completion_percentage'] != null
          ? double.parse(json['completion_percentage'].toString())
          : 0.0,
    );
  }
}

class UserBadge {
  final int id;
  final int userId;
  final int badgeId;
  final int progress;
  final int progressMax;
  final bool isUnlocked;
  final DateTime? unlockedAt;
  final int tokensEarned;
  final int xpEarned;
  final double progressPercentage;
  final Badge? badge;

  UserBadge({
    required this.id,
    required this.userId,
    required this.badgeId,
    required this.progress,
    required this.progressMax,
    required this.isUnlocked,
    this.unlockedAt,
    required this.tokensEarned,
    required this.xpEarned,
    required this.progressPercentage,
    this.badge,
  });

  factory UserBadge.fromJson(Map<String, dynamic> json) {
    return UserBadge(
      id: json['id'],
      userId: json['user_id'],
      badgeId: json['badge_id'],
      progress: json['progress'] ?? 0,
      progressMax: json['progress_max'] ?? 1,
      isUnlocked: json['is_unlocked'] ?? false,
      unlockedAt: json['unlocked_at'] != null
          ? DateTime.parse(json['unlocked_at'])
          : null,
      tokensEarned: json['tokens_earned'] ?? 0,
      xpEarned: json['xp_earned'] ?? 0,
      progressPercentage: json['progress_percentage'] != null
          ? double.parse(json['progress_percentage'].toString())
          : 0.0,
      badge: json['badge'] != null ? Badge.fromJson(json['badge']) : null,
    );
  }
}

class BadgeLeaderboardEntry {
  final int id;
  final String firstName;
  final String lastName;
  final String? profilePhoto;
  final int totalBadges;
  final int legendaryBadges;
  final int epicBadges;
  final int rareBadges;
  final int commonBadges;
  final double completionPercentage;
  final int rank;

  BadgeLeaderboardEntry({
    required this.id,
    required this.firstName,
    required this.lastName,
    this.profilePhoto,
    required this.totalBadges,
    required this.legendaryBadges,
    required this.epicBadges,
    required this.rareBadges,
    required this.commonBadges,
    required this.completionPercentage,
    required this.rank,
  });

  String get fullName => '$firstName $lastName';

  factory BadgeLeaderboardEntry.fromJson(Map<String, dynamic> json) {
    return BadgeLeaderboardEntry(
      id: json['id'],
      firstName: json['first_name'] ?? '',
      lastName: json['last_name'] ?? '',
      profilePhoto: json['profile_photo'],
      totalBadges: json['total_badges'] ?? 0,
      legendaryBadges: json['legendary_badges'] ?? 0,
      epicBadges: json['epic_badges'] ?? 0,
      rareBadges: json['rare_badges'] ?? 0,
      commonBadges: json['common_badges'] ?? 0,
      completionPercentage: json['completion_percentage'] != null
          ? double.parse(json['completion_percentage'].toString())
          : 0.0,
      rank: json['rank'] ?? 0,
    );
  }
}
