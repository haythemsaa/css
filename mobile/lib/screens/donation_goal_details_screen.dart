import 'package:flutter/material.dart';

class DonationGoalDetailsScreen extends StatelessWidget {
  final String slug;

  const DonationGoalDetailsScreen({super.key, required this.slug});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Détails objectif'),
        backgroundColor: Colors.black,
      ),
      body: const Center(
        child: Text('Écran en développement'),
      ),
    );
  }
}
