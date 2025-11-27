import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'config/app_config.dart';
import 'config/theme.dart';
import 'screens/home_screen.dart';
import 'screens/polls_screen.dart';
import 'screens/auctions_list_screen.dart';
import 'screens/donation_goals_screen.dart';
import 'screens/products_screen.dart';
import 'screens/ticket_marketplace_screen.dart';
import 'screens/fan_token_wallet_screen.dart';
import 'screens/rewards_store_screen.dart';
import 'screens/badges_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize Firebase
  // await Firebase.initializeApp();

  // Initialize Hive
  // await Hive.initFlutter();

  runApp(
    const ProviderScope(
      child: CSSApp(),
    ),
  );
}

class CSSApp extends StatelessWidget {
  const CSSApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: AppConfig.appName,
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      darkTheme: AppTheme.darkTheme,
      themeMode: ThemeMode.system,
      home: const SplashScreen(),
      routes: {
        '/home': (context) => const HomeScreen(),
        '/polls': (context) => const PollsScreen(),
        '/auctions': (context) => const AuctionsListScreen(),
        '/donation-goals': (context) => const DonationGoalsScreen(),
        '/products': (context) => const ProductsScreen(),
        '/marketplace': (context) => const TicketMarketplaceScreen(),
        '/tokens': (context) => const FanTokenWalletScreen(),
        '/rewards': (context) => const RewardsStoreScreen(),
        '/badges': (context) => const BadgesScreen(),
      },
    );
  }
}

class SplashScreen extends StatelessWidget {
  const SplashScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // CSS Logo
            Container(
              width: 150,
              height: 150,
              decoration: BoxDecoration(
                color: Colors.black,
                shape: BoxShape.circle,
                border: Border.all(color: Colors.amber, width: 3),
              ),
              child: const Center(
                child: Text(
                  'CSS',
                  style: TextStyle(
                    fontSize: 48,
                    fontWeight: FontWeight.bold,
                    color: Colors.amber,
                  ),
                ),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'Club Sportif Sfaxien',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Application Officielle',
              style: TextStyle(
                fontSize: 16,
                color: Colors.grey,
              ),
            ),
            const SizedBox(height: 48),
            const CircularProgressIndicator(
              color: Colors.amber,
            ),
          ],
        ),
      ),
    );
  }
}
