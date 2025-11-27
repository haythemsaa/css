class DonationGoal {
  final int id;
  final String title;
  final String slug;
  final String description;
  final String? fullDetails;
  final String category;
  final double targetAmount;
  final double currentAmount;
  final double remainingAmount;
  final double progressPercentage;
  final int donorsCount;
  final double minDonation;
  final String priority;
  final DateTime startDate;
  final DateTime? endDate;
  final int? daysRemaining;
  final String status;
  final bool isCompleted;
  final bool isFeatured;
  final String? featuredImage;
  final List<String>? galleryImages;
  final List<dynamic>? milestoneUpdates;
  final String? impactMetrics;
  final String? thankYouMessage;
  final List<DonationGoalMilestone>? milestones;

  DonationGoal({
    required this.id,
    required this.title,
    required this.slug,
    required this.description,
    this.fullDetails,
    required this.category,
    required this.targetAmount,
    required this.currentAmount,
    required this.remainingAmount,
    required this.progressPercentage,
    required this.donorsCount,
    required this.minDonation,
    required this.priority,
    required this.startDate,
    this.endDate,
    this.daysRemaining,
    required this.status,
    required this.isCompleted,
    required this.isFeatured,
    this.featuredImage,
    this.galleryImages,
    this.milestoneUpdates,
    this.impactMetrics,
    this.thankYouMessage,
    this.milestones,
  });

  factory DonationGoal.fromJson(Map<String, dynamic> json) {
    return DonationGoal(
      id: json['id'],
      title: json['title'],
      slug: json['slug'],
      description: json['description'],
      fullDetails: json['full_details'],
      category: json['category'],
      targetAmount: double.parse(json['target_amount'].toString()),
      currentAmount: double.parse(json['current_amount'].toString()),
      remainingAmount: double.parse(json['remaining_amount'].toString()),
      progressPercentage: double.parse(json['progress_percentage'].toString()),
      donorsCount: json['donors_count'],
      minDonation: double.parse(json['min_donation'].toString()),
      priority: json['priority'],
      startDate: DateTime.parse(json['start_date']),
      endDate: json['end_date'] != null
          ? DateTime.parse(json['end_date'])
          : null,
      daysRemaining: json['days_remaining'],
      status: json['status'],
      isCompleted: json['is_completed'],
      isFeatured: json['is_featured'],
      featuredImage: json['featured_image'],
      galleryImages: json['gallery_images'] != null
          ? List<String>.from(json['gallery_images'])
          : null,
      milestoneUpdates: json['milestone_updates'],
      impactMetrics: json['impact_metrics'],
      thankYouMessage: json['thank_you_message'],
      milestones: json['milestones'] != null
          ? (json['milestones'] as List)
              .map((m) => DonationGoalMilestone.fromJson(m))
              .toList()
          : null,
    );
  }

  String get categoryDisplay {
    switch (category) {
      case 'litigation': return 'Paiement de Litiges';
      case 'player_transfer': return 'Achat de Joueurs';
      case 'stadium_renovation': return 'Rénovation du Stade';
      case 'youth_academy': return 'Académie des Jeunes';
      case 'equipment': return 'Équipements';
      case 'debt_payment': return 'Remboursement de Dettes';
      default: return 'Autre';
    }
  }

  String get priorityDisplay {
    switch (priority) {
      case 'urgent': return 'Urgent';
      case 'high': return 'Élevé';
      case 'medium': return 'Moyen';
      case 'low': return 'Faible';
      default: return priority;
    }
  }
}

class DonationGoalMilestone {
  final int id;
  final String title;
  final String? description;
  final double targetAmount;
  final int percentage;
  final bool isAchieved;
  final DateTime? achievedAt;

  DonationGoalMilestone({
    required this.id,
    required this.title,
    this.description,
    required this.targetAmount,
    required this.percentage,
    required this.isAchieved,
    this.achievedAt,
  });

  factory DonationGoalMilestone.fromJson(Map<String, dynamic> json) {
    return DonationGoalMilestone(
      id: json['id'],
      title: json['title'],
      description: json['description'],
      targetAmount: double.parse(json['target_amount'].toString()),
      percentage: json['percentage'],
      isAchieved: json['is_achieved'],
      achievedAt: json['achieved_at'] != null
          ? DateTime.parse(json['achieved_at'])
          : null,
    );
  }
}
