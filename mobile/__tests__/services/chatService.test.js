import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import chatService from '../../src/services/chatService';

// Mock axios
jest.mock('axios');

// Mock AsyncStorage
jest.mock('@react-native-async-storage/async-storage', () => ({
  getItem: jest.fn(),
  setItem: jest.fn(),
}));

describe('ChatService', () => {
  const mockToken = 'mock-token-123';
  const mockUser = { id: 1, name: 'Test User' };

  beforeEach(() => {
    jest.clearAllMocks();
    AsyncStorage.getItem.mockImplementation((key) => {
      if (key === 'auth_token') return Promise.resolve(mockToken);
      if (key === 'user') return Promise.resolve(JSON.stringify(mockUser));
      return Promise.resolve(null);
    });
    chatService.stopPolling();
    chatService.messages = [];
    chatService.listeners = [];
  });

  afterEach(() => {
    chatService.stopPolling();
  });

  describe('getMessages', () => {
    it('should fetch messages from API', async () => {
      const mockMessages = [
        { id: 1, text: 'Hello', sender: 'user', created_at: '2025-01-01T10:00:00Z' },
        { id: 2, text: 'Hi!', sender: 'admin', created_at: '2025-01-01T10:01:00Z' },
      ];

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { messages: mockMessages },
        },
      });

      const messages = await chatService.getMessages();

      expect(axios.get).toHaveBeenCalledWith(
        expect.stringContaining('/support/messages'),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(messages).toEqual(mockMessages);
      expect(chatService.messages).toEqual(mockMessages);
    });

    it('should return cached messages on API error', async () => {
      const cachedMessages = [{ id: 1, text: 'Cached', sender: 'user' }];
      chatService.messages = cachedMessages;

      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const messages = await chatService.getMessages();

      expect(messages).toEqual(cachedMessages);
    });

    it('should return mock messages when API fails', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const messages = await chatService.getMessages();

      expect(messages.length).toBeGreaterThan(0);
      expect(messages[0]).toHaveProperty('text');
      expect(messages[0]).toHaveProperty('sender');
    });
  });

  describe('sendMessage', () => {
    it('should send message to API', async () => {
      const text = 'Test message';
      const mockResponse = {
        id: 3,
        text,
        sender: 'user',
        created_at: new Date().toISOString(),
      };

      axios.post.mockResolvedValueOnce({
        data: {
          success: true,
          data: { message: mockResponse },
        },
      });

      const message = await chatService.sendMessage(text);

      expect(axios.post).toHaveBeenCalledWith(
        expect.stringContaining('/support/messages'),
        { text },
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(message).toEqual(mockResponse);
    });

    it('should add optimistic message before API call', async () => {
      const text = 'Test message';
      let messagesAtStart = [];

      axios.post.mockImplementationOnce(() => {
        messagesAtStart = [...chatService.messages];
        return Promise.resolve({
          data: {
            success: true,
            data: { message: { id: 1, text, sender: 'user' } },
          },
        });
      });

      await chatService.sendMessage(text);

      expect(messagesAtStart.length).toBeGreaterThan(0);
      expect(messagesAtStart[0].text).toBe(text);
      expect(messagesAtStart[0].pending).toBe(true);
    });

    it('should throw error when text is empty', async () => {
      await expect(chatService.sendMessage('')).rejects.toThrow();
    });
  });

  describe('markAsRead', () => {
    it('should mark message as read via API', async () => {
      const messageId = 1;

      axios.post.mockResolvedValueOnce({
        data: { success: true },
      });

      await chatService.markAsRead(messageId);

      expect(axios.post).toHaveBeenCalledWith(
        expect.stringContaining(`/support/messages/${messageId}/read`),
        {},
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
    });

    it('should handle API errors gracefully', async () => {
      axios.post.mockRejectedValueOnce(new Error('Network error'));

      await expect(chatService.markAsRead(1)).rejects.toThrow();
    });
  });

  describe('polling', () => {
    it('should start polling messages', () => {
      chatService.startPolling();

      expect(chatService.pollingInterval).toBeDefined();
    });

    it('should stop polling messages', () => {
      chatService.startPolling();
      const intervalId = chatService.pollingInterval;

      chatService.stopPolling();

      expect(chatService.pollingInterval).toBeNull();
    });

    it('should not start multiple polling intervals', () => {
      chatService.startPolling();
      const firstInterval = chatService.pollingInterval;

      chatService.startPolling();
      const secondInterval = chatService.pollingInterval;

      expect(firstInterval).toBe(secondInterval);
    });
  });

  describe('listeners', () => {
    it('should subscribe to message updates', () => {
      const callback = jest.fn();

      const unsubscribe = chatService.subscribe(callback);

      expect(typeof unsubscribe).toBe('function');
      expect(chatService.listeners).toContain(callback);
    });

    it('should unsubscribe from message updates', () => {
      const callback = jest.fn();

      const unsubscribe = chatService.subscribe(callback);
      unsubscribe();

      expect(chatService.listeners).not.toContain(callback);
    });

    it('should notify all listeners on message update', () => {
      const callback1 = jest.fn();
      const callback2 = jest.fn();

      chatService.subscribe(callback1);
      chatService.subscribe(callback2);

      chatService.notifyListeners();

      expect(callback1).toHaveBeenCalledWith(chatService.messages);
      expect(callback2).toHaveBeenCalledWith(chatService.messages);
    });
  });

  describe('simulateAdminResponse', () => {
    it('should add admin response after delay', async () => {
      jest.useFakeTimers();

      chatService.simulateAdminResponse();

      expect(chatService.messages.length).toBe(0);

      jest.advanceTimersByTime(2000);

      await Promise.resolve(); // Wait for async operations

      expect(chatService.messages.length).toBeGreaterThan(0);
      expect(chatService.messages[0].sender).toBe('admin');

      jest.useRealTimers();
    });
  });

  describe('generateMockMessages', () => {
    it('should generate mock conversation', () => {
      const mockMessages = chatService.generateMockMessages();

      expect(mockMessages.length).toBeGreaterThan(0);
      expect(mockMessages[0]).toHaveProperty('id');
      expect(mockMessages[0]).toHaveProperty('text');
      expect(mockMessages[0]).toHaveProperty('sender');
      expect(mockMessages[0]).toHaveProperty('created_at');
    });

    it('should include both user and admin messages', () => {
      const mockMessages = chatService.generateMockMessages();

      const hasUserMessage = mockMessages.some((m) => m.sender === 'user');
      const hasAdminMessage = mockMessages.some((m) => m.sender === 'admin');

      expect(hasUserMessage).toBe(true);
      expect(hasAdminMessage).toBe(true);
    });
  });
});
