import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import commentsService from '../../src/services/commentsService';

// Mock axios
jest.mock('axios');

// Mock AsyncStorage
jest.mock('@react-native-async-storage/async-storage', () => ({
  getItem: jest.fn(),
}));

describe('CommentsService', () => {
  const mockToken = 'mock-token-123';
  const mockUser = { id: 1, name: 'Test User', email: 'test@example.com' };

  beforeEach(() => {
    jest.clearAllMocks();
    AsyncStorage.getItem.mockImplementation((key) => {
      if (key === 'auth_token') return Promise.resolve(mockToken);
      if (key === 'user') return Promise.resolve(JSON.stringify(mockUser));
      return Promise.resolve(null);
    });
  });

  describe('getComments', () => {
    it('should fetch comments for content', async () => {
      const contentSlug = 'test-content';
      const mockComments = [
        {
          id: 1,
          text: 'Great article!',
          user: { id: 2, name: 'User 2' },
          likes_count: 5,
          is_liked: false,
          created_at: '2025-01-18T10:00:00Z',
        },
        {
          id: 2,
          text: 'Thanks for sharing',
          user: { id: 3, name: 'User 3' },
          likes_count: 2,
          is_liked: true,
          created_at: '2025-01-18T11:00:00Z',
        },
      ];

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { comments: mockComments },
        },
      });

      const comments = await commentsService.getComments(contentSlug);

      expect(axios.get).toHaveBeenCalledWith(
        expect.stringContaining(`/content/${contentSlug}/comments`),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(comments).toEqual(mockComments);
    });

    it('should return mock comments on API error', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const comments = await commentsService.getComments('test-slug');

      expect(Array.isArray(comments)).toBe(true);
      expect(comments.length).toBeGreaterThan(0);
      expect(comments[0]).toHaveProperty('id');
      expect(comments[0]).toHaveProperty('text');
      expect(comments[0]).toHaveProperty('user');
    });
  });

  describe('addComment', () => {
    it('should add new comment to content', async () => {
      const contentSlug = 'test-content';
      const text = 'This is my comment';
      const mockComment = {
        id: 10,
        text,
        user: mockUser,
        likes_count: 0,
        is_liked: false,
        created_at: new Date().toISOString(),
      };

      axios.post.mockResolvedValueOnce({
        data: {
          success: true,
          data: { comment: mockComment },
        },
      });

      const comment = await commentsService.addComment(contentSlug, text);

      expect(axios.post).toHaveBeenCalledWith(
        expect.stringContaining(`/content/${contentSlug}/comments`),
        { text },
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(comment).toEqual(mockComment);
    });

    it('should create local comment on API error', async () => {
      const contentSlug = 'test-content';
      const text = 'My fallback comment';

      axios.post.mockRejectedValueOnce(new Error('Network error'));

      const comment = await commentsService.addComment(contentSlug, text);

      expect(comment.text).toBe(text);
      expect(comment.user.id).toBe(mockUser.id);
      expect(comment.likes_count).toBe(0);
      expect(comment.is_liked).toBe(false);
      expect(comment.id).toMatch(/^temp-/);
    });

    it('should throw error if text is empty', async () => {
      await expect(commentsService.addComment('slug', '')).rejects.toThrow();
    });
  });

  describe('deleteComment', () => {
    it('should delete comment by id', async () => {
      const commentId = 5;

      axios.delete.mockResolvedValueOnce({
        data: { success: true },
      });

      const result = await commentsService.deleteComment(commentId);

      expect(axios.delete).toHaveBeenCalledWith(
        expect.stringContaining(`/comments/${commentId}`),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toBe(true);
    });

    it('should handle delete errors', async () => {
      axios.delete.mockRejectedValueOnce(new Error('Delete failed'));

      await expect(commentsService.deleteComment(1)).rejects.toThrow('Delete failed');
    });
  });

  describe('likeComment', () => {
    it('should like a comment', async () => {
      const commentId = 3;

      axios.post.mockResolvedValueOnce({
        data: { success: true },
      });

      const result = await commentsService.likeComment(commentId);

      expect(axios.post).toHaveBeenCalledWith(
        expect.stringContaining(`/comments/${commentId}/like`),
        {},
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toBe(true);
    });

    it('should handle like errors', async () => {
      axios.post.mockRejectedValueOnce(new Error('Like failed'));

      await expect(commentsService.likeComment(1)).rejects.toThrow('Like failed');
    });
  });

  describe('unlikeComment', () => {
    it('should unlike a comment', async () => {
      const commentId = 3;

      axios.post.mockResolvedValueOnce({
        data: { success: true },
      });

      const result = await commentsService.unlikeComment(commentId);

      expect(axios.post).toHaveBeenCalledWith(
        expect.stringContaining(`/comments/${commentId}/unlike`),
        {},
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toBe(true);
    });

    it('should handle unlike errors', async () => {
      axios.post.mockRejectedValueOnce(new Error('Unlike failed'));

      await expect(commentsService.unlikeComment(1)).rejects.toThrow('Unlike failed');
    });
  });

  describe('reportComment', () => {
    it('should report comment with reason', async () => {
      const commentId = 7;
      const reason = 'spam';

      axios.post.mockResolvedValueOnce({
        data: { success: true },
      });

      const result = await commentsService.reportComment(commentId, reason);

      expect(axios.post).toHaveBeenCalledWith(
        expect.stringContaining(`/comments/${commentId}/report`),
        { reason },
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toBe(true);
    });

    it('should handle different report reasons', async () => {
      const reasons = ['spam', 'inappropriate', 'harassment'];

      axios.post.mockResolvedValue({
        data: { success: true },
      });

      for (const reason of reasons) {
        await commentsService.reportComment(1, reason);

        expect(axios.post).toHaveBeenCalledWith(
          expect.anything(),
          { reason },
          expect.anything()
        );
      }
    });

    it('should handle report errors', async () => {
      axios.post.mockRejectedValueOnce(new Error('Report failed'));

      await expect(commentsService.reportComment(1, 'spam')).rejects.toThrow('Report failed');
    });
  });

  describe('generateMockComments', () => {
    it('should generate mock comments array', () => {
      const mockComments = commentsService.generateMockComments();

      expect(Array.isArray(mockComments)).toBe(true);
      expect(mockComments.length).toBeGreaterThan(0);
    });

    it('should generate comments with required fields', () => {
      const mockComments = commentsService.generateMockComments();

      mockComments.forEach((comment) => {
        expect(comment).toHaveProperty('id');
        expect(comment).toHaveProperty('text');
        expect(comment).toHaveProperty('user');
        expect(comment).toHaveProperty('likes_count');
        expect(comment).toHaveProperty('is_liked');
        expect(comment).toHaveProperty('created_at');
      });
    });

    it('should generate comments with user information', () => {
      const mockComments = commentsService.generateMockComments();

      mockComments.forEach((comment) => {
        expect(comment.user).toHaveProperty('id');
        expect(comment.user).toHaveProperty('name');
        expect(comment.user.name).toBeTruthy();
      });
    });

    it('should generate comments with valid dates', () => {
      const mockComments = commentsService.generateMockComments();

      mockComments.forEach((comment) => {
        const date = new Date(comment.created_at);
        expect(date).toBeInstanceOf(Date);
        expect(date.getTime()).not.toBeNaN();
      });
    });

    it('should generate comments with varying likes', () => {
      const mockComments = commentsService.generateMockComments();

      const likesSet = new Set(mockComments.map((c) => c.likes_count));
      expect(likesSet.size).toBeGreaterThan(1); // Should have different like counts
    });
  });
});
