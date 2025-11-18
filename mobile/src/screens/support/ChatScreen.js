import React, { useState, useEffect, useRef } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TextInput,
  TouchableOpacity,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { COLORS } from '../../constants/theme';
import chatService from '../../services/chatService';

const ChatScreen = ({ navigation }) => {
  const [messages, setMessages] = useState([]);
  const [inputText, setInputText] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const flatListRef = useRef(null);

  useEffect(() => {
    navigation.setOptions({
      title: 'Support CSS',
      headerRight: () => (
        <TouchableOpacity
          onPress={handleClearHistory}
          style={{ marginRight: 15 }}
        >
          <Ionicons name="trash-outline" size={22} color={COLORS.text} />
        </TouchableOpacity>
      ),
    });

    loadMessages();

    // S'abonner aux changements de messages
    const unsubscribe = chatService.subscribe((newMessages) => {
      setMessages([...newMessages].reverse()); // Inverser pour afficher les plus récents en bas
    });

    // Démarrer le polling
    chatService.startPolling();

    // Marquer comme lu
    chatService.markAsRead();

    return () => {
      unsubscribe();
      chatService.stopPolling();
    };
  }, []);

  const loadMessages = async () => {
    try {
      setLoading(true);
      const msgs = await chatService.getMessages();
      setMessages([...msgs].reverse());
    } catch (error) {
      console.error('Error loading messages:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSend = async () => {
    if (!inputText.trim()) return;

    const text = inputText.trim();
    setInputText('');
    setSending(true);

    try {
      await chatService.sendMessage(text);

      // Simuler une réponse automatique du support
      chatService.simulateAdminResponse();

      // Scroller vers le bas
      setTimeout(() => {
        flatListRef.current?.scrollToEnd({ animated: true });
      }, 100);
    } catch (error) {
      console.error('Error sending message:', error);
      alert('Erreur lors de l\'envoi du message. Veuillez réessayer.');
    } finally {
      setSending(false);
    }
  };

  const handleClearHistory = () => {
    if (messages.length === 0) return;

    alert(
      'Effacer l\'historique',
      'Voulez-vous vraiment effacer tous les messages ?',
      [
        { text: 'Annuler', style: 'cancel' },
        {
          text: 'Effacer',
          style: 'destructive',
          onPress: async () => {
            await chatService.clearHistory();
            setMessages([]);
          },
        },
      ]
    );
  };

  const renderMessage = ({ item }) => {
    const isUser = item.sender === 'user';
    const isPending = item.pending;

    return (
      <View
        style={[
          styles.messageContainer,
          isUser ? styles.userMessage : styles.adminMessage,
        ]}
      >
        <View style={[styles.messageBubble, isUser ? styles.userBubble : styles.adminBubble]}>
          {!isUser && (
            <View style={styles.messageHeader}>
              <Text style={styles.senderName}>
                {item.user?.avatar || '👨‍💼'} {item.user?.name || 'Support CSS'}
              </Text>
            </View>
          )}
          <Text style={[styles.messageText, isUser ? styles.userText : styles.adminText]}>
            {item.text}
          </Text>
          <View style={styles.messageFooter}>
            <Text style={[styles.messageTime, isUser ? styles.userTime : styles.adminTime]}>
              {new Date(item.created_at).toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit',
              })}
            </Text>
            {isPending && (
              <ActivityIndicator size="small" color={COLORS.textLight} style={{ marginLeft: 5 }} />
            )}
          </View>
        </View>
      </View>
    );
  };

  const renderEmpty = () => {
    if (loading) {
      return (
        <View style={styles.emptyContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
          <Text style={styles.emptyText}>Chargement des messages...</Text>
        </View>
      );
    }

    return (
      <View style={styles.emptyContainer}>
        <Ionicons name="chatbubbles-outline" size={64} color={COLORS.gray} />
        <Text style={styles.emptyText}>Aucun message</Text>
        <Text style={styles.emptySubtext}>
          Envoyez un message pour contacter le support CSS
        </Text>
      </View>
    );
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
    >
      <FlatList
        ref={flatListRef}
        data={messages}
        renderItem={renderMessage}
        keyExtractor={(item) => item.id}
        contentContainerStyle={styles.messageList}
        ListEmptyComponent={renderEmpty}
        onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: true })}
      />

      <View style={styles.inputContainer}>
        <TextInput
          style={styles.input}
          placeholder="Écrivez votre message..."
          placeholderTextColor={COLORS.gray}
          value={inputText}
          onChangeText={setInputText}
          multiline
          maxLength={500}
        />
        <TouchableOpacity
          style={[styles.sendButton, (!inputText.trim() || sending) && styles.sendButtonDisabled]}
          onPress={handleSend}
          disabled={!inputText.trim() || sending}
        >
          {sending ? (
            <ActivityIndicator size="small" color={COLORS.white} />
          ) : (
            <Ionicons name="send" size={24} color={COLORS.white} />
          )}
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  messageList: {
    padding: 15,
    flexGrow: 1,
  },
  messageContainer: {
    marginBottom: 15,
    maxWidth: '80%',
  },
  userMessage: {
    alignSelf: 'flex-end',
  },
  adminMessage: {
    alignSelf: 'flex-start',
  },
  messageBubble: {
    padding: 12,
    borderRadius: 15,
  },
  userBubble: {
    backgroundColor: COLORS.primary,
    borderBottomRightRadius: 5,
  },
  adminBubble: {
    backgroundColor: COLORS.white,
    borderBottomLeftRadius: 5,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  messageHeader: {
    marginBottom: 5,
  },
  senderName: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.primary,
  },
  messageText: {
    fontSize: 15,
    lineHeight: 20,
  },
  userText: {
    color: COLORS.white,
  },
  adminText: {
    color: COLORS.text,
  },
  messageFooter: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 5,
  },
  messageTime: {
    fontSize: 11,
  },
  userTime: {
    color: COLORS.white,
    opacity: 0.8,
  },
  adminTime: {
    color: COLORS.textLight,
  },
  inputContainer: {
    flexDirection: 'row',
    padding: 15,
    backgroundColor: COLORS.white,
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
    alignItems: 'flex-end',
  },
  input: {
    flex: 1,
    backgroundColor: COLORS.background,
    borderRadius: 20,
    paddingHorizontal: 15,
    paddingVertical: 10,
    fontSize: 15,
    maxHeight: 100,
    marginRight: 10,
  },
  sendButton: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  sendButtonDisabled: {
    opacity: 0.5,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 60,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: '600',
    color: COLORS.text,
    marginTop: 15,
  },
  emptySubtext: {
    fontSize: 14,
    color: COLORS.textLight,
    marginTop: 5,
    textAlign: 'center',
    paddingHorizontal: 40,
  },
});

export default ChatScreen;
