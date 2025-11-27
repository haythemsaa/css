class PaymentMethod {
  final int id;
  final String name;
  final String code;
  final String type;
  final String? description;
  final String? logoUrl;
  final String? provider;
  final bool isActive;
  final bool isDefault;
  final List<String> supportedCurrencies;
  final double? minAmount;
  final double? maxAmount;
  final double transactionFee;
  final double transactionFeePercentage;
  final String? processingTime;
  final bool supportsRefund;
  final String? instructions;
  final List<String> availableFor;

  PaymentMethod({
    required this.id,
    required this.name,
    required this.code,
    required this.type,
    this.description,
    this.logoUrl,
    this.provider,
    required this.isActive,
    required this.isDefault,
    required this.supportedCurrencies,
    this.minAmount,
    this.maxAmount,
    required this.transactionFee,
    required this.transactionFeePercentage,
    this.processingTime,
    required this.supportsRefund,
    this.instructions,
    required this.availableFor,
  });

  factory PaymentMethod.fromJson(Map<String, dynamic> json) {
    return PaymentMethod(
      id: json['id'],
      name: json['name'],
      code: json['code'],
      type: json['type'],
      description: json['description'],
      logoUrl: json['logo_url'],
      provider: json['provider'],
      isActive: json['is_active'],
      isDefault: json['is_default'],
      supportedCurrencies: List<String>.from(json['supported_currencies'] ?? []),
      minAmount: json['min_amount'] != null
          ? double.parse(json['min_amount'].toString())
          : null,
      maxAmount: json['max_amount'] != null
          ? double.parse(json['max_amount'].toString())
          : null,
      transactionFee: double.parse(json['transaction_fee'].toString()),
      transactionFeePercentage: double.parse(json['transaction_fee_percentage'].toString()),
      processingTime: json['processing_time'],
      supportsRefund: json['supports_refund'],
      instructions: json['instructions'],
      availableFor: List<String>.from(json['available_for'] ?? []),
    );
  }

  String get typeDisplay {
    switch (type) {
      case 'mobile_wallet': return 'Portefeuille Mobile';
      case 'bank_card': return 'Carte Bancaire';
      case 'bank_transfer': return 'Virement Bancaire';
      case 'cash': return 'Espèces';
      case 'international': return 'Transfert International';
      default: return type;
    }
  }

  bool canProcessAmount(double amount) {
    if (minAmount != null && amount < minAmount!) return false;
    if (maxAmount != null && amount > maxAmount!) return false;
    return true;
  }

  Map<String, double> calculateFees(double amount) {
    final percentageFee = (amount * transactionFeePercentage) / 100;
    final totalFee = transactionFee + percentageFee;
    final netAmount = amount - totalFee;

    return {
      'amount': amount,
      'fixed_fee': transactionFee,
      'percentage_fee': percentageFee,
      'total_fee': totalFee,
      'net_amount': netAmount,
    };
  }
}
