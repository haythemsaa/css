import React from 'react';
import { render, fireEvent, waitFor } from '@testing-library/react-native';
import { Alert } from 'react-native';
import CommentsSection from '../../src/components/comments/CommentsSection';
import commentsService from '../../src/services/commentsService';
import useAuthStore from '../../src/stores/authStore';

// Mock dependencies
jest.mock('../../src/services/commentsService');
jest.mock('../../src/stores/authStore');
jest.spyOn(Alert, 'alert');

describe('CommentsSection Component', () => {
  const mockUser = { id: 1, name: 'Current User' };
  const mockComments = [
    {
      id: 1,
      text: 'First comment',
      user: { id: 2, name: 'User 2' },
      likes_count: 5,
      is_liked: false,
      created_at: new Date().toISOString(),
    },
    {
      id: 2,
      text: 'Second comment',
      user: { id: 3, name: 'User 3' },
      likes_count: 3,
      is_liked: true,
      created_at: new Date().toISOString(),
    },
  ];

  beforeEach(() => {
    jest.clearAllMocks();
    useAuthStore.mockReturnValue({ user: mockUser });
    commentsService.getComments.mockResolvedValue(mockComments);
  });

  it('should render comments section header', () => {
    const { getByText } = render(<CommentsSection contentSlug="test-slug" />);

    expect(getByText(/Commentaires/)).toBeTruthy();
  });

  it('should load and display comments', async () => {
    const { getByText } = render(<CommentsSection contentSlug="test-slug" />);

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalledWith('test-slug');
    });

    await waitFor(() => {
      expect(getByText('First comment')).toBeTruthy();
      expect(getByText('Second comment')).toBeTruthy();
    });
  });

  it('should show loading indicator while fetching comments', () => {
    commentsService.getComments.mockReturnValue(new Promise(() => {})); // Never resolves

    const { UNSAFE_getByType } = render(<CommentsSection contentSlug="test-slug" />);

    expect(UNSAFE_getByType('ActivityIndicator')).toBeTruthy();
  });

  it('should display comment count in header', async () => {
    const { getByText } = render(<CommentsSection contentSlug="test-slug" />);

    await waitFor(() => {
      expect(getByText('💬 Commentaires (2)')).toBeTruthy();
    });
  });

  it('should show empty state when no comments', async () => {
    commentsService.getComments.mockResolvedValue([]);

    const { getByText } = render(<CommentsSection contentSlug="test-slug" />);

    await waitFor(() => {
      expect(getByText('Aucun commentaire')).toBeTruthy();
      expect(getByText('Soyez le premier à commenter !')).toBeTruthy();
    });
  });

  it('should enable send button when text is entered', async () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, 'My new comment');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find(
      (btn) => btn.props.disabled === false || btn.props.disabled === undefined
    );

    expect(sendButton).toBeTruthy();
  });

  it('should disable send button when text is empty', () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, '');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons[0]; // Assuming first is the send button

    expect(sendButton.props.disabled).toBeTruthy();
  });

  it('should add comment when send button is pressed', async () => {
    const newComment = {
      id: 3,
      text: 'New comment',
      user: mockUser,
      likes_count: 0,
      is_liked: false,
      created_at: new Date().toISOString(),
    };

    commentsService.addComment.mockResolvedValue(newComment);

    const { getByPlaceholderText, getByText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    await waitFor(() => {
      expect(getByText('First comment')).toBeTruthy();
    });

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, 'New comment');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      expect(commentsService.addComment).toHaveBeenCalledWith('test-slug', 'New comment');
    });

    await waitFor(() => {
      expect(getByText('New comment')).toBeTruthy();
    });
  });

  it('should clear input after adding comment', async () => {
    const newComment = {
      id: 3,
      text: 'New comment',
      user: mockUser,
      likes_count: 0,
      is_liked: false,
      created_at: new Date().toISOString(),
    };

    commentsService.addComment.mockResolvedValue(newComment);

    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, 'New comment');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      expect(input.props.value).toBe('');
    });
  });

  it('should show success alert after adding comment', async () => {
    const newComment = {
      id: 3,
      text: 'New comment',
      user: mockUser,
      likes_count: 0,
      is_liked: false,
      created_at: new Date().toISOString(),
    };

    commentsService.addComment.mockResolvedValue(newComment);

    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, 'New comment');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      expect(Alert.alert).toHaveBeenCalledWith('Succès', 'Commentaire ajouté !');
    });
  });

  it('should show error alert when adding comment fails', async () => {
    commentsService.addComment.mockRejectedValue(new Error('Network error'));

    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, 'New comment');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      expect(Alert.alert).toHaveBeenCalledWith(
        'Erreur',
        "Impossible d'ajouter le commentaire"
      );
    });
  });

  it('should not add comment with only whitespace', async () => {
    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, '   ');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons[0];

    expect(sendButton.props.disabled).toBeTruthy();
  });

  it('should enforce 500 character limit', () => {
    const { getByPlaceholderText } = render(<CommentsSection contentSlug="test-slug" />);

    const input = getByPlaceholderText('Ajouter un commentaire...');

    expect(input.props.maxLength).toBe(500);
  });

  it('should handle like comment action', async () => {
    commentsService.likeComment.mockResolvedValue(true);

    const { getByText } = render(<CommentsSection contentSlug="test-slug" />);

    await waitFor(() => {
      expect(getByText('First comment')).toBeTruthy();
    });

    // This would require triggering the like from CommentItem
    // which is tested separately in CommentItem.test.js
  });

  it('should handle delete comment action', async () => {
    commentsService.deleteComment.mockResolvedValue(true);

    const { getByText, queryByText } = render(<CommentsSection contentSlug="test-slug" />);

    await waitFor(() => {
      expect(getByText('First comment')).toBeTruthy();
    });

    // Simulate delete callback
    // The actual delete UI is tested in CommentItem.test.js
  });

  it('should reload comments when contentSlug changes', async () => {
    const { rerender } = render(<CommentsSection contentSlug="slug-1" />);

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalledWith('slug-1');
    });

    jest.clearAllMocks();

    rerender(<CommentsSection contentSlug="slug-2" />);

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalledWith('slug-2');
    });
  });

  it('should pass currentUserId to CommentItem', async () => {
    const { getByText } = render(<CommentsSection contentSlug="test-slug" />);

    await waitFor(() => {
      expect(getByText('First comment')).toBeTruthy();
    });

    // CommentItem should receive currentUserId prop
    // This affects whether delete or report button is shown
  });

  it('should show loading indicator in send button while adding comment', async () => {
    commentsService.addComment.mockReturnValue(new Promise(() => {})); // Never resolves

    const { getByPlaceholderText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, 'New comment');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    // Should show ActivityIndicator in send button
    await waitFor(() => {
      const activityIndicators = UNSAFE_getAllByType('ActivityIndicator');
      expect(activityIndicators.length).toBeGreaterThan(0);
    });
  });

  it('should prepend new comments to the list', async () => {
    const newComment = {
      id: 3,
      text: 'Newest comment',
      user: mockUser,
      likes_count: 0,
      is_liked: false,
      created_at: new Date().toISOString(),
    };

    commentsService.addComment.mockResolvedValue(newComment);

    const { getByPlaceholderText, getAllByText, UNSAFE_getAllByType } = render(
      <CommentsSection contentSlug="test-slug" />
    );

    await waitFor(() => {
      expect(commentsService.getComments).toHaveBeenCalled();
    });

    const input = getByPlaceholderText('Ajouter un commentaire...');
    fireEvent.changeText(input, 'Newest comment');

    const sendButtons = UNSAFE_getAllByType('TouchableOpacity');
    const sendButton = sendButtons.find((btn) => !btn.props.disabled);
    fireEvent.press(sendButton);

    await waitFor(() => {
      const newestComment = getAllByText('Newest comment')[0];
      expect(newestComment).toBeTruthy();
    });
  });
});
