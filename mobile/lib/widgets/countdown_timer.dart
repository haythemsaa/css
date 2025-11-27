import 'package:flutter/material.dart';
import 'dart:async';

class CountdownTimer extends StatefulWidget {
  final int secondsRemaining;
  final TextStyle? textStyle;
  final Color? backgroundColor;
  final IconData? icon;
  final VoidCallback? onComplete;

  const CountdownTimer({
    super.key,
    required this.secondsRemaining,
    this.textStyle,
    this.backgroundColor,
    this.icon,
    this.onComplete,
  });

  @override
  State<CountdownTimer> createState() => _CountdownTimerState();
}

class _CountdownTimerState extends State<CountdownTimer> {
  late int _remainingSeconds;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _remainingSeconds = widget.secondsRemaining;
    _startTimer();
  }

  @override
  void didUpdateWidget(CountdownTimer oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.secondsRemaining != widget.secondsRemaining) {
      _remainingSeconds = widget.secondsRemaining;
      _timer?.cancel();
      _startTimer();
    }
  }

  void _startTimer() {
    if (_remainingSeconds > 0) {
      _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
        if (_remainingSeconds <= 0) {
          timer.cancel();
          if (widget.onComplete != null) {
            widget.onComplete!();
          }
        } else {
          if (mounted) {
            setState(() {
              _remainingSeconds--;
            });
          }
        }
      });
    }
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  String _formatTime() {
    if (_remainingSeconds <= 0) {
      return 'Terminé';
    }

    final days = _remainingSeconds ~/ 86400;
    final hours = (_remainingSeconds % 86400) ~/ 3600;
    final minutes = (_remainingSeconds % 3600) ~/ 60;
    final seconds = _remainingSeconds % 60;

    if (days > 0) {
      return '${days}j ${hours}h ${minutes}m';
    } else if (hours > 0) {
      return '${hours}h ${minutes}m ${seconds}s';
    } else if (minutes > 0) {
      return '${minutes}m ${seconds}s';
    } else {
      return '${seconds}s';
    }
  }

  Color _getTimeColor() {
    if (_remainingSeconds <= 0) {
      return Colors.grey;
    } else if (_remainingSeconds < 300) { // Less than 5 minutes
      return Colors.red;
    } else if (_remainingSeconds < 3600) { // Less than 1 hour
      return Colors.orange;
    } else {
      return Colors.green;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: widget.backgroundColor ?? _getTimeColor().withOpacity(0.1),
        borderRadius: BorderRadius.circular(4),
        border: Border.all(color: _getTimeColor()),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (widget.icon != null) ...[
            Icon(
              widget.icon,
              size: 14,
              color: _getTimeColor(),
            ),
            const SizedBox(width: 4),
          ],
          Text(
            _formatTime(),
            style: widget.textStyle ??
                TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: _getTimeColor(),
                ),
          ),
        ],
      ),
    );
  }
}

class LargeCountdownTimer extends StatefulWidget {
  final int secondsRemaining;
  final VoidCallback? onComplete;

  const LargeCountdownTimer({
    super.key,
    required this.secondsRemaining,
    this.onComplete,
  });

  @override
  State<LargeCountdownTimer> createState() => _LargeCountdownTimerState();
}

class _LargeCountdownTimerState extends State<LargeCountdownTimer> {
  late int _remainingSeconds;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _remainingSeconds = widget.secondsRemaining;
    _startTimer();
  }

  void _startTimer() {
    if (_remainingSeconds > 0) {
      _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
        if (_remainingSeconds <= 0) {
          timer.cancel();
          if (widget.onComplete != null) {
            widget.onComplete!();
          }
        } else {
          if (mounted) {
            setState(() {
              _remainingSeconds--;
            });
          }
        }
      });
    }
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final days = _remainingSeconds ~/ 86400;
    final hours = (_remainingSeconds % 86400) ~/ 3600;
    final minutes = (_remainingSeconds % 3600) ~/ 60;
    final seconds = _remainingSeconds % 60;

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Colors.blue[700]!, Colors.blue[900]!],
        ),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: [
          _buildTimeUnit(days.toString().padLeft(2, '0'), 'Jours'),
          _buildSeparator(),
          _buildTimeUnit(hours.toString().padLeft(2, '0'), 'Heures'),
          _buildSeparator(),
          _buildTimeUnit(minutes.toString().padLeft(2, '0'), 'Min'),
          _buildSeparator(),
          _buildTimeUnit(seconds.toString().padLeft(2, '0'), 'Sec'),
        ],
      ),
    );
  }

  Widget _buildTimeUnit(String value, String label) {
    return Column(
      children: [
        Text(
          value,
          style: const TextStyle(
            fontSize: 32,
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          label,
          style: const TextStyle(
            fontSize: 12,
            color: Colors.white70,
          ),
        ),
      ],
    );
  }

  Widget _buildSeparator() {
    return const Text(
      ':',
      style: TextStyle(
        fontSize: 32,
        fontWeight: FontWeight.bold,
        color: Colors.white,
      ),
    );
  }
}
