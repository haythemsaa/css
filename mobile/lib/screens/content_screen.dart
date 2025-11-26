import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../theme/juventus_theme.dart';

class ContentScreen extends StatefulWidget {
  const ContentScreen({super.key});

  @override
  State<ContentScreen> createState() => _ContentScreenState();
}

class _ContentScreenState extends State<ContentScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService();

  List<dynamic> _articles = [];
  List<dynamic> _videos = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _loadContent();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadContent() async {
    try {
      setState(() => _isLoading = true);

      // Load articles
      final articlesResponse = await _apiService.getContents(type: 'article');
      // Load videos
      final videosResponse = await _apiService.getContents(type: 'video');

      if (mounted) {
        setState(() {
          _articles = articlesResponse.data as List? ?? [];
          _videos = videosResponse.data as List? ?? [];
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: JuventusTheme.grey100,
      appBar: AppBar(
        title: const Text('ACTUALITÉS CSS'),
        backgroundColor: JuventusTheme.primaryBlack,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: JuventusTheme.primaryWhite,
          indicatorWeight: 3,
          labelColor: JuventusTheme.primaryWhite,
          unselectedLabelColor: JuventusTheme.grey500,
          labelStyle: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 13,
          ),
          tabs: const [
            Tab(icon: Icon(Icons.article_outlined), text: 'Articles'),
            Tab(icon: Icon(Icons.video_library_outlined), text: 'Vidéos'),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: () {
              // Open search
            },
          ),
        ],
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: JuventusTheme.primaryBlack,
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadContent,
              color: JuventusTheme.primaryBlack,
              child: TabBarView(
                controller: _tabController,
                children: [
                  _buildArticlesList(),
                  _buildVideosList(),
                ],
              ),
            ),
    );
  }

  Widget _buildArticlesList() {
    if (_articles.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.article_outlined,
              size: 80,
              color: JuventusTheme.grey400,
            ),
            const SizedBox(height: 16),
            const Text(
              'Aucun article disponible',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: JuventusTheme.grey700,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _articles.length,
      itemBuilder: (context, index) {
        final article = _articles[index];
        return _buildArticleCard(article);
      },
    );
  }

  Widget _buildArticleCard(dynamic article) {
    final dateFormat = DateFormat('dd MMM yyyy', 'fr_FR');
    final publishedAt = DateTime.parse(article['published_at'] ?? DateTime.now().toString());

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: JuventusDecorations.whiteCard,
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () {
          // Navigate to article details
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Featured Image
            if (article['featured_image'] != null)
              AspectRatio(
                aspectRatio: 16 / 9,
                child: Image.network(
                  article['featured_image'],
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) => Container(
                    color: JuventusTheme.grey200,
                    child: const Icon(
                      Icons.image_outlined,
                      size: 48,
                      color: JuventusTheme.grey400,
                    ),
                  ),
                ),
              ),

            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Category & Access Level
                  Row(
                    children: [
                      if (article['category'] != null)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: JuventusTheme.primaryBlack,
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Text(
                            article['category']['name'] ?? 'Actualités',
                            style: const TextStyle(
                              color: JuventusTheme.primaryWhite,
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 0.5,
                            ),
                          ),
                        ),
                      const SizedBox(width: 8),
                      if (article['access_level'] != 'free')
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: article['access_level'] == 'socios'
                                ? JuventusTheme.accentGold
                                : JuventusTheme.grey400,
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                article['access_level'] == 'socios'
                                    ? Icons.verified
                                    : Icons.star,
                                size: 12,
                                color: JuventusTheme.primaryWhite,
                              ),
                              const SizedBox(width: 4),
                              Text(
                                article['access_level'].toUpperCase(),
                                style: const TextStyle(
                                  color: JuventusTheme.primaryWhite,
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ],
                          ),
                        ),
                    ],
                  ),

                  const SizedBox(height: 12),

                  // Title
                  Text(
                    article['title'] ?? '',
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: JuventusTheme.primaryBlack,
                      height: 1.3,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),

                  const SizedBox(height: 8),

                  // Excerpt
                  if (article['excerpt'] != null)
                    Text(
                      article['excerpt'],
                      style: const TextStyle(
                        fontSize: 14,
                        color: JuventusTheme.grey600,
                        height: 1.4,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),

                  const SizedBox(height: 12),

                  // Meta Info
                  Row(
                    children: [
                      const Icon(Icons.calendar_today, size: 14, color: JuventusTheme.grey500),
                      const SizedBox(width: 4),
                      Text(
                        dateFormat.format(publishedAt),
                        style: const TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                      ),
                      const SizedBox(width: 16),
                      const Icon(Icons.visibility, size: 14, color: JuventusTheme.grey500),
                      const SizedBox(width: 4),
                      Text(
                        '${article['views_count'] ?? 0} vues',
                        style: const TextStyle(fontSize: 12, color: JuventusTheme.grey600),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildVideosList() {
    if (_videos.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.video_library_outlined,
              size: 80,
              color: JuventusTheme.grey400,
            ),
            const SizedBox(height: 16),
            const Text(
              'Aucune vidéo disponible',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: JuventusTheme.grey700,
              ),
            ),
          ],
        ),
      );
    }

    return GridView.builder(
      padding: const EdgeInsets.all(16),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 0.75,
        crossAxisSpacing: 16,
        mainAxisSpacing: 16,
      ),
      itemCount: _videos.length,
      itemBuilder: (context, index) {
        final video = _videos[index];
        return _buildVideoCard(video);
      },
    );
  }

  Widget _buildVideoCard(dynamic video) {
    final duration = video['video_duration']; // in seconds

    return Container(
      decoration: JuventusDecorations.whiteCard,
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () {
          // Navigate to video player
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Thumbnail
            AspectRatio(
              aspectRatio: 16 / 9,
              child: Stack(
                children: [
                  if (video['featured_image'] != null)
                    Image.network(
                      video['featured_image'],
                      width: double.infinity,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => Container(
                        color: JuventusTheme.grey900,
                        child: const Icon(
                          Icons.play_circle_outline,
                          size: 48,
                          color: JuventusTheme.primaryWhite,
                        ),
                      ),
                    )
                  else
                    Container(
                      color: JuventusTheme.grey900,
                      child: const Icon(
                        Icons.play_circle_outline,
                        size: 48,
                        color: JuventusTheme.primaryWhite,
                      ),
                    ),

                  // Play Button Overlay
                  const Center(
                    child: Icon(
                      Icons.play_circle_filled,
                      size: 48,
                      color: JuventusTheme.primaryWhite,
                    ),
                  ),

                  // Duration Badge
                  if (duration != null)
                    Positioned(
                      bottom: 8,
                      right: 8,
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 6,
                          vertical: 2,
                        ),
                        decoration: BoxDecoration(
                          color: JuventusTheme.primaryBlack.withOpacity(0.8),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          _formatDuration(duration),
                          style: const TextStyle(
                            color: JuventusTheme.primaryWhite,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),

                  // Access Level Badge
                  if (video['access_level'] != 'free')
                    Positioned(
                      top: 8,
                      right: 8,
                      child: Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(
                          color: video['access_level'] == 'socios'
                              ? JuventusTheme.accentGold
                              : JuventusTheme.grey400,
                          shape: BoxShape.circle,
                        ),
                        child: Icon(
                          video['access_level'] == 'socios'
                              ? Icons.verified
                              : Icons.star,
                          size: 12,
                          color: JuventusTheme.primaryWhite,
                        ),
                      ),
                    ),
                ],
              ),
            ),

            // Video Info
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(8),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      video['title'] ?? '',
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: JuventusTheme.primaryBlack,
                        height: 1.3,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const Spacer(),
                    Row(
                      children: [
                        const Icon(Icons.visibility, size: 12, color: JuventusTheme.grey500),
                        const SizedBox(width: 4),
                        Text(
                          '${video['views_count'] ?? 0}',
                          style: const TextStyle(
                            fontSize: 10,
                            color: JuventusTheme.grey600,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _formatDuration(int seconds) {
    final duration = Duration(seconds: seconds);
    final hours = duration.inHours;
    final minutes = duration.inMinutes.remainder(60);
    final secs = duration.inSeconds.remainder(60);

    if (hours > 0) {
      return '${hours}:${minutes.toString().padLeft(2, '0')}:${secs.toString().padLeft(2, '0')}';
    }
    return '${minutes}:${secs.toString().padLeft(2, '0')}';
  }
}
