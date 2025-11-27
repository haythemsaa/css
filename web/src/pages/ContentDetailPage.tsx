import { useEffect, useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Spinner } from '../components'
import { api } from '../services/api'

export const ContentDetailPage = () => {
  const { slug } = useParams()
  const [content, setContent] = useState<any>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [relatedContent, setRelatedContent] = useState([])

  useEffect(() => {
    const fetchContent = async () => {
      try {
        const response = await api.getContent(slug!)
        setContent(response.data)

        // Récupérer le contenu similaire
        if (response.data.category_id) {
          const related = await api.getContents({
            category_id: response.data.category_id,
            page: 1
          })
          setRelatedContent(related.data?.slice(0, 3) || [])
        }
      } catch (error) {
        console.error('Error fetching content:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchContent()
  }, [slug])

  if (isLoading) {
    return (
      <DashboardLayout>
        <Spinner size="xl" className="h-screen" />
      </DashboardLayout>
    )
  }

  if (!content) {
    return (
      <DashboardLayout>
        <Card>
          <p className="text-center text-gray-500 py-8">Contenu introuvable</p>
          <Link to="/content">
            <Button variant="outline" className="mx-auto block">
              Retour au contenu
            </Button>
          </Link>
        </Card>
      </DashboardLayout>
    )
  }

  return (
    <DashboardLayout>
      <div className="max-w-5xl mx-auto">
        {/* Header */}
        <div className="mb-6">
          <Link to="/content">
            <Button variant="ghost" size="sm">← Retour au contenu</Button>
          </Link>
        </div>

        {/* Main Content */}
        <article>
          {/* Hero Image/Video */}
          {content.type === 'video' ? (
            <div className="relative w-full aspect-video bg-black rounded-xl overflow-hidden mb-6">
              {content.video_url ? (
                <video
                  controls
                  className="w-full h-full"
                  poster={content.thumbnail}
                >
                  <source src={content.video_url} type="video/mp4" />
                  Votre navigateur ne supporte pas la lecture de vidéos.
                </video>
              ) : (
                <div className="flex items-center justify-center h-full text-white">
                  <div className="text-center">
                    <div className="text-6xl mb-4">🎥</div>
                    <p>Vidéo non disponible</p>
                  </div>
                </div>
              )}
            </div>
          ) : content.image && (
            <img
              src={content.image}
              alt={content.title}
              className="w-full h-96 object-cover rounded-xl mb-6"
            />
          )}

          {/* Content Header */}
          <div className="mb-6">
            <div className="flex items-center gap-3 mb-4">
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
              >
                {content.type.toUpperCase()}
              </Badge>
              {content.is_premium && (
                <Badge variant="gold">💎 PREMIUM</Badge>
              )}
              <Badge variant="default">{content.category?.name}</Badge>
            </div>

            <h1 className="text-4xl font-bold text-black mb-4">
              {content.title}
            </h1>

            <div className="flex items-center gap-6 text-sm text-gray-600 mb-4">
              <div className="flex items-center gap-2">
                <div className="w-10 h-10 rounded-full bg-css-gold flex items-center justify-center">
                  <span className="text-black font-bold text-sm">
                    {content.author?.name?.charAt(0) || 'A'}
                  </span>
                </div>
                <div>
                  <div className="font-semibold text-black">
                    {content.author?.name || 'CSS Socios'}
                  </div>
                  <div>{new Date(content.published_at).toLocaleDateString('fr-FR')}</div>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <span>👁 {content.views?.toLocaleString() || 0} vues</span>
                <span>❤️ {content.likes || 0}</span>
                {content.duration && <span>⏱️ {content.duration}</span>}
              </div>
            </div>

            {content.excerpt && (
              <p className="text-xl text-gray-700 leading-relaxed mb-6">
                {content.excerpt}
              </p>
            )}
          </div>

          {/* Content Body */}
          <Card variant="elevated" className="mb-6">
            <div
              className="prose prose-lg max-w-none"
              dangerouslySetInnerHTML={{ __html: content.body || content.content }}
            />
          </Card>

          {/* Tags */}
          {content.tags && content.tags.length > 0 && (
            <div className="mb-6">
              <div className="flex flex-wrap gap-2">
                {content.tags.map((tag: any) => (
                  <Badge key={tag.id} variant="default" size="sm">
                    #{tag.name}
                  </Badge>
                ))}
              </div>
            </div>
          )}

          {/* Actions */}
          <Card variant="bordered" className="mb-8">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-4">
                <Button variant="primary">
                  ❤️ J'aime ({content.likes || 0})
                </Button>
                <Button variant="outline">
                  💬 Commenter
                </Button>
                <Button variant="ghost">
                  🔗 Partager
                </Button>
              </div>
              <Button variant="ghost">
                🔖 Sauvegarder
              </Button>
            </div>
          </Card>

          {/* Related Content */}
          {relatedContent.length > 0 && (
            <div>
              <h2 className="text-2xl font-bold mb-4">Contenu similaire</h2>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {relatedContent.map((item: any) => (
                  <Link key={item.id} to={`/content/${item.slug}`}>
                    <Card variant="elevated" hover padding="none">
                      <img
                        src={item.thumbnail || item.image || '/placeholder-image.jpg'}
                        alt={item.title}
                        className="w-full h-48 object-cover rounded-t-xl"
                      />
                      <div className="p-4">
                        <Badge variant="default" size="sm" className="mb-2">
                          {item.type}
                        </Badge>
                        <h3 className="font-bold line-clamp-2 mb-2">
                          {item.title}
                        </h3>
                        <p className="text-sm text-gray-600 line-clamp-2">
                          {item.excerpt}
                        </p>
                      </div>
                    </Card>
                  </Link>
                ))}
              </div>
            </div>
          )}
        </article>
      </div>
    </DashboardLayout>
  )
}

export default ContentDetailPage
