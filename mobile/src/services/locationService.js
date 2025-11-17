import * as Location from 'expo-location';
import { Platform, Linking } from 'react-native';

class LocationService {
  constructor() {
    this.currentLocation = null;
    this.watchSubscription = null;
  }

  /**
   * Demander les permissions de localisation
   */
  async requestPermissions() {
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      return status === 'granted';
    } catch (error) {
      console.error('Erreur lors de la demande de permission:', error);
      return false;
    }
  }

  /**
   * Obtenir la position actuelle
   */
  async getCurrentPosition() {
    try {
      const hasPermission = await this.requestPermissions();
      if (!hasPermission) {
        throw new Error('Permission de localisation refusée');
      }

      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.Balanced,
      });

      this.currentLocation = {
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
      };

      return this.currentLocation;
    } catch (error) {
      console.error('Erreur lors de la récupération de la position:', error);
      throw error;
    }
  }

  /**
   * Commencer à suivre la position (pour navigation en temps réel)
   */
  async startWatchingPosition(callback) {
    try {
      const hasPermission = await this.requestPermissions();
      if (!hasPermission) {
        throw new Error('Permission de localisation refusée');
      }

      this.watchSubscription = await Location.watchPositionAsync(
        {
          accuracy: Location.Accuracy.High,
          timeInterval: 5000, // Mise à jour toutes les 5 secondes
          distanceInterval: 10, // Mise à jour tous les 10 mètres
        },
        (location) => {
          this.currentLocation = {
            latitude: location.coords.latitude,
            longitude: location.coords.longitude,
          };
          if (callback) {
            callback(this.currentLocation);
          }
        }
      );

      return true;
    } catch (error) {
      console.error('Erreur lors du suivi de la position:', error);
      throw error;
    }
  }

  /**
   * Arrêter de suivre la position
   */
  stopWatchingPosition() {
    if (this.watchSubscription) {
      this.watchSubscription.remove();
      this.watchSubscription = null;
    }
  }

  /**
   * Calculer la distance entre deux points en utilisant la formule Haversine
   * Retourne la distance en kilomètres
   */
  calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Rayon de la Terre en km
    const dLat = this.toRad(lat2 - lat1);
    const dLon = this.toRad(lon2 - lon1);

    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(this.toRad(lat1)) *
        Math.cos(this.toRad(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c;

    return distance;
  }

  /**
   * Convertir des degrés en radians
   */
  toRad(degrees) {
    return degrees * (Math.PI / 180);
  }

  /**
   * Formater la distance pour l'affichage
   */
  formatDistance(distanceKm) {
    if (distanceKm < 1) {
      return `${Math.round(distanceKm * 1000)} m`;
    }
    return `${distanceKm.toFixed(1)} km`;
  }

  /**
   * Calculer la distance depuis la position actuelle
   */
  getDistanceFromCurrentPosition(latitude, longitude) {
    if (!this.currentLocation) {
      return null;
    }

    return this.calculateDistance(
      this.currentLocation.latitude,
      this.currentLocation.longitude,
      latitude,
      longitude
    );
  }

  /**
   * Trier des partenaires par distance depuis la position actuelle
   */
  sortPartnersByDistance(partners, maxDistance = null) {
    if (!this.currentLocation) {
      return partners;
    }

    const partnersWithDistance = partners.map((partner) => ({
      ...partner,
      distance: this.calculateDistance(
        this.currentLocation.latitude,
        this.currentLocation.longitude,
        partner.latitude,
        partner.longitude
      ),
    }));

    // Filtrer par distance max si spécifié
    let filtered = partnersWithDistance;
    if (maxDistance) {
      filtered = partnersWithDistance.filter((p) => p.distance <= maxDistance);
    }

    // Trier par distance croissante
    return filtered.sort((a, b) => a.distance - b.distance);
  }

  /**
   * Obtenir les partenaires à proximité
   */
  getNearbyPartners(partners, radiusKm = 5) {
    return this.sortPartnersByDistance(partners, radiusKm);
  }

  /**
   * Obtenir l'adresse depuis les coordonnées (reverse geocoding)
   */
  async getAddressFromCoordinates(latitude, longitude) {
    try {
      const addresses = await Location.reverseGeocodeAsync({
        latitude,
        longitude,
      });

      if (addresses.length > 0) {
        const address = addresses[0];
        return {
          street: address.street,
          city: address.city,
          region: address.region,
          country: address.country,
          postalCode: address.postalCode,
          formattedAddress: `${address.street || ''}, ${address.city || ''}, ${address.region || ''}`.trim(),
        };
      }

      return null;
    } catch (error) {
      console.error('Erreur lors du reverse geocoding:', error);
      return null;
    }
  }

  /**
   * Obtenir les coordonnées depuis une adresse (geocoding)
   */
  async getCoordinatesFromAddress(address) {
    try {
      const locations = await Location.geocodeAsync(address);

      if (locations.length > 0) {
        return {
          latitude: locations[0].latitude,
          longitude: locations[0].longitude,
        };
      }

      return null;
    } catch (error) {
      console.error('Erreur lors du geocoding:', error);
      return null;
    }
  }

  /**
   * Ouvrir l'application de navigation vers une destination
   */
  openNavigation(latitude, longitude, label) {
    const scheme = Platform.select({
      ios: 'maps:0,0?q=',
      android: 'geo:0,0?q=',
    });
    const latLng = `${latitude},${longitude}`;
    const url = Platform.select({
      ios: `${scheme}${label}@${latLng}`,
      android: `${scheme}${latLng}(${label})`,
    });

    Linking.openURL(url);
  }

  /**
   * Vérifier si les services de localisation sont activés
   */
  async isLocationEnabled() {
    try {
      return await Location.hasServicesEnabledAsync();
    } catch (error) {
      console.error('Erreur lors de la vérification des services:', error);
      return false;
    }
  }

  /**
   * Obtenir la précision actuelle de la localisation
   */
  getCurrentAccuracy() {
    return Location.Accuracy;
  }
}

export default new LocationService();
