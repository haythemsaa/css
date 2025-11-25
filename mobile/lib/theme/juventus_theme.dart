import 'package:flutter/material.dart';

/// Juventus-inspired theme for CSS Socios
/// Colors: Black & White with subtle greys
/// Style: Minimalist, elegant, modern

class JuventusTheme {
  // Primary Colors - Juventus Black & White
  static const Color primaryBlack = Color(0xFF000000);
  static const Color primaryWhite = Color(0xFFFFFFFF);
  static const Color accentGold = Color(0xFFB89D68); // Subtle gold accent

  // CSS Socios Yellow (used sparingly as accent)
  static const Color cssYellow = Color(0xFFFFD700);

  // Greys
  static const Color grey100 = Color(0xFFF5F5F5);
  static const Color grey200 = Color(0xFFEEEEEE);
  static const Color grey300 = Color(0xFFE0E0E0);
  static const Color grey400 = Color(0xFFBDBDBD);
  static const Color grey500 = Color(0xFF9E9E9E);
  static const Color grey600 = Color(0xFF757575);
  static const Color grey700 = Color(0xFF616161);
  static const Color grey800 = Color(0xFF424242);
  static const Color grey900 = Color(0xFF212121);

  // Functional Colors
  static const Color success = Color(0xFF4CAF50);
  static const Color error = Color(0xFFD32F2F);
  static const Color warning = Color(0xFFFF9800);
  static const Color info = Color(0xFF2196F3);

  // Gradients
  static const LinearGradient blackGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFF000000), Color(0xFF2C2C2C)],
  );

  static const LinearGradient whiteGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [Color(0xFFFFFFFF), Color(0xFFF5F5F5)],
  );

  static const LinearGradient goldGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFFB89D68), Color(0xFFD4AF37)],
  );

  /// Main Theme Data
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,

      // Color Scheme
      colorScheme: const ColorScheme.light(
        primary: primaryBlack,
        secondary: accentGold,
        surface: primaryWhite,
        background: grey100,
        error: error,
        onPrimary: primaryWhite,
        onSecondary: primaryBlack,
        onSurface: primaryBlack,
        onBackground: primaryBlack,
        onError: primaryWhite,
      ),

      // Scaffold
      scaffoldBackgroundColor: grey100,

      // AppBar
      appBarTheme: const AppBarTheme(
        backgroundColor: primaryBlack,
        foregroundColor: primaryWhite,
        elevation: 0,
        centerTitle: false,
        titleTextStyle: TextStyle(
          fontFamily: 'Poppins',
          fontSize: 20,
          fontWeight: FontWeight.w600,
          color: primaryWhite,
          letterSpacing: 0.5,
        ),
        iconTheme: IconThemeData(color: primaryWhite),
      ),

      // Bottom Navigation Bar
      bottomNavigationBarTheme: const BottomNavigationBarThemeData(
        backgroundColor: primaryWhite,
        selectedItemColor: primaryBlack,
        unselectedItemColor: grey500,
        elevation: 8,
        type: BottomNavigationBarType.fixed,
        selectedLabelStyle: TextStyle(
          fontWeight: FontWeight.w600,
          fontSize: 12,
        ),
        unselectedLabelStyle: TextStyle(
          fontWeight: FontWeight.normal,
          fontSize: 11,
        ),
      ),

      // Cards
      cardTheme: CardTheme(
        color: primaryWhite,
        elevation: 2,
        shadowColor: primaryBlack.withOpacity(0.1),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
        margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 0),
      ),

      // Elevated Button
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryBlack,
          foregroundColor: primaryWhite,
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
          textStyle: const TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w600,
            letterSpacing: 0.5,
          ),
        ),
      ),

      // Outlined Button
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: primaryBlack,
          side: const BorderSide(color: primaryBlack, width: 1.5),
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
          textStyle: const TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w600,
            letterSpacing: 0.5,
          ),
        ),
      ),

      // Text Button
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: primaryBlack,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          textStyle: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            letterSpacing: 0.3,
          ),
        ),
      ),

      // Input Decoration
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: primaryWhite,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: grey300),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: grey300),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: primaryBlack, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: error),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        hintStyle: TextStyle(color: grey500, fontSize: 14),
      ),

      // Chip
      chipTheme: ChipThemeData(
        backgroundColor: grey200,
        selectedColor: primaryBlack,
        disabledColor: grey300,
        labelStyle: const TextStyle(color: primaryBlack, fontSize: 13),
        secondaryLabelStyle: const TextStyle(color: primaryWhite, fontSize: 13),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
      ),

      // Divider
      dividerTheme: const DividerThemeData(
        color: grey300,
        thickness: 1,
        space: 24,
      ),

      // Icon
      iconTheme: const IconThemeData(
        color: primaryBlack,
        size: 24,
      ),

      // Text Theme
      textTheme: const TextTheme(
        // Display
        displayLarge: TextStyle(
          fontSize: 32,
          fontWeight: FontWeight.bold,
          color: primaryBlack,
          letterSpacing: -0.5,
        ),
        displayMedium: TextStyle(
          fontSize: 28,
          fontWeight: FontWeight.bold,
          color: primaryBlack,
          letterSpacing: -0.5,
        ),
        displaySmall: TextStyle(
          fontSize: 24,
          fontWeight: FontWeight.bold,
          color: primaryBlack,
        ),

        // Headline
        headlineLarge: TextStyle(
          fontSize: 22,
          fontWeight: FontWeight.w600,
          color: primaryBlack,
          letterSpacing: 0.25,
        ),
        headlineMedium: TextStyle(
          fontSize: 20,
          fontWeight: FontWeight.w600,
          color: primaryBlack,
        ),
        headlineSmall: TextStyle(
          fontSize: 18,
          fontWeight: FontWeight.w600,
          color: primaryBlack,
        ),

        // Title
        titleLarge: TextStyle(
          fontSize: 18,
          fontWeight: FontWeight.w600,
          color: primaryBlack,
          letterSpacing: 0.15,
        ),
        titleMedium: TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.w600,
          color: primaryBlack,
          letterSpacing: 0.15,
        ),
        titleSmall: TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: primaryBlack,
          letterSpacing: 0.1,
        ),

        // Body
        bodyLarge: TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.normal,
          color: primaryBlack,
          letterSpacing: 0.15,
        ),
        bodyMedium: TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.normal,
          color: primaryBlack,
          letterSpacing: 0.25,
        ),
        bodySmall: TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.normal,
          color: grey700,
          letterSpacing: 0.4,
        ),

        // Label
        labelLarge: TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: primaryBlack,
          letterSpacing: 0.5,
        ),
        labelMedium: TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.w600,
          color: grey700,
          letterSpacing: 0.5,
        ),
        labelSmall: TextStyle(
          fontSize: 10,
          fontWeight: FontWeight.w600,
          color: grey600,
          letterSpacing: 0.5,
        ),
      ),

      // Font Family
      fontFamily: 'Poppins',
    );
  }

  /// Dark Theme (Optional - Juventus style dark mode)
  static ThemeData get darkTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.dark,

      colorScheme: const ColorScheme.dark(
        primary: primaryWhite,
        secondary: accentGold,
        surface: grey900,
        background: primaryBlack,
        error: error,
        onPrimary: primaryBlack,
        onSecondary: primaryBlack,
        onSurface: primaryWhite,
        onBackground: primaryWhite,
        onError: primaryWhite,
      ),

      scaffoldBackgroundColor: primaryBlack,

      appBarTheme: const AppBarTheme(
        backgroundColor: primaryBlack,
        foregroundColor: primaryWhite,
        elevation: 0,
        centerTitle: false,
        titleTextStyle: TextStyle(
          fontFamily: 'Poppins',
          fontSize: 20,
          fontWeight: FontWeight.w600,
          color: primaryWhite,
          letterSpacing: 0.5,
        ),
      ),

      cardTheme: CardTheme(
        color: grey900,
        elevation: 4,
        shadowColor: primaryBlack.withOpacity(0.5),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),

      fontFamily: 'Poppins',
    );
  }

  /// Custom Shadows
  static List<BoxShadow> get cardShadow => [
        BoxShadow(
          color: primaryBlack.withOpacity(0.08),
          blurRadius: 10,
          offset: const Offset(0, 4),
        ),
      ];

  static List<BoxShadow> get elevatedShadow => [
        BoxShadow(
          color: primaryBlack.withOpacity(0.12),
          blurRadius: 16,
          offset: const Offset(0, 6),
        ),
      ];

  /// Text Styles Shortcuts
  static const TextStyle heading1 = TextStyle(
    fontSize: 28,
    fontWeight: FontWeight.bold,
    color: primaryBlack,
    letterSpacing: -0.5,
  );

  static const TextStyle heading2 = TextStyle(
    fontSize: 22,
    fontWeight: FontWeight.w600,
    color: primaryBlack,
    letterSpacing: 0.25,
  );

  static const TextStyle heading3 = TextStyle(
    fontSize: 18,
    fontWeight: FontWeight.w600,
    color: primaryBlack,
  );

  static const TextStyle bodyBold = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: primaryBlack,
  );

  static const TextStyle bodyRegular = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.normal,
    color: primaryBlack,
  );

  static TextStyle bodySecondary = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.normal,
    color: grey700,
  );

  static const TextStyle caption = TextStyle(
    fontSize: 12,
    fontWeight: FontWeight.normal,
    color: grey600,
  );

  static const TextStyle overline = TextStyle(
    fontSize: 10,
    fontWeight: FontWeight.w600,
    color: grey600,
    letterSpacing: 1.5,
  );
}

/// Juventus-style Decorations
class JuventusDecorations {
  /// Premium Card with gradient
  static BoxDecoration premiumCard = BoxDecoration(
    gradient: JuventusTheme.goldGradient,
    borderRadius: BorderRadius.circular(12),
    boxShadow: JuventusTheme.elevatedShadow,
  );

  /// Simple white card
  static BoxDecoration whiteCard = BoxDecoration(
    color: JuventusTheme.primaryWhite,
    borderRadius: BorderRadius.circular(12),
    boxShadow: JuventusTheme.cardShadow,
  );

  /// Black card with subtle border
  static BoxDecoration blackCard = BoxDecoration(
    color: JuventusTheme.primaryBlack,
    borderRadius: BorderRadius.circular(12),
    border: Border.all(color: JuventusTheme.grey800, width: 1),
  );

  /// Image container with overlay
  static BoxDecoration imageOverlay = BoxDecoration(
    gradient: LinearGradient(
      begin: Alignment.topCenter,
      end: Alignment.bottomCenter,
      colors: [
        Colors.transparent,
        JuventusTheme.primaryBlack.withOpacity(0.7),
      ],
    ),
  );
}

/// Juventus-style Animations
class JuventusAnimations {
  static const Duration fast = Duration(milliseconds: 200);
  static const Duration normal = Duration(milliseconds: 300);
  static const Duration slow = Duration(milliseconds: 500);

  static const Curve defaultCurve = Curves.easeInOut;
  static const Curve bounceCurve = Curves.elasticOut;
}
