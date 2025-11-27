class Player {
  final int id;
  final String firstName;
  final String lastName;
  final String? nickname;
  final int? jerseyNumber;
  final String position;
  final String? nationality;
  final DateTime? dateOfBirth;
  final String? photoUrl;
  final String? bio;
  final Map<String, dynamic>? stats;
  final bool isActive;

  Player({
    required this.id,
    required this.firstName,
    required this.lastName,
    this.nickname,
    this.jerseyNumber,
    required this.position,
    this.nationality,
    this.dateOfBirth,
    this.photoUrl,
    this.bio,
    this.stats,
    required this.isActive,
  });

  String get fullName => '$firstName $lastName';
  String get displayName => nickname ?? fullName;

  factory Player.fromJson(Map<String, dynamic> json) {
    return Player(
      id: json['id'],
      firstName: json['first_name'] ?? '',
      lastName: json['last_name'] ?? '',
      nickname: json['nickname'],
      jerseyNumber: json['jersey_number'],
      position: json['position'] ?? 'Unknown',
      nationality: json['nationality'],
      dateOfBirth: json['date_of_birth'] != null ? DateTime.parse(json['date_of_birth']) : null,
      photoUrl: json['photo_url'],
      bio: json['bio'],
      stats: json['stats'],
      isActive: json['is_active'] ?? true,
    );
  }
}

class Challenge {
  final int id;
  final String name;
  final String description;
  final String type;
  final int targetValue;
  final int rewardTokens;
  final int rewardXp;
  final DateTime startsAt;
  final DateTime endsAt;
  final bool isActive;
  final UserChallenge? userProgress;

  Challenge({
    required this.id,
    required this.name,
    required this.description,
    required this.type,
    required this.targetValue,
    required this.rewardTokens,
    required this.rewardXp,
    required this.startsAt,
    required this.endsAt,
    required this.isActive,
    this.userProgress,
  });

  factory Challenge.fromJson(Map<String, dynamic> json) {
    return Challenge(
      id: json['id'],
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      type: json['type'] ?? 'daily',
      targetValue: json['target_value'] ?? 0,
      rewardTokens: json['reward_tokens'] ?? 0,
      rewardXp: json['reward_xp'] ?? 0,
      startsAt: DateTime.parse(json['starts_at']),
      endsAt: DateTime.parse(json['ends_at']),
      isActive: json['is_active'] ?? true,
      userProgress: json['user_progress'] != null
          ? UserChallenge.fromJson(json['user_progress'])
          : null,
    );
  }
}

class UserChallenge {
  final int currentProgress;
  final int targetValue;
  final double progressPercentage;
  final bool isCompleted;
  final DateTime? completedAt;

  UserChallenge({
    required this.currentProgress,
    required this.targetValue,
    required this.progressPercentage,
    required this.isCompleted,
    this.completedAt,
  });

  factory UserChallenge.fromJson(Map<String, dynamic> json) {
    return UserChallenge(
      currentProgress: json['current_progress'] ?? 0,
      targetValue: json['target_value'] ?? 1,
      progressPercentage: json['progress_percentage'] != null
          ? double.parse(json['progress_percentage'].toString())
          : 0.0,
      isCompleted: json['is_completed'] ?? false,
      completedAt: json['completed_at'] != null
          ? DateTime.parse(json['completed_at'])
          : null,
    );
  }
}

class Partner {
  final int id;
  final String name;
  final String description;
  final String category;
  final String? logoUrl;
  final String? address;
  final double? latitude;
  final double? longitude;
  final bool isActive;
  final List<Offer>? offers;

  Partner({
    required this.id,
    required this.name,
    required this.description,
    required this.category,
    this.logoUrl,
    this.address,
    this.latitude,
    this.longitude,
    required this.isActive,
    this.offers,
  });

  factory Partner.fromJson(Map<String, dynamic> json) {
    return Partner(
      id: json['id'],
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      category: json['category'] ?? 'general',
      logoUrl: json['logo_url'],
      address: json['address'],
      latitude: json['latitude'] != null ? double.parse(json['latitude'].toString()) : null,
      longitude: json['longitude'] != null ? double.parse(json['longitude'].toString()) : null,
      isActive: json['is_active'] ?? true,
      offers: json['offers'] != null
          ? (json['offers'] as List).map((o) => Offer.fromJson(o)).toList()
          : null,
    );
  }
}

class Offer {
  final int id;
  final String title;
  final String description;
  final String? imageUrl;
  final String discountType;
  final double? discountValue;
  final DateTime? validFrom;
  final DateTime? validUntil;
  final bool isActive;

  Offer({
    required this.id,
    required this.title,
    required this.description,
    this.imageUrl,
    required this.discountType,
    this.discountValue,
    this.validFrom,
    this.validUntil,
    required this.isActive,
  });

  factory Offer.fromJson(Map<String, dynamic> json) {
    return Offer(
      id: json['id'],
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      imageUrl: json['image_url'],
      discountType: json['discount_type'] ?? 'percentage',
      discountValue: json['discount_value'] != null
          ? double.parse(json['discount_value'].toString())
          : null,
      validFrom: json['valid_from'] != null ? DateTime.parse(json['valid_from']) : null,
      validUntil: json['valid_until'] != null ? DateTime.parse(json['valid_until']) : null,
      isActive: json['is_active'] ?? true,
    );
  }
}
