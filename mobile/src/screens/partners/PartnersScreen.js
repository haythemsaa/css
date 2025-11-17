import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Card, Button } from '../../components/common';
import useAuthStore from '../../stores/authStore';
import { partnersService } from '../../services/api';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS } from '../../constants/theme';

const PartnersScreen = ({ navigation }) => {
  const { user, isPremium } = useAuthStore();
  const [partners, setPartners] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedCategory, setSelectedCategory] = useState('all');

  const categories = [
    { value: 'all', label: 'Tous', icon: '🏪' },
    { value: 'restaurant', label: 'Restaurants', icon: '🍽️' },
    { value: 'shopping', label: 'Shopping', icon: '🛍️' },
    { value: 'sport', label: 'Sport', icon: '⚽' },
    { value: 'sante', label: 'Santé', icon: '🏥' },
  ];

  useEffect(() => {
    loadPartners();
  }, [selectedCategory]);

  const loadPartners = async () => {
    setLoading(true);
    try {
      const params = selectedCategory !== 'all' ? { category: selectedCategory } : {};
      const response = await partnersService.getPartners(params);

      if (response.data.success) {
        setPartners(response.data.data);
      }
    } catch (error) {
      console.error('Error loading partners:', error);
      Alert.alert('Erreur', 'Impossible de charger les partenaires');
    } finally {
      setLoading(false);
    }
  };

  const getDiscount = (partner) => {
    if (!user) return 0;

    switch (user.user_type) {
      case 'socios':
        return partner.reduction_value_socios || 0;
      case 'premium':
        return partner.reduction_value_premium || 0;
      default:
        return 0;
    }
  };

  const canGenerate = isPremium();

  return (
    <View style={styles.container}>
      {/* Header Info */}
      <View style={styles.header}>
        <Text style={styles.headerTitle}>CSS Privilèges Partners</Text>
        <Text style={styles.headerSubtitle}>
          {canGenerate
            ? `Profitez de réductions jusqu'à ${user?.user_type === 'socios' ? '25%' : '15%'}`
            : 'Découvrez nos partenaires (Premium requis pour générer des codes)'}
        </Text>
      </View>

      {/* Categories Filter */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        style={styles.categoriesScroll}
        contentContainerStyle={styles.categoriesContent}
      >
        {categories.map((category) => (
          <TouchableOpacity
            key={category.value}
            style={[
              styles.categoryChip,
              selectedCategory === category.value && styles.categoryChipActive,
            ]}
            onPress={() => setSelectedCategory(category.value)}
          >
            <Text style={styles.categoryIcon}>{category.icon}</Text>
            <Text
              style={[
                styles.categoryLabel,
                selectedCategory === category.value && styles.categoryLabelActive,
              ]}
            >
              {category.label}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Partners List */}
      <ScrollView
        style={styles.partnersList}
        contentContainerStyle={styles.partnersContent}
      >
        {loading ? (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color={COLORS.gold} />
            <Text style={styles.loadingText}>Chargement des partenaires...</Text>
          </View>
        ) : partners.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyIcon}>🏪</Text>
            <Text style={styles.emptyText}>Aucun partenaire trouvé</Text>
          </View>
        ) : (
          partners.map((partner) => {
            const discount = getDiscount(partner);

            return (
              <Card key={partner.id} padding="lg" style={styles.partnerCard}>
                <View style={styles.partnerHeader}>
                  <View style={styles.partnerInfo}>
                    <Text style={styles.partnerName}>{partner.name}</Text>
                    <Text style={styles.partnerCategory}>
                      {partner.category?.name || 'Non catégorisé'}
                    </Text>
                  </View>

                  {discount > 0 && (
                    <View style={styles.discountBadge}>
                      <Text style={styles.discountText}>-{discount}%</Text>
                    </View>
                  )}
                </View>

                {partner.address && (
                  <Text style={styles.partnerAddress}>
                    📍 {partner.address}, {partner.city}
                  </Text>
                )}

                {partner.description && (
                  <Text style={styles.partnerDescription} numberOfLines={2}>
                    {partner.description}
                  </Text>
                )}

                <View style={styles.partnerFooter}>
                  <Text style={styles.offersCount}>
                    {partner.active_offers_count || 0} offre(s) active(s)
                  </Text>

                  <Button
                    title="Voir les offres"
                    variant="secondary"
                    size="sm"
                    onPress={() => navigation.navigate('PartnerDetail', { partnerSlug: partner.slug })}
                  />
                </View>
              </Card>
            );
          })
        )}
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.backgroundSecondary,
  },
  header: {
    backgroundColor: COLORS.black,
    padding: SPACING.lg,
  },
  headerTitle: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.gold,
    marginBottom: SPACING.xs,
  },
  headerSubtitle: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray300,
  },
  categoriesScroll: {
    backgroundColor: COLORS.white,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.gray200,
  },
  categoriesContent: {
    padding: SPACING.md,
    gap: SPACING.sm,
  },
  categoryChip: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: SPACING.md,
    paddingVertical: SPACING.sm,
    borderRadius: 20,
    backgroundColor: COLORS.gray100,
    marginRight: SPACING.sm,
  },
  categoryChipActive: {
    backgroundColor: COLORS.gold,
  },
  categoryIcon: {
    fontSize: 16,
    marginRight: SPACING.xs,
  },
  categoryLabel: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    fontWeight: FONT_WEIGHTS.medium,
  },
  categoryLabelActive: {
    color: COLORS.black,
    fontWeight: FONT_WEIGHTS.bold,
  },
  partnersList: {
    flex: 1,
  },
  partnersContent: {
    padding: SPACING.lg,
  },
  loadingContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: SPACING.xxxl,
  },
  loadingText: {
    marginTop: SPACING.md,
    color: COLORS.gray600,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: SPACING.xxxl,
  },
  emptyIcon: {
    fontSize: 64,
    marginBottom: SPACING.md,
  },
  emptyText: {
    fontSize: FONT_SIZES.md,
    color: COLORS.gray600,
  },
  partnerCard: {
    marginBottom: SPACING.md,
  },
  partnerHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: SPACING.sm,
  },
  partnerInfo: {
    flex: 1,
  },
  partnerName: {
    fontSize: FONT_SIZES.lg,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.xs,
  },
  partnerCategory: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
  },
  discountBadge: {
    backgroundColor: COLORS.gold,
    paddingHorizontal: SPACING.sm,
    paddingVertical: SPACING.xs,
    borderRadius: 6,
  },
  discountText: {
    fontSize: FONT_SIZES.sm,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
  },
  partnerAddress: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
    marginBottom: SPACING.sm,
  },
  partnerDescription: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    marginBottom: SPACING.md,
  },
  partnerFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingTop: SPACING.md,
    borderTopWidth: 1,
    borderTopColor: COLORS.gray200,
  },
  offersCount: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
  },
});

export default PartnersScreen;
