import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Alert,
  TouchableOpacity,
  Dimensions,
} from 'react-native';
import { Camera, CameraView } from 'expo-camera';
import { Button } from '../../components/common';
import { codesService } from '../../services/api';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS } from '../../constants/theme';

const { width } = Dimensions.get('window');
const SCAN_AREA_SIZE = width * 0.7;

const QRScannerScreen = ({ navigation }) => {
  const [hasPermission, setHasPermission] = useState(null);
  const [scanned, setScanned] = useState(false);
  const [validating, setValidating] = useState(false);

  useEffect(() => {
    requestCameraPermission();
  }, []);

  const requestCameraPermission = async () => {
    const { status } = await Camera.requestCameraPermissionsAsync();
    setHasPermission(status === 'granted');
  };

  const handleBarCodeScanned = async ({ type, data }) => {
    if (scanned || validating) return;

    setScanned(true);
    setValidating(true);

    try {
      // Validate the code with backend
      const response = await codesService.validateCode(data);

      if (response.data.success) {
        const codeData = response.data.data;

        Alert.alert(
          'Code valide! ✓',
          `Code: ${codeData.code}\n` +
            `Offre: ${codeData.offer?.title || 'N/A'}\n` +
            `Réduction: ${codeData.offer?.discount_value}${
              codeData.offer?.discount_type === 'percentage' ? '%' : ' TND'
            }\n` +
            `Partenaire: ${codeData.offer?.partner?.name || 'N/A'}\n` +
            `Expire le: ${new Date(codeData.expires_at).toLocaleDateString('fr-FR')}`,
          [
            {
              text: 'Scanner à nouveau',
              onPress: () => {
                setScanned(false);
                setValidating(false);
              },
            },
            {
              text: 'Terminer',
              onPress: () => navigation.goBack(),
            },
          ]
        );
      }
    } catch (error) {
      const message =
        error.response?.data?.message ||
        'Code invalide ou expiré';

      Alert.alert('Erreur de validation', message, [
        {
          text: 'Réessayer',
          onPress: () => {
            setScanned(false);
            setValidating(false);
          },
        },
        {
          text: 'Annuler',
          onPress: () => navigation.goBack(),
          style: 'cancel',
        },
      ]);
    } finally {
      setValidating(false);
    }
  };

  if (hasPermission === null) {
    return (
      <View style={styles.container}>
        <Text style={styles.messageText}>Demande d'accès à la caméra...</Text>
      </View>
    );
  }

  if (hasPermission === false) {
    return (
      <View style={styles.container}>
        <View style={styles.permissionContainer}>
          <Text style={styles.permissionIcon}>📷</Text>
          <Text style={styles.permissionTitle}>Accès caméra refusé</Text>
          <Text style={styles.permissionText}>
            Veuillez autoriser l'accès à la caméra dans les paramètres de votre
            appareil pour scanner les QR codes.
          </Text>
          <Button
            title="Retour"
            variant="outline"
            onPress={() => navigation.goBack()}
            style={styles.backButton}
          />
        </View>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <CameraView
        style={styles.camera}
        facing="back"
        onBarcodeScanned={scanned ? undefined : handleBarCodeScanned}
        barcodeScannerSettings={{
          barcodeTypes: ['qr', 'code128', 'code39'],
        }}
      >
        <View style={styles.overlay}>
          {/* Top overlay */}
          <View style={styles.overlaySection} />

          {/* Middle section with scan area */}
          <View style={styles.middleSection}>
            <View style={styles.overlaySide} />

            <View style={styles.scanArea}>
              {/* Corner indicators */}
              <View style={[styles.corner, styles.cornerTopLeft]} />
              <View style={[styles.corner, styles.cornerTopRight]} />
              <View style={[styles.corner, styles.cornerBottomLeft]} />
              <View style={[styles.corner, styles.cornerBottomRight]} />

              {validating && (
                <View style={styles.validatingContainer}>
                  <Text style={styles.validatingText}>Validation...</Text>
                </View>
              )}
            </View>

            <View style={styles.overlaySide} />
          </View>

          {/* Bottom overlay with instructions */}
          <View style={[styles.overlaySection, styles.bottomSection]}>
            <View style={styles.instructionsContainer}>
              <Text style={styles.instructionsTitle}>
                Scanner un QR Code Freeoui
              </Text>
              <Text style={styles.instructionsText}>
                Placez le QR code dans la zone de scan
              </Text>

              <TouchableOpacity
                style={styles.cancelButton}
                onPress={() => navigation.goBack()}
              >
                <Text style={styles.cancelButtonText}>Annuler</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </CameraView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.black,
    justifyContent: 'center',
    alignItems: 'center',
  },
  messageText: {
    color: COLORS.white,
    fontSize: FONT_SIZES.md,
  },
  permissionContainer: {
    padding: SPACING.xxxl,
    alignItems: 'center',
  },
  permissionIcon: {
    fontSize: 64,
    marginBottom: SPACING.lg,
  },
  permissionTitle: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.white,
    marginBottom: SPACING.md,
    textAlign: 'center',
  },
  permissionText: {
    fontSize: FONT_SIZES.md,
    color: COLORS.gray400,
    textAlign: 'center',
    marginBottom: SPACING.xl,
    lineHeight: 22,
  },
  backButton: {
    minWidth: 150,
  },
  camera: {
    flex: 1,
    width: '100%',
  },
  overlay: {
    flex: 1,
  },
  overlaySection: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.6)',
  },
  middleSection: {
    flexDirection: 'row',
    height: SCAN_AREA_SIZE,
  },
  overlaySide: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.6)',
  },
  scanArea: {
    width: SCAN_AREA_SIZE,
    height: SCAN_AREA_SIZE,
    position: 'relative',
    justifyContent: 'center',
    alignItems: 'center',
  },
  corner: {
    position: 'absolute',
    width: 30,
    height: 30,
    borderColor: COLORS.gold,
  },
  cornerTopLeft: {
    top: 0,
    left: 0,
    borderTopWidth: 4,
    borderLeftWidth: 4,
  },
  cornerTopRight: {
    top: 0,
    right: 0,
    borderTopWidth: 4,
    borderRightWidth: 4,
  },
  cornerBottomLeft: {
    bottom: 0,
    left: 0,
    borderBottomWidth: 4,
    borderLeftWidth: 4,
  },
  cornerBottomRight: {
    bottom: 0,
    right: 0,
    borderBottomWidth: 4,
    borderRightWidth: 4,
  },
  validatingContainer: {
    backgroundColor: 'rgba(212, 175, 55, 0.9)',
    paddingHorizontal: SPACING.xl,
    paddingVertical: SPACING.md,
    borderRadius: 8,
  },
  validatingText: {
    color: COLORS.black,
    fontSize: FONT_SIZES.lg,
    fontWeight: FONT_WEIGHTS.bold,
  },
  bottomSection: {
    justifyContent: 'center',
  },
  instructionsContainer: {
    alignItems: 'center',
    paddingHorizontal: SPACING.xl,
  },
  instructionsTitle: {
    fontSize: FONT_SIZES.xl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.white,
    marginBottom: SPACING.sm,
    textAlign: 'center',
  },
  instructionsText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray300,
    marginBottom: SPACING.xl,
    textAlign: 'center',
  },
  cancelButton: {
    paddingHorizontal: SPACING.xl,
    paddingVertical: SPACING.md,
    borderRadius: 8,
    borderWidth: 2,
    borderColor: COLORS.white,
  },
  cancelButtonText: {
    color: COLORS.white,
    fontSize: FONT_SIZES.md,
    fontWeight: FONT_WEIGHTS.semibold,
  },
});

export default QRScannerScreen;
