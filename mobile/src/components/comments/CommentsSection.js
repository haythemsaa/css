import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { COLORS } from '../../constants/theme';
import commentsService from '../../services/commentsService';
import CommentItem from './CommentItem';
import useAuthStore from '../../stores/authStore';

const CommentsSection = ({ contentSlug }) => {
  const [comments, setComments] = useState([]);
  const [commentText, setCommentText] = useState('');
  const [loading, setLoading] = useState(true);
  const [adding, setAdding] = useState(false);
  const { user } = useAuthStore();

  useEffect(() => {
    loadComments();
  }, [contentSlug]);

  const loadComments = async () => {
    try {
      setLoading(true);
      const data = await commentsService.getComments(contentSlug);
      setComments(data);
    } catch (error) {
      console.error('Error loading comments:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleAddComment = async () => {
    if (!commentText.trim()) return;

    try {
      setAdding(true);
      const newComment = await commentsService.addComment(contentSlug, commentText.trim());
      setComments([newComment, ...comments]);
      setCommentText('');
      Alert.alert('Succès', 'Commentaire ajouté !');
    } catch (error) {
      console.error('Error adding comment:', error);
      Alert.alert('Erreur', 'Impossible d\'ajouter le commentaire');
    } finally {
      setAdding(false);
    }
  };

  const handleLikeComment = async (commentId, isLiked) => {
    try {
      if (isLiked) {
        await commentsService.likeComment(commentId);
      } else {
        await commentsService.unlikeComment(commentId);
      }
    } catch (error) {
      console.error('Error liking comment:', error);
      throw error;
    }
  };

  const handleDeleteComment = async (commentId) => {
    try {
      await commentsService.deleteComment(commentId);
      setComments(comments.filter(c => c.id !== commentId));
      Alert.alert('Succès', 'Commentaire supprimé');
    } catch (error) {
      console.error('Error deleting comment:', error);
      throw error;
    }
  };

  const handleReportComment = async (commentId, reason) => {
    try {
      await commentsService.reportComment(commentId, reason);
      Alert.alert('Merci', 'Le commentaire a été signalé et sera examiné par notre équipe.');
    } catch (error) {
      console.error('Error reporting comment:', error);
      Alert.alert('Erreur', 'Impossible de signaler le commentaire');
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>💬 Commentaires ({comments.length})</Text>
      </View>

      {/* Add Comment Input */}
      <View style={styles.addCommentContainer}>
        <TextInput
          style={styles.input}
          placeholder="Ajouter un commentaire..."
          placeholderTextColor={COLORS.gray}
          value={commentText}
          onChangeText={setCommentText}
          multiline
          maxLength={500}
        />
        <TouchableOpacity
          style={[
            styles.sendButton,
            (!commentText.trim() || adding) && styles.sendButtonDisabled,
          ]}
          onPress={handleAddComment}
          disabled={!commentText.trim() || adding}
        >
          {adding ? (
            <ActivityIndicator size="small" color={COLORS.white} />
          ) : (
            <Ionicons name="send" size={20} color={COLORS.white} />
          )}
        </TouchableOpacity>
      </View>

      {/* Comments List */}
      {loading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
        </View>
      ) : comments.length > 0 ? (
        <View style={styles.commentsList}>
          {comments.map((comment) => (
            <CommentItem
              key={comment.id}
              comment={comment}
              currentUserId={user?.id}
              onLike={handleLikeComment}
              onDelete={handleDeleteComment}
              onReport={handleReportComment}
            />
          ))}
        </View>
      ) : (
        <View style={styles.emptyContainer}>
          <Ionicons name="chatbubbles-outline" size={48} color={COLORS.gray} />
          <Text style={styles.emptyText}>Aucun commentaire</Text>
          <Text style={styles.emptySubtext}>Soyez le premier à commenter !</Text>
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginTop: 30,
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
    paddingTop: 20,
  },
  header: {
    marginBottom: 15,
  },
  title: {
    fontSize: 18,
    fontWeight: '700',
    color: COLORS.text,
  },
  addCommentContainer: {
    flexDirection: 'row',
    alignItems: 'flex-end',
    marginBottom: 20,
    backgroundColor: COLORS.white,
    borderRadius: 25,
    borderWidth: 1,
    borderColor: COLORS.border,
    padding: 10,
  },
  input: {
    flex: 1,
    fontSize: 15,
    color: COLORS.text,
    maxHeight: 100,
    paddingHorizontal: 10,
  },
  sendButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
    marginLeft: 10,
  },
  sendButtonDisabled: {
    opacity: 0.5,
  },
  loadingContainer: {
    paddingVertical: 40,
    alignItems: 'center',
  },
  commentsList: {
    marginTop: 10,
  },
  emptyContainer: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  emptyText: {
    fontSize: 16,
    fontWeight: '600',
    color: COLORS.text,
    marginTop: 15,
  },
  emptySubtext: {
    fontSize: 14,
    color: COLORS.textLight,
    marginTop: 5,
  },
});

export default CommentsSection;
