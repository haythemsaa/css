import React from 'react';
import { View, StyleSheet } from 'react-native';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../constants/theme';

const Card = ({
  children,
  variant = 'default',
  padding = 'md',
  style,
}) => {
  const getVariantStyles = () => {
    switch (variant) {
      case 'gold':
        return {
          backgroundColor: COLORS.gold,
          borderColor: COLORS.gold,
        };
      case 'outlined':
        return {
          backgroundColor: 'transparent',
          borderWidth: 1,
          borderColor: COLORS.gray300,
        };
      default:
        return {
          backgroundColor: COLORS.white,
          borderColor: COLORS.gray200,
        };
    }
  };

  const getPaddingValue = () => {
    switch (padding) {
      case 'none':
        return 0;
      case 'sm':
        return SPACING.sm;
      case 'md':
        return SPACING.md;
      case 'lg':
        return SPACING.lg;
      case 'xl':
        return SPACING.xl;
      default:
        return SPACING.md;
    }
  };

  return (
    <View
      style={[
        styles.card,
        getVariantStyles(),
        { padding: getPaddingValue() },
        style,
      ]}
    >
      {children}
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    borderRadius: BORDER_RADIUS.lg,
    ...SHADOWS.md,
  },
});

export default Card;
