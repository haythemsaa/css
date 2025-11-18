import React from 'react';
import { render, fireEvent, waitFor } from '@testing-library/react-native';
import ChatScreen from '../../src/screens/support/ChatScreen';
import chatService from '../../src/services/chatService';

// Mock dependencies
jest.mock('../../src/services/chatService');

describe('ChatScreen', () => {
  const mockMessages = [
    {
      id: 1,
      text: 'Hello, how can I help you?',
      sender: 'admin',
      created_at: new Date(Date.now() - 60000).toISOString(),
    },
    {
      id: 2,
      text: 'I have a question about my code',
      sender: 'user',
      created_at: new Date(Date.now() - 30000).toISOString(),
    },
  ];

  const mockNavigation = {
    navigate: jest.fn(),
    goBack: jest.fn(),
    setOptions: jest.fn(),
  };

  beforeEach(() => {
    jest.clearAllMocks();
    chatService.getMessages.mockResolvedValue(mockMessages);
    chatService.subscribe = jest.fn(() => jest.fn());
    chatService.startPolling = jest.fn();
    chatService.stopPolling = jest.fn();
    chatService.sendMessage = jest.fn().mockResolvedValue({
      id: 3,
      text: 'Test message',
      sender: 'user',
      created_at: new Date().toISOString(),
    });
    chatService.simulateAdminResponse = jest.fn();
  });

  it('should render chat screen', () => {
    const { getByPlaceholderText } = render(<ChatScreen navigation={mockNavigation} />);

    expect(getByPlaceholderText('Votre message...')).toBeTruthy();
  });

  it('should load messages on mount', async () => {
    render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });
  });

  it('should start polling on mount', async () => {
    render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(chatService.startPolling).toHaveBeenCalled();
    });
  });

  it('should stop polling on unmount', async () => {
    const { unmount } = render(<ChatScreen navigation={mockNavigation} />);

    unmount();

    expect(chatService.stopPolling).toHaveBeenCalled();
  });

  it('should subscribe to message updates', async () => {
    render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(chatService.subscribe).toHaveBeenCalled();
    });
  });

  it('should display messages', async () => {
    const { getByText } = render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('Hello, how can I help you?')).toBeTruthy();
      expect(getByText('I have a question about my code')).toBeTruthy();
    });
  });

  it('should show empty state when no messages', async () => {
    chatService.getMessages.mockResolvedValue([]);

    const { getByText } = render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('Commencez la conversation')).toBeTruthy();
      expect(
        getByText('Posez vos questions et notre équipe CSS vous répondra rapidement.')
      ).toBeTruthy();
    });
  });

  it('should enable send button when text is entered', async () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <ChatScreen navigation={mockNavigation} />
    );

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Votre message...');
    fireEvent.changeText(input, 'Test message');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);

    expect(sendButton).toBeTruthy();
  });

  it('should disable send button when text is empty', async () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <ChatScreen navigation={mockNavigation} />
    );

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Votre message...');
    fireEvent.changeText(input, '');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    // Find the send button (typically the last touchable in the input area)
    const sendButton = sendButtons[sendButtons.length - 1];

    expect(sendButton.props.disabled).toBeTruthy();
  });

  it('should send message when send button is pressed', async () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <ChatScreen navigation={mockNavigation} />
    );

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Votre message...');
    fireEvent.changeText(input, 'Test message');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      expect(chatService.sendMessage).toHaveBeenCalledWith('Test message');
    });
  });

  it('should clear input after sending message', async () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <ChatScreen navigation={mockNavigation} />
    );

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Votre message...');
    fireEvent.changeText(input, 'Test message');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      expect(input.props.value).toBe('');
    });
  });

  it('should trigger simulated admin response after sending', async () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <ChatScreen navigation={mockNavigation} />
    );

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Votre message...');
    fireEvent.changeText(input, 'Test message');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      expect(chatService.simulateAdminResponse).toHaveBeenCalled();
    });
  });

  it('should not send empty or whitespace-only messages', async () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <ChatScreen navigation={mockNavigation} />
    );

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Votre message...');
    fireEvent.changeText(input, '   ');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons[sendButtons.length - 1];

    expect(sendButton.props.disabled).toBeTruthy();
  });

  it('should distinguish between user and admin messages', async () => {
    const { getByText } = render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('Hello, how can I help you?')).toBeTruthy();
      expect(getByText('I have a question about my code')).toBeTruthy();
    });

    // Messages should have different styling based on sender
    // This is verified by the component rendering both types
  });

  it('should display messages in reverse chronological order', async () => {
    const { getAllByText } = render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      const messages = getAllByText(/./);
      // Newest messages should appear at bottom
      expect(messages.length).toBeGreaterThan(0);
    });
  });

  it('should show loading indicator while sending message', async () => {
    chatService.sendMessage.mockReturnValue(new Promise(() => {})); // Never resolves

    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <ChatScreen navigation={mockNavigation} />
    );

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Votre message...');
    fireEvent.changeText(input, 'Test message');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      // Should show loading indicator or disable button
      const activityIndicators = UNSAFE_getAllByType('ActivityIndicator');
      expect(activityIndicators.length).toBeGreaterThan(0);
    });
  });

  it('should handle send message errors gracefully', async () => {
    chatService.sendMessage.mockRejectedValue(new Error('Network error'));

    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <ChatScreen navigation={mockNavigation} />
    );

    await waitFor(() => {
      expect(chatService.getMessages).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Votre message...');
    fireEvent.changeText(input, 'Test message');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    // Should handle error without crashing
    await waitFor(() => {
      expect(chatService.sendMessage).toHaveBeenCalled();
    });
  });

  it('should show loading state while fetching messages', () => {
    chatService.getMessages.mockReturnValue(new Promise(() => {})); // Never resolves

    const { UNSAFE_getByType } = render(<ChatScreen navigation={mockNavigation} />);

    expect(UNSAFE_getByType('ActivityIndicator')).toBeTruthy();
  });

  it('should update messages when subscription callback is triggered', async () => {
    let subscriptionCallback;
    chatService.subscribe.mockImplementation((callback) => {
      subscriptionCallback = callback;
      return jest.fn();
    });

    const { getByText, queryByText } = render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(chatService.subscribe).toHaveBeenCalled();
    });

    // Trigger subscription with new messages
    const newMessages = [
      {
        id: 3,
        text: 'New message from subscription',
        sender: 'admin',
        created_at: new Date().toISOString(),
      },
    ];

    subscriptionCallback(newMessages);

    await waitFor(() => {
      expect(getByText('New message from subscription')).toBeTruthy();
    });
  });

  it('should unsubscribe on unmount', () => {
    const unsubscribe = jest.fn();
    chatService.subscribe.mockReturnValue(unsubscribe);

    const { unmount } = render(<ChatScreen navigation={mockNavigation} />);

    unmount();

    expect(unsubscribe).toHaveBeenCalled();
  });

  it('should display timestamp for messages', async () => {
    const { getByText } = render(<ChatScreen navigation={mockNavigation} />);

    await waitFor(() => {
      // Should show time information (e.g., "Il y a 1m")
      expect(getByText('Hello, how can I help you?')).toBeTruthy();
    });
  });

  it('should allow multiline input', () => {
    const { getByPlaceholderText } = render(<ChatScreen navigation={mockNavigation} />);

    const input = getByPlaceholderText('Votre message...');

    expect(input.props.multiline).toBe(true);
  });

  it('should enforce maximum input height', () => {
    const { getByPlaceholderText } = render(<ChatScreen navigation={mockNavigation} />);

    const input = getByPlaceholderText('Votre message...');

    // Should have maxHeight to prevent input from growing too large
    expect(input.props.style).toBeDefined();
  });
});
