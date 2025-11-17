import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { contentService } from '../../services/api';
import { Button, Badge, Card } from '../../components/common';
import useAuthStore from '../../stores/authStore';

const ContentDetail = () => {
  const { slug } = useParams();
  const navigate = useNavigate();
  const { user, isAuthenticated, isPremium } = useAuthStore();

  const [content, setContent] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [liked, setLiked] = useState(false);
  const [likesCount, setLikesCount] = useState(0);

  useEffect(() => {
    loadContent();
  }, [slug]);

  const loadContent = async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await contentService.getContentDetail(slug);

      if (response.success) {
        setContent(response.data);
        setLikesCount(response.data.likes_count || 0);
        // TODO: Check if user has liked this content
        setLiked(false);
      }
    } catch (err) {
      // Si erreur 403, c'est un contenu premium
      if (err.status === 403) {
        setError('Ce contenu est réservé aux membres Premium et Socios');
      } else {
        setError(err.message || 'Erreur lors du chargement du contenu');
      }
    } finally {
      setLoading(false);
    }
  };

  const handleLike = async () => {
    if (!isAuthenticated) {
      navigate('/login');
      return;
    }

    try {
      if (liked) {
        await contentService.unlikeContent(slug);
        setLiked(false);
        setLikesCount((prev) => prev - 1);
      } else {
        await contentService.likeContent(slug);
        setLiked(true);
        setLikesCount((prev) => prev + 1);
      }
    } catch (err) {
      console.error('Erreur like:', err);
    }
  };

  const getContentTypeInfo = () => {
    const types = {
      article: { icon: '📰', label: 'Article', color: 'info' },
      video: { icon: '🎥', label: 'Vidéo', color: 'error' },
      gallery: { icon: '📷', label: 'Galerie', color: 'success' },
      podcast: { icon: '🎙️', label: 'Podcast', color: 'default' },
    };
    return types[content?.type] || { icon: '📄', label: 'Contenu', color: 'default' };
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-black mb-4"></div>
          <p className="text-gray-600">Chargement...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Card padding="lg" className="max-w-md text-center">
          <div className="text-6xl mb-4">🔒</div>
          <h2 className="text-2xl font-bold text-gray-900 mb-4">
            Accès restreint
          </h2>
          <p className="text-gray-600 mb-6">{error}</p>
          <div className="flex gap-3">
            <Button variant="outline" fullWidth onClick={() => navigate('/content')}>
              Retour
            </Button>
            {!isAuthenticated ? (
              <Button variant="secondary" fullWidth onClick={() => navigate('/register')}>
                S'abonner
              </Button>
            ) : (
              <Button variant="secondary" fullWidth onClick={() => navigate('/upgrade')}>
                Passer Premium
              </Button>
            )}
          </div>
        </Card>
      </div>
    );
  }

  if (!content) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">
            Contenu non trouvé
          </h2>
          <Button variant="outline" onClick={() => navigate('/content')}>
            Retour aux actualités
          </Button>
        </div>
      </div>
    );
  }

  const typeInfo = getContentTypeInfo();

  return (
    <div className="bg-gray-50 min-h-screen">
      {/* Hero Image */}
      {(content.image || content.thumbnail) && (
        <div className="h-96 bg-gray-900 relative">
          <img
            src={content.image || content.thumbnail}
            alt={content.title}
            className="w-full h-full object-cover opacity-80"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
        </div>
      )}

      {/* Content */}
      <div className="container mx-auto px-4 -mt-20 relative z-10 pb-12">
        <Card padding="lg" className="max-w-4xl mx-auto">
          {/* Back button */}
          <button
            onClick={() => navigate('/content')}
            className="text-css-gold hover:underline mb-4 inline-flex items-center"
          >
            ← Retour aux actualités
          </button>

          {/* Badges */}
          <div className="flex flex-wrap gap-2 mb-4">
            <Badge variant={typeInfo.color}>
              {typeInfo.icon} {typeInfo.label}
            </Badge>
            {content.is_premium && (
              <Badge variant="secondary">⭐ Premium</Badge>
            )}
            {content.is_featured && (
              <Badge variant="warning">🔥 Featured</Badge>
            )}
          </div>

          {/* Title */}
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            {content.title}
          </h1>

          {/* Meta */}
          <div className="flex items-center gap-6 text-sm text-gray-600 mb-6 pb-6 border-b border-gray-200">
            <span className="flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              {new Date(content.published_at || content.created_at).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
              })}
            </span>

            <span className="flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              {content.views_count || 0} vues
            </span>

            <span className="flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              {likesCount} like{likesCount > 1 ? 's' : ''}
            </span>

            {content.author && (
              <span className="flex items-center">
                <svg className="w-4 h-4 mr-2" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                  <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {content.author.name}
              </span>
            )}
          </div>

          {/* Excerpt */}
          {content.excerpt && (
            <div className="bg-gray-50 p-6 rounded-lg mb-6">
              <p className="text-lg text-gray-700 italic">
                {content.excerpt}
              </p>
            </div>
          )}

          {/* Body */}
          {content.body && (
            <div className="prose prose-lg max-w-none mb-8">
              <div dangerouslySetInnerHTML={{ __html: content.body }} />
            </div>
          )}

          {/* Video player */}
          {content.type === 'video' && content.video_url && (
            <div className="mb-8">
              <div className="aspect-video bg-black rounded-lg overflow-hidden">
                <video
                  controls
                  className="w-full h-full"
                  poster={content.thumbnail}
                >
                  <source src={content.video_url} type="video/mp4" />
                  Votre navigateur ne supporte pas la lecture de vidéos.
                </video>
              </div>
            </div>
          )}

          {/* Gallery */}
          {content.type === 'gallery' && content.images && content.images.length > 0 && (
            <div className="grid md:grid-cols-2 gap-4 mb-8">
              {content.images.map((image, index) => (
                <img
                  key={index}
                  src={image.url}
                  alt={image.title || `Image ${index + 1}`}
                  className="w-full rounded-lg"
                />
              ))}
            </div>
          )}

          {/* Actions */}
          <div className="flex items-center gap-4 pt-6 border-t border-gray-200">
            <Button
              variant={liked ? 'secondary' : 'outline'}
              onClick={handleLike}
              icon={
                <svg className="w-5 h-5" fill={liked ? 'currentColor' : 'none'} strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                  <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              }
            >
              {liked ? 'Aimé' : 'Aimer'}
            </Button>

            <Button
              variant="outline"
              icon={
                <svg className="w-5 h-5" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                  <path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
              }
            >
              Partager
            </Button>
          </div>
        </Card>
      </div>
    </div>
  );
};

export default ContentDetail;
