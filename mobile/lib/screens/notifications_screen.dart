import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  final ApiService _apiService = ApiService();
  List<Map<String, dynamic>> _notifications = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  Future<void> _loadNotifications() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getNotifications();
      setState(() {
        _notifications = List<Map<String, dynamic>>.from(response.data['data']);
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur: $e'), backgroundColor: JuventusTheme.error),
        );
      }
    }
  }

  Future<void> _markAsRead(int id) async {
    try {
      await _apiService.markNotificationAsRead(id);
      _loadNotifications();
    } catch (e) {
      // Silently fail
    }
  }

  Future<void> _markAllAsRead() async {
    try {
      await _apiService.markAllNotificationsAsRead();
      _loadNotifications();
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur: $e'), backgroundColor: JuventusTheme.error),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final unreadCount = _notifications.where((n) => n['read_at'] == null).length;

    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: Text('NOTIFICATIONS${unreadCount > 0 ? " ($unreadCount)" : ""}'),
        backgroundColor: JuventusTheme.primaryBlack,
        actions: [
          if (unreadCount > 0)
            TextButton(
              onPressed: _markAllAsRead,
              child: const Text(
                'Tout lire',
                style: TextStyle(color: JuventusTheme.accentGold),
              ),
            ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: JuventusTheme.primaryBlack))
          : RefreshIndicator(
              onRefresh: _loadNotifications,
              color: JuventusTheme.primaryBlack,
              child: _buildNotificationsList(),
            ),
    );
  }

  Widget _buildNotificationsList() {
    if (_notifications.isEmpty) {
      return const Center(child: Text('Aucune notification'));
    }

    return ListView.builder(
      itemCount: _notifications.length,
      itemBuilder: (context, index) => _buildNotificationItem(_notifications[index]),
    );
  }

  Widget _buildNotificationItem(Map<String, dynamic> notification) {
    final isUnread = notification['read_at'] == null;
    final createdAt = DateTime.parse(notification['created_at']);

    return Container(
      color: isUnread ? JuventusTheme.accentGold.withOpacity(0.05) : null,
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: _getTypeColor(notification['type']).withOpacity(0.1),
          child: Icon(
            _getTypeIcon(notification['type']),
            color: _getTypeColor(notification['type']),
            size: 20,
          ),
        ),
        title: Text(
          notification['title'] ?? 'Notification',
          style: TextStyle(
            fontWeight: isUnread ? FontWeight.bold : FontWeight.normal,
            fontSize: 14,
          ),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (notification['message'] != null)
              Text(
                notification['message'],
                style: const TextStyle(fontSize: 13),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            const SizedBox(height: 4),
            Text(
              _formatDate(createdAt),
              style: const TextStyle(fontSize: 11, color: JuventusTheme.grey500),
            ),
          ],
        ),
        trailing: isUnread
            ? Container(
                width: 8,
                height: 8,
                decoration: const BoxDecoration(
                  color: JuventusTheme.accentGold,
                  shape: BoxShape.circle,
                ),
              )
            : null,
        onTap: () {
          if (isUnread) {
            _markAsRead(notification['id']);
          }
        },
      ),
    );
  }

  IconData _getTypeIcon(String type) {
    switch (type) {
      case 'match':
        return Icons.sports_soccer;
      case 'token':
        return Icons.stars;
      case 'badge':
        return Icons.emoji_events;
      case 'prediction':
        return Icons.sports;
      default:
        return Icons.notifications;
    }
  }

  Color _getTypeColor(String type) {
    switch (type) {
      case 'match':
        return JuventusTheme.info;
      case 'token':
        return JuventusTheme.accentGold;
      case 'badge':
        return const Color(0xFFA855F7);
      case 'prediction':
        return JuventusTheme.success;
      default:
        return JuventusTheme.primaryBlack;
    }
  }

  String _formatDate(DateTime date) {
    final now = DateTime.now();
    final difference = now.difference(date);

    if (difference.inMinutes < 60) {
      return 'Il y a ${difference.inMinutes} min';
    } else if (difference.inHours < 24) {
      return 'Il y a ${difference.inHours}h';
    } else if (difference.inDays < 7) {
      return 'Il y a ${difference.inDays}j';
    } else {
      return DateFormat('dd MMM').format(date);
    }
  }
}
