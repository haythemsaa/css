import React from 'react';

const Card = ({
  children,
  variant = 'default',
  padding = 'md',
  hover = false,
  className = '',
  onClick,
}) => {
  const baseStyles = 'bg-white rounded-lg shadow-md transition-all duration-200';

  const variants = {
    default: 'border border-gray-200',
    elevated: 'shadow-lg',
    outline: 'border-2 border-gray-300',
    gold: 'border-2 border-css-gold shadow-gold',
  };

  const paddings = {
    none: '',
    sm: 'p-3',
    md: 'p-4',
    lg: 'p-6',
    xl: 'p-8',
  };

  const hoverClass = hover ? 'hover:shadow-xl hover:scale-[1.02] cursor-pointer' : '';
  const clickableClass = onClick ? 'cursor-pointer' : '';

  return (
    <div
      className={`${baseStyles} ${variants[variant]} ${paddings[padding]} ${hoverClass} ${clickableClass} ${className}`}
      onClick={onClick}
    >
      {children}
    </div>
  );
};

export default Card;
