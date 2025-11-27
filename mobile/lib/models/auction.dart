class AuctionProduct {
  final int id;
  final String title;
  final String slug;
  final String description;
  final List<String> images;
  final String category;
  final double startingPrice;
  final double? reservePrice;
  final double currentBid;
  final double? buyNowPrice;
  final int bidIncrement;
  final int totalBids;
  final DateTime startTime;
  final DateTime endTime;
  final int? timeRemaining;
  final bool isActive;
  final bool reserveMet;
  final bool isFeatured;
  final String status;
  final String? termsConditions;
  final Map<String, dynamic>? metadata;

  AuctionProduct({
    required this.id,
    required this.title,
    required this.slug,
    required this.description,
    required this.images,
    required this.category,
    required this.startingPrice,
    this.reservePrice,
    required this.currentBid,
    this.buyNowPrice,
    required this.bidIncrement,
    required this.totalBids,
    required this.startTime,
    required this.endTime,
    this.timeRemaining,
    required this.isActive,
    required this.reserveMet,
    required this.isFeatured,
    required this.status,
    this.termsConditions,
    this.metadata,
  });

  factory AuctionProduct.fromJson(Map<String, dynamic> json) {
    return AuctionProduct(
      id: json['id'],
      title: json['title'],
      slug: json['slug'],
      description: json['description'],
      images: List<String>.from(json['images'] ?? []),
      category: json['category'],
      startingPrice: double.parse(json['starting_price'].toString()),
      reservePrice: json['reserve_price'] != null
          ? double.parse(json['reserve_price'].toString())
          : null,
      currentBid: double.parse(json['current_bid'].toString()),
      buyNowPrice: json['buy_now_price'] != null
          ? double.parse(json['buy_now_price'].toString())
          : null,
      bidIncrement: json['bid_increment'],
      totalBids: json['total_bids'],
      startTime: DateTime.parse(json['start_time']),
      endTime: DateTime.parse(json['end_time']),
      timeRemaining: json['time_remaining'],
      isActive: json['is_active'],
      reserveMet: json['reserve_met'],
      isFeatured: json['is_featured'],
      status: json['status'],
      termsConditions: json['terms_conditions'],
      metadata: json['metadata'],
    );
  }

  double get minimumBid => currentBid > 0
      ? currentBid + bidIncrement
      : startingPrice;

  String get categoryDisplay {
    switch (category) {
      case 'collectibles': return 'Objets de Collection';
      case 'memorabilia': return 'Souvenirs';
      case 'experiences': return 'Expériences';
      case 'signed_items': return 'Articles Dédicacés';
      default: return category;
    }
  }
}

class AuctionBid {
  final int id;
  final int auctionProductId;
  final double bidAmount;
  final bool isWinning;
  final bool wasOutbid;
  final DateTime? outbidAt;
  final DateTime createdAt;

  AuctionBid({
    required this.id,
    required this.auctionProductId,
    required this.bidAmount,
    required this.isWinning,
    required this.wasOutbid,
    this.outbidAt,
    required this.createdAt,
  });

  factory AuctionBid.fromJson(Map<String, dynamic> json) {
    return AuctionBid(
      id: json['id'],
      auctionProductId: json['auction_product_id'],
      bidAmount: double.parse(json['bid_amount'].toString()),
      isWinning: json['is_winning'],
      wasOutbid: json['was_outbid'],
      outbidAt: json['outbid_at'] != null
          ? DateTime.parse(json['outbid_at'])
          : null,
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}
