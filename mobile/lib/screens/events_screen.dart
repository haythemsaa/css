import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class EventsScreen extends StatefulWidget {
  const EventsScreen({super.key});

  @override
  State<EventsScreen> createState() => _EventsScreenState();
}

class _EventsScreenState extends State<EventsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();

  List<dynamic> _upcomingEvents = [];
  List<dynamic> _pastEvents = [];
  List<dynamic> _myEvents = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadEvents();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadEvents() async {
    try {
      setState(() => _isLoading = true);

      final response = await _apiService.getEvents();
      final events = response.data as List;

      final now = DateTime.now();

      if (mounted) {
        setState(() {
          _upcomingEvents = events.where((e) {
            final eventDate = DateTime.parse(e['event_date'] ?? DateTime.now().toString());
            return eventDate.isAfter(now);
          }).toList();

          _pastEvents = events.where((e) {
            final eventDate = DateTime.parse(e['event_date'] ?? DateTime.now().toString());
            return eventDate.isBefore(now);
          }).toList();

          _myEvents = []; // Would come from user's registrations
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('ÉVÉNEMENTS CSS'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: JuventusTheme.primaryWhite,
          indicatorWeight: 3,
          labelColor: JuventusTheme.primaryWhite,
          unselectedLabelColor: JuventusTheme.grey500,
          labelStyle: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 13,
          ),
          tabs: const [
            Tab(text: 'À VENIR'),
            Tab(text: 'MES ÉVÉNEMENTS'),
            Tab(text: 'PASSÉS'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: JuventusTheme.primaryBlack,
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadEvents,
              color: JuventusTheme.primaryBlack,
              child: TabBarView(
                controller: _tabController,
                children: [
                  _buildUpcomingEvents(),
                  _buildMyEvents(),
                  _buildPastEvents(),
                ],
              ),
            ),
    );
  }

  Widget _buildUpcomingEvents() {
    if (_upcomingEvents.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.event_outlined,
              size: 80,
              color: JuventusTheme.grey400,
            ),
            const SizedBox(height: 16),
            const Text(
              'Aucun événement à venir',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: JuventusTheme.grey700,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _upcomingEvents.length,
      itemBuilder: (context, index) {
        final event = _upcomingEvents[index];
        return _buildEventCard(event, canRegister: true);
      },
    );
  }

  Widget _buildMyEvents() {
    if (_myEvents.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.event_available_outlined,
              size: 80,
              color: JuventusTheme.grey400,
            ),
            const SizedBox(height: 16),
            const Text(
              'Vous n\'êtes inscrit à aucun événement',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: JuventusTheme.grey700,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _myEvents.length,
      itemBuilder: (context, index) {
        final event = _myEvents[index];
        return _buildEventCard(event, isRegistered: true);
      },
    );
  }

  Widget _buildPastEvents() {
    if (_pastEvents.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.history,
              size: 80,
              color: JuventusTheme.grey400,
            ),
            const SizedBox(height: 16),
            const Text(
              'Aucun événement passé',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: JuventusTheme.grey700,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _pastEvents.length,
      itemBuilder: (context, index) {
        final event = _pastEvents[index];
        return _buildEventCard(event);
      },
    );
  }

  Widget _buildEventCard(
    dynamic event, {
    bool canRegister = false,
    bool isRegistered = false,
  }) {
    final dateFormat = DateFormat('EEE dd MMM yyyy - HH:mm', 'fr_FR');
    final eventDate = DateTime.parse(event['event_date'] ?? DateTime.now().toString());
    final capacity = event['capacity'];
    final registeredCount = event['registered_count'] ?? 0;
    final isFull = capacity != null && registeredCount >= capacity;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () {
          // Navigate to event details
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Event Image
            if (event['banner_image'] != null)
              AspectRatio(
                aspectRatio: 16 / 9,
                child: Stack(
                  children: [
                    Image.network(
                      event['banner_image'],
                      width: double.infinity,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => Container(
                        color: JuventusTheme.grey200,
                        child: const Icon(
                          Icons.event_outlined,
                          size: 48,
                          color: JuventusTheme.grey400,
                        ),
                      ),
                    ),

                    // Event Type Badge
                    Positioned(
                      top: 12,
                      left: 12,
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: _getEventTypeColor(event['type']),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          _getEventTypeLabel(event['type']),
                          style: const TextStyle(
                            color: JuventusTheme.primaryWhite,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 0.5,
                          ),
                        ),
                      ),
                    ),

                    // Status Badge
                    if (isRegistered)
                      Positioned(
                        top: 12,
                        right: 12,
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            color: JuventusTheme.success,
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: const Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.check_circle, size: 14, color: JuventusTheme.primaryWhite),
                              SizedBox(width: 4),
                              Text(
                                'INSCRIT',
                                style: TextStyle(
                                  color: JuventusTheme.primaryWhite,
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                  ],
                ),
              ),

            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Title
                  Text(
                    event['title'] ?? '',
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: JuventusTheme.primaryBlack,
                    ),
                  ),

                  const SizedBox(height: 12),

                  // Date & Time
                  Row(
                    children: [
                      const Icon(Icons.calendar_today, size: 16, color: JuventusTheme.grey600),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          dateFormat.format(eventDate),
                          style: const TextStyle(
                            fontSize: 14,
                            color: JuventusTheme.grey700,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 8),

                  // Location
                  if (event['location'] != null)
                    Row(
                      children: [
                        const Icon(Icons.location_on, size: 16, color: JuventusTheme.grey600),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            event['location'],
                            style: const TextStyle(
                              fontSize: 14,
                              color: JuventusTheme.grey700,
                            ),
                          ),
                        ),
                      ],
                    ),

                  const SizedBox(height: 12),

                  // Capacity
                  if (capacity != null)
                    Row(
                      children: [
                        Expanded(
                          child: LinearProgressIndicator(
                            value: registeredCount / capacity,
                            backgroundColor: JuventusTheme.grey200,
                            color: isFull ? JuventusTheme.error : JuventusTheme.success,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Text(
                          '$registeredCount/$capacity',
                          style: const TextStyle(
                            fontSize: 12,
                            color: JuventusTheme.grey600,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),

                  if (capacity != null) const SizedBox(height: 12),

                  // Action Button
                  if (canRegister)
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: isFull ? null : () => _registerForEvent(event),
                        icon: Icon(
                          isFull ? Icons.block : Icons.check_circle,
                          color: JuventusTheme.primaryWhite,
                        ),
                        label: Text(
                          isFull ? 'COMPLET' : 'S\'INSCRIRE',
                          style: const TextStyle(
                            color: JuventusTheme.primaryWhite,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 0.5,
                          ),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: isFull ? JuventusTheme.grey400 : JuventusTheme.primaryBlack,
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          elevation: 0,
                        ),
                      ),
                    ),

                  if (isRegistered)
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton.icon(
                        onPressed: () => _showQRCode(event),
                        icon: const Icon(Icons.qr_code, color: JuventusTheme.primaryBlack),
                        label: const Text(
                          'VOIR QR CODE',
                          style: TextStyle(
                            color: JuventusTheme.primaryBlack,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 0.5,
                          ),
                        ),
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          side: const BorderSide(color: JuventusTheme.primaryBlack, width: 2),
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getEventTypeColor(String? type) {
    switch (type) {
      case 'match':
        return JuventusTheme.success;
      case 'meet_greet':
        return JuventusTheme.accentGold;
      case 'training':
        return JuventusTheme.info;
      case 'conference':
        return JuventusTheme.warning;
      default:
        return JuventusTheme.grey600;
    }
  }

  String _getEventTypeLabel(String? type) {
    switch (type) {
      case 'match':
        return 'MATCH';
      case 'meet_greet':
        return 'RENCONTRE';
      case 'training':
        return 'ENTRAÎNEMENT';
      case 'conference':
        return 'CONFÉRENCE';
      default:
        return 'ÉVÉNEMENT';
    }
  }

  Future<void> _registerForEvent(dynamic event) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: JuventusTheme.primaryWhite,
        title: const Text(
          'Inscription',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        content: Text(
          'Voulez-vous vous inscrire à "${event['title']}"?',
          style: const TextStyle(color: JuventusTheme.grey700),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text(
              'Annuler',
              style: TextStyle(color: JuventusTheme.grey600),
            ),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: JuventusTheme.primaryBlack,
              elevation: 0,
            ),
            child: const Text(
              'Confirmer',
              style: TextStyle(
                color: JuventusTheme.primaryWhite,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );

    if (confirm == true) {
      try {
        await _apiService.registerForEvent(event['id']);

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Inscription réussie!'),
              backgroundColor: JuventusTheme.success,
            ),
          );
          _loadEvents();
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Erreur: $e'),
              backgroundColor: JuventusTheme.error,
            ),
          );
        }
      }
    }
  }

  void _showQRCode(dynamic event) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: JuventusTheme.primaryWhite,
        title: const Text(
          'Code QR',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: JuventusTheme.primaryBlack,
          ),
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 200,
              height: 200,
              decoration: BoxDecoration(
                color: JuventusTheme.grey200,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: JuventusTheme.grey300, width: 2),
              ),
              child: const Center(
                child: Icon(
                  Icons.qr_code,
                  size: 100,
                  color: JuventusTheme.primaryBlack,
                ),
              ),
            ),
            const SizedBox(height: 16),
            Text(
              event['title'],
              style: const TextStyle(
                fontWeight: FontWeight.bold,
                color: JuventusTheme.primaryBlack,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),
            const Text(
              'Présentez ce code à l\'entrée',
              style: TextStyle(
                fontSize: 12,
                color: JuventusTheme.grey600,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text(
              'Fermer',
              style: TextStyle(
                color: JuventusTheme.primaryBlack,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
