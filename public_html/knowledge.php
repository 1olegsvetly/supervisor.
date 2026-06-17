<?php
require_once __DIR__ . '/functions.php';

$allData = getData('knowledge');
$knowledgeItems = array_values(array_filter($allData, static function ($item) {
    return !empty($item['is_published']);
}));

usort($knowledgeItems, static function ($a, $b) {
    return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
});

$requestedSlug = trim((string) ($_GET['slug'] ?? ''));
// 301: /knowledge.php?slug=foo -> /knowledge/foo  (and /knowledge?slug=foo -> /knowledge/foo)
if ($requestedSlug !== '' && in_array(currentPath(), ['/knowledge.php', '/knowledge'], true)) {
    header('Location: ' . knowledgeUrl($requestedSlug), true, 301);
    exit;
}
// 301: /knowledge.php -> /knowledge
if (currentPath() === '/knowledge.php') {
    header('Location: ' . knowledgeUrl(), true, 301);
    exit;
}
$currentArticle = null;
$groupedKnowledge = [];

foreach ($knowledgeItems as $item) {
    $category = trim((string) ($item['category'] ?? ''));
    if ($category === '' || $category === 'knowledge') {
        $category = 'Материалы базы знаний';
    }

    $groupedKnowledge[$category][] = $item;

    if ($requestedSlug !== '' && ($item['slug'] ?? '') === $requestedSlug) {
        $currentArticle = $item;
    }
}

if ($currentArticle === null && !empty($knowledgeItems)) {
    $currentArticle = $knowledgeItems[0];
} elseif ($currentArticle === null) {
    $currentArticle = [
        'title' => 'Статьи не найдены',
        'content_html' => '<p>В данный момент в базе знаний нет опубликованных статей.</p>',
        'slug' => 'empty'
    ];
}

$page_title = strip_tags(renderDynamicContent((string) ($currentArticle['meta_title'] ?? ('База знаний ' . ($config['site_name'] ?? 'VPN')))));
$page_description = strip_tags(renderDynamicContent((string) ($currentArticle['meta_description'] ?? ('Инструкции, протоколы, решения проблем и технические материалы по использованию ' . ($config['site_name'] ?? 'VPN') . '.'))));
$page_keywords = strip_tags(renderDynamicContent((string) ($currentArticle['keywords'] ?? 'база знаний, настройка vpn, инструкции vpn, протоколы vpn, faq vpn')));
// Сохраняем JSON-LD в глобальную переменную, footer.php выведет её перед </body>
if ($currentArticle && ($currentArticle['slug'] ?? '') !== 'empty') {
    $GLOBALS['_article_schema_html'] = generateArticleSchema($currentArticle, 'TechArticle');
    $page_og_type = 'article';
    // Также передаём og:image статьи в header.php
    if (!empty($currentArticle['image'])) {
        $siteUrl = baseSiteUrl();
        $imgPath = (string) $currentArticle['image'];
        $page_og_image = str_starts_with($imgPath, 'http') ? $imgPath : $siteUrl . '/' . ltrim($imgPath, '/');
    }
}
include __DIR__ . '/header.php';

$renderKnowledgeContent = static function (array $item): string {
    $content = (string) ($item['content_html'] ?? '');

    if ($content === '') {
        return '<p>Материал для этой статьи пока не добавлен.</p>';
    }

    return renderDynamicContent($content);
};
?>

<style>
.knowledge-docs {
    padding: 0 0 72px;
}

.knowledge-docs__layout {
    display: grid;
    grid-template-columns: minmax(260px, 320px) minmax(0, 1fr);
    gap: 28px;
    align-items: start;
}

.knowledge-docs__sidebar {
    position: sticky;
    top: 104px;
    padding: 24px;
    border-radius: 24px;
    max-height: calc(100vh - 104px - 40px);
    overflow-y: auto;
    overflow-x: hidden;
    scroll-behavior: smooth;
}

.knowledge-docs__sidebar-header {
    margin-bottom: 22px;
}

.knowledge-docs__sidebar-header h2 {
    margin: 0 0 10px;
    font-size: 1.25rem;
}

.knowledge-docs__sidebar-header p {
    margin: 0;
    color: var(--text-secondary);
    line-height: 1.6;
}

.knowledge-docs__nav-group + .knowledge-docs__nav-group {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.08);
}

.knowledge-docs__nav-title {
    display: block;
    margin-bottom: 10px;
    color: var(--accent-cyan);
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.knowledge-docs__nav-list {
    display: grid;
    gap: 8px;
}

.knowledge-docs__nav-link {
    display: block;
    padding: 10px 12px;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 14px;
    border: 1px solid transparent;
    transition: 0.25s ease;
    line-height: 1.45;
}

.knowledge-docs__nav-link:hover,
.knowledge-docs__nav-link.is-active {
    color: var(--text-main);
    border-color: rgba(0, 240, 255, 0.18);
    background: rgba(0, 240, 255, 0.08);
    box-shadow: inset 0 0 0 1px rgba(0, 240, 255, 0.08);
}

.knowledge-docs__content {
    display: grid;
    gap: 24px;
    max-height: calc(100vh - 104px - 40px);
    overflow-y: auto;
    overflow-x: hidden;
    scroll-behavior: smooth;
    padding-right: 8px;
}

.knowledge-docs__content::-webkit-scrollbar {
    width: 8px;
}

.knowledge-docs__content::-webkit-scrollbar-track {
    background: transparent;
}

.knowledge-docs__content::-webkit-scrollbar-thumb {
    background: rgba(0, 240, 255, 0.3);
    border-radius: 4px;
}

.knowledge-docs__content::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 240, 255, 0.5);
}

.knowledge-docs__sidebar::-webkit-scrollbar {
    width: 6px;
}

.knowledge-docs__sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.knowledge-docs__sidebar::-webkit-scrollbar-thumb {
    background: rgba(0, 240, 255, 0.3);
    border-radius: 3px;
}

.knowledge-docs__sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 240, 255, 0.5);
}

.knowledge-docs__article {
    padding: 32px;
    border-radius: 28px;
    scroll-margin-top: 108px;
}

.knowledge-docs__article.is-featured {
    border: 1px solid rgba(0, 240, 255, 0.16);
}

.knowledge-docs__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,0.04);
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.knowledge-docs__article h2 {
    margin: 0 0 12px;
    font-size: clamp(1.8rem, 2.5vw, 2.5rem);
    line-height: 1.15;
}

.knowledge-docs__summary {
    margin: 0 0 20px;
    color: var(--text-secondary);
    line-height: 1.75;
    font-size: 1.02rem;
}

.knowledge-docs__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 22px;
}

.knowledge-docs__meta span {
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,0.04);
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.knowledge-docs__image {
    width: 100%;
    max-height: 360px;
    object-fit: cover;
    border-radius: 22px;
    margin-bottom: 22px;
    border: 1px solid rgba(255,255,255,0.08);
}

.knowledge-docs__body {
    color: var(--text-main);
    line-height: 1.8;
}

.knowledge-docs__body h2,
.knowledge-docs__body h3,
.knowledge-docs__body h4 {
    margin-top: 1.6em;
    margin-bottom: 0.6em;
}

.knowledge-docs__body p,
.knowledge-docs__body ul,
.knowledge-docs__body ol,
.knowledge-docs__body table,
.knowledge-docs__body blockquote {
    margin-bottom: 1.1em;
}

.knowledge-docs__body ul,
.knowledge-docs__body ol {
    padding-left: 1.4rem;
}

.knowledge-docs__body table {
    width: 100%;
    border-collapse: collapse;
    overflow: hidden;
    border-radius: 18px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.02);
}

.knowledge-docs__body th,
.knowledge-docs__body td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    text-align: left;
    vertical-align: top;
}

.knowledge-docs__body th {
    color: var(--accent-cyan);
    font-weight: 700;
}

.knowledge-docs__cta {
    margin-top: 28px;
    padding: 24px;
    border-radius: 22px;
    border: 1px solid rgba(255,255,255,0.08);
    background: linear-gradient(135deg, rgba(0, 240, 255, 0.08), rgba(112, 0, 255, 0.08));
}

.knowledge-docs__cta h3 {
    margin: 0 0 10px;
}

.knowledge-docs__cta p {
    margin: 0 0 16px;
    color: var(--text-secondary);
}

.knowledge-docs__empty {
    padding: 42px;
    text-align: center;
    color: var(--text-secondary);
    border-radius: 24px;
}

@media (max-width: 1024px) {
    .knowledge-docs__layout {
        grid-template-columns: 1fr;
    }

    .knowledge-docs__sidebar {
        position: static;
        max-height: none;
        overflow: visible;
    }

    .knowledge-docs__content {
        max-height: none;
        overflow: visible;
    }
}

@media (max-width: 768px) {
    .knowledge-docs__article {
        padding: 24px;
    }
}
</style>

<section class="page-hero page-hero--compact">
    <div class="container">
        <div class="page-hero__content glass">
            <p class="eyebrow">База знаний</p>
            <h1 class="section-title">База знаний <?php echo e($config['site_name'] ?? 'SuperVisor VPN'); ?></h1>
            <p class="hero__subtitle">Все инструкции, протоколы, ответы на частые вопросы и технические материалы собраны на одной странице в удобном формате документации.</p>
        </div>
    </div>
</section>

<section class="knowledge-docs">
    <div class="container">
        <?php if (!empty($knowledgeItems)): ?>
            <div class="knowledge-docs__layout">
                <aside class="knowledge-docs__sidebar glass">
                    <div class="knowledge-docs__sidebar-header">
                        <h2>Содержание</h2>
                        <p>Слева собраны все заголовки материалов. Нажмите на нужный раздел, и страница плавно прокрутится к статье.</p>
                    </div>

                    <?php foreach ($groupedKnowledge as $category => $categoryItems): ?>
                        <div class="knowledge-docs__nav-group">
                            <span class="knowledge-docs__nav-title"><?php echo e($category); ?></span>
                            <nav class="knowledge-docs__nav-list" aria-label="Навигация по разделу <?php echo e($category); ?>">
                                <?php foreach ($categoryItems as $item): ?>
                                    <a
                                        href="<?php echo e(knowledgeUrl($item['slug'])); ?>#article-<?php echo e($item['slug']); ?>"
                                        class="knowledge-docs__nav-link<?php echo (($currentArticle['slug'] ?? '') === ($item['slug'] ?? '')) ? ' is-active' : ''; ?>"
                                        data-knowledge-nav="<?php echo e($item['slug']); ?>"
                                    >
                                        <?php echo renderDynamicContent($item['title'] ?? ''); ?>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>
                    <?php endforeach; ?>
                </aside>

                <div class="knowledge-docs__content">
                    <?php foreach ($knowledgeItems as $index => $item): ?>
                        <article
                            id="article-<?php echo e($item['slug']); ?>"
                            class="knowledge-docs__article glass<?php echo $index === 0 ? ' is-featured' : ''; ?>"
                            data-knowledge-article="<?php echo e($item['slug']); ?>"
                        >
                            <div class="knowledge-docs__eyebrow">
                                <span><?php echo e($item['category'] && $item['category'] !== 'knowledge' ? $item['category'] : 'База знаний'); ?></span>
                                <span>Обновлено: <?php echo e($item['updated_at'] ?? date('Y-m-d')); ?></span>
                            </div>

                            <h2><?php echo renderDynamicContent($item['title'] ?? ''); ?></h2>

                            <?php if (!empty($item['excerpt'])): ?>
                                <p class="knowledge-docs__summary"><?php echo renderDynamicContent($item['excerpt'] ?? ''); ?></p>
                            <?php endif; ?>

                            <div class="knowledge-docs__meta">
                                <span>Ссылка на раздел: #<?php echo e($item['slug']); ?></span>
                                <span>Статус: <?php echo !empty($item['is_published']) ? 'Опубликовано' : 'Черновик'; ?></span>
                            </div>

                            <?php if (!empty($item['image'])): ?>
                                <img class="knowledge-docs__image" src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy" decoding="async">
                            <?php endif; ?>

                            <div class="knowledge-docs__body">
                                <?php echo $renderKnowledgeContent($item); ?>
                            </div>

                            <div class="knowledge-docs__cta">
                                <h3>Нужна помощь с настройкой?</h3>
                                <p>Если инструкция не решила вопрос, перейдите в Telegram и получите помощь по подключению, выбору протокола или диагностике ошибки.</p>
                                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary js-tg-track" data-track="tg_direct">Перейти в Telegram</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="knowledge-docs__empty glass">
                <h2>База знаний пока пуста</h2>
                <p>Добавьте статьи через админку, и они автоматически появятся на этой странице в формате wiki.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const navLinks = Array.from(document.querySelectorAll('[data-knowledge-nav]'));
    const articles = Array.from(document.querySelectorAll('[data-knowledge-article]'));
    const sidebar = document.querySelector('.knowledge-docs__sidebar');
    const content = document.querySelector('.knowledge-docs__content');

    function setActive(slug) {
        navLinks.forEach(function (link) {
            link.classList.toggle('is-active', link.getAttribute('data-knowledge-nav') === slug);
        });
    }

    navLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            const slug = link.getAttribute('data-knowledge-nav');
            const article = document.getElementById('article-' + slug);

            if (!article) {
                return;
            }

            event.preventDefault();
            article.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setActive(slug);
            history.replaceState({ slug: slug }, '', '/knowledge/' + encodeURIComponent(slug));
        });
    });

    if ('IntersectionObserver' in window && articles.length) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const slug = entry.target.getAttribute('data-knowledge-article');
                    setActive(slug);
                }
            });
        }, { threshold: 0.2, rootMargin: '-18% 0px -60% 0px' });

        articles.forEach(function (article) {
            observer.observe(article);
        });
    }

    const requestedSlug = <?php echo json_encode($requestedSlug, JSON_UNESCAPED_UNICODE); ?> || new URLSearchParams(window.location.search).get('slug');
    if (requestedSlug) {
        const targetArticle = document.getElementById('article-' + requestedSlug);
        if (targetArticle) {
            setActive(requestedSlug);
            setTimeout(function () {
                targetArticle.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 250);
        }
    }

    // Independent scrolling for sidebar and content with wheel support
    if (sidebar && content) {
        const layout = document.querySelector('.knowledge-docs__layout');
        
        function handleWheel(e, scrollableElement) {
            if (!scrollableElement) return;
            
            const isOverSidebar = sidebar && sidebar.contains(e.target);
            const isOverContent = content && content.contains(e.target);
            
            if (!isOverSidebar && !isOverContent) return;
            
            const target = isOverSidebar ? sidebar : content;
            const rect = target.getBoundingClientRect();
            const isMouseInTarget = e.clientX >= rect.left && e.clientX <= rect.right &&
                                   e.clientY >= rect.top && e.clientY <= rect.bottom;
            
            if (!isMouseInTarget) return;
            
            e.preventDefault();
            
            const scrollAmount = e.deltaY > 0 ? 80 : -80;
            target.scrollTop += scrollAmount;
        }
        
        layout.addEventListener('wheel', function (e) {
            handleWheel(e, e.target);
        }, { passive: false });
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
