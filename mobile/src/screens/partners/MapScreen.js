import React, { useState, useEffect, useRef } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  ScrollView,
} from 'react-native';
import MapView, { Marker, Callout, PROVIDER_GOOGLE } from 'react-native-maps';
import { partnersService } from '../../services/api';
import locationService from '../../services/locationService';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS } from '../../constants/theme';

const MapScreen = ({ navigation }) => {
  const [partners, setPartners] = useState([]);
  const [loading, setLoading] = useState(true);
  const [userLocation, setUserLocation] = useState(null);
  const [selectedPartner, setSelectedPartner] = useState(null);
  const [region, setRegion] = useState({
    latitude: 34.7400, // Sfax coordinates
    longitude: 10.7600,
    latitudeDelta: 0.1,
    longitudeDelta: 0.1,
  });
  const mapRef = useRef(null);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      // Charger les partenaires
      const response = await partnersService.getPartners();
      if (response.data.success) {
        setPartners(response.data.data);
      }

      // Obtenir la position utilisateur
      try {
        const location = await locationService.getCurrentPosition();
        setUserLocation(location);
        setRegion({
          ...location,
          latitudeDelta: 0.05,
          longitudeDelta: 0.05,
        });
      } catch (error) {
        console.log('Position utilisateur non disponible');
      }
    } catch (error) {
      console.error('Erreur lors du chargement des données:', error);
      Alert.alert('Erreur', 'Impossible de charger les partenaires');
    } finally {
      setLoading(false);
    }
  };

  const handleMarkerPress = (partner) => {
    setSelectedPartner(partner);

    // Calculer la distance si on a la position utilisateur
    if (userLocation) {
      const distance = locationService.getDistanceFromCurrentPosition(
        partner.latitude,
        partner.longitude
      );
      partner.distance = distance;
    }
  };

  const handleCenterOnUser = () => {
    if (userLocation && mapRef.current) {
      mapRef.current.animateToRegion({
        ...userLocation,
        latitudeDelta: 0.05,
        longitudeDelta: 0.05,
      });
    } else {
      Alert.alert(
        'Localisation désactivée',
        'Activez la localisation pour centrer la carte sur votre position'
      );
    }
  };

  const handleShowNearby = () => {
    if (!userLocation) {
      Alert.alert(
        'Localisation désactivée',
        'Activez la localisation pour voir les partenaires à proximité'
      );
      return;
    }

    const nearby = locationService.getNearbyPartners(partners, 5); // 5 km
    if (nearby.length === 0) {
      Alert.alert('Aucun partenaire', 'Aucun partenaire trouvé dans un rayon de 5 km');
    } else {
      Alert.alert(
        'Partenaires à proximité',
        `${nearby.length} partenaire(s) dans un rayon de 5 km`,
        [
          { text: 'OK' },
          {
            text: 'Afficher',
            onPress: () => {
              // Zoomer sur les partenaires à proximité
              if (mapRef.current) {
                mapRef.current.fitToCoordinates(
                  nearby.map((p) => ({
                    latitude: p.latitude,
                    longitude: p.longitude,
                  })),
                  {
                    edgePadding: { top: 50, right: 50, bottom: 50, left: 50 },
                    animated: true,
                  }
                );
              }
            },
          },
        ]
      );
    }
  };

  const getMarkerColor = (category) => {
    const colors = {
      restauration: '#FF6B6B',
      shopping: '#4ECDC4',
      sport: '#45B7D1',
      sante: '#96CEB4',
      culture: '#FFEAA7',
      hotellerie: '#DFE6E9',
      auto: '#74B9FF',
      services: '#A29BFE',
    };
    return colors[category] || COLORS.gold;
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.gold} />
        <Text style={styles.loadingText}>Chargement de la carte...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Map */}
      <MapView
        ref={mapRef}
        provider={PROVIDER_GOOGLE}
        style={styles.map}
        initialRegion={region}
        showsUserLocation={userLocation !== null}
        showsMyLocationButton={false}
        showsCompass={true}
        showsScale={true}
      >
        {/* Markers pour les partenaires */}
        {partners.map((partner) => (
          <Marker
            key={partner.id}
            coordinate={{
              latitude: partner.latitude,
              longitude: partner.longitude,
            }}
            pinColor={getMarkerColor(partner.category)}
            onPress={() => handleMarkerPress(partner)}
          >
            <Callout
              tooltip
              onPress={() => navigation.navigate('PartnerDetail', { partnerSlug: partner.slug })}
            >
              <View style={styles.callout}>
                <Text style={styles.calloutTitle}>{partner.name}</Text>
                <Text style={styles.calloutCategory}>{partner.category_label}</Text>
                <Text style={styles.calloutAddress}>{partner.address}</Text>
                <Text style={styles.calloutAction}>Tap pour voir les détails →</Text>
              </View>
            </Callout>
          </Marker>
        ))}
      </MapView>

      {/* Control buttons */}
      <View style={styles.controls}>
        <TouchableOpacity style={styles.controlButton} onPress={handleCenterOnUser}>
          <Text style={styles.controlIcon}>📍</Text>
          <Text style={styles.controlText}>Ma position</Text>
        </TouchableOpacity>

        <TouchableOpacity style={styles.controlButton} onPress={handleShowNearby}>
          <Text style={styles.controlIcon}>🔍</Text>
          <Text style={styles.controlText}>À proximité</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.controlButton}
          onPress={() => navigation.navigate('PartnersList')}
        >
          <Text style={styles.controlIcon}>📋</Text>
          <Text style={styles.controlText}>Liste</Text>
        </TouchableOpacity>
      </View>

      {/* Selected partner card */}
      {selectedPartner && (
        <View style={styles.selectedCard}>
          <ScrollView>
            <Text style={styles.selectedName}>{selectedPartner.name}</Text>
            <Text style={styles.selectedCategory}>{selectedPartner.category_label}</Text>
            {selectedPartner.distance && (
              <Text style={styles.selectedDistance}>
                📍 {locationService.formatDistance(selectedPartner.distance)}
              </Text>
            )}
            <Text style={styles.selectedAddress}>{selectedPartner.address}</Text>

            <View style={styles.selectedActions}>
              <TouchableOpacity
                style={styles.actionButton}
                onPress={() =>
                  navigation.navigate('PartnerDetail', { partnerSlug: selectedPartner.slug })
                }
              >
                <Text style={styles.actionButtonText}>Voir les offres</Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.actionButton, styles.actionButtonSecondary]}
                onPress={() => setSelectedPartner(null)}
              >
                <Text style={styles.actionButtonTextSecondary}>Fermer</Text>
              </TouchableOpacity>
            </View>
          </ScrollView>
        </View>
      )}

      {/* Legend */}
      <View style={styles.legend}>
        <Text style={styles.legendTitle}>Catégories</Text>
        <View style={styles.legendItems}>
          <View style={styles.legendItem}>
            <View style={[styles.legendDot, { backgroundColor: '#FF6B6B' }]} />
            <Text style={styles.legendText}>Restaurant</Text>
          </View>
          <View style={styles.legendItem}>
            <View style={[styles.legendDot, { backgroundColor: '#4ECDC4' }]} />
            <Text style={styles.legendText}>Shopping</Text>
          </View>
          <View style={styles.legendItem}>
            <View style={[styles.legendDot, { backgroundColor: '#45B7D1' }]} />
            <Text style={styles.legendText}>Sport</Text>
          </View>
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.backgroundSecondary,
  },
  loadingText: {
    marginTop: SPACING.md,
    color: COLORS.gray600,
    fontSize: FONT_SIZES.md,
  },
  map: {
    flex: 1,
  },
  callout: {
    backgroundColor: COLORS.white,
    borderRadius: 8,
    padding: SPACING.md,
    width: 200,
    shadowColor: COLORS.black,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
  },
  calloutTitle: {
    fontSize: FONT_SIZES.md,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.xs,
  },
  calloutCategory: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gold,
    textTransform: 'uppercase',
    marginBottom: SPACING.xs,
  },
  calloutAddress: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
    marginBottom: SPACING.sm,
  },
  calloutAction: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.info,
    fontWeight: FONT_WEIGHTS.medium,
  },
  controls: {
    position: 'absolute',
    top: SPACING.lg,
    right: SPACING.md,
    gap: SPACING.sm,
  },
  controlButton: {
    backgroundColor: COLORS.white,
    borderRadius: 8,
    padding: SPACING.sm,
    alignItems: 'center',
    shadowColor: COLORS.black,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
    minWidth: 80,
  },
  controlIcon: {
    fontSize: 24,
    marginBottom: SPACING.xs,
  },
  controlText: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.black,
    fontWeight: FONT_WEIGHTS.medium,
  },
  selectedCard: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: COLORS.white,
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    padding: SPACING.lg,
    maxHeight: 250,
    shadowColor: COLORS.black,
    shadowOffset: { width: 0, height: -2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
  },
  selectedName: {
    fontSize: FONT_SIZES.xl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.xs,
  },
  selectedCategory: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gold,
    textTransform: 'uppercase',
    marginBottom: SPACING.sm,
  },
  selectedDistance: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.info,
    marginBottom: SPACING.xs,
  },
  selectedAddress: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
    marginBottom: SPACING.md,
  },
  selectedActions: {
    flexDirection: 'row',
    gap: SPACING.sm,
  },
  actionButton: {
    flex: 1,
    backgroundColor: COLORS.gold,
    borderRadius: 8,
    padding: SPACING.sm,
    alignItems: 'center',
  },
  actionButtonText: {
    color: COLORS.black,
    fontSize: FONT_SIZES.sm,
    fontWeight: FONT_WEIGHTS.bold,
  },
  actionButtonSecondary: {
    backgroundColor: COLORS.white,
    borderWidth: 1,
    borderColor: COLORS.gray300,
  },
  actionButtonTextSecondary: {
    color: COLORS.gray700,
    fontSize: FONT_SIZES.sm,
    fontWeight: FONT_WEIGHTS.medium,
  },
  legend: {
    position: 'absolute',
    bottom: SPACING.lg,
    left: SPACING.md,
    backgroundColor: COLORS.white,
    borderRadius: 8,
    padding: SPACING.sm,
    shadowColor: COLORS.black,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
  },
  legendTitle: {
    fontSize: FONT_SIZES.xs,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.xs,
  },
  legendItems: {
    gap: SPACING.xs,
  },
  legendItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.xs,
  },
  legendDot: {
    width: 12,
    height: 12,
    borderRadius: 6,
  },
  legendText: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray700,
  },
});

export default MapScreen;
