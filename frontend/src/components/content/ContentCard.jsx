import React from 'react';
import { Link } from 'react-router-dom';
import { Card, Badge } from '../common';
import useAuthStore from '../../stores/authStore';

const ContentCard = ({ content }) => {
  const { user, isAuthenticated, isPremium } = useAuthStore();

  const getContentTypeIcon = () => {
    const icons = {
      article: '📰',
      video: '🎥',
      gallery: '📷',
      podcast: '🎙️',
    };
    return icons[content.type] || '📄';
  };

  const getContentTypeBadge = () => {
    const types = {
      article: { label: 'Article', variant: 'info' },
      video: { label: 'Vidéo', variant: 'error' },
      gallery: { label: 'Galerie', variant: 'success' },
      podcast: { label: 'Podcast', variant: 'default' },
    };
    return types[content.type] || { label: content.type, variant: 'default' };
  };

  const canAccess = !content.is_premium || (isAuthenticated && isPremium());

  const typeBadge = getContentTypeBadge();

  return (
    <Link to={canAccess ? `/content/${content.slug}` : '#'}>
      <Card hover={canAccess} padding="none" className="overflow-hidden h-full">
        {/* Image */}
        <div className="relative h-48 bg-gray-200 flex items-center justify-center">
          {content.thumbnail || content.image ? (
            <img
              src={content.thumbnail || content.image}
              alt={content.title}
              className="w-full h-full object-cover"
            />
          ) : (
            <div className="text-6xl">{getContentTypeIcon()}</div>
          )}

          {/* Badges */}
          <div className="absolute top-3 left-3 flex gap-2">
            <Badge variant={typeBadge.variant} size="sm">
              {typeBadge.label}
            </Badge>
            {content.is_premium && (
              <Badge variant="secondary" size="sm">
                ⭐ Premium
              </Badge>
            )}
            {content.is_featured && (
              <Badge variant="warning" size="sm">
                🔥 Featured
              </Badge>
            )}
          </div>

          {/* Premium overlay */}
          {!canAccess && (
            <div className="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
              <div className="text-center text-white p-4">
                <div className="text-3xl mb-2">🔒</div>
                <p className="font-bold">Contenu Premium</p>
                <p className="text-sm">Abonnez-vous pour y accéder</p>
              </div>
            </div>
          )}
        </div>

        {/* Content */}
        <div className="p-4">
          {/* Titre */}
          <h3 className="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
            {content.title}
          </h3>

          {/* Excerpt */}
          {content.excerpt && (
            <p className="text-gray-600 text-sm mb-3 line-clamp-3">
              {content.excerpt}
            </p>
          )}

          {/* Meta */}
          <div className="flex items-center justify-between text-xs text-gray-500">
            <div className="flex items-center gap-4">
              {/* Date */}
              <span className="flex items-center">
                <svg className="w-4 h-4 mr-1" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                  <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {new Date(content.published_at || content.created_at).toLocaleDateString('fr-FR')}
              </span>

              {/* Views */}
              {content.views_count > 0 && (
                <span className="flex items-center">
                  <svg className="w-4 h-4 mr-1" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  {content.views_count}
                </span>
              )}

              {/* Likes */}
              {content.likes_count > 0 && (
                <span className="flex items-center">
                  <svg className="w-4 h-4 mr-1" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                  {content.likes_count}
                </span>
              )}
            </div>
          </div>

          {/* Duration for videos */}
          {content.type === 'video' && content.duration && (
            <div className="mt-2">
              <Badge variant="default" size="sm">
                ⏱️ {content.duration}
              </Badge>
            </div>
          )}
        </div>
      </Card>
    </Link>
  );
};

export default ContentCard;
