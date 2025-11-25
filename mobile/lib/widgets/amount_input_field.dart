import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

class AmountInputField extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final String? hintText;
  final double? minAmount;
  final double? maxAmount;
  final String currency;
  final Function(String)? onChanged;
  final bool enabled;

  const AmountInputField({
    super.key,
    required this.controller,
    required this.label,
    this.hintText,
    this.minAmount,
    this.maxAmount,
    this.currency = 'TND',
    this.onChanged,
    this.enabled = true,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        TextField(
          controller: controller,
          enabled: enabled,
          keyboardType: const TextInputType.numberWithOptions(decimal: true),
          inputFormatters: [
            FilteringTextInputFormatter.allow(RegExp(r'^\d+\.?\d{0,2}')),
          ],
          onChanged: onChanged,
          decoration: InputDecoration(
            labelText: label,
            hintText: hintText,
            prefixIcon: const Icon(Icons.attach_money),
            suffixText: currency,
            border: const OutlineInputBorder(),
            enabledBorder: OutlineInputBorder(
              borderSide: BorderSide(color: Colors.grey[400]!),
            ),
            focusedBorder: OutlineInputBorder(
              borderSide: BorderSide(
                color: Theme.of(context).primaryColor,
                width: 2,
              ),
            ),
          ),
        ),
        if (minAmount != null || maxAmount != null) ...[
          const SizedBox(height: 4),
          Padding(
            padding: const EdgeInsets.only(left: 12),
            child: Text(
              _buildRangeText(),
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[600],
              ),
            ),
          ),
        ],
      ],
    );
  }

  String _buildRangeText() {
    if (minAmount != null && maxAmount != null) {
      return 'Montant: $minAmount - $maxAmount $currency';
    } else if (minAmount != null) {
      return 'Minimum: $minAmount $currency';
    } else if (maxAmount != null) {
      return 'Maximum: $maxAmount $currency';
    }
    return '';
  }
}

class QuickAmountSelector extends StatelessWidget {
  final List<double> amounts;
  final double? selectedAmount;
  final Function(double) onAmountSelected;
  final String currency;

  const QuickAmountSelector({
    super.key,
    required this.amounts,
    required this.onAmountSelected,
    this.selectedAmount,
    this.currency = 'TND',
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Montants rapides',
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: Colors.grey[700],
          ),
        ),
        const SizedBox(height: 8),
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: amounts.map((amount) {
            final isSelected = selectedAmount == amount;
            return ChoiceChip(
              label: Text('$amount $currency'),
              selected: isSelected,
              onSelected: (selected) {
                if (selected) {
                  onAmountSelected(amount);
                }
              },
              selectedColor: Theme.of(context).primaryColor,
              labelStyle: TextStyle(
                color: isSelected ? Colors.white : Colors.black87,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
              ),
            );
          }).toList(),
        ),
      ],
    );
  }
}

class AmountDisplay extends StatelessWidget {
  final double amount;
  final String label;
  final String currency;
  final Color? amountColor;
  final double fontSize;
  final Color? backgroundColor;
  final EdgeInsets padding;

  const AmountDisplay({
    super.key,
    required this.amount,
    required this.label,
    this.currency = 'TND',
    this.amountColor,
    this.fontSize = 20,
    this.backgroundColor,
    this.padding = const EdgeInsets.all(16),
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: padding,
      decoration: BoxDecoration(
        color: backgroundColor ?? Colors.grey[100],
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey[600],
            ),
          ),
          const SizedBox(height: 4),
          Text(
            '${amount.toStringAsFixed(2)} $currency',
            style: TextStyle(
              fontSize: fontSize,
              fontWeight: FontWeight.bold,
              color: amountColor ?? Colors.black,
            ),
          ),
        ],
      ),
    );
  }
}
