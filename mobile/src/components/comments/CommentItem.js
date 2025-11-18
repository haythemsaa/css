import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Alert } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { COLORS } from '../../constants/theme';

const CommentItem = ({ comment, currentUserId, onLike, onDelete, onReport }) => {
  const [isLiked, setIsLiked] = useState(comment.is_liked);
  const [likesCount, setLikesCount] = useState(comment.likes_count);
  const [isDeleting, setIsDeleting] = useState(false);

  const handleLike = async () => {
    try {
      const newIsLiked = !isLiked;
      setIsLiked(newIsLiked);
      setLikesCount(prev => newIsLiked ? prev + 1 : prev - 1);

      await onLike(comment.id, newIsLiked);
    } catch (error) {
      // Revert on error
      setIsLiked(!isLiked);
      setLikesCount(prev => isLiked ? prev + 1 : prev - 1);
    }
  };

  const handleDelete = () => {
    Alert.alert(
      'Supprimer le commentaire',
      'Êtes-vous sûr de vouloir supprimer ce commentaire ?',
      [
        { text: 'Annuler', style: 'cancel' },
        {
          text: 'Supprimer',
          style: 'destructive',
          onPress: async () => {
            try {
              setIsDeleting(true);
              await onDelete(comment.id);
            } catch (error) {
              setIsDeleting(false);
              Alert.alert('Erreur', 'Impossible de supprimer le commentaire');
            }
          },
        },
      ]
    );
  };

  const handleReport = () => {
    Alert.alert(
      'Signaler le commentaire',
      'Pourquoi signalez-vous ce commentaire ?',
      [
        { text: 'Annuler', style: 'cancel' },
        {
          text: 'Spam',
          onPress: () => onReport(comment.id, 'spam'),
        },
        {
          text: 'Contenu inapproprié',
          onPress: () => onReport(comment.id, 'inappropriate'),
        },
        {
          text: 'Harcèlement',
          onPress: () => onReport(comment.id, 'harassment'),
        },
      ]
    );
  };

  const getTimeAgo = (date) => {
    const now = new Date();
    const commentDate = new Date(date);
    const diffInMs = now - commentDate;
    const diffInMins = Math.floor(diffInMs / 60000);
    const diffInHours = Math.floor(diffInMs / 3600000);
    const diffInDays = Math.floor(diffInMs / 86400000);

    if (diffInMins < 1) return 'À l\'instant';
    if (diffInMins < 60) return `Il y a ${diffInMins}m`;
    if (diffInHours < 24) return `Il y a ${diffInHours}h`;
    if (diffInDays < 7) return `Il y a ${diffInDays}j`;
    return commentDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
  };

  if (isDeleting) {
    return null;
  }

  const isOwner = comment.user.id === currentUserId;

  return (
    <View style={styles.container}>
      <View style={styles.avatar}>
        <Ionicons name="person-circle" size={40} color={COLORS.gray} />
      </View>

      <View style={styles.content}>
        <View style={styles.header}>
          <Text style={styles.username}>{comment.user.name}</Text>
          <Text style={styles.time}>{getTimeAgo(comment.created_at)}</Text>
        </View>

        <Text style={styles.text}>{comment.text}</Text>

        <View style={styles.actions}>
          <TouchableOpacity style={styles.actionButton} onPress={handleLike}>
            <Ionicons
              name={isLiked ? 'heart' : 'heart-outline'}
              size={18}
              color={isLiked ? COLORS.error : COLORS.textLight}
            />
            {likesCount > 0 && (
              <Text style={[styles.actionText, isLiked && styles.actionTextActive]}>
                {likesCount}
              </Text>
            )}
          </TouchableOpacity>

          {isOwner ? (
            <TouchableOpacity style={styles.actionButton} onPress={handleDelete}>
              <Ionicons name="trash-outline" size={18} color={COLORS.error} />
              <Text style={[styles.actionText, { color: COLORS.error }]}>Supprimer</Text>
            </TouchableOpacity>
          ) : (
            <TouchableOpacity style={styles.actionButton} onPress={handleReport}>
              <Ionicons name="flag-outline" size={18} color={COLORS.textLight} />
              <Text style={styles.actionText}>Signaler</Text>
            </TouchableOpacity>
          )}
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border,
  },
  avatar: {
    marginRight: 12,
  },
  content: {
    flex: 1,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 5,
  },
  username: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.text,
  },
  time: {
    fontSize: 12,
    color: COLORS.textLight,
  },
  text: {
    fontSize: 14,
    color: COLORS.text,
    lineHeight: 20,
    marginBottom: 10,
  },
  actions: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    marginRight: 20,
  },
  actionText: {
    fontSize: 13,
    color: COLORS.textLight,
    marginLeft: 5,
  },
  actionTextActive: {
    color: COLORS.error,
  },
});

export default CommentItem;
