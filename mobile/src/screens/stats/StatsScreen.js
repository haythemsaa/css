import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Dimensions,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { COLORS } from '../../constants/theme';
import statsService from '../../services/statsService';

const { width } = Dimensions.get('window');

const StatsScreen = () => {
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [globalStats, setGlobalStats] = useState(null);
  const [codesStats, setCodesStats] = useState(null);
  const [savingsHistory, setSavingsHistory] = useState(null);
  const [activityStats, setActivityStats] = useState(null);
  const [topPartners, setTopPartners] = useState([]);
  const [selectedPeriod, setSelectedPeriod] = useState('month');

  useEffect(() => {
    loadStats();
  }, []);

  useEffect(() => {
    if (globalStats) {
      loadSavingsHistory(selectedPeriod);
    }
  }, [selectedPeriod]);

  const loadStats = async () => {
    try {
      setLoading(true);
      const [global, codes, savings, activity, partners] = await Promise.all([
        statsService.getGlobalStats(),
        statsService.getCodesStats(),
        statsService.getSavingsHistory('month'),
        statsService.getActivityStats(),
        statsService.getTopPartners(),
      ]);

      setGlobalStats(global);
      setCodesStats(codes);
      setSavingsHistory(savings);
      setActivityStats(activity);
      setTopPartners(partners);
    } catch (error) {
      console.error('Error loading stats:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadSavingsHistory = async (period) => {
    try {
      const data = await statsService.getSavingsHistory(period);
      setSavingsHistory(data);
    } catch (error) {
      console.error('Error loading savings history:', error);
    }
  };

  const handleRefresh = async () => {
    setRefreshing(true);
    await loadStats();
    setRefreshing(false);
  };

  const renderStatCard = (icon, label, value, color, suffix = '') => (
    <View style={[styles.statCard, { borderLeftColor: color }]}>
      <Ionicons name={icon} size={24} color={color} />
      <Text style={styles.statValue}>{value}{suffix}</Text>
      <Text style={styles.statLabel}>{label}</Text>
    </View>
  );

  const renderProgressBar = (current, total, color) => {
    const percentage = (current / total) * 100;
    return (
      <View style={styles.progressBarContainer}>
        <View style={styles.progressBarBackground}>
          <View style={[styles.progressBarFill, { width: `${percentage}%`, backgroundColor: color }]} />
        </View>
        <Text style={styles.progressText}>{current} / {total}</Text>
      </View>
    );
  };

  const renderSimpleBarChart = () => {
    if (!savingsHistory) return null;

    const maxValue = Math.max(...savingsHistory.data);

    return (
      <View style={styles.chartContainer}>
        <View style={styles.chartBars}>
          {savingsHistory.data.map((value, index) => {
            const height = (value / maxValue) * 150;
            return (
              <View key={index} style={styles.barWrapper}>
                <View style={styles.barContainer}>
                  <View style={[styles.bar, { height, backgroundColor: COLORS.gold }]} />
                </View>
                <Text style={styles.barLabel}>{savingsHistory.labels[index]}</Text>
                <Text style={styles.barValue}>{value.toFixed(0)}€</Text>
              </View>
            );
          })}
        </View>
      </View>
    );
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Chargement des statistiques...</Text>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {/* Vue d'ensemble */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>📊 Vue d'ensemble</Text>

        <View style={styles.statsGrid}>
          {renderStatCard('barcode-outline', 'Codes générés', globalStats?.totalCodes || 0, COLORS.primary)}
          {renderStatCard('checkmark-done', 'Codes utilisés', globalStats?.usedCodes || 0, COLORS.success)}
          {renderStatCard('cash-outline', 'Économies totales', globalStats?.totalSavings || 0, COLORS.gold, ' TND')}
          {renderStatCard('trending-up', 'Moyenne/code', globalStats?.averageSaving || 0, COLORS.info, ' TND')}
        </View>
      </View>

      {/* Niveau de fidélité */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>🏆 Programme de fidélité</Text>

        <View style={styles.loyaltyCard}>
          <View style={styles.loyaltyHeader}>
            <View>
              <Text style={styles.loyaltyLevel}>{globalStats?.currentLevel || 'Gold'}</Text>
              <Text style={styles.loyaltyPoints}>{globalStats?.loyaltyPoints || 0} points</Text>
            </View>
            <View style={styles.levelBadge}>
              <Ionicons name="trophy" size={32} color={COLORS.gold} />
            </View>
          </View>

          <Text style={styles.loyaltyNextLevel}>
            Prochain niveau : {globalStats?.nextLevel || 'Platinum'}
          </Text>
          {renderProgressBar(
            globalStats?.loyaltyPoints || 0,
            (globalStats?.loyaltyPoints || 0) + (globalStats?.pointsToNextLevel || 750),
            COLORS.gold
          )}
        </View>
      </View>

      {/* Historique d'économies */}
      <View style={styles.section}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>💰 Historique d'économies</Text>
          <View style={styles.periodSelector}>
            {['week', 'month', 'year'].map((period) => (
              <TouchableOpacity
                key={period}
                style={[
                  styles.periodButton,
                  selectedPeriod === period && styles.periodButtonActive,
                ]}
                onPress={() => setSelectedPeriod(period)}
              >
                <Text
                  style={[
                    styles.periodButtonText,
                    selectedPeriod === period && styles.periodButtonTextActive,
                  ]}
                >
                  {period === 'week' ? 'Sem' : period === 'month' ? 'Mois' : 'Année'}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {renderSimpleBarChart()}
      </View>

      {/* Répartition des codes */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>📈 Répartition des codes</Text>

        <View style={styles.distributionGrid}>
          {codesStats?.byType.map((item, index) => (
            <View key={index} style={styles.distributionCard}>
              <View style={styles.distributionHeader}>
                <Text style={styles.distributionType}>{item.type.toUpperCase()}</Text>
                <Text style={styles.distributionPercentage}>{item.percentage.toFixed(1)}%</Text>
              </View>
              <Text style={styles.distributionCount}>{item.count} codes</Text>
            </View>
          ))}
        </View>
      </View>

      {/* Top partenaires */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>⭐ Top Partenaires</Text>

        {topPartners.map((partner, index) => (
          <View key={partner.id} style={styles.partnerCard}>
            <View style={styles.partnerRank}>
              <Text style={styles.partnerRankText}>#{index + 1}</Text>
            </View>
            <View style={styles.partnerIcon}>
              <Text style={styles.partnerEmoji}>{partner.logo}</Text>
            </View>
            <View style={styles.partnerInfo}>
              <Text style={styles.partnerName}>{partner.name}</Text>
              <Text style={styles.partnerCategory}>{partner.category}</Text>
            </View>
            <View style={styles.partnerStats}>
              <Text style={styles.partnerUses}>{partner.uses} fois</Text>
              <Text style={styles.partnerSavings}>{partner.savings.toFixed(2)} TND</Text>
            </View>
          </View>
        ))}
      </View>

      {/* Activités récentes */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>📋 Activités récentes</Text>

        {activityStats?.recentActivities.map((activity) => (
          <View key={activity.id} style={styles.activityCard}>
            <View style={styles.activityIcon}>
              <Ionicons
                name={
                  activity.type === 'code_used' ? 'checkmark-circle' :
                  activity.type === 'code_generated' ? 'qr-code' :
                  activity.type === 'content_viewed' ? 'eye' :
                  activity.type === 'points_earned' ? 'star' :
                  'arrow-up-circle'
                }
                size={24}
                color={
                  activity.type === 'code_used' ? COLORS.success :
                  activity.type === 'code_generated' ? COLORS.primary :
                  activity.type === 'content_viewed' ? COLORS.info :
                  COLORS.gold
                }
              />
            </View>
            <View style={styles.activityContent}>
              <Text style={styles.activityDescription}>{activity.description}</Text>
              <Text style={styles.activityDate}>
                {new Date(activity.date).toLocaleDateString('fr-FR', {
                  day: 'numeric',
                  month: 'short',
                })}
              </Text>
            </View>
            {activity.amount && (
              <Text style={styles.activityAmount}>
                +{activity.amount}{activity.type === 'code_used' ? ' TND' : ' pts'}
              </Text>
            )}
          </View>
        ))}
      </View>

      <TouchableOpacity style={styles.refreshButton} onPress={handleRefresh} disabled={refreshing}>
        {refreshing ? (
          <ActivityIndicator color={COLORS.white} />
        ) : (
          <>
            <Ionicons name="refresh" size={20} color={COLORS.white} />
            <Text style={styles.refreshButtonText}>Actualiser</Text>
          </>
        )}
      </TouchableOpacity>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  content: {
    padding: 15,
    paddingBottom: 30,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.background,
  },
  loadingText: {
    marginTop: 15,
    fontSize: 16,
    color: COLORS.textLight,
  },
  section: {
    marginBottom: 25,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 15,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: COLORS.text,
    marginBottom: 15,
  },
  statsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  statCard: {
    width: (width - 45) / 2,
    backgroundColor: COLORS.white,
    padding: 15,
    borderRadius: 12,
    marginBottom: 15,
    borderLeftWidth: 4,
    alignItems: 'center',
  },
  statValue: {
    fontSize: 24,
    fontWeight: '700',
    color: COLORS.text,
    marginTop: 10,
  },
  statLabel: {
    fontSize: 12,
    color: COLORS.textLight,
    marginTop: 5,
    textAlign: 'center',
  },
  loyaltyCard: {
    backgroundColor: COLORS.white,
    padding: 20,
    borderRadius: 12,
  },
  loyaltyHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 15,
  },
  loyaltyLevel: {
    fontSize: 24,
    fontWeight: '700',
    color: COLORS.text,
  },
  loyaltyPoints: {
    fontSize: 16,
    color: COLORS.gold,
    marginTop: 5,
  },
  levelBadge: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: COLORS.goldLight,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loyaltyNextLevel: {
    fontSize: 14,
    color: COLORS.textLight,
    marginBottom: 10,
  },
  progressBarContainer: {
    marginTop: 10,
  },
  progressBarBackground: {
    height: 8,
    backgroundColor: COLORS.border,
    borderRadius: 4,
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    borderRadius: 4,
  },
  progressText: {
    fontSize: 12,
    color: COLORS.textLight,
    marginTop: 5,
    textAlign: 'right',
  },
  periodSelector: {
    flexDirection: 'row',
    backgroundColor: COLORS.white,
    borderRadius: 8,
    padding: 3,
  },
  periodButton: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 6,
  },
  periodButtonActive: {
    backgroundColor: COLORS.primary,
  },
  periodButtonText: {
    fontSize: 12,
    color: COLORS.textLight,
  },
  periodButtonTextActive: {
    color: COLORS.white,
    fontWeight: '600',
  },
  chartContainer: {
    backgroundColor: COLORS.white,
    padding: 15,
    borderRadius: 12,
  },
  chartBars: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-end',
    height: 180,
  },
  barWrapper: {
    flex: 1,
    alignItems: 'center',
  },
  barContainer: {
    width: '80%',
    justifyContent: 'flex-end',
    alignItems: 'center',
  },
  bar: {
    width: '100%',
    borderTopLeftRadius: 4,
    borderTopRightRadius: 4,
  },
  barLabel: {
    fontSize: 11,
    color: COLORS.textLight,
    marginTop: 5,
  },
  barValue: {
    fontSize: 10,
    color: COLORS.text,
    fontWeight: '600',
  },
  distributionGrid: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  distributionCard: {
    flex: 1,
    backgroundColor: COLORS.white,
    padding: 15,
    borderRadius: 12,
    marginHorizontal: 5,
  },
  distributionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  distributionType: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.primary,
  },
  distributionPercentage: {
    fontSize: 16,
    fontWeight: '700',
    color: COLORS.text,
  },
  distributionCount: {
    fontSize: 14,
    color: COLORS.textLight,
  },
  partnerCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    padding: 15,
    borderRadius: 12,
    marginBottom: 10,
  },
  partnerRank: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: COLORS.goldLight,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 10,
  },
  partnerRankText: {
    fontSize: 14,
    fontWeight: '700',
    color: COLORS.gold,
  },
  partnerIcon: {
    marginRight: 12,
  },
  partnerEmoji: {
    fontSize: 32,
  },
  partnerInfo: {
    flex: 1,
  },
  partnerName: {
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.text,
    marginBottom: 3,
  },
  partnerCategory: {
    fontSize: 12,
    color: COLORS.textLight,
  },
  partnerStats: {
    alignItems: 'flex-end',
  },
  partnerUses: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 3,
  },
  partnerSavings: {
    fontSize: 13,
    color: COLORS.gold,
  },
  activityCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    padding: 15,
    borderRadius: 12,
    marginBottom: 10,
  },
  activityIcon: {
    marginRight: 12,
  },
  activityContent: {
    flex: 1,
  },
  activityDescription: {
    fontSize: 14,
    color: COLORS.text,
    marginBottom: 3,
  },
  activityDate: {
    fontSize: 12,
    color: COLORS.textLight,
  },
  activityAmount: {
    fontSize: 14,
    fontWeight: '700',
    color: COLORS.success,
  },
  refreshButton: {
    flexDirection: 'row',
    backgroundColor: COLORS.primary,
    padding: 15,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 10,
  },
  refreshButtonText: {
    color: COLORS.white,
    fontSize: 16,
    fontWeight: '600',
    marginLeft: 8,
  },
});

export default StatsScreen;
