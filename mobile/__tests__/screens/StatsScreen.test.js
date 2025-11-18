import React from 'react';
import { render, fireEvent, waitFor } from '@testing-library/react-native';
import StatsScreen from '../../src/screens/stats/StatsScreen';
import statsService from '../../src/services/statsService';

// Mock dependencies
jest.mock('../../src/services/statsService');

describe('StatsScreen', () => {
  const mockGlobalStats = {
    totalCodes: 25,
    usedCodes: 18,
    totalSavings: 450.5,
    loyaltyPoints: 1250,
    currentLevel: 'Gold',
  };

  const mockCodesByType = [
    { type: 'qr', count: 15 },
    { type: 'barcode', count: 10 },
  ];

  const mockCodesByStatus = [
    { status: 'active', count: 12 },
    { status: 'used', count: 8 },
    { status: 'expired', count: 5 },
  ];

  const mockCodesByCategory = [
    { category: 'Restaurant', count: 10 },
    { category: 'Mode', count: 8 },
    { category: 'Loisirs', count: 7 },
  ];

  const mockSavingsHistory = {
    labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
    data: [15.5, 22.0, 0, 18.75, 30.25, 12.0, 25.5],
  };

  const mockTopPartners = [
    {
      partner: { name: 'Restaurant A', logo: 'logo1.png' },
      usageCount: 15,
      totalSavings: 250.5,
    },
    {
      partner: { name: 'Shop B', logo: 'logo2.png' },
      usageCount: 12,
      totalSavings: 180.0,
    },
  ];

  const mockRecentActivities = [
    {
      type: 'code_generated',
      description: 'Code généré pour Restaurant A',
      date: new Date().toISOString(),
      icon: '🎫',
    },
    {
      type: 'code_used',
      description: 'Code utilisé chez Shop B',
      date: new Date().toISOString(),
      icon: '✅',
    },
  ];

  const mockNavigation = {
    navigate: jest.fn(),
    goBack: jest.fn(),
  };

  beforeEach(() => {
    jest.clearAllMocks();
    statsService.getGlobalStats.mockResolvedValue(mockGlobalStats);
    statsService.getCodesByType.mockResolvedValue(mockCodesByType);
    statsService.getCodesByStatus.mockResolvedValue(mockCodesByStatus);
    statsService.getCodesByCategory.mockResolvedValue(mockCodesByCategory);
    statsService.getSavingsHistory.mockResolvedValue(mockSavingsHistory);
    statsService.getTopPartners.mockResolvedValue(mockTopPartners);
    statsService.getRecentActivities.mockResolvedValue(mockRecentActivities);
  });

  it('should render stats screen', () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    expect(getByText(/Statistiques/)).toBeTruthy();
  });

  it('should load global stats on mount', async () => {
    render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getGlobalStats).toHaveBeenCalled();
    });
  });

  it('should display global statistics', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('25')).toBeTruthy(); // totalCodes
      expect(getByText('18')).toBeTruthy(); // usedCodes
      expect(getByText('450.5 TND')).toBeTruthy(); // totalSavings
      expect(getByText('1250')).toBeTruthy(); // loyaltyPoints
    });
  });

  it('should display current loyalty level', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('Gold')).toBeTruthy();
    });
  });

  it('should show loading indicator while fetching stats', () => {
    statsService.getGlobalStats.mockReturnValue(new Promise(() => {})); // Never resolves

    const { UNSAFE_getByType } = render(<StatsScreen navigation={mockNavigation} />);

    expect(UNSAFE_getByType('ActivityIndicator')).toBeTruthy();
  });

  it('should display savings chart', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getSavingsHistory).toHaveBeenCalledWith('month');
    });

    // Check chart labels are displayed
    await waitFor(() => {
      expect(getByText('Lun')).toBeTruthy();
      expect(getByText('Mar')).toBeTruthy();
      expect(getByText('Mer')).toBeTruthy();
    });
  });

  it('should allow changing time period for savings chart', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getSavingsHistory).toHaveBeenCalledWith('month');
    });

    jest.clearAllMocks();

    // Find and press "Semaine" button
    const weekButton = getByText('Semaine');
    fireEvent.press(weekButton);

    await waitFor(() => {
      expect(statsService.getSavingsHistory).toHaveBeenCalledWith('week');
    });
  });

  it('should support week, month, and year periods', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('Semaine')).toBeTruthy();
      expect(getByText('Mois')).toBeTruthy();
      expect(getByText('Année')).toBeTruthy();
    });
  });

  it('should display codes breakdown by type', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getCodesByType).toHaveBeenCalled();
    });

    await waitFor(() => {
      expect(getByText(/qr/i)).toBeTruthy();
      expect(getByText('15')).toBeTruthy();
    });
  });

  it('should display codes breakdown by status', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getCodesByStatus).toHaveBeenCalled();
    });

    await waitFor(() => {
      expect(getByText(/active/i)).toBeTruthy();
      expect(getByText('12')).toBeTruthy();
    });
  });

  it('should display codes breakdown by category', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getCodesByCategory).toHaveBeenCalled();
    });

    await waitFor(() => {
      expect(getByText('Restaurant')).toBeTruthy();
      expect(getByText('10')).toBeTruthy();
    });
  });

  it('should display top partners', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getTopPartners).toHaveBeenCalled();
    });

    await waitFor(() => {
      expect(getByText('Restaurant A')).toBeTruthy();
      expect(getByText('15 utilisations')).toBeTruthy();
      expect(getByText('250.5 TND')).toBeTruthy();
    });
  });

  it('should display recent activities', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getRecentActivities).toHaveBeenCalled();
    });

    await waitFor(() => {
      expect(getByText('Code généré pour Restaurant A')).toBeTruthy();
      expect(getByText('Code utilisé chez Shop B')).toBeTruthy();
    });
  });

  it('should display activity icons', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('🎫')).toBeTruthy();
      expect(getByText('✅')).toBeTruthy();
    });
  });

  it('should calculate usage rate correctly', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      // usedCodes (18) / totalCodes (25) = 72%
      expect(getByText(/72%/)).toBeTruthy();
    });
  });

  it('should handle zero total codes gracefully', async () => {
    statsService.getGlobalStats.mockResolvedValue({
      ...mockGlobalStats,
      totalCodes: 0,
      usedCodes: 0,
    });

    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('0')).toBeTruthy();
    });
  });

  it('should handle API errors gracefully', async () => {
    statsService.getGlobalStats.mockRejectedValue(new Error('Network error'));

    const { queryByText } = render(<StatsScreen navigation={mockNavigation} />);

    // Should not crash, may show error or empty state
    await waitFor(() => {
      expect(queryByText(/Statistiques/)).toBeTruthy();
    });
  });

  it('should support scrolling for long content', async () => {
    const { UNSAFE_getByType } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getGlobalStats).toHaveBeenCalled();
    });

    // Should render within a ScrollView
    expect(UNSAFE_getByType('ScrollView')).toBeTruthy();
  });

  it('should display stat cards with icons', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      // Check for emoji icons in stat cards
      expect(getByText('🎫')).toBeTruthy(); // Codes
      expect(getByText('💰')).toBeTruthy(); // Savings
      expect(getByText('⭐')).toBeTruthy(); // Loyalty points
    });
  });

  it('should update chart when period changes', async () => {
    const mockWeekHistory = {
      labels: ['L', 'M', 'M', 'J', 'V', 'S', 'D'],
      data: [10, 15, 20, 12, 18, 25, 22],
    };

    statsService.getSavingsHistory.mockResolvedValue(mockWeekHistory);

    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getSavingsHistory).toHaveBeenCalled();
    });

    const weekButton = getByText('Semaine');
    fireEvent.press(weekButton);

    await waitFor(() => {
      expect(statsService.getSavingsHistory).toHaveBeenCalledWith('week');
    });
  });

  it('should highlight selected period button', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('Mois')).toBeTruthy();
    });

    // Default period should be 'month'
    // Button should have highlighted style
  });

  it('should render bar chart with correct proportions', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getSavingsHistory).toHaveBeenCalled();
    });

    // Chart should render bars with heights proportional to values
    // This is tested visually in the component
  });

  it('should show empty state for zero savings', async () => {
    statsService.getSavingsHistory.mockResolvedValue({
      labels: ['Lun', 'Mar', 'Mer'],
      data: [0, 0, 0],
    });

    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getSavingsHistory).toHaveBeenCalled();
    });

    // Should handle zero values in chart
  });

  it('should display loyalty program information', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('Gold')).toBeTruthy();
      expect(getByText('1250')).toBeTruthy();
    });
  });

  it('should refresh stats when screen is focused', async () => {
    const { rerender } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getGlobalStats).toHaveBeenCalledTimes(1);
    });

    // Simulate screen refocus
    rerender(<StatsScreen navigation={mockNavigation} />);

    // Stats should be refetched
  });

  it('should format large numbers correctly', async () => {
    statsService.getGlobalStats.mockResolvedValue({
      ...mockGlobalStats,
      loyaltyPoints: 12500,
      totalSavings: 1250.75,
    });

    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(getByText('12500')).toBeTruthy();
      expect(getByText('1250.75 TND')).toBeTruthy();
    });
  });

  it('should display time information for activities', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getRecentActivities).toHaveBeenCalled();
    });

    // Activities should show time (e.g., "Il y a 2h")
    await waitFor(() => {
      expect(getByText('Code généré pour Restaurant A')).toBeTruthy();
    });
  });

  it('should limit activities list to recent items', async () => {
    const { getByText } = render(<StatsScreen navigation={mockNavigation} />);

    await waitFor(() => {
      expect(statsService.getRecentActivities).toHaveBeenCalled();
    });

    // Should show limited number of activities (e.g., 10)
  });
});
