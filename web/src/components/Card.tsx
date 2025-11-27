import { ReactNode } from 'react'
import { clsx } from 'clsx'

interface CardProps {
  children: ReactNode
  className?: string
  variant?: 'default' | 'bordered' | 'elevated'
  padding?: 'none' | 'sm' | 'md' | 'lg'
  hover?: boolean
}

export const Card = ({
  children,
  className,
  variant = 'default',
  padding = 'md',
  hover = false
}: CardProps) => {
  const baseStyles = 'rounded-xl'

  const variants = {
    default: 'bg-white',
    bordered: 'bg-white border-2 border-gray-200',
    elevated: 'bg-white shadow-lg'
  }

  const paddings = {
    none: '',
    sm: 'p-4',
    md: 'p-6',
    lg: 'p-8'
  }

  const hoverStyles = hover ? 'transition-all duration-200 hover:shadow-xl hover:-translate-y-1' : ''

  return (
    <div className={clsx(baseStyles, variants[variant], paddings[padding], hoverStyles, className)}>
      {children}
    </div>
  )
}
