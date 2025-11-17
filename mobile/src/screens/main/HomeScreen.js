import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
} from 'react-native';
import { Card, Button } from '../../components/common';
import useAuthStore from '../../stores/authStore';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS } from '../../constants/theme';

const HomeScreen = ({ navigation }) => {
  const { user, getUserTypeInfo } = useAuthStore();
  const userTypeInfo = getUserTypeInfo();

  const quickActions = [
    { icon: '🏪', title: 'Partenaires', subtitle: '29 partenaires CSS Privilèges', screen: 'Partners' },
    { icon: '📰', title: 'Actualités', subtitle: 'Dernières news CSS', screen: 'Content' },
    { icon: '👥', title: 'Profil', subtitle: 'Mon compte', screen: 'Profile' },
  ];

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {/* Welcome Header */}
      <View style={styles.header}>
        <Text style={styles.welcome}>Bienvenue,</Text>
        <Text style={styles.userName}>{user?.name} {userTypeInfo.icon}</Text>
      </View>

      {/* User Status Card */}
      <Card variant="gold" padding="lg" style={styles.statusCard}>
        <View style={styles.statusContent}>
          <View>
            <Text style={styles.statusLabel}>Type de compte</Text>
            <Text style={styles.statusValue}>{userTypeInfo.name}</Text>
          </View>
          <View style={styles.statusRight}>
            <Text style={styles.statusIcon}>{userTypeInfo.icon}</Text>
          </View>
        </View>

        <View style={styles.pointsContainer}>
          <View style={styles.pointsItem}>
            <Text style={styles.pointsValue}>{user?.loyalty_points || 0}</Text>
            <Text style={styles.pointsLabel}>Points de fidélité</Text>
          </View>
        </View>
      </Card>

      {/* Quick Actions */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Actions rapides</Text>
        <View style={styles.actionsGrid}>
          {quickActions.map((action, index) => (
            <TouchableOpacity
              key={index}
              style={styles.actionCard}
              onPress={() => navigation.navigate(action.screen)}
            >
              <Text style={styles.actionIcon}>{action.icon}</Text>
              <Text style={styles.actionTitle}>{action.title}</Text>
              <Text style={styles.actionSubtitle}>{action.subtitle}</Text>
            </TouchableOpacity>
          ))}
        </View>
      </View>

      {/* CSS Privilèges Section */}
      <Card padding="lg" style={styles.privileges}>
        <Text style={styles.privilegesTitle}>
          Système <Text style={styles.privilegesGold}>CSS Privilèges</Text>
        </Text>
        <Text style={styles.privilegesText}>
          Profitez de réductions exclusives chez nos 29 partenaires à Sfax
        </Text>

        <View style={styles.privilegesStats}>
          <View style={styles.privilegesStat}>
            <Text style={styles.privilegesStatValue}>29</Text>
            <Text style={styles.privilegesStatLabel}>Partenaires</Text>
          </View>
          <View style={styles.privilegesStat}>
            <Text style={styles.privilegesStatValue}>60+</Text>
            <Text style={styles.privilegesStatLabel}>Offres</Text>
          </View>
          <View style={styles.privilegesStat}>
            <Text style={styles.privilegesStatValue}>
              {user?.user_type === 'socios' ? '25%' :
               user?.user_type === 'premium' ? '15%' : '0%'}
            </Text>
            <Text style={styles.privilegesStatLabel}>Réduction</Text>
          </View>
        </View>

        <Button
          title="Découvrir les partenaires"
          variant="secondary"
          fullWidth
          onPress={() => navigation.navigate('Partners')}
          style={styles.privilegesButton}
        />
      </Card>

      {/* Upgrade Section (for free users) */}
      {user?.user_type === 'free' && (
        <Card padding="lg" style={styles.upgrade}>
          <Text style={styles.upgradeTitle}>Passer à Premium</Text>
          <Text style={styles.upgradeText}>
            Débloquez le contenu premium HD, les codes CSS Privilèges et les points de fidélité
          </Text>
          <Text style={styles.upgradePrice}>15 TND / mois</Text>
          <Button
            title="Découvrir Premium"
            variant="outline"
            fullWidth
            style={styles.upgradeButton}
          />
        </Card>
      )}

      {/* Footer */}
      <View style={styles.footer}>
        <Text style={styles.footerText}>يا CSS يا نجوم السما ⚽</Text>
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
  header: {
    marginBottom: SPACING.xl,
  },
  welcome: {
    fontSize: FONT_SIZES.md,
    color: COLORS.gray600,
  },
  userName: {
    fontSize: FONT_SIZES.xxxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
  },
  statusCard: {
    marginBottom: SPACING.xl,
  },
  statusContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.lg,
  },
  statusLabel: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray800,
  },
  statusValue: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
  },
  statusRight: {
    alignItems: 'center',
  },
  statusIcon: {
    fontSize: 48,
  },
  pointsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  pointsItem: {
    alignItems: 'center',
  },
  pointsValue: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
  },
  pointsLabel: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray700,
  },
  section: {
    marginBottom: SPACING.xl,
  },
  sectionTitle: {
    fontSize: FONT_SIZES.xl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.md,
  },
  actionsGrid: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  actionCard: {
    flex: 1,
    backgroundColor: COLORS.white,
    padding: SPACING.md,
    borderRadius: 12,
    alignItems: 'center',
    marginHorizontal: SPACING.xs,
  },
  actionIcon: {
    fontSize: 32,
    marginBottom: SPACING.sm,
  },
  actionTitle: {
    fontSize: FONT_SIZES.sm,
    fontWeight: FONT_WEIGHTS.semibold,
    color: COLORS.black,
    marginBottom: SPACING.xs,
    textAlign: 'center',
  },
  actionSubtitle: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
    textAlign: 'center',
  },
  privileges: {
    marginBottom: SPACING.xl,
  },
  privilegesTitle: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.sm,
  },
  privilegesGold: {
    color: COLORS.gold,
  },
  privilegesText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
    marginBottom: SPACING.lg,
  },
  privilegesStats: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginBottom: SPACING.lg,
  },
  privilegesStat: {
    alignItems: 'center',
  },
  privilegesStatValue: {
    fontSize: FONT_SIZES.xxxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
  },
  privilegesStatLabel: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
  },
  privilegesButton: {
    marginTop: SPACING.md,
  },
  upgrade: {
    marginBottom: SPACING.xl,
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
  upgradePrice: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.gold,
    marginBottom: SPACING.lg,
  },
  upgradeButton: {
    marginTop: SPACING.sm,
  },
  footer: {
    alignItems: 'center',
    paddingVertical: SPACING.xl,
  },
  footerText: {
    fontSize: FONT_SIZES.lg,
    color: COLORS.gray600,
  },
});

export default HomeScreen;
