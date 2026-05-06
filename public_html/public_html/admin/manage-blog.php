<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../functions.php';

$message = '';
$error = '';
$editSlug = trim((string) ($_GET['slug'] ?? ''));
// Get all unique categories from existing articles
$existingCategories = array_unique(array_filter(array_map(fn($item) => trim((string) ($item['category'] ?? '')), getData('blog')), fn($cat) => $cat !== ''));
$defaultCategories = ['Блог', 'Новости', 'Обзоры', 'Безопасность', 'Инструкции', 'Сервисы', 'Гайды'];
$categories = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));
sort($categories);
$articles = getData('blog');

function findBlogIndex(array $articles, string $slug): ?int {
    foreach ($articles as $index => $article) {
        if (($article['slug'] ?? '') === $slug) {
            return $index;
        }
    }
    return null;
}

function normalizeBlogItem(array $input, ?array $existing = null): array {
    $title = trim((string) ($input['title'] ?? ''));
    $slug = trim((string) ($input['slug'] ?? ''));
    $slug = $slug !== '' ? slugify($slug) : slugify($title);
    $now = date('Y-m-d');
    $publishedValue = $input['is_published'] ?? false;

    return [
        'id' => (int) ($existing['id'] ?? $input['id'] ?? time()),
        'title' => $title,
        'slug' => $slug,
        'category' => trim((string) ($input['category'] ?? 'Блог')) ?: 'Блог',
        'meta_title' => trim((string) ($input['meta_title'] ?? $title)),
        'meta_description' => trim((string) ($input['meta_description'] ?? '')),
        'keywords' => trim((string) ($input['keywords'] ?? '')),
        'excerpt' => trim((string) ($input['excerpt'] ?? '')),
        'image' => trim((string) ($input['image'] ?? '/img/blog/blog-1.jpg')),
        'updated_at' => trim((string) ($input['updated_at'] ?? $existing['updated_at'] ?? $now)),
        'author' => trim((string) ($input['author'] ?? $existing['author'] ?? 'Администратор')),
        'is_published' => normalizeBoolean($publishedValue, true),
        'content_html' => trim((string) ($input['content_html'] ?? '')),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save_article') {
        $existingIndex = findBlogIndex($articles, trim((string) ($_POST['slug'] ?? '')));
        $existingArticle = $existingIndex !== null ? $articles[$existingIndex] : null;
        $payload = normalizeBlogItem($_POST, $existingArticle);

        if ($payload['title'] === '' || $payload['content_html'] === '') {
            $error = 'Заполните заголовок и содержимое статьи.';
        } else {
            if ($existingIndex !== null) {
                $payload['id'] = $articles[$existingIndex]['id'] ?? $payload['id'];
                $articles[$existingIndex] = $payload;
            } else {
                $payload['id'] = count($articles) ? (max(array_column($articles, 'id')) + 1) : 1;
                $articles[] = $payload;
            }

            usort($articles, static fn($a, $b) => strcmp((string) ($b['updated_at'] ?? ''), (string) ($a['updated_at'] ?? '')));
            saveData('blog', $articles);
            $message = 'Статья сохранена.';
            $editSlug = $payload['slug'];
        }
    }

    if ($action === 'delete_article') {
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $articles = array_values(array_filter($articles, static fn($item) => ($item['slug'] ?? '') !== $slug));
        saveData('blog', $articles);
        $message = 'Статья удалена.';
        $editSlug = '';
    }

    if ($action === 'delete_all') {
        saveData('blog', []);
        $articles = [];
        $message = 'Все статьи удалены.';
        $editSlug = '';
    }

    if ($action === 'import_csv') {
        if (!isset($_FILES['csv_file']) || ($_FILES['csv_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $error = 'Загрузите CSV-файл.';
        } else {
            $rows = readCsvRows($_FILES['csv_file']['tmp_name']);
            if ($rows === []) {
                $error = 'Не удалось прочитать CSV-файл или в нём нет корректных строк.';
            } else {
                $imported = 0;

                foreach ($rows as $rowData) {
                    $payload = normalizeBlogItem($rowData, null);
                    if ($payload['title'] === '' || $payload['content_html'] === '') {
                        continue;
                    }

                    $existingIndex = findBlogIndex($articles, $payload['slug']);
                    if ($existingIndex !== null) {
                        $payload['id'] = $articles[$existingIndex]['id'] ?? $payload['id'];
                        $articles[$existingIndex] = $payload;
                    } else {
                        $payload['id'] = count($articles) ? (max(array_column($articles, 'id')) + 1) : 1;
                        $articles[] = $payload;
                    }
                    $imported++;
                }

                usort($articles, static fn($a, $b) => strcmp((string) ($b['updated_at'] ?? ''), (string) ($a['updated_at'] ?? '')));
                saveData('blog', $articles);
                $message = "Импортировано $imported статей.";
            }
        }
    }


    if ($action === 'interlink_content') {
        $blogItems = getData('blog');
        $knowledgeItems = getData('knowledge');
        $blogTargets = array_map(static function ($item) { $item['_url'] = blogUrl((string) ($item['slug'] ?? '')); return $item; }, $blogItems);
        $knowledgeTargets = array_map(static function ($item) { $item['_url'] = knowledgeUrl((string) ($item['slug'] ?? '')); return $item; }, $knowledgeItems);
        $targets = array_merge($blogTargets, $knowledgeTargets);
        $changedBlog = 0;
        foreach ($blogItems as &$article) {
            $old = (string) ($article['content_html'] ?? '');
            $article['content_html'] = interlinkContentHtml($old, $targets, (string) ($article['slug'] ?? ''), 5);
            if ($article['content_html'] !== $old) { $changedBlog++; }
        }
        unset($article);
        $changedKnowledge = 0;
        foreach ($knowledgeItems as &$item) {
            $old = (string) ($item['content_html'] ?? '');
            $item['content_html'] = interlinkContentHtml($old, $targets, (string) ($item['slug'] ?? ''), 5);
            if ($item['content_html'] !== $old) { $changedKnowledge++; }
        }
        unset($item);
        saveData('blog', $blogItems);
        saveData('knowledge', $knowledgeItems);
        $message = 'Перелинковка выполнена. Обновлено материалов: блог — ' . $changedBlog . ', база знаний — ' . $changedKnowledge . '.';
    }

    if ($action === 'convert_webp') {
        $result = convertLocalImagesToWebp(__DIR__ . '/..');
        if (!empty($result['paths'])) {
            $blogItems = getData('blog');
            foreach ($blogItems as &$article) {
                $article['image'] = replaceImagePathsInContent((string) ($article['image'] ?? ''), $result['paths']);
                $article['content_html'] = replaceImagePathsInContent((string) ($article['content_html'] ?? ''), $result['paths']);
            }
            unset($article);
            saveData('blog', $blogItems);

            $knowledgeItems = getData('knowledge');
            foreach ($knowledgeItems as &$item) {
                $item['image'] = replaceImagePathsInContent((string) ($item['image'] ?? ''), $result['paths']);
                $item['content_html'] = replaceImagePathsInContent((string) ($item['content_html'] ?? ''), $result['paths']);
            }
            unset($item);
            saveData('knowledge', $knowledgeItems);
        }
        $message = 'WebP-обработка завершена. Преобразовано: ' . (int) $result['converted'] . ', пропущено: ' . (int) $result['skipped'] . '.';
        if (!empty($result['errors'])) {
            $error = implode(' ', array_slice($result['errors'], 0, 4));
        }
    }

    if ($action === 'export_csv' || $action === 'download_template') {
        $filename = ($action === 'export_csv') ? 'blog_export_' . date('Y-m-d') . '.csv' : 'blog_template.csv';
        $exportData = ($action === 'export_csv') ? $articles : [
            [
                'title' => 'Заголовок примера',
                'slug' => 'example-slug',
                'category' => 'Блог',
                'meta_title' => 'SEO заголовок',
                'meta_description' => 'SEO описание',
                'keywords' => 'ключ1, ключ2',
                'excerpt' => 'Краткий анонс статьи...',
                'image' => '/img/blog/blog-1.jpg',
                'updated_at' => date('Y-m-d'),
                'author' => 'Администратор',
                'is_published' => '1',
                'content_html' => '<h2>Заголовок</h2><p>Текст статьи...</p>'
            ]
        ];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel
        
        if (!empty($exportData)) {
            fputcsv($output, array_keys($exportData[0]), ';');
            foreach ($exportData as $row) {
                fputcsv($output, array_values($row), ';');
            }
        }
        fclose($output);
        exit;
    }
}

$articles = getData('blog');
if (!is_array($articles)) {
    $articles = [];
}
usort($articles, static function ($a, $b) {
    return strcmp((string) ($b['updated_at'] ?? ''), (string) ($a['updated_at'] ?? ''));
});

$currentArticle = null;
if ($editSlug !== '') {
    foreach ($articles as $article) {
        if (($article['slug'] ?? '') === $editSlug) {
            $currentArticle = $article;
            break;
        }
    }
}

if (!$currentArticle) {
    $currentArticle = [
        'title' => '',
        'slug' => '',
        'category' => 'Блог',
        'meta_title' => '',
        'meta_description' => '',
        'keywords' => '',
        'excerpt' => '',
        'image' => '/img/blog/blog-1.jpg',
        'updated_at' => date('Y-m-d'),
        'author' => 'Администратор',
        'is_published' => true,
        'content_html' => "<h2>Введение</h2>\n<p>Текст статьи...</p>",
    ];
}

$categorySummary = [];
foreach ($articles as $article) {
    $categoryName = trim((string) ($article['category'] ?? 'Блог')) ?: 'Блог';
    $categorySummary[$categoryName] = ($categorySummary[$categoryName] ?? 0) + 1;
}
ksort($categorySummary);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Блог | SuperVisor Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        :root { --sidebar-width: 260px; }
        body { background: var(--bg-main); color: var(--text-main); font-family: 'Inter', sans-serif; margin: 0; }
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); background: var(--bg-secondary); border-right: 1px solid rgba(255,255,255,0.05); position: fixed; height: 100vh; padding: 30px 20px; box-sizing: border-box; }
        .sidebar__logo { font-size: 22px; font-weight: 800; margin-bottom: 40px; display: block; color: var(--text-main); text-decoration: none; }
        .sidebar__logo span { color: var(--accent-cyan); }
        .sidebar__nav ul { list-style: none; padding: 0; margin: 0; }
        .sidebar__nav li { margin-bottom: 8px; }
        .sidebar__link { display: flex; align-items: center; padding: 12px 15px; color: var(--text-secondary); text-decoration: none; border-radius: 10px; transition: 0.3s; }
        .sidebar__link:hover, .sidebar__link.active { background: rgba(0, 240, 255, 0.1); color: var(--accent-cyan); }

        .admin-main { flex: 1; margin-left: var(--sidebar-width); padding: 36px; }
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 28px; }
        .page-title { font-size: 30px; font-weight: 800; margin: 0 0 8px; }
        .page-subtitle { margin: 0; color: var(--text-secondary); line-height: 1.7; max-width: 780px; }

        .top-grid { display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(320px, 0.8fr); gap: 22px; margin-bottom: 22px; }
        .content-grid { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(330px, 0.85fr); gap: 22px; }
        .card { background: var(--bg-secondary); border-radius: 20px; padding: 24px; border: 1px solid rgba(255,255,255,0.05); }
        .card-title { margin: 0 0 14px; font-size: 20px; font-weight: 700; }
        .card-subtitle { margin: -4px 0 18px; color: var(--text-secondary); line-height: 1.7; font-size: 14px; }

        .metrics { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .metric { padding: 18px; border-radius: 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
        .metric__label { color: var(--text-secondary); font-size: 13px; margin-bottom: 10px; }
        .metric__value { font-size: 28px; font-weight: 800; color: var(--accent-cyan); }

        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-secondary); font-size: 14px; }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px 14px;
            color: var(--text-main);
            font-family: inherit;
            box-sizing: border-box;
        }
        .form-textarea { min-height: 420px; font-family: 'Fira Code', monospace; font-size: 14px; line-height: 1.6; }
        .checkbox-row { display: flex; align-items: center; gap: 10px; margin: 6px 0 22px; }
        .actions-row { display: flex; gap: 12px; flex-wrap: wrap; }

        .btn-danger { background: rgba(255, 77, 77, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 77, 77, 0.2); padding: 10px 18px; border-radius: 10px; cursor: pointer; transition: 0.3s; }
        .btn-danger:hover { background: #ff4d4d; color: white; }

        .article-list { display: grid; gap: 12px; max-height: 820px; overflow-y: auto; }
        .article-item { border-radius: 16px; padding: 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
        .article-item.active { border-color: rgba(0, 240, 255, 0.18); background: rgba(0, 240, 255, 0.07); }
        .article-item__top { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 10px; }
        .article-title-link { color: var(--text-main); text-decoration: none; font-weight: 700; line-height: 1.4; }
        .article-title-link:hover { color: var(--accent-cyan); }
        .article-meta { display: flex; flex-wrap: wrap; gap: 8px; font-size: 12px; color: var(--text-secondary); margin-bottom: 10px; }
        .article-meta span { padding: 6px 10px; border-radius: 999px; background: rgba(255,255,255,0.04); }
        .article-excerpt { color: var(--text-secondary); font-size: 14px; line-height: 1.65; margin: 0 0 14px; }
        .article-actions { display: flex; gap: 10px; flex-wrap: wrap; }

        .badge { display: inline-flex; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .badge--published { background: rgba(0, 255, 100, 0.12); color: #00ff88; }
        .badge--draft { background: rgba(255, 188, 92, 0.14); color: #ffbc5c; }

        .category-list { display: grid; gap: 10px; }
        .category-row { display: flex; justify-content: space-between; gap: 14px; padding: 12px 14px; border-radius: 14px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
        .alert { padding: 15px 18px; border-radius: 14px; margin-bottom: 20px; font-weight: 600; }
        .alert--success { background: rgba(0, 255, 100, 0.1); color: #00ff64; border: 1px solid rgba(0, 255, 100, 0.2); }
        .alert--error { background: rgba(255, 77, 77, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 77, 77, 0.2); }

        @media (max-width: 1180px) {
            .top-grid, .content-grid { grid-template-columns: 1fr; }
            .metrics { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 900px) {
            .admin-layout { flex-direction: column; }
            .sidebar { position: static; width: 100%; height: auto; }
            .admin-main { margin-left: 0; padding: 22px; }
            .page-header { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <a href="/" class="sidebar__logo">SuperVisor<span>Admin</span></a>
            <nav class="sidebar__nav">
                <ul>
                    <li><a href="index.php" class="sidebar__link">Дашборд</a></li>
                    <li><a href="edit-page.php" class="sidebar__link">Редактор страниц</a></li>
                    <li><a href="manage-blog.php" class="sidebar__link active">Блог</a></li>
                    <li><a href="manage-knowledge.php" class="sidebar__link">База знаний</a></li>
                    <li><a href="seo.php" class="sidebar__link">SEO</a></li>
                    <li><a href="settings.php" class="sidebar__link">Настройки</a></li>
                    <li><a href="logout.php" class="sidebar__link" style="margin-top: 40px; color: #ff4d4d;">Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Управление блогом</h1>
                    <p class="page-subtitle">Раздел позволяет создавать и редактировать статьи, назначать категории, загружать материалы через CSV и быстро удалять отдельные записи или весь список целиком.</p>
                </div>
                <a href="manage-blog.php" class="btn btn--primary" style="text-decoration:none;">+ Новая статья</a>
            </div>

            <?php if ($message): ?><div class="alert alert--success"><?php echo e($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert--error"><?php echo e($error); ?></div><?php endif; ?>

            <section class="top-grid">
                <div class="card glass">
                    <h2 class="card-title">Сводка по блогу</h2>
                    <p class="card-subtitle">Короткий обзор текущего состояния контентного раздела и структуры категорий.</p>
                    <div class="metrics">
                        <div class="metric">
                            <div class="metric__label">Всего статей</div>
                            <div class="metric__value"><?php echo number_format(count($articles), 0, ',', ' '); ?></div>
                        </div>
                        <div class="metric">
                            <div class="metric__label">Опубликовано</div>
                            <div class="metric__value"><?php echo number_format(count(array_filter($articles, static fn($item) => !empty($item['is_published']))), 0, ',', ' '); ?></div>
                        </div>
                        <div class="metric">
                            <div class="metric__label">Категорий</div>
                            <div class="metric__value"><?php echo number_format(count($categorySummary), 0, ',', ' '); ?></div>
                        </div>
                    </div>
                </div>

                <div class="card glass">
                    <h2 class="card-title">Массовый импорт CSV</h2>
                    <p class="card-subtitle">Поддерживаются поля: <strong>title, slug, category, meta_title, meta_description, keywords, excerpt, image, updated_at, author, is_published, content_html</strong>.</p>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="import_csv">
                        <div class="form-group">
                            <label>CSV-файл</label>
                            <input type="file" name="csv_file" accept=".csv" class="form-input">
                        </div>
                        <div class="actions-row">
                            <button type="submit" class="btn btn--secondary">Импортировать статьи</button>
                        </div>
                    </form>
                    <div class="actions-row" style="margin-top:12px;">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="export_csv">
                            <button type="submit" class="btn btn--secondary">Выгрузить все статьи</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="download_template">
                            <button type="submit" class="btn btn--secondary" style="background:rgba(255,255,255,0.05);">Скачать шаблон</button>
                        </form>
                    </div>

                    <form method="POST" onsubmit="return confirm('Запустить автоматическую перелинковку между блогом и базой знаний?');" style="margin-top:12px;">
                        <input type="hidden" name="action" value="interlink_content">
                        <button type="submit" class="btn btn--secondary">Перелинковать</button>
                    </form>
                    <form method="POST" onsubmit="return confirm('Преобразовать найденные JPG/PNG изображения в WebP и обновить ссылки в статьях?');" style="margin-top:12px;">
                        <input type="hidden" name="action" value="convert_webp">
                        <button type="submit" class="btn btn--secondary">Преобразовать в WebP</button>
                    </form>
                    <form method="POST" onsubmit="return confirm('Удалить все статьи блога?');" style="margin-top:12px;">
                        <input type="hidden" name="action" value="delete_all">
                        <button type="submit" class="btn-danger">Удалить все статьи</button>
                    </form>
                </div>
            </section>

            <section class="content-grid">
                <div class="card glass">
                    <h2 class="card-title"><?php echo $currentArticle['slug'] ? 'Редактирование статьи' : 'Новая статья'; ?></h2>
                    <p class="card-subtitle">Форма сохраняет все SEO-поля, изображение, категорию и HTML-содержимое статьи.</p>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_article">
                        <input type="hidden" name="slug" value="<?php echo e($currentArticle['slug']); ?>">

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Заголовок статьи</label>
                                <input type="text" name="title" value="<?php echo e($currentArticle['title']); ?>" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label>Категория</label>
                                <select name="category" class="form-select">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo e($category); ?>" <?php echo (($currentArticle['category'] ?? '') === $category) ? 'selected' : ''; ?>><?php echo e($category); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Meta Title</label>
                                <input type="text" name="meta_title" value="<?php echo e($currentArticle['meta_title']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Автор</label>
                                <input type="text" name="author" value="<?php echo e($currentArticle['author']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Meta Description</label>
                                <input type="text" name="meta_description" value="<?php echo e($currentArticle['meta_description']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Дата обновления</label>
                                <input type="date" name="updated_at" value="<?php echo e($currentArticle['updated_at']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Ключевые слова</label>
                                <input type="text" name="keywords" value="<?php echo e($currentArticle['keywords']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Изображение</label>
                                <input type="text" name="image" value="<?php echo e($currentArticle['image']); ?>" class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Краткое описание</label>
                            <textarea name="excerpt" class="form-input" style="min-height:110px;"><?php echo e($currentArticle['excerpt']); ?></textarea>
                        </div>

                        <div class="checkbox-row">
                            <input type="checkbox" id="is_published" name="is_published" value="1" <?php echo !empty($currentArticle['is_published']) ? 'checked' : ''; ?>>
                            <label for="is_published" style="margin:0;">Опубликовать статью</label>
                        </div>

                        <div class="form-group">
                            <label>HTML-контент статьи</label>
                            <textarea name="content_html" class="form-textarea"><?php echo e($currentArticle['content_html']); ?></textarea>
                        </div>

                        <div class="actions-row">
                            <button type="submit" class="btn btn--primary">Сохранить статью</button>
                            <?php if (!empty($currentArticle['slug'])): ?>
                                <a href="<?php echo e(blogUrl($currentArticle['slug'])); ?>" target="_blank" rel="noopener" class="btn btn--secondary" style="text-decoration:none;">Открыть на сайте</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div>
                    <section class="card glass" style="margin-bottom:22px;">
                        <h2 class="card-title">Категории</h2>
                        <p class="card-subtitle">Сколько материалов относится к каждой категории.</p>
                        <div class="category-list">
                            <?php foreach ($categorySummary as $category => $count): ?>
                                <div class="category-row">
                                    <span><?php echo e($category); ?></span>
                                    <strong><?php echo number_format((int) $count, 0, ',', ' '); ?></strong>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($categorySummary)): ?>
                                <div class="category-row"><span>Категории пока отсутствуют</span><strong>0</strong></div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section class="card glass">
                        <h2 class="card-title">Список статей</h2>
                        <p class="card-subtitle">Быстрый переход к редактированию и удалению отдельных материалов.</p>
                        <div class="article-list">
                            <?php foreach ($articles as $article): ?>
                                <div class="article-item <?php echo (($article['slug'] ?? '') === ($currentArticle['slug'] ?? '')) ? 'active' : ''; ?>">
                                    <div class="article-item__top">
                                        <a class="article-title-link" href="?slug=<?php echo urlencode((string) ($article['slug'] ?? '')); ?>"><?php echo e((string) ($article['title'] ?? 'Без названия')); ?></a>
                                        <span class="badge <?php echo !empty($article['is_published']) ? 'badge--published' : 'badge--draft'; ?>"><?php echo !empty($article['is_published']) ? 'Опубликовано' : 'Черновик'; ?></span>
                                    </div>
                                    <div class="article-meta">
                                        <span><?php echo e((string) ($article['category'] ?? 'Блог')); ?></span>
                                        <span><?php echo e((string) ($article['updated_at'] ?? '')); ?></span>
                                        <span><?php echo e((string) ($article['author'] ?? 'Администратор')); ?></span>
                                    </div>
                                    <p class="article-excerpt"><?php echo e((string) ($article['excerpt'] ?? '')); ?></p>
                                    <div class="article-actions">
                                        <a href="?slug=<?php echo urlencode((string) ($article['slug'] ?? '')); ?>" class="btn btn--secondary" style="text-decoration:none;">Редактировать</a>
                                        <a href="<?php echo e(blogUrl((string) ($article['slug'] ?? ''))); ?>" target="_blank" rel="noopener" class="btn btn--secondary" style="text-decoration:none;">Открыть</a>
                                        <form method="POST" onsubmit="return confirm('Удалить статью «<?php echo e((string) ($article['title'] ?? '')); ?>»?');">
                                            <input type="hidden" name="action" value="delete_article">
                                            <input type="hidden" name="slug" value="<?php echo e((string) ($article['slug'] ?? '')); ?>">
                                            <button type="submit" class="btn-danger">Удалить</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($articles)): ?>
                                <div class="article-item"><p class="article-excerpt" style="margin:0;">Статьи пока не добавлены.</p></div>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
