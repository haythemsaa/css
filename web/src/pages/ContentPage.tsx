import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Spinner } from '../components'
import { api } from '../services/api'

export const ContentPage = () => {
  const [contents, setContents] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [filter, setFilter] = useState('all')

  useEffect(() => {
    const fetchContents = async () => {
      try {
        const params = filter !== 'all' ? { type: filter } : {}
        const response = await api.getContents(params)
        setContents(response.data || [])
      } catch (error) {
        console.error('Error fetching contents:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchContents()
  }, [filter])

  if (isLoading) {
    return (
      <DashboardLayout>
        <Spinner size="xl" className="h-screen" />
      </DashboardLayout>
    )
  }

  return (
    <DashboardLayout>
      <div className="mb-8">
        <h1 className="text-4xl font-bold text-black mb-4">Contenu Exclusif</h1>

        {/* Filters */}
        <div className="flex flex-wrap gap-2">
          <Button
            variant={filter === 'all' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('all')}
          >
            Tous
          </Button>
          <Button
            variant={filter === 'article' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('article')}
          >
            📰 Articles
          </Button>
          <Button
            variant={filter === 'video' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('video')}
          >
            🎥 Vidéos
          </Button>
          <Button
            variant={filter === 'podcast' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('podcast')}
          >
            🎧 Podcasts
          </Button>
          <Button
            variant={filter === 'story' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('story')}
          >
            📱 Stories
          </Button>
        </div>
      </div>

      {/* Content Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {contents.length > 0 ? (
          contents.map((content: any) => (
            <Card key={content.id} variant="elevated" hover padding="none">
              {/* Thumbnail */}
              <div className="relative">
                <img
                  src={content.thumbnail || content.image || '/placeholder-image.jpg'}
                  alt={content.title}
                  className="w-full h-48 object-cover rounded-t-xl"
                />
                {content.is_premium && (
                  <div className="absolute top-2 right-2">
                    <Badge variant="gold" size="sm">
                      💎 PREMIUM
                    </Badge>
                  </div>
                )}
                {content.type === 'video' && (
                  <div className="absolute inset-0 flex items-center justify-center">
                    <div className="w-16 h-16 bg-black/60 rounded-full flex items-center justify-center">
                      <svg
                        className="w-8 h-8 text-white ml-1"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                      </svg>
                    </div>
                  </div>
                )}
              </div>

              {/* Content Info */}
              <div className="p-4">
                <div className="flex items-center gap-2 mb-2">
                  <Badge
                    variant={
                      content.type === 'article'
                        ? 'default'
                        : content.type === 'video'
                        ? 'danger'
                        : content.type === 'podcast'
                        ? 'info'
                        : 'warning'
                    }
                    size="sm"
                  >
                    {content.type.toUpperCase()}
                  </Badge>
                  <span className="text-xs text-gray-500">
                    {new Date(content.published_at).toLocaleDateString('fr-FR')}
                  </span>
                </div>

                <h3 className="font-bold text-lg mb-2 line-clamp-2">
                  {content.title}
                </h3>

                <p className="text-sm text-gray-600 line-clamp-3 mb-4">
                  {content.excerpt}
                </p>

                {/* Stats */}
                <div className="flex items-center justify-between text-xs text-gray-500 mb-4">
                  <div className="flex items-center gap-3">
                    <span>👁 {content.views || 0} vues</span>
                    <span>❤️ {content.likes || 0}</span>
                  </div>
                  {content.type === 'video' && content.duration && (
                    <span>{content.duration}</span>
                  )}
                </div>

                {/* Action */}
                <Link to={`/content/${content.slug}`}>
                  <Button variant="outline" size="sm" className="w-full">
                    {content.type === 'video' ? 'Regarder' : 'Lire'}
                  </Button>
                </Link>
              </div>
            </Card>
          ))
        ) : (
          <div className="col-span-full">
            <Card>
              <p className="text-center text-gray-500 py-8">
                Aucun contenu trouvé
              </p>
            </Card>
          </div>
        )}
      </div>
    </DashboardLayout>
  )
}

export default ContentPage
