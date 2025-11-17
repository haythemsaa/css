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
import { Card } from '../../components/common';
import useAuthStore from '../../stores/authStore';
import { contentService } from '../../services/api';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS } from '../../constants/theme';

const ContentScreen = () => {
  const { isPremium } = useAuthStore();
  const [content, setContent] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedType, setSelectedType] = useState('all');

  const types = [
    { value: 'all', label: 'Tous', icon: '📱' },
    { value: 'article', label: 'Articles', icon: '📰' },
    { value: 'video', label: 'Vidéos', icon: '🎥' },
    { value: 'gallery', label: 'Galeries', icon: '📷' },
    { value: 'podcast', label: 'Podcasts', icon: '🎙️' },
  ];

  useEffect(() => {
    loadContent();
  }, [selectedType]);

  const loadContent = async () => {
    setLoading(true);
    try {
      const params = selectedType !== 'all' ? { type: selectedType } : {};
      const response = await contentService.getContent(params);

      if (response.data.success) {
        setContent(response.data.data);
      }
    } catch (error) {
      console.error('Error loading content:', error);
      Alert.alert('Erreur', 'Impossible de charger le contenu');
    } finally {
      setLoading(false);
    }
  };

  const handleContentPress = (item) => {
    if (item.access_level === 'premium' && !isPremium()) {
      Alert.alert(
        'Contenu Premium',
        'Ce contenu est réservé aux abonnés Premium et Socios.\n\nPassez à Premium pour y accéder!',
        [{ text: 'OK' }]
      );
      return;
    }

    // Navigate to content detail
    navigation.navigate('ContentDetail', { contentSlug: item.slug });
  };

  const getTypeIcon = (type) => {
    const typeObj = types.find((t) => t.value === type);
    return typeObj?.icon || '📱';
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Actualités CSS</Text>
        <Text style={styles.headerSubtitle}>
          Les dernières nouvelles du Club Sportif Sfaxien
        </Text>
      </View>

      {/* Type Filter */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        style={styles.typesScroll}
        contentContainerStyle={styles.typesContent}
      >
        {types.map((type) => (
          <TouchableOpacity
            key={type.value}
            style={[
              styles.typeChip,
              selectedType === type.value && styles.typeChipActive,
            ]}
            onPress={() => setSelectedType(type.value)}
          >
            <Text style={styles.typeIcon}>{type.icon}</Text>
            <Text
              style={[
                styles.typeLabel,
                selectedType === type.value && styles.typeLabelActive,
              ]}
            >
              {type.label}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Content List */}
      <ScrollView
        style={styles.contentList}
        contentContainerStyle={styles.contentListContainer}
      >
        {loading ? (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color={COLORS.gold} />
            <Text style={styles.loadingText}>Chargement du contenu...</Text>
          </View>
        ) : content.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyIcon}>📰</Text>
            <Text style={styles.emptyText}>Aucun contenu disponible</Text>
          </View>
        ) : (
          content.map((item) => (
            <TouchableOpacity
              key={item.id}
              onPress={() => handleContentPress(item)}
              activeOpacity={0.7}
            >
              <Card padding="lg" style={styles.contentCard}>
                <View style={styles.contentHeader}>
                  <View style={styles.contentMeta}>
                    <Text style={styles.contentType}>
                      {getTypeIcon(item.type)} {item.type}
                    </Text>
                    {item.access_level === 'premium' && (
                      <View style={styles.premiumBadge}>
                        <Text style={styles.premiumText}>⭐ Premium</Text>
                      </View>
                    )}
                  </View>
                  <Text style={styles.contentDate}>
                    {new Date(item.published_at || item.created_at).toLocaleDateString('fr-FR')}
                  </Text>
                </View>

                <Text style={styles.contentTitle} numberOfLines={2}>
                  {item.title}
                </Text>

                {item.excerpt && (
                  <Text style={styles.contentExcerpt} numberOfLines={3}>
                    {item.excerpt}
                  </Text>
                )}

                <View style={styles.contentFooter}>
                  <Text style={styles.contentViews}>
                    👁️ {item.views_count || 0} vues
                  </Text>
                  {item.likes_count > 0 && (
                    <Text style={styles.contentLikes}>
                      ❤️ {item.likes_count}
                    </Text>
                  )}
                </View>
              </Card>
            </TouchableOpacity>
          ))
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
  typesScroll: {
    backgroundColor: COLORS.white,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.gray200,
  },
  typesContent: {
    padding: SPACING.md,
    gap: SPACING.sm,
  },
  typeChip: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: SPACING.md,
    paddingVertical: SPACING.sm,
    borderRadius: 20,
    backgroundColor: COLORS.gray100,
    marginRight: SPACING.sm,
  },
  typeChipActive: {
    backgroundColor: COLORS.gold,
  },
  typeIcon: {
    fontSize: 16,
    marginRight: SPACING.xs,
  },
  typeLabel: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    fontWeight: FONT_WEIGHTS.medium,
  },
  typeLabelActive: {
    color: COLORS.black,
    fontWeight: FONT_WEIGHTS.bold,
  },
  contentList: {
    flex: 1,
  },
  contentListContainer: {
    padding: SPACING.lg,
  },
  loadingContainer: {
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
  contentCard: {
    marginBottom: SPACING.md,
  },
  contentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: SPACING.sm,
  },
  contentMeta: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  contentType: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
    textTransform: 'uppercase',
    marginRight: SPACING.sm,
  },
  premiumBadge: {
    backgroundColor: COLORS.gold,
    paddingHorizontal: SPACING.xs,
    paddingVertical: 2,
    borderRadius: 4,
  },
  premiumText: {
    fontSize: FONT_SIZES.xs,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
  },
  contentDate: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray500,
  },
  contentTitle: {
    fontSize: FONT_SIZES.lg,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.sm,
  },
  contentExcerpt: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray700,
    lineHeight: 20,
    marginBottom: SPACING.sm,
  },
  contentFooter: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: SPACING.sm,
    borderTopWidth: 1,
    borderTopColor: COLORS.gray200,
  },
  contentViews: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
    marginRight: SPACING.md,
  },
  contentLikes: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
  },
});

export default ContentScreen;
