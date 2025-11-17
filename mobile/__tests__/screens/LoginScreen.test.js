import React from 'react';
import { render, fireEvent, waitFor, act } from '@testing-library/react-native';
import { Alert } from 'react-native';
import LoginScreen from '../../src/screens/auth/LoginScreen';
import useAuthStore from '../../src/stores/authStore';

// Mock the navigation
const mockNavigation = {
  navigate: jest.fn(),
  goBack: jest.fn(),
};

// Mock the auth store
jest.mock('../../src/stores/authStore');

// Mock Alert
jest.spyOn(Alert, 'alert');

describe('LoginScreen', () => {
  const mockLogin = jest.fn();

  beforeEach(() => {
    jest.clearAllMocks();
    useAuthStore.mockReturnValue({
      login: mockLogin,
      isAuthenticated: false,
    });
  });

  it('should render login form correctly', () => {
    const { getByText, getByPlaceholderText } = render(
      <LoginScreen navigation={mockNavigation} />
    );

    expect(getByText('Club Sportif Sfaxien')).toBeTruthy();
    expect(getByText('Bienvenue sur CSS Platform')).toBeTruthy();
    expect(getByPlaceholderText('votre@email.com')).toBeTruthy();
    expect(getByPlaceholderText('Votre mot de passe')).toBeTruthy();
    expect(getByText('Se connecter')).toBeTruthy();
  });

  it('should show validation errors for empty fields', async () => {
    const { getByText } = render(<LoginScreen navigation={mockNavigation} />);

    const loginButton = getByText('Se connecter');
    await act(async () => {
      fireEvent.press(loginButton);
    });

    await waitFor(() => {
      expect(getByText("L'email est requis")).toBeTruthy();
      expect(getByText('Le mot de passe est requis')).toBeTruthy();
    });

    expect(mockLogin).not.toHaveBeenCalled();
  });

  it('should show validation error for invalid email', async () => {
    const { getByPlaceholderText, getByText } = render(
      <LoginScreen navigation={mockNavigation} />
    );

    const emailInput = getByPlaceholderText('votre@email.com');
    const passwordInput = getByPlaceholderText('Votre mot de passe');

    await act(async () => {
      fireEvent.changeText(emailInput, 'invalid-email');
      fireEvent.changeText(passwordInput, 'password123');
    });

    const loginButton = getByText('Se connecter');
    await act(async () => {
      fireEvent.press(loginButton);
    });

    await waitFor(() => {
      expect(getByText('Email invalide')).toBeTruthy();
    });

    expect(mockLogin).not.toHaveBeenCalled();
  });

  it('should show validation error for short password', async () => {
    const { getByPlaceholderText, getByText } = render(
      <LoginScreen navigation={mockNavigation} />
    );

    const emailInput = getByPlaceholderText('votre@email.com');
    const passwordInput = getByPlaceholderText('Votre mot de passe');

    await act(async () => {
      fireEvent.changeText(emailInput, 'test@example.com');
      fireEvent.changeText(passwordInput, '12345');
    });

    const loginButton = getByText('Se connecter');
    await act(async () => {
      fireEvent.press(loginButton);
    });

    await waitFor(() => {
      expect(getByText('Le mot de passe doit contenir au moins 6 caractères')).toBeTruthy();
    });

    expect(mockLogin).not.toHaveBeenCalled();
  });

  it('should call login with valid credentials', async () => {
    mockLogin.mockResolvedValue({ success: true });

    const { getByPlaceholderText, getByText } = render(
      <LoginScreen navigation={mockNavigation} />
    );

    const emailInput = getByPlaceholderText('votre@email.com');
    const passwordInput = getByPlaceholderText('Votre mot de passe');

    await act(async () => {
      fireEvent.changeText(emailInput, 'test@example.com');
      fireEvent.changeText(passwordInput, 'password123');
    });

    const loginButton = getByText('Se connecter');
    await act(async () => {
      fireEvent.press(loginButton);
    });

    await waitFor(() => {
      expect(mockLogin).toHaveBeenCalledWith({
        email: 'test@example.com',
        password: 'password123',
      });
    });
  });

  it('should show alert on login failure', async () => {
    mockLogin.mockResolvedValue({
      success: false,
      message: 'Invalid credentials',
    });

    const { getByPlaceholderText, getByText } = render(
      <LoginScreen navigation={mockNavigation} />
    );

    const emailInput = getByPlaceholderText('votre@email.com');
    const passwordInput = getByPlaceholderText('Votre mot de passe');

    await act(async () => {
      fireEvent.changeText(emailInput, 'test@example.com');
      fireEvent.changeText(passwordInput, 'wrongpassword');
    });

    const loginButton = getByText('Se connecter');
    await act(async () => {
      fireEvent.press(loginButton);
    });

    await waitFor(() => {
      expect(Alert.alert).toHaveBeenCalledWith(
        'Erreur de connexion',
        'Invalid credentials'
      );
    });
  });

  it('should clear errors when user types', async () => {
    const { getByPlaceholderText, getByText, queryByText } = render(
      <LoginScreen navigation={mockNavigation} />
    );

    // Trigger validation errors
    const loginButton = getByText('Se connecter');
    await act(async () => {
      fireEvent.press(loginButton);
    });

    await waitFor(() => {
      expect(getByText("L'email est requis")).toBeTruthy();
    });

    // Start typing in email field
    const emailInput = getByPlaceholderText('votre@email.com');
    await act(async () => {
      fireEvent.changeText(emailInput, 'test@example.com');
    });

    // Error should be cleared
    await waitFor(() => {
      expect(queryByText("L'email est requis")).toBeNull();
    });
  });

  it('should navigate to register screen when link is pressed', () => {
    const { getByText } = render(<LoginScreen navigation={mockNavigation} />);

    const registerLink = getByText("S'inscrire");
    fireEvent.press(registerLink);

    expect(mockNavigation.navigate).toHaveBeenCalledWith('Register');
  });
});
