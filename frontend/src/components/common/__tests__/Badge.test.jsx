import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import Badge from '../Badge';

describe('Badge Component', () => {
  it('renders children text', () => {
    render(<Badge>New</Badge>);
    expect(screen.getByText('New')).toBeInTheDocument();
  });

  it('renders with default variant', () => {
    render(<Badge>Default</Badge>);
    const badge = screen.getByText('Default');
    expect(badge).toHaveClass('bg-gray-100', 'text-gray-800');
  });

  it('renders with primary variant', () => {
    render(<Badge variant="primary">Primary</Badge>);
    const badge = screen.getByText('Primary');
    expect(badge).toHaveClass('bg-black', 'text-white');
  });

  it('renders with secondary variant', () => {
    render(<Badge variant="secondary">Secondary</Badge>);
    const badge = screen.getByText('Secondary');
    expect(badge).toHaveClass('bg-gradient-gold', 'text-black');
  });

  it('renders with success variant', () => {
    render(<Badge variant="success">Success</Badge>);
    const badge = screen.getByText('Success');
    expect(badge).toHaveClass('bg-green-100', 'text-green-800');
  });

  it('renders with warning variant', () => {
    render(<Badge variant="warning">Warning</Badge>);
    const badge = screen.getByText('Warning');
    expect(badge).toHaveClass('bg-yellow-100', 'text-yellow-800');
  });

  it('renders with error variant', () => {
    render(<Badge variant="error">Error</Badge>);
    const badge = screen.getByText('Error');
    expect(badge).toHaveClass('bg-red-100', 'text-red-800');
  });

  it('renders with info variant', () => {
    render(<Badge variant="info">Info</Badge>);
    const badge = screen.getByText('Info');
    expect(badge).toHaveClass('bg-blue-100', 'text-blue-800');
  });

  it('renders with small size', () => {
    render(<Badge size="sm">Small</Badge>);
    const badge = screen.getByText('Small');
    expect(badge).toHaveClass('px-2', 'py-0.5', 'text-xs');
  });

  it('renders with medium size by default', () => {
    render(<Badge>Medium</Badge>);
    const badge = screen.getByText('Medium');
    expect(badge).toHaveClass('px-2.5', 'py-1', 'text-sm');
  });

  it('renders with large size', () => {
    render(<Badge size="lg">Large</Badge>);
    const badge = screen.getByText('Large');
    expect(badge).toHaveClass('px-3', 'py-1.5', 'text-base');
  });

  it('renders with icon', () => {
    const icon = <span data-testid="badge-icon">✓</span>;
    render(<Badge icon={icon}>With Icon</Badge>);

    expect(screen.getByTestId('badge-icon')).toBeInTheDocument();
    expect(screen.getByText('With Icon')).toBeInTheDocument();
  });

  it('applies correct spacing when icon is present', () => {
    const icon = <span data-testid="badge-icon">✓</span>;
    render(<Badge icon={icon}>Text</Badge>);

    const iconElement = screen.getByTestId('badge-icon');
    const parent = iconElement.parentElement;
    expect(parent).toHaveClass('mr-1');
  });

  it('applies custom className', () => {
    render(<Badge className="custom-badge">Custom</Badge>);
    const badge = screen.getByText('Custom');
    expect(badge).toHaveClass('custom-badge');
  });

  it('applies base styles', () => {
    render(<Badge>Badge</Badge>);
    const badge = screen.getByText('Badge');
    expect(badge).toHaveClass('inline-flex', 'items-center', 'font-medium', 'rounded-full');
  });

  it('renders as span element', () => {
    render(<Badge>Badge</Badge>);
    const badge = screen.getByText('Badge');
    expect(badge.tagName).toBe('SPAN');
  });

  it('combines variant and size correctly', () => {
    render(<Badge variant="success" size="lg">Large Success</Badge>);
    const badge = screen.getByText('Large Success');

    // Variant
    expect(badge).toHaveClass('bg-green-100', 'text-green-800');
    // Size
    expect(badge).toHaveClass('px-3', 'py-1.5', 'text-base');
  });

  it('renders all variant combinations', () => {
    const variants = ['default', 'primary', 'secondary', 'success', 'warning', 'error', 'info'];

    variants.forEach(variant => {
      const { rerender } = render(<Badge variant={variant}>{variant}</Badge>);
      expect(screen.getByText(variant)).toBeInTheDocument();
      rerender(<div />);
    });
  });

  it('renders all size combinations', () => {
    const sizes = ['sm', 'md', 'lg'];

    sizes.forEach(size => {
      const { rerender } = render(<Badge size={size}>{size}</Badge>);
      expect(screen.getByText(size)).toBeInTheDocument();
      rerender(<div />);
    });
  });

  it('renders complex content with icon and multiple variants', () => {
    const icon = <span data-testid="star">⭐</span>;
    render(
      <Badge variant="secondary" size="lg" icon={icon} className="premium-badge">
        Premium
      </Badge>
    );

    const badge = screen.getByText('Premium');
    expect(badge).toHaveClass('bg-gradient-gold', 'text-black', 'px-3', 'py-1.5', 'premium-badge');
    expect(screen.getByTestId('star')).toBeInTheDocument();
  });
});
