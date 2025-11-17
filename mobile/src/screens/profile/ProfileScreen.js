import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
} from 'react-native';
import { Card, Button } from '../../components/common';
import useAuthStore from '../../stores/authStore';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS } from '../../constants/theme';

const ProfileScreen = () => {
  const { user, logout, getUserTypeInfo } = useAuthStore();
  const userTypeInfo = getUserTypeInfo();

  const getLoyaltyLevel = () => {
    const points = user?.loyalty_points || 0;
    if (points >= 5000) return { name: 'Platinum', icon: '💎', color: COLORS.info };
    if (points >= 2000) return { name: 'Gold', icon: '🥇', color: COLORS.gold };
    if (points >= 500) return { name: 'Silver', icon: '🥈', color: COLORS.gray500 };
    return { name: 'Bronze', icon: '🥉', color: COLORS.warning };
  };

  const loyaltyLevel = getLoyaltyLevel();

  const handleLogout = () => {
    Alert.alert(
      'Déconnexion',
      'Êtes-vous sûr de vouloir vous déconnecter ?',
      [
        { text: 'Annuler', style: 'cancel' },
        {
          text: 'Déconnexion',
          style: 'destructive',
          onPress: async () => {
            await logout();
          },
        },
      ]
    );
  };

  const menuItems = [
    { icon: '👤', title: 'Informations personnelles', subtitle: 'Modifier votre profil' },
    { icon: '🔐', title: 'Sécurité', subtitle: 'Mot de passe et sécurité' },
    { icon: '🎫', title: 'Mes codes', subtitle: 'Codes CSS Privilèges générés' },
    { icon: '⭐', title: 'Points de fidélité', subtitle: `${user?.loyalty_points || 0} points` },
    { icon: '⚙️', title: 'Paramètres', subtitle: 'Préférences et notifications' },
  ];

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {/* Profile Header */}
      <Card variant="gold" padding="lg" style={styles.headerCard}>
        <View style={styles.profileHeader}>
          <View style={styles.avatarContainer}>
            <Text style={styles.avatar}>{userTypeInfo.icon}</Text>
          </View>
          <View style={styles.profileInfo}>
            <Text style={styles.userName}>{user?.name}</Text>
            <Text style={styles.userEmail}>{user?.email}</Text>
          </View>
        </View>

        <View style={styles.statsContainer}>
          <View style={styles.statItem}>
            <Text style={styles.statIcon}>{userTypeInfo.icon}</Text>
            <Text style={styles.statValue}>{userTypeInfo.name}</Text>
            <Text style={styles.statLabel}>Type de compte</Text>
          </View>

          <View style={styles.statDivider} />

          <View style={styles.statItem}>
            <Text style={styles.statIcon}>{loyaltyLevel.icon}</Text>
            <Text style={styles.statValue}>{loyaltyLevel.name}</Text>
            <Text style={styles.statLabel}>Niveau fidélité</Text>
          </View>
        </View>
      </Card>

      {/* Points Card */}
      <Card padding="lg" style={styles.pointsCard}>
        <View style={styles.pointsHeader}>
          <Text style={styles.pointsTitle}>Points de fidélité</Text>
          <Text style={styles.pointsValue}>{user?.loyalty_points || 0}</Text>
        </View>
        <Text style={styles.pointsSubtext}>
          Gagnez 10% de points sur chaque achat avec un code CSS Privilèges
        </Text>
      </Card>

      {/* Menu Items */}
      <View style={styles.menuSection}>
        {menuItems.map((item, index) => (
          <TouchableOpacity
            key={index}
            style={styles.menuItem}
            onPress={() =>
              Alert.alert(item.title, 'Fonctionnalité à venir dans une prochaine mise à jour!')
            }
            activeOpacity={0.7}
          >
            <View style={styles.menuItemLeft}>
              <Text style={styles.menuItemIcon}>{item.icon}</Text>
              <View>
                <Text style={styles.menuItemTitle}>{item.title}</Text>
                <Text style={styles.menuItemSubtitle}>{item.subtitle}</Text>
              </View>
            </View>
            <Text style={styles.menuItemArrow}>›</Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Upgrade Section (for free users) */}
      {user?.user_type === 'free' && (
        <Card padding="lg" style={styles.upgradeCard}>
          <Text style={styles.upgradeTitle}>Passer à Premium</Text>
          <Text style={styles.upgradeText}>
            Débloquez tout le contenu et profitez des réductions CSS Privilèges
          </Text>
          <Button
            title="Découvrir Premium - 15 TND/mois"
            variant="secondary"
            fullWidth
            onPress={() =>
              Alert.alert(
                'Premium',
                'Fonctionnalité de paiement à venir!\n\nAvantages Premium:\n' +
                  '• Contenu HD/4K\n' +
                  '• Réductions CSS Privilèges 10-15%\n' +
                  '• Génération codes QR/Promo/NFC\n' +
                  '• Points de fidélité\n' +
                  '• Support prioritaire'
              )
            }
            style={styles.upgradeButton}
          />
        </Card>
      )}

      {/* Logout Button */}
      <Button
        title="Se déconnecter"
        variant="danger"
        fullWidth
        onPress={handleLogout}
        style={styles.logoutButton}
      />

      {/* App Info */}
      <View style={styles.appInfo}>
        <Text style={styles.appInfoText}>CSS Platform Mobile v1.3.0</Text>
        <Text style={styles.appInfoText}>يا CSS يا نجوم السما ⚽</Text>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.backgroundSecondary,
  },
  content: {
    padding: SPACING.lg,
  },
  headerCard: {
    marginBottom: SPACING.lg,
  },
  profileHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.lg,
  },
  avatarContainer: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: COLORS.white,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: SPACING.md,
  },
  avatar: {
    fontSize: 40,
  },
  profileInfo: {
    flex: 1,
  },
  userName: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.xs,
  },
  userEmail: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingTop: SPACING.lg,
    borderTopWidth: 1,
    borderTopColor: COLORS.gray300,
  },
  statItem: {
    alignItems: 'center',
  },
  statIcon: {
    fontSize: 32,
    marginBottom: SPACING.xs,
  },
  statValue: {
    fontSize: FONT_SIZES.lg,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.xs,
  },
  statLabel: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray700,
  },
  statDivider: {
    width: 1,
    backgroundColor: COLORS.gray300,
  },
  pointsCard: {
    marginBottom: SPACING.lg,
  },
  pointsHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  pointsTitle: {
    fontSize: FONT_SIZES.lg,
    fontWeight: FONT_WEIGHTS.semibold,
    color: COLORS.black,
  },
  pointsValue: {
    fontSize: FONT_SIZES.xxxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.gold,
  },
  pointsSubtext: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
  },
  menuSection: {
    backgroundColor: COLORS.white,
    borderRadius: 12,
    marginBottom: SPACING.lg,
    overflow: 'hidden',
  },
  menuItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.gray200,
  },
  menuItemLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  menuItemIcon: {
    fontSize: 24,
    marginRight: SPACING.md,
  },
  menuItemTitle: {
    fontSize: FONT_SIZES.md,
    fontWeight: FONT_WEIGHTS.medium,
    color: COLORS.black,
    marginBottom: SPACING.xs,
  },
  menuItemSubtitle: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
  },
  menuItemArrow: {
    fontSize: 24,
    color: COLORS.gray400,
  },
  upgradeCard: {
    marginBottom: SPACING.lg,
  },
  upgradeTitle: {
    fontSize: FONT_SIZES.xl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.sm,
  },
  upgradeText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
    marginBottom: SPACING.md,
  },
  upgradeButton: {
    marginTop: SPACING.sm,
  },
  logoutButton: {
    marginBottom: SPACING.xl,
  },
  appInfo: {
    alignItems: 'center',
    paddingVertical: SPACING.lg,
  },
  appInfoText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray500,
    marginBottom: SPACING.xs,
  },
});

export default ProfileScreen;
