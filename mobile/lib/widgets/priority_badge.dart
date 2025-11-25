import 'package:flutter/material.dart';

class PriorityBadge extends StatelessWidget {
  final String priority;
  final double fontSize;
  final EdgeInsets padding;

  const PriorityBadge({
    super.key,
    required this.priority,
    this.fontSize = 11,
    this.padding = const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
  });

  Color _getPriorityColor() {
    switch (priority.toLowerCase()) {
      case 'urgent':
        return Colors.red;
      case 'high':
        return Colors.orange;
      case 'medium':
        return Colors.blue;
      case 'low':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  String _getPriorityLabel() {
    switch (priority.toLowerCase()) {
      case 'urgent':
        return 'URGENT';
      case 'high':
        return 'HAUTE';
      case 'medium':
        return 'MOYENNE';
      case 'low':
        return 'BASSE';
      default:
        return priority.toUpperCase();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: padding,
      decoration: BoxDecoration(
        color: _getPriorityColor(),
        borderRadius: BorderRadius.circular(4),
      ),
      child: Text(
        _getPriorityLabel(),
        style: TextStyle(
          color: Colors.white,
          fontSize: fontSize,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}

class StatusBadge extends StatelessWidget {
  final String status;
  final double fontSize;
  final EdgeInsets padding;

  const StatusBadge({
    super.key,
    required this.status,
    this.fontSize = 11,
    this.padding = const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
  });

  Color _getStatusColor() {
    switch (status.toLowerCase()) {
      case 'active':
      case 'open':
      case 'completed':
        return Colors.green;
      case 'scheduled':
      case 'pending':
        return Colors.orange;
      case 'ended':
      case 'closed':
      case 'cancelled':
        return Colors.grey;
      case 'draft':
        return Colors.blue;
      default:
        return Colors.grey;
    }
  }

  String _getStatusLabel() {
    switch (status.toLowerCase()) {
      case 'active':
        return 'ACTIF';
      case 'scheduled':
        return 'PROGRAMMÉ';
      case 'ended':
        return 'TERMINÉ';
      case 'pending':
        return 'EN ATTENTE';
      case 'completed':
        return 'COMPLÉTÉ';
      case 'cancelled':
        return 'ANNULÉ';
      case 'draft':
        return 'BROUILLON';
      default:
        return status.toUpperCase();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: padding,
      decoration: BoxDecoration(
        color: _getStatusColor(),
        borderRadius: BorderRadius.circular(4),
      ),
      child: Text(
        _getStatusLabel(),
        style: TextStyle(
          color: Colors.white,
          fontSize: fontSize,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}

class CategoryChip extends StatelessWidget {
  final String category;
  final Color? backgroundColor;
  final Color? textColor;

  const CategoryChip({
    super.key,
    required this.category,
    this.backgroundColor,
    this.textColor,
  });

  @override
  Widget build(BuildContext context) {
    return Chip(
      label: Text(
        category,
        style: TextStyle(
          fontSize: 12,
          color: textColor ?? Colors.black87,
        ),
      ),
      backgroundColor: backgroundColor ?? Colors.grey[200],
      padding: const EdgeInsets.symmetric(horizontal: 4),
      materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
    );
  }
}
