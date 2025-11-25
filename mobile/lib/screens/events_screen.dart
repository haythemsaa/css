import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';

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
      appBar: AppBar(
        title: const Text('Événements CSS'),
        backgroundColor: Colors.black,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.yellow[700],
          labelColor: Colors.yellow[700],
          unselectedLabelColor: Colors.white70,
          tabs: const [
            Tab(text: 'À Venir'),
            Tab(text: 'Mes Événements'),
            Tab(text: 'Passés'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadEvents,
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
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.event, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Aucun événement à venir'),
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
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.event_available, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Vous n\'êtes inscrit à aucun événement'),
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
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.history, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Aucun événement passé'),
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

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
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
                        color: Colors.grey[300],
                        child: const Icon(Icons.event, size: 48),
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
                            color: Colors.white,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
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
                            color: Colors.green,
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: const Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.check_circle, size: 14, color: Colors.white),
                              SizedBox(width: 4),
                              Text(
                                'INSCRIT',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
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
                    ),
                  ),

                  const SizedBox(height: 12),

                  // Date & Time
                  Row(
                    children: [
                      Icon(Icons.calendar_today, size: 16, color: Colors.grey[600]),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          dateFormat.format(eventDate),
                          style: TextStyle(
                            fontSize: 14,
                            color: Colors.grey[700],
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
                        Icon(Icons.location_on, size: 16, color: Colors.grey[600]),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            event['location'],
                            style: TextStyle(
                              fontSize: 14,
                              color: Colors.grey[700],
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
                            backgroundColor: Colors.grey[200],
                            color: isFull ? Colors.red : Colors.green,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Text(
                          '$registeredCount/$capacity',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
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
                          color: Colors.white,
                        ),
                        label: Text(
                          isFull ? 'COMPLET' : 'S\'INSCRIRE',
                          style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: isFull ? Colors.grey : Colors.black,
                          padding: const EdgeInsets.symmetric(vertical: 12),
                        ),
                      ),
                    ),

                  if (isRegistered)
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton.icon(
                        onPressed: () => _showQRCode(event),
                        icon: const Icon(Icons.qr_code),
                        label: const Text('VOIR QR CODE'),
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 12),
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
        return Colors.green;
      case 'meet_greet':
        return Colors.purple;
      case 'training':
        return Colors.blue;
      case 'conference':
        return Colors.orange;
      default:
        return Colors.grey;
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
        title: const Text('Inscription'),
        content: Text('Voulez-vous vous inscrire à "${event['title']}"?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Annuler'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Confirmer'),
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
              backgroundColor: Colors.green,
            ),
          );
          _loadEvents();
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Erreur: $e'),
              backgroundColor: Colors.red,
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
        title: const Text('Code QR'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 200,
              height: 200,
              color: Colors.grey[300],
              child: const Center(
                child: Icon(Icons.qr_code, size: 100),
              ),
            ),
            const SizedBox(height: 16),
            Text(
              event['title'],
              style: const TextStyle(fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),
            const Text(
              'Présentez ce code à l\'entrée',
              style: TextStyle(fontSize: 12),
              textAlign: TextAlign.center,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Fermer'),
          ),
        ],
      ),
    );
  }
}
