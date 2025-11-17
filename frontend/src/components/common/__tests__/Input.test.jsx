import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import Input from '../Input';

describe('Input Component', () => {
  it('renders with label', () => {
    render(<Input label="Username" name="username" />);
    expect(screen.getByLabelText('Username')).toBeInTheDocument();
  });

  it('renders without label', () => {
    render(<Input name="username" placeholder="Enter username" />);
    expect(screen.getByPlaceholderText('Enter username')).toBeInTheDocument();
  });

  it('shows required asterisk when required', () => {
    render(<Input label="Email" name="email" required />);
    expect(screen.getByText('*')).toBeInTheDocument();
  });

  it('calls onChange when value changes', async () => {
    const handleChange = vi.fn();
    const user = userEvent.setup();

    render(<Input name="username" onChange={handleChange} />);
    const input = screen.getByRole('textbox');

    await user.type(input, 'test');
    expect(handleChange).toHaveBeenCalled();
  });

  it('displays the current value', () => {
    render(<Input name="username" value="john_doe" onChange={vi.fn()} />);
    const input = screen.getByRole('textbox');
    expect(input).toHaveValue('john_doe');
  });

  it('renders with placeholder', () => {
    render(<Input name="email" placeholder="Enter your email" />);
    expect(screen.getByPlaceholderText('Enter your email')).toBeInTheDocument();
  });

  it('shows error message when error prop is provided', () => {
    render(<Input name="email" error="Email is required" />);
    expect(screen.getByText('Email is required')).toBeInTheDocument();
  });

  it('applies error styles when error is present', () => {
    render(<Input name="email" error="Invalid email" />);
    const input = screen.getByRole('textbox');
    expect(input).toHaveClass('border-red-500');
  });

  it('shows helper text when provided and no error', () => {
    render(<Input name="password" helperText="Must be at least 8 characters" />);
    expect(screen.getByText('Must be at least 8 characters')).toBeInTheDocument();
  });

  it('hides helper text when error is present', () => {
    render(
      <Input
        name="password"
        helperText="Must be at least 8 characters"
        error="Password too short"
      />
    );
    expect(screen.queryByText('Must be at least 8 characters')).not.toBeInTheDocument();
    expect(screen.getByText('Password too short')).toBeInTheDocument();
  });

  it('renders with text type by default', () => {
    render(<Input name="username" />);
    const input = screen.getByRole('textbox');
    expect(input).toHaveAttribute('type', 'text');
  });

  it('renders with password type', () => {
    render(<Input name="password" type="password" />);
    const input = document.querySelector('input[type="password"]');
    expect(input).toBeInTheDocument();
  });

  it('renders with email type', () => {
    render(<Input name="email" type="email" />);
    const input = document.querySelector('input[type="email"]');
    expect(input).toBeInTheDocument();
  });

  it('renders with number type', () => {
    render(<Input name="age" type="number" />);
    const input = screen.getByRole('spinbutton');
    expect(input).toBeInTheDocument();
  });

  it('is disabled when disabled prop is true', () => {
    render(<Input name="username" disabled />);
    const input = screen.getByRole('textbox');
    expect(input).toBeDisabled();
  });

  it('applies disabled styles when disabled', () => {
    render(<Input name="username" disabled />);
    const input = screen.getByRole('textbox');
    expect(input).toHaveClass('bg-gray-100', 'cursor-not-allowed');
  });

  it('renders with icon', () => {
    const icon = <span data-testid="icon">📧</span>;
    render(<Input name="email" icon={icon} />);
    expect(screen.getByTestId('icon')).toBeInTheDocument();
  });

  it('applies left padding when icon is present', () => {
    const icon = <span data-testid="icon">📧</span>;
    render(<Input name="email" icon={icon} />);
    const input = screen.getByRole('textbox');
    expect(input).toHaveClass('pl-10');
  });

  it('renders full width by default', () => {
    render(<Input name="username" />);
    const input = screen.getByRole('textbox');
    expect(input).toHaveClass('w-full');
  });

  it('renders without full width when fullWidth is false', () => {
    render(<Input name="username" fullWidth={false} />);
    const input = screen.getByRole('textbox');
    expect(input).not.toHaveClass('w-full');
  });

  it('applies custom className', () => {
    render(<Input name="username" className="custom-input" />);
    const input = screen.getByRole('textbox');
    expect(input).toHaveClass('custom-input');
  });

  it('passes through additional props', () => {
    render(
      <Input
        name="username"
        data-testid="custom-input"
        aria-describedby="username-help"
      />
    );
    const input = screen.getByTestId('custom-input');
    expect(input).toHaveAttribute('aria-describedby', 'username-help');
  });

  it('associates label with input using htmlFor and id', () => {
    render(<Input label="Email" name="email" />);
    const label = screen.getByText('Email');
    const input = screen.getByRole('textbox');
    expect(label).toHaveAttribute('for', 'email');
    expect(input).toHaveAttribute('id', 'email');
  });

  it('has required attribute when required prop is true', () => {
    render(<Input name="email" required />);
    const input = screen.getByRole('textbox');
    expect(input).toBeRequired();
  });

  it('can be typed into when not disabled', async () => {
    const user = userEvent.setup();
    render(<Input name="username" />);
    const input = screen.getByRole('textbox');

    await user.type(input, 'hello');
    expect(input).toHaveValue('hello');
  });
});
