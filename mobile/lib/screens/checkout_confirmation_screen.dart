import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/payment_method.dart';
import '../services/api_service.dart';

import '../theme/juventus_theme.dart';
class CheckoutConfirmationScreen extends StatefulWidget {
  final String transactionType; // 'donation', 'auction', 'product', 'ticket'
  final int itemId;
  final String itemTitle;
  final double amount;
  final PaymentMethod paymentMethod;
  final Map<String, dynamic>? additionalData;

  const CheckoutConfirmationScreen({
    super.key,
    required this.transactionType,
    required this.itemId,
    required this.itemTitle,
    required this.amount,
    required this.paymentMethod,
    this.additionalData,
  });

  @override
  State<CheckoutConfirmationScreen> createState() => _CheckoutConfirmationScreenState();
}

class _CheckoutConfirmationScreenState extends State<CheckoutConfirmationScreen> {
  final ApiService _apiService = ApiService();
  bool _isProcessing = false;
  bool _termsAccepted = false;

  Future<void> _confirmPayment() async {
    if (!_termsAccepted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Veuillez accepter les conditions générales'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() => _isProcessing = true);

    try {
      Map<String, dynamic> paymentData = {
        'transaction_type': widget.transactionType,
        'item_id': widget.itemId,
        'amount': widget.amount,
        'payment_method_id': widget.paymentMethod.id,
        ...?widget.additionalData,
      };

      // Call appropriate API based on transaction type
      dynamic response;
      switch (widget.transactionType) {
        case 'donation':
          response = await _apiService.donateToGoal(widget.itemId, paymentData);
          break;
        case 'auction':
          response = await _apiService.buyNow(widget.itemId);
          break;
        default:
          throw Exception('Type de transaction non supporté');
      }

      if (mounted) {
        // Show success and navigate back
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Paiement effectué avec succès!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 3),
          ),
        );

        // Navigate back to root and show success screen
        Navigator.of(context).popUntil((route) => route.isFirst);

        // Optionally navigate to a success/receipt screen
        _showSuccessDialog();
      }
    } catch (e) {
      setState(() => _isProcessing = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Erreur lors du paiement: $e'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  void _showSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: Row(
          children: [
            Icon(Icons.check_circle, color: JuventusTheme.success, size: 32),
            const SizedBox(width: 12),
            const Text('Paiement Réussi'),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text(
              'Votre paiement a été traité avec succès.',
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),
            Text(
              'Vous recevrez une confirmation par email.',
              style: TextStyle(fontSize: 12, color: JuventusTheme.grey600[600]),
              textAlign: TextAlign.center,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('OK'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormat = NumberFormat.currency(locale: 'fr_TN', symbol: 'TND');
    final fees = widget.paymentMethod.calculateFees(widget.amount);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Confirmation de paiement'),
        backgroundColor: JuventusTheme.primaryBlack,
      ),
      body: _isProcessing
          ? const Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  CircularProgressIndicator(),
                  SizedBox(height: 16),
                  Text(
                    'Traitement en cours...',
                    style: TextStyle(fontSize: 16),
                  ),
                  SizedBox(height: 8),
                  Text(
                    'Veuillez ne pas fermer cette page',
                    style: TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                  ),
                ],
              ),
            )
          : SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Transaction Type Badge
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: _getTransactionTypeColor(widget.transactionType),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Text(
                        _getTransactionTypeLabel(widget.transactionType),
                        style: const TextStyle(
                          color: JuventusTheme.primaryWhite,
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),

                    const SizedBox(height: 16),

                    // Item Title
                    const Text(
                      'Détails de la transaction',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: JuventusTheme.grey600[100],
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            widget.itemTitle,
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          if (widget.additionalData?['donor_message'] != null) ...[
                            const SizedBox(height: 8),
                            Text(
                              'Message: ${widget.additionalData!['donor_message']}',
                              style: TextStyle(fontSize: 12, color: JuventusTheme.grey600[600]),
                            ),
                          ],
                          if (widget.additionalData?['is_anonymous'] == true) ...[
                            const SizedBox(height: 8),
                            Row(
                              children: [
                                Icon(Icons.visibility_off, size: 14, color: JuventusTheme.grey600[600]),
                                const SizedBox(width: 4),
                                Text(
                                  'Don anonyme',
                                  style: TextStyle(fontSize: 12, color: JuventusTheme.grey600[600]),
                                ),
                              ],
                            ),
                          ],
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // Payment Method
                    const Text(
                      'Méthode de paiement',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        border: Border.all(color: JuventusTheme.grey600[300]!),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Row(
                        children: [
                          Container(
                            width: 50,
                            height: 50,
                            decoration: BoxDecoration(
                              color: JuventusTheme.grey600[200],
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: widget.paymentMethod.logoUrl != null
                                ? Image.network(
                                    widget.paymentMethod.logoUrl!,
                                    fit: BoxFit.contain,
                                  )
                                : Icon(
                                    Icons.payment,
                                    size: 30,
                                    color: JuventusTheme.grey600[600],
                                  ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  widget.paymentMethod.name,
                                  style: const TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                Text(
                                  widget.paymentMethod.typeDisplay,
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: JuventusTheme.grey600[600],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // Amount Breakdown
                    const Text(
                      'Récapitulatif',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.blue[50],
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: Colors.blue),
                      ),
                      child: Column(
                        children: [
                          _buildAmountRow(
                            'Montant',
                            currencyFormat.format(fees['amount']),
                          ),
                          const SizedBox(height: 8),
                          _buildAmountRow(
                            'Frais de transaction (${widget.paymentMethod.transactionFeePercentage}%)',
                            '- ${currencyFormat.format(fees['total_fee'])}',
                            color: JuventusTheme.error[700],
                          ),
                          const Divider(height: 24),
                          _buildAmountRow(
                            'Montant net reçu',
                            currencyFormat.format(fees['net_amount']),
                            isBold: true,
                            color: JuventusTheme.success,
                            fontSize: 18,
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // Processing Time Info
                    if (widget.paymentMethod.processingTime != null)
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.amber[50],
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.amber),
                        ),
                        child: Row(
                          children: [
                            Icon(Icons.access_time, color: Colors.amber[700], size: 20),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                'Temps de traitement: ${widget.paymentMethod.processingTime}',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.amber[900],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                    const SizedBox(height: 16),

                    // Instructions
                    if (widget.paymentMethod.instructions != null)
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: JuventusTheme.grey600[100],
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Icon(Icons.info_outline, size: 20, color: JuventusTheme.grey600[700]),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                widget.paymentMethod.instructions!,
                                style: TextStyle(
                                  fontSize: 12,
                                  color: JuventusTheme.grey600[700],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                    const SizedBox(height: 24),

                    // Terms and Conditions
                    CheckboxListTile(
                      value: _termsAccepted,
                      onChanged: (value) => setState(() => _termsAccepted = value!),
                      controlAffinity: ListTileControlAffinity.leading,
                      contentPadding: EdgeInsets.zero,
                      title: const Text(
                        'J\'accepte les conditions générales de vente et la politique de confidentialité',
                        style: TextStyle(fontSize: 12),
                      ),
                    ),

                    const SizedBox(height: 16),

                    // Confirm Button
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _termsAccepted ? _confirmPayment : null,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green,
                          padding: const EdgeInsets.all(16),
                          disabledBackgroundColor: Colors.grey[300],
                        ),
                        child: const Text(
                          'Confirmer le paiement',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: JuventusTheme.primaryWhite,
                          ),
                        ),
                      ),
                    ),

                    const SizedBox(height: 12),

                    // Security Notice
                    Center(
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.lock, size: 14, color: JuventusTheme.grey600[600]),
                          const SizedBox(width: 4),
                          Text(
                            'Paiement sécurisé',
                            style: TextStyle(
                              fontSize: 12,
                              color: JuventusTheme.grey600[600],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildAmountRow(
    String label,
    String amount, {
    Color? color,
    bool isBold = false,
    double fontSize = 14,
  }) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: fontSize,
            fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
            color: color,
          ),
        ),
        Text(
          amount,
          style: TextStyle(
            fontSize: fontSize,
            fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
            color: color,
          ),
        ),
      ],
    );
  }

  Color _getTransactionTypeColor(String type) {
    switch (type) {
      case 'donation':
        return Colors.green;
      case 'auction':
        return Colors.orange;
      case 'product':
        return Colors.blue;
      case 'ticket':
        return Colors.purple;
      default:
        return Colors.grey;
    }
  }

  String _getTransactionTypeLabel(String type) {
    switch (type) {
      case 'donation':
        return 'DON';
      case 'auction':
        return 'ENCHÈRE';
      case 'product':
        return 'PRODUIT';
      case 'ticket':
        return 'BILLET';
      default:
        return type.toUpperCase();
    }
  }
}
