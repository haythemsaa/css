import React from 'react';
import { render, fireEvent, waitFor } from '@testing-library/react-native';
import { Alert } from 'react-native';
import CommentItem from '../../src/components/comments/CommentItem';

// Mock Alert
jest.spyOn(Alert, 'alert');

describe('CommentItem Component', () => {
  const mockComment = {
    id: 1,
    text: 'This is a test comment',
    user: {
      id: 2,
      name: 'Test User',
      avatar: null,
    },
    likes_count: 5,
    is_liked: false,
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(), // 2 hours ago
  };

  const mockCurrentUserId = 1;
  const mockOnLike = jest.fn();
  const mockOnDelete = jest.fn();
  const mockOnReport = jest.fn();

  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('should render comment correctly', () => {
    const { getByText } = render(
      <CommentItem
        comment={mockComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    expect(getByText('Test User')).toBeTruthy();
    expect(getByText('This is a test comment')).toBeTruthy();
    expect(getByText('5')).toBeTruthy();
  });

  it('should display time ago correctly', () => {
    const { getByText } = render(
      <CommentItem
        comment={mockComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    expect(getByText('Il y a 2h')).toBeTruthy();
  });

  it('should handle like action', async () => {
    mockOnLike.mockResolvedValueOnce(true);

    const { getByTestId, getAllByText } = render(
      <CommentItem
        comment={mockComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    // Find and press the like button (heart icon)
    const likeButtons = getAllByText('5')[0].parent;
    fireEvent.press(likeButtons);

    await waitFor(() => {
      expect(mockOnLike).toHaveBeenCalledWith(1, true);
    });
  });

  it('should toggle like state optimistically', async () => {
    mockOnLike.mockResolvedValueOnce(true);

    const { getByText, getAllByText } = render(
      <CommentItem
        comment={mockComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    const likeCountBefore = getByText('5');
    expect(likeCountBefore).toBeTruthy();

    // Press like button
    const likeButtons = getAllByText('5')[0].parent;
    fireEvent.press(likeButtons);

    await waitFor(() => {
      expect(getByText('6')).toBeTruthy();
    });
  });

  it('should revert like on error', async () => {
    mockOnLike.mockRejectedValueOnce(new Error('Network error'));

    const { getByText, getAllByText } = render(
      <CommentItem
        comment={{ ...mockComment, is_liked: false, likes_count: 5 }}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    const likeButtons = getAllByText('5')[0].parent;
    fireEvent.press(likeButtons);

    // Should increment optimistically
    await waitFor(() => {
      expect(getByText('6')).toBeTruthy();
    });

    // Should revert on error
    await waitFor(() => {
      expect(getByText('5')).toBeTruthy();
    });
  });

  it('should show delete button for comment owner', () => {
    const ownComment = { ...mockComment, user: { ...mockComment.user, id: mockCurrentUserId } };

    const { getByText } = render(
      <CommentItem
        comment={ownComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    expect(getByText('Supprimer')).toBeTruthy();
  });

  it('should show report button for non-owner comments', () => {
    const { getByText } = render(
      <CommentItem
        comment={mockComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    expect(getByText('Signaler')).toBeTruthy();
  });

  it('should confirm before deleting comment', () => {
    const ownComment = { ...mockComment, user: { ...mockComment.user, id: mockCurrentUserId } };

    const { getByText } = render(
      <CommentItem
        comment={ownComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    fireEvent.press(getByText('Supprimer'));

    expect(Alert.alert).toHaveBeenCalledWith(
      'Supprimer le commentaire',
      'Êtes-vous sûr de vouloir supprimer ce commentaire ?',
      expect.any(Array)
    );
  });

  it('should call onDelete when delete is confirmed', () => {
    const ownComment = { ...mockComment, user: { ...mockComment.user, id: mockCurrentUserId } };
    mockOnDelete.mockResolvedValueOnce(true);

    Alert.alert.mockImplementation((title, message, buttons) => {
      const deleteButton = buttons.find((b) => b.text === 'Supprimer');
      if (deleteButton && deleteButton.onPress) {
        deleteButton.onPress();
      }
    });

    const { getByText } = render(
      <CommentItem
        comment={ownComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    fireEvent.press(getByText('Supprimer'));

    expect(mockOnDelete).toHaveBeenCalledWith(1);
  });

  it('should show report options when report is pressed', () => {
    const { getByText } = render(
      <CommentItem
        comment={mockComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    fireEvent.press(getByText('Signaler'));

    expect(Alert.alert).toHaveBeenCalledWith(
      'Signaler le commentaire',
      'Pourquoi signalez-vous ce commentaire ?',
      expect.arrayContaining([
        expect.objectContaining({ text: 'Spam' }),
        expect.objectContaining({ text: 'Contenu inapproprié' }),
        expect.objectContaining({ text: 'Harcèlement' }),
      ])
    );
  });

  it('should call onReport with correct reason', () => {
    Alert.alert.mockImplementation((title, message, buttons) => {
      const spamButton = buttons.find((b) => b.text === 'Spam');
      if (spamButton && spamButton.onPress) {
        spamButton.onPress();
      }
    });

    const { getByText } = render(
      <CommentItem
        comment={mockComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    fireEvent.press(getByText('Signaler'));

    expect(mockOnReport).toHaveBeenCalledWith(1, 'spam');
  });

  it('should hide comment when deleting', async () => {
    const ownComment = { ...mockComment, user: { ...mockComment.user, id: mockCurrentUserId } };
    mockOnDelete.mockImplementation(() => new Promise(() => {})); // Never resolves

    Alert.alert.mockImplementation((title, message, buttons) => {
      const deleteButton = buttons.find((b) => b.text === 'Supprimer');
      if (deleteButton && deleteButton.onPress) {
        deleteButton.onPress();
      }
    });

    const { getByText, queryByText } = render(
      <CommentItem
        comment={ownComment}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    fireEvent.press(getByText('Supprimer'));

    // Component should hide while deleting
    await waitFor(() => {
      expect(queryByText('This is a test comment')).toBeNull();
    });
  });

  it('should handle comments without likes', () => {
    const commentWithoutLikes = { ...mockComment, likes_count: 0 };

    const { queryByText } = render(
      <CommentItem
        comment={commentWithoutLikes}
        currentUserId={mockCurrentUserId}
        onLike={mockOnLike}
        onDelete={mockOnDelete}
        onReport={mockOnReport}
      />
    );

    // Like count should not be displayed when 0
    expect(queryByText('0')).toBeNull();
  });

  describe('getTimeAgo', () => {
    it('should show "À l\'instant" for very recent comments', () => {
      const recentComment = {
        ...mockComment,
        created_at: new Date(Date.now() - 30 * 1000).toISOString(), // 30 seconds ago
      };

      const { getByText } = render(
        <CommentItem
          comment={recentComment}
          currentUserId={mockCurrentUserId}
          onLike={mockOnLike}
          onDelete={mockOnDelete}
          onReport={mockOnReport}
        />
      );

      expect(getByText("À l'instant")).toBeTruthy();
    });

    it('should show minutes for comments less than 1 hour old', () => {
      const recentComment = {
        ...mockComment,
        created_at: new Date(Date.now() - 30 * 60 * 1000).toISOString(), // 30 minutes ago
      };

      const { getByText } = render(
        <CommentItem
          comment={recentComment}
          currentUserId={mockCurrentUserId}
          onLike={mockOnLike}
          onDelete={mockOnDelete}
          onReport={mockOnReport}
        />
      );

      expect(getByText('Il y a 30m')).toBeTruthy();
    });

    it('should show days for comments less than 1 week old', () => {
      const oldComment = {
        ...mockComment,
        created_at: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000).toISOString(), // 3 days ago
      };

      const { getByText } = render(
        <CommentItem
          comment={oldComment}
          currentUserId={mockCurrentUserId}
          onLike={mockOnLike}
          onDelete={mockOnDelete}
          onReport={mockOnReport}
        />
      );

      expect(getByText('Il y a 3j')).toBeTruthy();
    });
  });
});
