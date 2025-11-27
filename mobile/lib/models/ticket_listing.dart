class TicketListing {
  final int id;
  final int sellerId;
  final int matchId;
  final String ticketNumber;
  final String category;
  final String? section;
  final String? row;
  final String? seatNumber;
  final int quantity;
  final double originalPrice;
  final double sellingPrice;
  final double platformFeePercentage;
  final String status;
  final bool isVerified;
  final DateTime? verifiedAt;
  final int? verifiedBy;
  final String? description;
  final List<String> images;
  final bool isFeatured;
  final bool allowNegotiation;
  final double? minimumPrice;
  final int? reservedBy;
  final DateTime? reservedUntil;
  final DateTime? listedAt;
  final DateTime? soldAt;
  final DateTime createdAt;

  // Relationships
  final User? seller;
  final Match? match;
  final TicketTransaction? transaction;

  // Computed
  final bool isAvailable;
  final bool isReserved;
  final double platformFee;
  final double sellerAmount;
  final String categoryDisplay;
  final String statusDisplay;

  TicketListing({
    required this.id,
    required this.sellerId,
    required this.matchId,
    required this.ticketNumber,
    required this.category,
    this.section,
    this.row,
    this.seatNumber,
    required this.quantity,
    required this.originalPrice,
    required this.sellingPrice,
    required this.platformFeePercentage,
    required this.status,
    required this.isVerified,
    this.verifiedAt,
    this.verifiedBy,
    this.description,
    this.images = const [],
    required this.isFeatured,
    required this.allowNegotiation,
    this.minimumPrice,
    this.reservedBy,
    this.reservedUntil,
    this.listedAt,
    this.soldAt,
    required this.createdAt,
    this.seller,
    this.match,
    this.transaction,
    required this.isAvailable,
    required this.isReserved,
    required this.platformFee,
    required this.sellerAmount,
    required this.categoryDisplay,
    required this.statusDisplay,
  });

  factory TicketListing.fromJson(Map<String, dynamic> json) {
    return TicketListing(
      id: json['id'],
      sellerId: json['seller_id'],
      matchId: json['match_id'],
      ticketNumber: json['ticket_number'],
      category: json['category'],
      section: json['section'],
      row: json['row'],
      seatNumber: json['seat_number'],
      quantity: json['quantity'],
      originalPrice: double.parse(json['original_price'].toString()),
      sellingPrice: double.parse(json['selling_price'].toString()),
      platformFeePercentage: double.parse(json['platform_fee_percentage'].toString()),
      status: json['status'],
      isVerified: json['is_verified'] ?? false,
      verifiedAt: json['verified_at'] != null ? DateTime.parse(json['verified_at']) : null,
      verifiedBy: json['verified_by'],
      description: json['description'],
      images: json['images'] != null ? List<String>.from(json['images']) : [],
      isFeatured: json['is_featured'] ?? false,
      allowNegotiation: json['allow_negotiation'] ?? false,
      minimumPrice: json['minimum_price'] != null ? double.parse(json['minimum_price'].toString()) : null,
      reservedBy: json['reserved_by'],
      reservedUntil: json['reserved_until'] != null ? DateTime.parse(json['reserved_until']) : null,
      listedAt: json['listed_at'] != null ? DateTime.parse(json['listed_at']) : null,
      soldAt: json['sold_at'] != null ? DateTime.parse(json['sold_at']) : null,
      createdAt: DateTime.parse(json['created_at']),
      seller: json['seller'] != null ? User.fromJson(json['seller']) : null,
      match: json['match'] != null ? Match.fromJson(json['match']) : null,
      transaction: json['transaction'] != null ? TicketTransaction.fromJson(json['transaction']) : null,
      isAvailable: json['is_available'] ?? false,
      isReserved: json['is_reserved'] ?? false,
      platformFee: json['platform_fee'] != null ? double.parse(json['platform_fee'].toString()) : 0,
      sellerAmount: json['seller_amount'] != null ? double.parse(json['seller_amount'].toString()) : 0,
      categoryDisplay: json['category_display'] ?? '',
      statusDisplay: json['status_display'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'seller_id': sellerId,
      'match_id': matchId,
      'ticket_number': ticketNumber,
      'category': category,
      'section': section,
      'row': row,
      'seat_number': seatNumber,
      'quantity': quantity,
      'original_price': originalPrice,
      'selling_price': sellingPrice,
      'platform_fee_percentage': platformFeePercentage,
      'status': status,
      'is_verified': isVerified,
      'description': description,
      'images': images,
      'is_featured': isFeatured,
      'allow_negotiation': allowNegotiation,
      'minimum_price': minimumPrice,
    };
  }
}

class TicketTransaction {
  final int id;
  final int listingId;
  final int buyerId;
  final int sellerId;
  final String transactionNumber;
  final double ticketPrice;
  final double platformFee;
  final double totalAmount;
  final String paymentStatus;
  final String? paymentMethod;
  final String? paymentReference;
  final DateTime? paidAt;
  final String transferStatus;
  final DateTime? transferredAt;
  final String? transferNotes;
  final String deliveryMethod;
  final String? meetingDetails;
  final DateTime? meetingScheduledAt;
  final bool buyerReviewed;
  final bool sellerReviewed;
  final DateTime createdAt;

  // Relationships
  final TicketListing? listing;
  final User? buyer;
  final User? seller;

  // Computed
  final bool isCompleted;
  final bool canReview;
  final String deliveryMethodDisplay;

  TicketTransaction({
    required this.id,
    required this.listingId,
    required this.buyerId,
    required this.sellerId,
    required this.transactionNumber,
    required this.ticketPrice,
    required this.platformFee,
    required this.totalAmount,
    required this.paymentStatus,
    this.paymentMethod,
    this.paymentReference,
    this.paidAt,
    required this.transferStatus,
    this.transferredAt,
    this.transferNotes,
    required this.deliveryMethod,
    this.meetingDetails,
    this.meetingScheduledAt,
    required this.buyerReviewed,
    required this.sellerReviewed,
    required this.createdAt,
    this.listing,
    this.buyer,
    this.seller,
    required this.isCompleted,
    required this.canReview,
    required this.deliveryMethodDisplay,
  });

  factory TicketTransaction.fromJson(Map<String, dynamic> json) {
    return TicketTransaction(
      id: json['id'],
      listingId: json['listing_id'],
      buyerId: json['buyer_id'],
      sellerId: json['seller_id'],
      transactionNumber: json['transaction_number'],
      ticketPrice: double.parse(json['ticket_price'].toString()),
      platformFee: double.parse(json['platform_fee'].toString()),
      totalAmount: double.parse(json['total_amount'].toString()),
      paymentStatus: json['payment_status'],
      paymentMethod: json['payment_method'],
      paymentReference: json['payment_reference'],
      paidAt: json['paid_at'] != null ? DateTime.parse(json['paid_at']) : null,
      transferStatus: json['transfer_status'],
      transferredAt: json['transferred_at'] != null ? DateTime.parse(json['transferred_at']) : null,
      transferNotes: json['transfer_notes'],
      deliveryMethod: json['delivery_method'],
      meetingDetails: json['meeting_details'],
      meetingScheduledAt: json['meeting_scheduled_at'] != null ? DateTime.parse(json['meeting_scheduled_at']) : null,
      buyerReviewed: json['buyer_reviewed'] ?? false,
      sellerReviewed: json['seller_reviewed'] ?? false,
      createdAt: DateTime.parse(json['created_at']),
      listing: json['listing'] != null ? TicketListing.fromJson(json['listing']) : null,
      buyer: json['buyer'] != null ? User.fromJson(json['buyer']) : null,
      seller: json['seller'] != null ? User.fromJson(json['seller']) : null,
      isCompleted: json['is_completed'] ?? false,
      canReview: json['can_review'] ?? false,
      deliveryMethodDisplay: json['delivery_method_display'] ?? '',
    );
  }
}

class MarketplaceReview {
  final int id;
  final int transactionId;
  final int reviewerId;
  final int reviewedUserId;
  final String role;
  final int rating;
  final String? comment;
  final bool wouldTradeAgain;
  final String? response;
  final DateTime? respondedAt;
  final DateTime createdAt;

  // Relationships
  final User? reviewer;
  final User? reviewedUser;

  MarketplaceReview({
    required this.id,
    required this.transactionId,
    required this.reviewerId,
    required this.reviewedUserId,
    required this.role,
    required this.rating,
    this.comment,
    required this.wouldTradeAgain,
    this.response,
    this.respondedAt,
    required this.createdAt,
    this.reviewer,
    this.reviewedUser,
  });

  factory MarketplaceReview.fromJson(Map<String, dynamic> json) {
    return MarketplaceReview(
      id: json['id'],
      transactionId: json['transaction_id'],
      reviewerId: json['reviewer_id'],
      reviewedUserId: json['reviewed_user_id'],
      role: json['role'],
      rating: json['rating'],
      comment: json['comment'],
      wouldTradeAgain: json['would_trade_again'] ?? true,
      response: json['response'],
      respondedAt: json['responded_at'] != null ? DateTime.parse(json['responded_at']) : null,
      createdAt: DateTime.parse(json['created_at']),
      reviewer: json['reviewer'] != null ? User.fromJson(json['reviewer']) : null,
      reviewedUser: json['reviewed_user'] != null ? User.fromJson(json['reviewed_user']) : null,
    );
  }
}

// Simple placeholder classes - these should match your existing models
class User {
  final int id;
  final String name;
  final String? avatar;

  User({required this.id, required this.name, this.avatar});

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      avatar: json['avatar'],
    );
  }
}

class Match {
  final int id;
  final String homeTeam;
  final String awayTeam;
  final DateTime matchDate;
  final String venue;

  Match({
    required this.id,
    required this.homeTeam,
    required this.awayTeam,
    required this.matchDate,
    required this.venue,
  });

  factory Match.fromJson(Map<String, dynamic> json) {
    return Match(
      id: json['id'],
      homeTeam: json['home_team'],
      awayTeam: json['away_team'],
      matchDate: DateTime.parse(json['match_date']),
      venue: json['venue'],
    );
  }
}

class MarketplaceStats {
  final double averageRating;
  final int totalReviews;
  final int positiveReviews;
  final int negativeReviews;

  MarketplaceStats({
    required this.averageRating,
    required this.totalReviews,
    required this.positiveReviews,
    required this.negativeReviews,
  });

  factory MarketplaceStats.fromJson(Map<String, dynamic> json) {
    return MarketplaceStats(
      averageRating: json['average_rating'] != null ? double.parse(json['average_rating'].toString()) : 0,
      totalReviews: json['total_reviews'] ?? 0,
      positiveReviews: json['positive_reviews'] ?? 0,
      negativeReviews: json['negative_reviews'] ?? 0,
    );
  }
}
