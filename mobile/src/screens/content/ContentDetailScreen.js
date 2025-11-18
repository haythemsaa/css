import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  ActivityIndicator,
  Image,
  TouchableOpacity,
  Share,
  Alert,
} from 'react-native';
import { Video } from 'expo-av';
import { Audio } from 'expo-av';
import ImageView from 'react-native-image-viewing';
import * as Sharing from 'expo-sharing';
import { contentService } from '../../services/api';
import { COLORS, SPACING, FONT_SIZES, FONT_WEIGHTS } from '../../constants/theme';
import CommentsSection from "../../components/comments/CommentsSection";

const ContentDetailScreen = ({ route, navigation }) => {
  const { contentSlug } = route.params;
  const [content, setContent] = useState(null);
  const [loading, setLoading] = useState(true);
  const [liked, setLiked] = useState(false);

  // Video state
  const [videoStatus, setVideoStatus] = useState({});
  const videoRef = React.useRef(null);

  // Audio state
  const [sound, setSound] = useState(null);
  const [isPlaying, setIsPlaying] = useState(false);
  const [audioPosition, setAudioPosition] = useState(0);
  const [audioDuration, setAudioDuration] = useState(0);

  // Gallery state
  const [galleryVisible, setGalleryVisible] = useState(false);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);

  useEffect(() => {
    loadContent();

    return () => {
      // Cleanup audio
      if (sound) {
        sound.unloadAsync();
      }
    };
  }, [contentSlug]);

  const loadContent = async () => {
    try {
      const response = await contentService.getContentBySlug(contentSlug);
      if (response.data.success) {
        const contentData = response.data.data;
        setContent(contentData);
        setLiked(contentData.is_liked || false);

        // Si c'est un podcast, préparer l'audio
        if (contentData.type === 'podcast' && contentData.media_url) {
          await prepareAudio(contentData.media_url);
        }
      }
    } catch (error) {
      console.error('Erreur lors du chargement du contenu:', error);
      Alert.alert('Erreur', 'Impossible de charger le contenu');
    } finally {
      setLoading(false);
    }
  };

  const prepareAudio = async (uri) => {
    try {
      await Audio.setAudioModeAsync({
        playsInSilentModeIOS: true,
        staysActiveInBackground: true,
      });

      const { sound: audioSound, status } = await Audio.Sound.createAsync(
        { uri },
        { shouldPlay: false },
        onAudioStatusUpdate
      );

      setSound(audioSound);
      if (status.isLoaded) {
        setAudioDuration(status.durationMillis || 0);
      }
    } catch (error) {
      console.error('Erreur lors de la préparation de l\'audio:', error);
    }
  };

  const onAudioStatusUpdate = (status) => {
    if (status.isLoaded) {
      setIsPlaying(status.isPlaying);
      setAudioPosition(status.positionMillis || 0);
      setAudioDuration(status.durationMillis || 0);

      // Si la lecture est terminée
      if (status.didJustFinish) {
        setIsPlaying(false);
        sound?.setPositionAsync(0);
      }
    }
  };

  const toggleAudioPlayback = async () => {
    if (!sound) return;

    try {
      if (isPlaying) {
        await sound.pauseAsync();
      } else {
        await sound.playAsync();
      }
    } catch (error) {
      console.error('Erreur lecture audio:', error);
    }
  };

  const seekAudio = async (position) => {
    if (!sound) return;
    try {
      await sound.setPositionAsync(position);
    } catch (error) {
      console.error('Erreur seek audio:', error);
    }
  };

  const formatTime = (milliseconds) => {
    const totalSeconds = Math.floor(milliseconds / 1000);
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
  };

  const handleLike = async () => {
    try {
      await contentService.likeContent(contentSlug);
      setLiked(!liked);
      setContent((prev) => ({
        ...prev,
        likes_count: liked ? prev.likes_count - 1 : prev.likes_count + 1,
      }));
    } catch (error) {
      console.error('Erreur lors du like:', error);
    }
  };

  const handleShare = async () => {
    try {
      const result = await Share.share({
        message: `${content.title}\n\n${content.excerpt || ''}\n\nVia CSS Platform`,
        title: content.title,
      });

      if (result.action === Share.sharedAction) {
        console.log('Contenu partagé');
      }
    } catch (error) {
      console.error('Erreur lors du partage:', error);
    }
  };

  const openGallery = (index) => {
    setCurrentImageIndex(index);
    setGalleryVisible(true);
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.gold} />
      </View>
    );
  }

  if (!content) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Contenu introuvable</Text>
      </View>
    );
  }

  const getTypeIcon = (type) => {
    const icons = {
      article: '📰',
      video: '🎥',
      gallery: '📷',
      podcast: '🎙️',
    };
    return icons[type] || '📱';
  };

  const getTypeLabel = (type) => {
    const labels = {
      article: 'Article',
      video: 'Vidéo',
      gallery: 'Galerie',
      podcast: 'Podcast',
    };
    return labels[type] || type;
  };

  // Préparer les images pour la galerie
  const galleryImages = content.gallery_images
    ? content.gallery_images.map((url) => ({ uri: url }))
    : [];

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* Header Image */}
      {content.featured_image && content.type !== 'video' && (
        <Image source={{ uri: content.featured_image }} style={styles.featuredImage} />
      )}

      {/* Video Player */}
      {content.type === 'video' && content.media_url && (
        <Video
          ref={videoRef}
          source={{ uri: content.media_url }}
          style={styles.video}
          useNativeControls
          resizeMode="contain"
          onPlaybackStatusUpdate={(status) => setVideoStatus(status)}
        />
      )}

      <View style={styles.content}>
        {/* Type Badge */}
        <View style={styles.typeBadge}>
          <Text style={styles.typeIcon}>{getTypeIcon(content.type)}</Text>
          <Text style={styles.typeText}>{getTypeLabel(content.type)}</Text>
        </View>

        {/* Title */}
        <Text style={styles.title}>{content.title}</Text>

        {/* Meta */}
        <View style={styles.meta}>
          <Text style={styles.metaText}>
            📅 {new Date(content.published_at).toLocaleDateString('fr-FR')}
          </Text>
          <Text style={styles.metaText}>👁️ {content.views_count || 0} vues</Text>
          <Text style={styles.metaText}>
            ❤️ {content.likes_count || 0} j'aime
          </Text>
        </View>

        {/* Actions */}
        <View style={styles.actions}>
          <TouchableOpacity
            style={[styles.actionButton, liked && styles.actionButtonLiked]}
            onPress={handleLike}
          >
            <Text style={styles.actionIcon}>{liked ? '❤️' : '🤍'}</Text>
            <Text style={[styles.actionText, liked && styles.actionTextLiked]}>
              J'aime
            </Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.actionButton} onPress={handleShare}>
            <Text style={styles.actionIcon}>📤</Text>
            <Text style={styles.actionText}>Partager</Text>
          </TouchableOpacity>
        </View>

        {/* Audio Player (for podcasts) */}
        {content.type === 'podcast' && sound && (
          <View style={styles.audioPlayer}>
            <View style={styles.audioControls}>
              <TouchableOpacity onPress={toggleAudioPlayback} style={styles.playButton}>
                <Text style={styles.playIcon}>{isPlaying ? '⏸️' : '▶️'}</Text>
              </TouchableOpacity>

              <View style={styles.audioInfo}>
                <View style={styles.progressBarContainer}>
                  <View
                    style={[
                      styles.progressBar,
                      {
                        width: `${audioDuration > 0 ? (audioPosition / audioDuration) * 100 : 0}%`,
                      },
                    ]}
                  />
                </View>
                <View style={styles.audioTimes}>
                  <Text style={styles.audioTime}>{formatTime(audioPosition)}</Text>
                  <Text style={styles.audioTime}>{formatTime(audioDuration)}</Text>
                </View>
              </View>
            </View>
          </View>
        )}

        {/* Gallery Grid */}
        {content.type === 'gallery' && galleryImages.length > 0 && (
          <View style={styles.galleryGrid}>
            {galleryImages.map((image, index) => (
              <TouchableOpacity
                key={index}
                style={styles.galleryItem}
                onPress={() => openGallery(index)}
              >
                <Image source={image} style={styles.galleryImage} />
              </TouchableOpacity>
            ))}
          </View>
        )}

        {/* Content Body */}
        <View style={styles.body}>
          {content.excerpt && (
            <Text style={styles.excerpt}>{content.excerpt}</Text>
          )}

          {content.body && (
            <Text style={styles.bodyText}>{content.body}</Text>
          )}
        </View>

        {/* Tags */}
        {content.tags && content.tags.length > 0 && (
          <View style={styles.tags}>
            {content.tags.map((tag, index) => (
              <View key={index} style={styles.tag}>
                <Text style={styles.tagText}>#{tag}</Text>
              </View>
            ))}
          </View>
        )}

        {/* Comments Section */}
        <CommentsSection contentSlug={contentSlug} />
      </View>

      {/* Image Viewer for Gallery */}
      <ImageView
        images={galleryImages}
        imageIndex={currentImageIndex}
        visible={galleryVisible}
        onRequestClose={() => setGalleryVisible(false)}
      />
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.white,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.backgroundSecondary,
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.backgroundSecondary,
  },
  errorText: {
    fontSize: FONT_SIZES.lg,
    color: COLORS.error,
  },
  featuredImage: {
    width: '100%',
    height: 250,
    resizeMode: 'cover',
  },
  video: {
    width: '100%',
    height: 250,
    backgroundColor: COLORS.black,
  },
  content: {
    padding: SPACING.lg,
  },
  typeBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    backgroundColor: COLORS.gold,
    paddingHorizontal: SPACING.sm,
    paddingVertical: SPACING.xs,
    borderRadius: 20,
    marginBottom: SPACING.md,
  },
  typeIcon: {
    fontSize: 16,
    marginRight: SPACING.xs,
  },
  typeText: {
    fontSize: FONT_SIZES.sm,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    textTransform: 'uppercase',
  },
  title: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: FONT_WEIGHTS.bold,
    color: COLORS.black,
    marginBottom: SPACING.md,
  },
  meta: {
    flexDirection: 'row',
    gap: SPACING.md,
    marginBottom: SPACING.md,
  },
  metaText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.gray600,
  },
  actions: {
    flexDirection: 'row',
    gap: SPACING.sm,
    marginBottom: SPACING.lg,
  },
  actionButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: SPACING.xs,
    backgroundColor: COLORS.gray100,
    paddingVertical: SPACING.sm,
    borderRadius: 8,
  },
  actionButtonLiked: {
    backgroundColor: COLORS.error + '20',
  },
  actionIcon: {
    fontSize: 20,
  },
  actionText: {
    fontSize: FONT_SIZES.md,
    fontWeight: FONT_WEIGHTS.medium,
    color: COLORS.gray700,
  },
  actionTextLiked: {
    color: COLORS.error,
  },
  audioPlayer: {
    backgroundColor: COLORS.gray100,
    borderRadius: 12,
    padding: SPACING.md,
    marginBottom: SPACING.lg,
  },
  audioControls: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.md,
  },
  playButton: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: COLORS.gold,
    justifyContent: 'center',
    alignItems: 'center',
  },
  playIcon: {
    fontSize: 24,
  },
  audioInfo: {
    flex: 1,
  },
  progressBarContainer: {
    height: 4,
    backgroundColor: COLORS.gray300,
    borderRadius: 2,
    marginBottom: SPACING.xs,
  },
  progressBar: {
    height: '100%',
    backgroundColor: COLORS.gold,
    borderRadius: 2,
  },
  audioTimes: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  audioTime: {
    fontSize: FONT_SIZES.xs,
    color: COLORS.gray600,
  },
  galleryGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.xs,
    marginBottom: SPACING.lg,
  },
  galleryItem: {
    width: '32%',
    aspectRatio: 1,
    borderRadius: 8,
    overflow: 'hidden',
  },
  galleryImage: {
    width: '100%',
    height: '100%',
    resizeMode: 'cover',
  },
  body: {
    marginBottom: SPACING.lg,
  },
  excerpt: {
    fontSize: FONT_SIZES.md,
    fontWeight: FONT_WEIGHTS.medium,
    color: COLORS.gray700,
    marginBottom: SPACING.md,
    fontStyle: 'italic',
  },
  bodyText: {
    fontSize: FONT_SIZES.md,
    color: COLORS.gray800,
    lineHeight: 24,
  },
  tags: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.xs,
  },
  tag: {
    backgroundColor: COLORS.info + '20',
    paddingHorizontal: SPACING.sm,
    paddingVertical: SPACING.xs,
    borderRadius: 16,
  },
  tagText: {
    fontSize: FONT_SIZES.sm,
    color: COLORS.info,
    fontWeight: FONT_WEIGHTS.medium,
  },
});

export default ContentDetailScreen;
