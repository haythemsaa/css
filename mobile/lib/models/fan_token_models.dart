class FanTokenWallet {
  final int userId;
  final double balance;
  final double lifetimeEarned;
  final double lifetimeSpent;
  final int level;
  final String levelName;
  final int experiencePoints;
  final double progressToNextLevel;
  final bool canClaimDailyBonus;
  final DateTime? lastDailyBonusAt;

  FanTokenWallet({
    required this.userId,
    required this.balance,
    required this.lifetimeEarned,
    required this.lifetimeSpent,
    required this.level,
    required this.levelName,
    required this.experiencePoints,
    required this.progressToNextLevel,
    required this.canClaimDailyBonus,
    this.lastDailyBonusAt,
  });

  factory FanTokenWallet.fromJson(Map<String, dynamic> json) {
    return FanTokenWallet(
      userId: json['user_id'] ?? 0,
      balance: double.parse(json['balance'].toString()),
      lifetimeEarned: double.parse(json['lifetime_earned'].toString()),
      lifetimeSpent: double.parse(json['lifetime_spent'].toString()),
      level: json['level'] ?? 1,
      levelName: json['level_name'] ?? 'Débutant',
      experiencePoints: json['experience_points'] ?? 0,
      progressToNextLevel: json['progress_to_next_level'] != null
          ? double.parse(json['progress_to_next_level'].toString())
          : 0.0,
      canClaimDailyBonus: json['can_claim_daily_bonus'] ?? false,
      lastDailyBonusAt: json['last_daily_bonus_at'] != null
          ? DateTime.parse(json['last_daily_bonus_at'])
          : null,
    );
  }
}

class FanTokenTransaction {
  final int id;
  final int userId;
  final String transactionNumber;
  final String type;
  final double amount;
  final double balanceAfter;
  final String? description;
  final String? sourceType;
  final int? sourceId;
  final DateTime createdAt;
  final String typeDisplay;
  final bool isPositive;

  FanTokenTransaction({
    required this.id,
    required this.userId,
    required this.transactionNumber,
    required this.type,
    required this.amount,
    required this.balanceAfter,
    this.description,
    this.sourceType,
    this.sourceId,
    required this.createdAt,
    required this.typeDisplay,
    required this.isPositive,
  });

  factory FanTokenTransaction.fromJson(Map<String, dynamic> json) {
    return FanTokenTransaction(
      id: json['id'],
      userId: json['user_id'],
      transactionNumber: json['transaction_number'],
      type: json['type'],
      amount: double.parse(json['amount'].toString()),
      balanceAfter: double.parse(json['balance_after'].toString()),
      description: json['description'],
      sourceType: json['source_type'],
      sourceId: json['source_id'],
      createdAt: DateTime.parse(json['created_at']),
      typeDisplay: json['type_display'] ?? '',
      isPositive: json['is_positive'] ?? false,
    );
  }
}

class Reward {
  final int id;
  final String name;
  final String? description;
  final String category;
  final int tokenCost;
  final double? monetaryValue;
  final int? stockRemaining;
  final bool isActive;
  final bool isFeatured;
  final int minimumLevel;
  final List<String> images;
  final DateTime? validFrom;
  final DateTime? validUntil;
  final int? maxPerUser;
  final int totalRedeemed;
  final int popularityScore;
  final String categoryDisplay;
  final bool isAvailable;
  final double? discountPercentage;
  final bool canRedeem;

  Reward({
    required this.id,
    required this.name,
    this.description,
    required this.category,
    required this.tokenCost,
    this.monetaryValue,
    this.stockRemaining,
    required this.isActive,
    required this.isFeatured,
    required this.minimumLevel,
    this.images = const [],
    this.validFrom,
    this.validUntil,
    this.maxPerUser,
    required this.totalRedeemed,
    required this.popularityScore,
    required this.categoryDisplay,
    required this.isAvailable,
    this.discountPercentage,
    this.canRedeem = false,
  });

  factory Reward.fromJson(Map<String, dynamic> json) {
    return Reward(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      category: json['category'],
      tokenCost: json['token_cost'],
      monetaryValue: json['monetary_value'] != null
          ? double.parse(json['monetary_value'].toString())
          : null,
      stockRemaining: json['stock_remaining'],
      isActive: json['is_active'] ?? false,
      isFeatured: json['is_featured'] ?? false,
      minimumLevel: json['minimum_level'] ?? 1,
      images: json['images'] != null ? List<String>.from(json['images']) : [],
      validFrom: json['valid_from'] != null ? DateTime.parse(json['valid_from']) : null,
      validUntil: json['valid_until'] != null ? DateTime.parse(json['valid_until']) : null,
      maxPerUser: json['max_per_user'],
      totalRedeemed: json['total_redeemed'] ?? 0,
      popularityScore: json['popularity_score'] ?? 0,
      categoryDisplay: json['category_display'] ?? '',
      isAvailable: json['is_available'] ?? false,
      discountPercentage: json['discount_percentage'] != null
          ? double.parse(json['discount_percentage'].toString())
          : null,
      canRedeem: json['can_redeem'] ?? false,
    );
  }
}

class RewardRedemption {
  final int id;
  final int userId;
  final int rewardId;
  final String redemptionCode;
  final int tokensSpent;
  final String status;
  final DateTime? fulfilledAt;
  final DateTime? expiresAt;
  final String? deliveryMethod;
  final DateTime createdAt;
  final String statusDisplay;
  final bool isActive;
  final Reward? reward;

  RewardRedemption({
    required this.id,
    required this.userId,
    required this.rewardId,
    required this.redemptionCode,
    required this.tokensSpent,
    required this.status,
    this.fulfilledAt,
    this.expiresAt,
    this.deliveryMethod,
    required this.createdAt,
    required this.statusDisplay,
    required this.isActive,
    this.reward,
  });

  factory RewardRedemption.fromJson(Map<String, dynamic> json) {
    return RewardRedemption(
      id: json['id'],
      userId: json['user_id'],
      rewardId: json['reward_id'],
      redemptionCode: json['redemption_code'],
      tokensSpent: json['tokens_spent'],
      status: json['status'],
      fulfilledAt: json['fulfilled_at'] != null ? DateTime.parse(json['fulfilled_at']) : null,
      expiresAt: json['expires_at'] != null ? DateTime.parse(json['expires_at']) : null,
      deliveryMethod: json['delivery_method'],
      createdAt: DateTime.parse(json['created_at']),
      statusDisplay: json['status_display'] ?? '',
      isActive: json['is_active'] ?? false,
      reward: json['reward'] != null ? Reward.fromJson(json['reward']) : null,
    );
  }
}

class LeaderboardEntry {
  final int id;
  final String name;
  final String? avatar;
  final double balance;
  final int level;
  final double lifetimeEarned;
  final int rank;

  LeaderboardEntry({
    required this.id,
    required this.name,
    this.avatar,
    required this.balance,
    required this.level,
    required this.lifetimeEarned,
    required this.rank,
  });

  factory LeaderboardEntry.fromJson(Map<String, dynamic> json) {
    return LeaderboardEntry(
      id: json['id'],
      name: json['name'],
      avatar: json['avatar'],
      balance: double.parse(json['balance'].toString()),
      level: json['level'],
      lifetimeEarned: double.parse(json['lifetime_earned'].toString()),
      rank: json['rank'],
    );
  }
}
