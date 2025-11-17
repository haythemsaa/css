import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  RefreshControl,
  TouchableOpacity,
  Alert,
} from 'react-native';
import { Card, Button } from '../../components/common';
import { codesService } from '../../services/api';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS, BORDER_RADIUS } from '../../constants/theme';

const MyCodesScreen = ({ navigation }) => {
  const [codes, setCodes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [filter, setFilter] = useState('active'); // active, used, expired, all

  useEffect(() => {
    loadCodes();
  }, [filter]);

  const loadCodes = async () => {
    try {
      const params = filter !== 'all' ? { status: filter } : {};
      const response = await codesService.getMyCodes(params);

      if (response.data.success) {
        setCodes(response.data.data);
      }
    } catch (error) {
      console.error('Error loading codes:', error);
      if (!refreshing) {
        Alert.alert('Erreur', 'Impossible de charger vos codes');
      }
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadCodes();
  };

  const getCodeStatus = (code) => {
    const now = new Date();
    const expiresAt = new Date(code.expires_at);

    if (code.used_at) {
      return { label: 'Utilisé', color: COLORS.gray500, icon: '✓' };
    }

    if (now > expiresAt) {
      return { label: 'Expiré', color: COLORS.error, icon: '⏰' };
    }

    return { label: 'Actif', color: COLORS.success, icon: '✓' };
  };

  const getCodeTypeIcon = (type) => {
    switch (type) {
      case 'qr':
        return '📱';
      case 'promo':
        return '🎫';
      case 'nfc':
        return '💳';
      default:
        return '📝';
    }
  };

  const handleCodePress = (code) => {
    const status = getCodeStatus(code);

    Alert.alert(
      `Code ${code.type.toUpperCase()}`,
      `Code: ${code.code}\n` +
        `Offre: ${code.offer?.title || 'N/A'}\n` +
        `Partenaire: ${code.offer?.partner?.name || 'N/A'}\n` +
        `Statut: ${status.label}\n` +
        `Expire le: ${new Date(code.expires_at).toLocaleDateString('fr-FR')}\n` +
        (code.used_at
          ? `\nUtilisé le: ${new Date(code.used_at).toLocaleString('fr-FR')}`
          : ''),
      [
        {
          text: 'Fermer',
          style: 'cancel',
        },
        ...(status.label === 'Actif'
          ? [
              {
                text: 'Voir le QR Code',
                onPress: () => {
                  // TODO: Navigate to QR display screen
                  Alert.alert('QR Code', 'Affichage du QR code à venir!');
                },
              },
            ]
          : []),
      ]
    );
  };

  const filterOptions = [
    { value: 'active', label: 'Actifs', icon: '✓' },
    { value: 'used', label: 'Utilisés', icon: '📌' },
    { value: 'expired', label: 'Expirés', icon: '⏰' },
    { value: 'all', label: 'Tous', icon: '📋' },
  ];

  return (
    <View style={styles.container}>
      {/* Filter Tabs */}
      <View style={styles.filterContainer}>
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.filterScroll}
        >
          {filterOptions.map((option) => (
            <TouchableOpacity
              key={option.value}
              style={[
                styles.filterChip,
                filter === option.value && styles.filterChipActive,
              ]}
              onPress={() => setFilter(option.value)}
            >
              <Text style={styles.filterIcon}>{option.icon}</Text>
              <Text
                style={[
                  styles.filterLabel,
                  filter === option.value && styles.filterLabelActive,
                ]}
              >
                {option.label}
              </Text>
            </TouchableOpacity>
          ))}
        </ScrollView>
      </View>

      {/* Codes List */}
      <ScrollView
        style={styles.codesList}
        contentContainerStyle={styles.codesContent}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            tintColor={COLORS.gold}
          />
        }
      >
        {loading && !refreshing ? (
          <View style={styles.loadingContainer}>
            <Text style={styles.loadingText}>Chargement...</Text>
          </View>
        ) : codes.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyIcon}>🎫</Text>
            <Text style={styles.emptyTitle}>Aucun code</Text>
            <Text style={styles.emptyText}>
              Vous n'avez pas encore généré de code Freeoui
            </Text>
            <Button
              title="Découvrir les partenaires"
              variant="secondary"
              onPress={() => navigation.navigate('Partners')}
              style={styles.emptyButton}
            />
          </View>
        ) : (
          codes.map((code) => {
            const status = getCodeStatus(code);

            return (
              <TouchableOpacity
                key={code.id}
                onPress={() => handleCodePress(code)}
                activeOpacity={0.7}
              >
                <Card padding="lg" style={styles.codeCard}>
                  <View style={styles.codeHeader}>
                    <View style={styles.codeTypeContainer}>
                      <Text style={styles.codeTypeIcon}>
                        {getCodeTypeIcon(code.type)}
                      </Text>
                      <View>
                        <Text style={styles.codeType}>
                          Code {code.type.toUpperCase()}
                        </Text>
                        <Text style={styles.codeValue}>{code.code}</Text>
                      </View>
                    </View>

                    <View
                      style={[
                        styles.statusBadge,
                        { backgroundColor: status.color },
                      ]}
                    >
                      <Text style={styles.statusText}>
                        {status.icon} {status.label}
                      </Text>
                    </View>
                  </View>

                  <View style={styles.codeDivider} />

                  <View style={styles.codeDetails}>
                    <Text style={styles.offerTitle} numberOfLines={1}>
                      {code.offer?.title || 'Offre inconnue'}
                    </Text>
                    <Text style={styles.partnerName} numberOfLines={1}>
                      📍 {code.offer?.partner?.name || 'Partenaire inconnu'}
                    </Text>

                    <View style={styles.codeFooter}>
                      <Text style={styles.codeDate}>
                        Expire: {new Date(code.expires_at).toLocaleDateString('fr-FR')}
                      </Text>

                      {code.used_at && (
                        <Text style={styles.usedDate}>
                          Utilisé le{' '}
                          {new Date(code.used_at).toLocaleDateString('fr-FR')}
                        </Text>
                      )}
                    </View>
                  </View>
                </Card>
              </TouchableOpacity>
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
  filterContainer: {
    backgroundColor: COLORS.white,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.gray200,
  },
  filterScroll: {
    padding: SPACING.md,
    gap: SPACING.sm,
  },
  filterChip: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: SPACING.md,
    paddingVertical: SPACING.sm,
    borderRadius: 20,
    backgroundColor: COLORS.gray100,
    marginRight: SPACING.sm,
  },
  filterChipActive: {
    backgroundColor: COLORS.gold,
  },
  filterIcon: {
    fontSize: 16,
    marginRight: SPACING.xs,
  },
  filterLabel: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    fontWeight: FONT_WEIGHTS.medium,
  },
  filterLabelActive: {
    color: COLORS.black,
    fontWeight: FONT_WEIGHTS.bold,
  },
  codesList: {
    flex: 1,
  },
  codesContent: {
    padding: SPACING.lg,
  },
  loadingContainer: {
    padding: SPACING.xxxl,
    alignItems: 'center',
  },
  loadingText: {
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
  emptyTitle: {
    fontSize: FONT_SIZES.xl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.sm,
  },
  emptyText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
    textAlign: 'center',
    marginBottom: SPACING.lg,
    paddingHorizontal: SPACING.xl,
  },
  emptyButton: {
    marginTop: SPACING.md,
  },
  codeCard: {
    marginBottom: SPACING.md,
  },
  codeHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  codeTypeContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  codeTypeIcon: {
    fontSize: 32,
    marginRight: SPACING.md,
  },
  codeType: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
    textTransform: 'uppercase',
  },
  codeValue: {
    fontSize: FONT_SIZES.lg,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
  },
  statusBadge: {
    paddingHorizontal: SPACING.sm,
    paddingVertical: SPACING.xs,
    borderRadius: BORDER_RADIUS.sm,
  },
  statusText: {
    fontSize: FONT_SIZES.xs,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.white,
  },
  codeDivider: {
    height: 1,
    backgroundColor: COLORS.gray200,
    marginBottom: SPACING.md,
  },
  codeDetails: {},
  offerTitle: {
    fontSize: FONT_SIZES.md,
    fontWeight: FONT_WEIGHTS.semibold,
    color: COLORS.black,
    marginBottom: SPACING.xs,
  },
  partnerName: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    marginBottom: SPACING.md,
  },
  codeFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  codeDate: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray500,
  },
  usedDate: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
    fontStyle: 'italic',
  },
});

export default MyCodesScreen;
