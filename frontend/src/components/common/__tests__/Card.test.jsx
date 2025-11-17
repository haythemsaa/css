import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import Card from '../Card';

describe('Card Component', () => {
  it('renders children content', () => {
    const { container } = render(
      <Card>
        <p>Card content</p>
      </Card>
    );
    expect(screen.getByText('Card content')).toBeInTheDocument();
  });

  it('renders with default variant', () => {
    const { container } = render(<Card>Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('border', 'border-gray-200');
  });

  it('renders with elevated variant', () => {
    const { container } = render(<Card variant="elevated">Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('shadow-lg');
  });

  it('renders with outline variant', () => {
    const { container } = render(<Card variant="outline">Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('border-2', 'border-gray-300');
  });

  it('renders with gold variant', () => {
    const { container } = render(<Card variant="gold">Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('border-2', 'border-css-gold', 'shadow-gold');
  });

  it('renders with no padding when padding is none', () => {
    const { container } = render(<Card padding="none">Content</Card>);
    const card = container.firstChild;
    expect(card).not.toHaveClass('p-3', 'p-4', 'p-6', 'p-8');
  });

  it('renders with small padding', () => {
    const { container } = render(<Card padding="sm">Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('p-3');
  });

  it('renders with medium padding by default', () => {
    const { container } = render(<Card>Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('p-4');
  });

  it('renders with large padding', () => {
    const { container } = render(<Card padding="lg">Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('p-6');
  });

  it('renders with extra large padding', () => {
    const { container } = render(<Card padding="xl">Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('p-8');
  });

  it('applies hover effects when hover is true', () => {
    const { container } = render(<Card hover>Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('hover:shadow-xl', 'hover:scale-[1.02]', 'cursor-pointer');
  });

  it('does not apply hover effects by default', () => {
    const { container } = render(<Card>Content</Card>);
    const card = container.firstChild;
    expect(card).not.toHaveClass('hover:shadow-xl');
  });

  it('calls onClick when clicked', () => {
    const handleClick = vi.fn();
    const { container } = render(<Card onClick={handleClick}>Click me</Card>);

    const card = container.firstChild;
    fireEvent.click(card);

    expect(handleClick).toHaveBeenCalledTimes(1);
  });

  it('applies cursor pointer when onClick is provided', () => {
    const handleClick = vi.fn();
    const { container } = render(<Card onClick={handleClick}>Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('cursor-pointer');
  });

  it('does not apply cursor pointer when onClick is not provided', () => {
    const { container } = render(<Card>Content</Card>);
    const card = container.firstChild;
    expect(card).not.toHaveClass('cursor-pointer');
  });

  it('applies custom className', () => {
    const { container } = render(<Card className="custom-card">Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('custom-card');
  });

  it('applies base styles', () => {
    const { container } = render(<Card>Content</Card>);
    const card = container.firstChild;
    expect(card).toHaveClass('bg-white', 'rounded-lg', 'shadow-md', 'transition-all', 'duration-200');
  });

  it('combines multiple props correctly', () => {
    const handleClick = vi.fn();
    const { container } = render(
      <Card
        variant="gold"
        padding="lg"
        hover
        onClick={handleClick}
        className="extra-class"
      >
        Complex Card
      </Card>
    );

    const card = container.firstChild;

    // Variant
    expect(card).toHaveClass('border-2', 'border-css-gold');
    // Padding
    expect(card).toHaveClass('p-6');
    // Hover
    expect(card).toHaveClass('hover:shadow-xl');
    // Click
    expect(card).toHaveClass('cursor-pointer');
    // Custom
    expect(card).toHaveClass('extra-class');

    fireEvent.click(card);
    expect(handleClick).toHaveBeenCalledTimes(1);
  });

  it('renders nested components correctly', () => {
    render(
      <Card>
        <div>
          <h2>Title</h2>
          <p>Description</p>
        </div>
      </Card>
    );

    expect(screen.getByText('Title')).toBeInTheDocument();
    expect(screen.getByText('Description')).toBeInTheDocument();
  });
});
