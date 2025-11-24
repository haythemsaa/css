import 'package:flutter/material.dart';

class AppTheme {
  // CSS Colors
  static const Color primaryBlack = Color(0xFF000000);
  static const Color primaryGold = Color(0xFFFFD700);
  static const Color primaryWhite = Color(0xFFFFFFFF);

  static const Color accentRed = Color(0xFFDC143C);
  static const Color successGreen = Color(0xFF28A745);
  static const Color warningOrange = Color(0xFFFF8C00);

  // Light Theme
  static ThemeData lightTheme = ThemeData(
    useMaterial3: true,
    brightness: Brightness.light,
    primaryColor: primaryBlack,
    colorScheme: const ColorScheme.light(
      primary: primaryBlack,
      secondary: primaryGold,
      tertiary: accentRed,
      surface: Colors.white,
      background: Color(0xFFF5F5F5),
      error: Colors.red,
    ),
    scaffoldBackgroundColor: const Color(0xFFF5F5F5),
    appBarTheme: const AppBarTheme(
      backgroundColor: primaryBlack,
      foregroundColor: primaryGold,
      elevation: 0,
      centerTitle: true,
    ),
    bottomNavigationBarTheme: const BottomNavigationBarTheme(
      backgroundColor: primaryBlack,
      selectedItemColor: primaryGold,
      unselectedItemColor: Colors.grey,
      type: BottomNavigationBarType.fixed,
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: primaryBlack,
        foregroundColor: primaryGold,
        padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
    ),
    cardTheme: CardTheme(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: Colors.white,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide.none,
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: primaryGold, width: 2),
      ),
    ),
  );

  // Dark Theme
  static ThemeData darkTheme = ThemeData(
    useMaterial3: true,
    brightness: Brightness.dark,
    primaryColor: primaryGold,
    colorScheme: const ColorScheme.dark(
      primary: primaryGold,
      secondary: primaryBlack,
      tertiary: accentRed,
      surface: Color(0xFF1E1E1E),
      background: Color(0xFF121212),
      error: Colors.red,
    ),
    scaffoldBackgroundColor: const Color(0xFF121212),
    appBarTheme: const AppBarTheme(
      backgroundColor: Color(0xFF1E1E1E),
      foregroundColor: primaryGold,
      elevation: 0,
      centerTitle: true,
    ),
    bottomNavigationBarTheme: const BottomNavigationBarTheme(
      backgroundColor: Color(0xFF1E1E1E),
      selectedItemColor: primaryGold,
      unselectedItemColor: Colors.grey,
      type: BottomNavigationBarType.fixed,
    ),
  );
}
