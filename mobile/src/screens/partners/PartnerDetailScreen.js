import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  ActivityIndicator,
  Alert,
  TouchableOpacity,
  Modal,
} from 'react-native';
import { Card, Button } from '../../components/common';
import useAuthStore from '../../stores/authStore';
import { partnersService, offersService, codesService } from '../../services/api';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS, BORDER_RADIUS } from '../../constants/theme';

const PartnerDetailScreen = ({ route, navigation }) => {
  const { partnerSlug } = route.params;
  const { user, isPremium } = useAuthStore();

  const [partner, setPartner] = useState(null);
  const [offers, setOffers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [generatingCode, setGeneratingCode] = useState(false);

  // Modal state
  const [showCodeModal, setShowCodeModal] = useState(false);
  const [selectedOffer, setSelectedOffer] = useState(null);
  const [selectedCodeType, setSelectedCodeType] = useState('qr');
  const [generatedCode, setGeneratedCode] = useState(null);

  useEffect(() => {
    loadPartnerDetails();
  }, [partnerSlug]);

  const loadPartnerDetails = async () => {
    setLoading(true);
    try {
      const [partnerRes, offersRes] = await Promise.all([
        partnersService.getPartner(partnerSlug),
        offersService.getOffers(partnerSlug),
      ]);

      if (partnerRes.data.success) {
        setPartner(partnerRes.data.data);
      }

      if (offersRes.data.success) {
        setOffers(offersRes.data.data);
      }
    } catch (error) {
      console.error('Error loading partner details:', error);
      Alert.alert('Erreur', 'Impossible de charger les détails du partenaire');
    } finally {
      setLoading(false);
    }
  };

  const getDiscount = () => {
    if (!partner || !user) return 0;

    switch (user.user_type) {
      case 'socios':
        return partner.reduction_value_socios || 0;
      case 'premium':
        return partner.reduction_value_premium || 0;
      default:
        return 0;
    }
  };

  const handleGenerateCode = (offer) => {
    if (!isPremium()) {
      Alert.alert(
        'Premium requis',
        'Vous devez avoir un compte Premium ou Socios pour générer des codes de réduction.\n\nPassez à Premium pour 15 TND/mois!',
        [{ text: 'OK' }]
      );
      return;
    }

    setSelectedOffer(offer);
    setSelectedCodeType('qr');
    setGeneratedCode(null);
    setShowCodeModal(true);
  };

  const confirmGenerateCode = async () => {
    if (!selectedOffer) return;

    setGeneratingCode(true);

    try {
      const response = await codesService.generateCode(
        selectedOffer.slug,
        selectedCodeType
      );

      if (response.data.success) {
        setGeneratedCode(response.data.data);
        Alert.alert(
          'Code généré!',
          `Votre code ${selectedCodeType.toUpperCase()} a été généré avec succès.\n\nCode: ${response.data.data.code}\nValide jusqu'au: ${new Date(response.data.data.expires_at).toLocaleDateString('fr-FR')}`,
          [
            {
              text: 'Voir mes codes',
              onPress: () => {
                setShowCodeModal(false);
                navigation.navigate('MyCodes');
              },
            },
            { text: 'OK', onPress: () => setShowCodeModal(false) },
          ]
        );
      }
    } catch (error) {
      console.error('Error generating code:', error);
      Alert.alert(
        'Erreur',
        error.response?.data?.message || 'Impossible de générer le code'
      );
    } finally {
      setGeneratingCode(false);
    }
  };

  const isOfferValid = (offer) => {
    const now = new Date();
    const validFrom = new Date(offer.valid_from);
    const validUntil = new Date(offer.valid_until);

    return (
      offer.status === 'active' &&
      now >= validFrom &&
      now <= validUntil &&
      (offer.stock_available === null || offer.stock_remaining > 0)
    );
  };

  const getStockPercentage = (offer) => {
    if (offer.stock_available === null) return 100;
    return (offer.stock_remaining / offer.stock_available) * 100;
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.gold} />
        <Text style={styles.loadingText}>Chargement...</Text>
      </View>
    );
  }

  if (!partner) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Partenaire introuvable</Text>
        <Button
          title="Retour"
          variant="outline"
          onPress={() => navigation.goBack()}
        />
      </View>
    );
  }

  const discount = getDiscount();

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.content}>
        {/* Partner Header */}
        <Card padding="lg" style={styles.headerCard}>
          <View style={styles.headerContent}>
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

          {partner.description && (
            <Text style={styles.description}>{partner.description}</Text>
          )}

          {partner.address && (
            <View style={styles.infoRow}>
              <Text style={styles.infoIcon}>📍</Text>
              <Text style={styles.infoText}>
                {partner.address}, {partner.city}
              </Text>
            </View>
          )}

          {partner.phone && (
            <View style={styles.infoRow}>
              <Text style={styles.infoIcon}>📞</Text>
              <Text style={styles.infoText}>{partner.phone}</Text>
            </View>
          )}
        </Card>

        {/* Offers Section */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>
            Offres disponibles ({offers.length})
          </Text>

          {offers.length === 0 ? (
            <Card padding="lg">
              <Text style={styles.emptyText}>Aucune offre disponible pour le moment</Text>
            </Card>
          ) : (
            offers.map((offer) => {
              const valid = isOfferValid(offer);
              const stockPercent = getStockPercentage(offer);
              const isLowStock = stockPercent < 20 && stockPercent > 0;

              return (
                <Card key={offer.id} padding="lg" style={styles.offerCard}>
                  <View style={styles.offerHeader}>
                    <Text style={styles.offerTitle}>{offer.title}</Text>
                    {offer.type !== 'standard' && (
                      <View style={[
                        styles.offerTypeBadge,
                        offer.type === 'flash' && styles.flashBadge,
                      ]}>
                        <Text style={styles.offerTypeText}>
                          {offer.type === 'flash' ? '⚡ Flash' : '🎁 Exclusif'}
                        </Text>
                      </View>
                    )}
                  </View>

                  {offer.description && (
                    <Text style={styles.offerDescription}>{offer.description}</Text>
                  )}

                  <View style={styles.offerMeta}>
                    <Text style={styles.offerDiscount}>
                      Réduction: {offer.discount_value}
                      {offer.discount_type === 'percentage' ? '%' : ' TND'}
                    </Text>

                    {offer.stock_available && (
                      <Text style={[
                        styles.offerStock,
                        isLowStock && styles.offerStockLow,
                      ]}>
                        Stock: {offer.stock_remaining}/{offer.stock_available}
                      </Text>
                    )}
                  </View>

                  <Text style={styles.offerValidity}>
                    Valide jusqu'au{' '}
                    {new Date(offer.valid_until).toLocaleDateString('fr-FR')}
                  </Text>

                  <Button
                    title={isPremium() ? 'Générer un code' : 'Premium requis'}
                    variant={valid ? 'secondary' : 'outline'}
                    fullWidth
                    disabled={!valid}
                    onPress={() => handleGenerateCode(offer)}
                    style={styles.generateButton}
                  />

                  {!valid && (
                    <Text style={styles.invalidText}>
                      {offer.stock_remaining === 0
                        ? 'Stock épuisé'
                        : offer.status !== 'active'
                        ? 'Offre inactive'
                        : 'Offre expirée ou pas encore active'}
                    </Text>
                  )}
                </Card>
              );
            })
          )}
        </View>
      </ScrollView>

      {/* Code Generation Modal */}
      <Modal
        visible={showCodeModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setShowCodeModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Générer un code</Text>

            <Text style={styles.modalOfferTitle}>{selectedOffer?.title}</Text>

            <Text style={styles.modalLabel}>Type de code:</Text>
            <View style={styles.codeTypeContainer}>
              {['qr', 'promo', 'nfc'].map((type) => (
                <TouchableOpacity
                  key={type}
                  style={[
                    styles.codeTypeButton,
                    selectedCodeType === type && styles.codeTypeButtonActive,
                  ]}
                  onPress={() => setSelectedCodeType(type)}
                >
                  <Text style={styles.codeTypeIcon}>
                    {type === 'qr' ? '📱' : type === 'promo' ? '🎫' : '💳'}
                  </Text>
                  <Text
                    style={[
                      styles.codeTypeLabel,
                      selectedCodeType === type && styles.codeTypeLabelActive,
                    ]}
                  >
                    {type.toUpperCase()}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>

            <View style={styles.modalButtons}>
              <Button
                title="Annuler"
                variant="outline"
                onPress={() => setShowCodeModal(false)}
                style={styles.modalButton}
              />
              <Button
                title="Générer"
                variant="secondary"
                onPress={confirmGenerateCode}
                loading={generatingCode}
                style={styles.modalButton}
              />
            </View>
          </View>
        </View>
      </Modal>
    </View>
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
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: SPACING.md,
    color: COLORS.gray600,
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: SPACING.xl,
  },
  errorText: {
    fontSize: FONT_SIZES.lg,
    color: COLORS.gray600,
    marginBottom: SPACING.lg,
  },
  headerCard: {
    marginBottom: SPACING.lg,
  },
  headerContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: SPACING.md,
  },
  partnerInfo: {
    flex: 1,
  },
  partnerName: {
    fontSize: FONT_SIZES.xxl,
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
    paddingHorizontal: SPACING.md,
    paddingVertical: SPACING.sm,
    borderRadius: BORDER_RADIUS.md,
  },
  discountText: {
    fontSize: FONT_SIZES.lg,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
  },
  description: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    marginBottom: SPACING.md,
    lineHeight: 20,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  infoIcon: {
    fontSize: 16,
    marginRight: SPACING.sm,
  },
  infoText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    flex: 1,
  },
  section: {
    marginBottom: SPACING.lg,
  },
  sectionTitle: {
    fontSize: FONT_SIZES.xl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.md,
  },
  emptyText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
    textAlign: 'center',
  },
  offerCard: {
    marginBottom: SPACING.md,
  },
  offerHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: SPACING.sm,
  },
  offerTitle: {
    fontSize: FONT_SIZES.lg,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    flex: 1,
  },
  offerTypeBadge: {
    backgroundColor: COLORS.info,
    paddingHorizontal: SPACING.sm,
    paddingVertical: SPACING.xs,
    borderRadius: BORDER_RADIUS.sm,
  },
  flashBadge: {
    backgroundColor: COLORS.warning,
  },
  offerTypeText: {
    fontSize: FONT_SIZES.xs,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.white,
  },
  offerDescription: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    marginBottom: SPACING.md,
  },
  offerMeta: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: SPACING.sm,
  },
  offerDiscount: {
    fontSize: FONT_SIZES.md,
    fontWeight: FONT_WEIGHTS.semibold,
    color: COLORS.gold,
  },
  offerStock: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
  },
  offerStockLow: {
    color: COLORS.error,
    fontWeight: FONT_WEIGHTS.semibold,
  },
  offerValidity: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray500,
    marginBottom: SPACING.md,
  },
  generateButton: {
    marginTop: SPACING.sm,
  },
  invalidText: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.error,
    textAlign: 'center',
    marginTop: SPACING.sm,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: SPACING.xl,
  },
  modalContent: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.xl,
    width: '100%',
    maxWidth: 400,
  },
  modalTitle: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.md,
    textAlign: 'center',
  },
  modalOfferTitle: {
    fontSize: FONT_SIZES.md,
    color: COLORS.gray700,
    marginBottom: SPACING.lg,
    textAlign: 'center',
  },
  modalLabel: {
    fontSize: FONT_SIZES.sm,
    fontWeight: FONT_WEIGHTS.medium,
    color: COLORS.gray700,
    marginBottom: SPACING.sm,
  },
  codeTypeContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: SPACING.xl,
  },
  codeTypeButton: {
    flex: 1,
    alignItems: 'center',
    padding: SPACING.md,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.gray100,
    marginHorizontal: SPACING.xs,
  },
  codeTypeButtonActive: {
    backgroundColor: COLORS.gold,
  },
  codeTypeIcon: {
    fontSize: 32,
    marginBottom: SPACING.xs,
  },
  codeTypeLabel: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray700,
    fontWeight: FONT_WEIGHTS.medium,
  },
  codeTypeLabelActive: {
    color: COLORS.black,
    fontWeight: FONT_WEIGHTS.bold,
  },
  modalButtons: {
    flexDirection: 'row',
    gap: SPACING.md,
  },
  modalButton: {
    flex: 1,
  },
});

export default PartnerDetailScreen;
