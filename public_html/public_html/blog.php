<?php
require_once __DIR__ . '/functions.php';

$legacySlug = trim((string) ($_GET['slug'] ?? ''));
// 301: /blog.php?slug=foo -> /blog/foo  (and /blog?slug=foo -> /blog/foo)
if ($legacySlug !== '' && in_array(currentPath(), ['/blog.php', '/blog'], true)) {
    header('Location: ' . blogUrl($legacySlug), true, 301);
    exit;
}
// 301: /blog.php -> /blog
if (currentPath() === '/blog.php') {
    $params = $_GET;
    header('Location: ' . blogUrl(null, $params), true, 301);
    exit;
}

$allArticles = array_values(array_filter(getData('blog'), fn($item) => !empty($item['is_published'])));
usort($allArticles, fn($a, $b) => strcmp((string) ($b['updated_at'] ?? ''), (string) ($a['updated_at'] ?? '')));

$allCategories = array_values(array_unique(array_filter(array_map(fn($item) => trim((string) ($item['category'] ?? 'Блог')), $allArticles), fn($cat) => $cat !== '')));
sort($allCategories);

$selectedCategory = trim((string) ($_GET['category'] ?? ''));
$searchQuery = trim((string) ($_GET['q'] ?? ''));
$articles = $allArticles;

if ($selectedCategory !== '' && in_array($selectedCategory, $allCategories, true)) {
    $articles = array_values(array_filter($articles, fn($item) => trim((string) ($item['category'] ?? 'Блог')) === $selectedCategory));
}
if ($searchQuery !== '') {
    $needle = mb_strtolower($searchQuery);
    $articles = array_values(array_filter($articles, static function ($item) use ($needle) {
        $haystack = mb_strtolower(plainTextFromHtml(($item['title'] ?? '') . ' ' . ($item['excerpt'] ?? '') . ' ' . ($item['keywords'] ?? '') . ' ' . ($item['content_html'] ?? '')));
        return str_contains($haystack, $needle);
    }));
}

$currentSlug = $legacySlug;
$currentArticle = null;
foreach ($allArticles as $article) {
    if (($article['slug'] ?? '') === $currentSlug) {
        $currentArticle = $article;
        break;
    }
}

$perPage = 6;
$totalArticles = count($articles);
$totalPages = max(1, (int) ceil($totalArticles / $perPage));
$currentPage = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));

if ($currentArticle) {
    $page_title = strip_tags(renderDynamicContent((string) ($currentArticle['meta_title'] ?: $currentArticle['title'])));
    $page_description = strip_tags(renderDynamicContent((string) ($currentArticle['meta_description'] ?: $currentArticle['excerpt'])));
    $page_keywords = strip_tags(renderDynamicContent((string) ($currentArticle['keywords'] ?? ('vpn, блог, ' . ($config['site_name'] ?? 'VPN')))));
} else {
    $page_title = 'Блог ' . ($config['site_name'] ?? 'VPN') . ' — статьи о VPN, безопасности и обходе блокировок';
    $page_description = 'Подборка SEO-статей ' . ($config['site_name'] ?? 'VPN') . ' о выборе VPN, настройке устройств, безопасности, скорости и стабильной работе сервисов.';
    $page_keywords = 'блог VPN, статьи про VPN, настройка VPN, безопасность VPN, обход блокировок';
}

// Сохраняем JSON-LD в глобальную переменную, footer.php выведет её перед </body>
if ($currentArticle) {
    $GLOBALS['_article_schema_html'] = generateArticleSchema($currentArticle, 'BlogPosting');
    $page_og_type = 'article';
    // Также передаём og:image статьи в header.php
    if (!empty($currentArticle['image'])) {
        $siteUrl = baseSiteUrl();
        $imgPath = (string) $currentArticle['image'];
        $page_og_image = str_starts_with($imgPath, 'http') ? $imgPath : $siteUrl . '/' . ltrim($imgPath, '/');
    }
}
	include __DIR__ . '/header.php';
	?>


<style>
.blog-tools { margin-bottom: 28px; display: grid; gap: 16px; }
.blog-search { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.blog-search__input { flex:1; min-width:240px; padding: 13px 16px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04); color: var(--text-main); }
.blog-categories-filter { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.blog-category-btn { display: inline-block; padding: 8px 16px; border-radius: 20px; text-decoration: none; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; cursor: pointer; font-weight: 500; color: var(--text-secondary); background: rgba(255,255,255,0.05); }
.blog-category-btn.active { color: var(--accent-cyan); background: rgba(0, 240, 255, 0.15); border-color: rgba(0, 240, 255, 0.3); }
.blog-card-link { color: inherit; text-decoration: none; display: contents; }
.blog-card-item { display:flex; flex-direction:column; }
.related-articles { margin-top: 28px; padding: 24px; border-radius: 22px; border: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03); }
.related-articles__grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap:14px; margin-top:14px; }
.related-articles__card { padding:16px; border-radius:16px; background:rgba(255,255,255,0.04); color:var(--text-main); text-decoration:none; border:1px solid rgba(255,255,255,0.06); }
.related-articles__card span { display:block; margin-bottom:8px; color:var(--accent-cyan); font-size:.85rem; }
</style>

<?php if ($currentArticle): ?>
<section class="page-hero page-hero--compact blog-page">
    <div class="container">
        <div class="page-hero__content glass wow fadeInUp">
            <span class="eyebrow">Блог <?php echo e($config['site_name'] ?? 'SuperVisor VPN'); ?></span>
            <h1><?php echo renderDynamicContent($currentArticle['title'] ?? ''); ?></h1>
            <p class="hero__subtitle"><?php echo renderDynamicContent($currentArticle['excerpt'] ?? ''); ?></p>
            <div class="knowledge-article__meta">
                <span>Категория: <?php echo e($currentArticle['category'] ?? 'Блог'); ?></span>
                <span>Автор: <?php echo e($currentArticle['author'] ?? 'Администратор'); ?></span>
                <span>Обновлено: <?php echo e(normalizeLastmod($currentArticle['updated_at'] ?? '')); ?></span>
            </div>
        </div>
    </div>
</section>

<section class="knowledge-page">
    <div class="container knowledge-layout">
        <aside class="knowledge-sidebar glass wow fadeInLeft">
            <h3>Другие статьи</h3>
            <ul class="knowledge-sidebar__list">
                <?php foreach (array_slice($allArticles, 0, 12) as $article): ?>
                    <li>
                        <a class="knowledge-sidebar__link <?php echo (($article['slug'] ?? '') === ($currentArticle['slug'] ?? '')) ? 'is-active' : ''; ?>"
                           href="<?php echo e(blogUrl($article['slug'] ?? '')); ?>">
                            <?php echo e($article['title'] ?? ''); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <article class="knowledge-article glass wow fadeInUp">
            <?php if (!empty($currentArticle['image'])): ?>
                <img class="knowledge-article__image" src="<?php echo e($currentArticle['image']); ?>" alt="<?php echo e($currentArticle['title'] ?? ''); ?>" loading="lazy" decoding="async">
            <?php endif; ?>
            <div class="knowledge-article__content">
                <?php echo renderDynamicContent($currentArticle['content_html'] ?? ''); ?>
                <?php $relatedArticles = findRelatedItems($currentArticle, $allArticles, 3); ?>
                <?php if ($relatedArticles): ?>
                    <section class="related-articles">
                        <h2>Смотрите также</h2>
                        <div class="related-articles__grid">
                            <?php foreach ($relatedArticles as $related): ?>
                                <a class="related-articles__card" href="<?php echo e(blogUrl($related['slug'] ?? '')); ?>">
                                    <span><?php echo e($related['category'] ?? 'Блог'); ?></span>
                                    <?php echo e($related['title'] ?? ''); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
                <div class="knowledge-article__cta glass wow fadeInUp">
                    <h3>Нужен рабочий VPN без лишней настройки?</h3>
                    <p>Получите доступ через Telegram-бота <?php echo e($config['site_name'] ?? 'SuperVisor VPN'); ?> и выберите подходящий формат подключения под ваше устройство.</p>
                    <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Подключить VPN</a>
                </div>
            </div>
        </article>
    </div>
</section>
<?php else: ?>
<section class="page-hero page-hero--compact blog-page">
    <div class="container">
        <div class="page-hero__content glass wow fadeInUp">
            <span class="eyebrow">Блог <?php echo e($config['site_name'] ?? 'SuperVisor VPN'); ?></span>
            <h1>Полезные статьи о VPN, безопасности и стабильной работе сервисов</h1>
            <p class="hero__subtitle">В блоге собраны практические материалы по выбору VPN, настройке устройств, диагностике ошибок, защите трафика и обходу блокировок.</p>
        </div>
    </div>
</section>

<section class="knowledge-page">
    <div class="container">
        <div class="blog-tools">
            <form class="blog-search" method="GET" action="<?php echo e(blogUrl()); ?>">
                <?php if ($selectedCategory !== ''): ?><input type="hidden" name="category" value="<?php echo e($selectedCategory); ?>"><?php endif; ?>
                <input class="blog-search__input" type="search" name="q" value="<?php echo e($searchQuery); ?>" placeholder="Поиск по статьям">
                <button class="btn btn--primary" type="submit">Найти</button>
                <?php if ($searchQuery !== '' || $selectedCategory !== ''): ?><a class="btn btn--secondary" href="<?php echo e(blogUrl()); ?>">Сбросить</a><?php endif; ?>
            </form>
            <nav class="blog-categories-filter" aria-label="Фильтр по категориям">
                <span style="color: var(--text-secondary); font-weight: 600;">Категории:</span>
                <a href="<?php echo e(blogUrl(null, ['q' => $searchQuery])); ?>" class="blog-category-btn <?php echo $selectedCategory === '' ? 'active' : ''; ?>">Все</a>
                <?php foreach ($allCategories as $category): ?>
                    <a href="<?php echo e(blogUrl(null, ['category' => $category, 'q' => $searchQuery])); ?>" class="blog-category-btn <?php echo $selectedCategory === $category ? 'active' : ''; ?>"><?php echo e($category); ?></a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div style="margin-bottom: 16px; color: var(--text-secondary); font-size: 14px;">
            Найдено статей: <strong><?php echo count($articles); ?></strong>
        </div>

        <div class="knowledge-grid" id="blogGrid">
            <?php $pageArticles = array_slice($articles, ($currentPage - 1) * $perPage, $perPage); ?>
            <?php foreach ($pageArticles as $article): ?>
                <article class="knowledge-card glass wow fadeInUp blog-card-item" title="<?php echo e($article['title'] ?? ''); ?>">
                    <a class="blog-card-link" href="<?php echo e(blogUrl($article['slug'] ?? '')); ?>">
                        <?php if (!empty($article['image'])): ?>
                            <img class="knowledge-card__image" src="<?php echo e($article['image']); ?>" alt="<?php echo e($article['title'] ?? ''); ?>" loading="lazy" decoding="async">
                        <?php endif; ?>
                        <div class="knowledge-card__content">
                            <div class="knowledge-card__date"><?php echo e($article['category'] ?? 'Блог'); ?> · <?php echo e(normalizeLastmod($article['updated_at'] ?? '')); ?></div>
                            <h2 class="knowledge-card__title"><?php echo e($article['title'] ?? ''); ?></h2>
                            <p class="knowledge-card__excerpt"><?php echo e($article['excerpt'] ?? ''); ?></p>
                            <div style="margin-top:auto;"><span class="btn btn--secondary" style="display:inline-block;">Читать статью</span></div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if (!$pageArticles): ?>
            <div class="glass" style="padding:28px;border-radius:20px;color:var(--text-secondary);">По заданным условиям статьи не найдены.</div>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
        <nav class="blog-pagination" aria-label="Пагинация блога">
            <?php if ($currentPage > 1): ?>
                <a href="<?php echo e(blogUrl(null, ['page' => $currentPage - 1, 'category' => $selectedCategory, 'q' => $searchQuery])); ?>" class="blog-pagination__btn">← Назад</a>
            <?php endif; ?>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a href="<?php echo e(blogUrl(null, ['page' => $p, 'category' => $selectedCategory, 'q' => $searchQuery])); ?>" class="blog-pagination__btn <?php echo $p === $currentPage ? 'active' : ''; ?>"><?php echo $p; ?></a>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages): ?>
                <a href="<?php echo e(blogUrl(null, ['page' => $currentPage + 1, 'category' => $selectedCategory, 'q' => $searchQuery])); ?>" class="blog-pagination__btn">Вперёд →</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
