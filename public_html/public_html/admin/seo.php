<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../functions.php';

$message = '';
$error = '';

$defaultSeo = [
    'home_title' => 'VPN — Безопасный VPN-сервис',
    'home_description' => 'Надёжный VPN для приватности и обхода блокировок.',
    'home_keywords' => 'vpn, proxy, wireguard, vless',
    'knowledge_title' => 'База знаний VPN',
    'knowledge_description' => 'Инструкции, решения проблем и материалы по настройке VPN.',
    'blog_title' => 'Блог VPN',
    'blog_description' => 'Статьи о VPN, безопасности и обходе блокировок.',
    'robots_txt' => "User-agent: *\nAllow: /\nSitemap: " . rtrim(baseSiteUrl(), '/') . "/sitemap.xml",
];

function buildSitemapXml(string $siteUrl): string {
    $siteUrl = rtrim($siteUrl, '/');
    $entries = [];

    $staticPages = [
        ['path' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['path' => '/pricing', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['path' => '/setup', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['path' => '/knowledge', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['path' => '/blog', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['path' => '/about', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['path' => '/contacts', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ];

    foreach ($staticPages as $page) {
        $entries[] = [
            'loc' => $siteUrl . $page['path'],
            'lastmod' => date('Y-m-d'),
            'changefreq' => $page['changefreq'],
            'priority' => $page['priority'],
        ];
    }

    foreach (getData('blog') as $article) {
        if (!empty($article['is_published']) && !empty($article['slug'])) {
            $entries[] = [
                'loc' => $siteUrl . blogUrl((string) $article['slug']),
                'lastmod' => normalizeLastmod((string) ($article['updated_at'] ?? '')),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }
    }

    foreach (getData('knowledge') as $article) {
        if (!empty($article['is_published']) && !empty($article['slug'])) {
            $entries[] = [
                'loc' => $siteUrl . knowledgeUrl((string) $article['slug']),
                'lastmod' => normalizeLastmod((string) ($article['updated_at'] ?? '')),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    foreach ($entries as $entry) {
        $xml .= '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . htmlspecialchars($entry['loc'], ENT_XML1) . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . htmlspecialchars($entry['lastmod'], ENT_XML1) . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>' . htmlspecialchars($entry['changefreq'], ENT_XML1) . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . htmlspecialchars($entry['priority'], ENT_XML1) . '</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;
    }
    $xml .= '</urlset>' . PHP_EOL;

    return $xml;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save_seo') {
        $config['seo'] = [
            'home_title' => trim((string) ($_POST['home_title'] ?? '')),
            'home_description' => trim((string) ($_POST['home_description'] ?? '')),
            'home_keywords' => trim((string) ($_POST['home_keywords'] ?? '')),
            'knowledge_title' => trim((string) ($_POST['knowledge_title'] ?? '')),
            'knowledge_description' => trim((string) ($_POST['knowledge_description'] ?? '')),
            'blog_title' => trim((string) ($_POST['blog_title'] ?? '')),
            'blog_description' => trim((string) ($_POST['blog_description'] ?? '')),
            'robots_txt' => trim((string) ($_POST['robots_txt'] ?? '')),
        ];
        saveData('config', $config);
        file_put_contents(__DIR__ . '/../robots.txt', $config['seo']['robots_txt']);
        $message = 'SEO-настройки сохранены.';
    }

    if ($action === 'generate_sitemap') {
        $siteUrl = trim((string) ($config['site_url'] ?? baseSiteUrl()));
        $xml = buildSitemapXml($siteUrl);
        file_put_contents(__DIR__ . '/../sitemap.xml', $xml);
        $message = 'sitemap.xml успешно перегенерирован и включает опубликованные статьи блога и базы знаний.';
    }

    if ($action === 'export_site_texts') {
        $texts = [];
        $filesToScan = ['index.php', 'pricing.php', 'setup.php', 'contacts.php', 'about.php', 'header.php', 'footer.php'];
        
        $currentSeo = array_merge($defaultSeo, $config['seo'] ?? []);
        
        // 1. Export SEO from config
        $texts[] = ['source' => 'config_seo', 'key' => 'home_title', 'text' => $currentSeo['home_title']];
        $texts[] = ['source' => 'config_seo', 'key' => 'home_description', 'text' => $currentSeo['home_description']];
        $texts[] = ['source' => 'config_seo', 'key' => 'home_keywords', 'text' => $currentSeo['home_keywords']];
        $texts[] = ['source' => 'config_seo', 'key' => 'knowledge_title', 'text' => $currentSeo['knowledge_title']];
        $texts[] = ['source' => 'config_seo', 'key' => 'knowledge_description', 'text' => $currentSeo['knowledge_description']];
        $texts[] = ['source' => 'config_seo', 'key' => 'blog_title', 'text' => $currentSeo['blog_title']];
        $texts[] = ['source' => 'config_seo', 'key' => 'blog_description', 'text' => $currentSeo['blog_description']];

        // 2. Export static texts from files
        foreach ($filesToScan as $file) {
            $path = __DIR__ . '/../' . $file;
            if (file_exists($path)) {
                $content = file_get_contents($path);
                // Match texts inside t('...')
                preg_match_all('/t\(\'(.*?)\'(?:,\s*\'(.*?)\')?\)/s', $content, $matches);
                
                foreach ($matches[1] as $i => $defaultText) {
                    $key = !empty($matches[2][$i]) ? $matches[2][$i] : md5(trim($defaultText));
                    $texts[] = [
                        'source' => $file,
                        'key' => $key,
                        'text' => isset($config['seo'][$key]) ? $config['seo'][$key] : $defaultText
                    ];
                }
            }
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=site_texts_export_' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['source', 'key', 'text'], ';');
        foreach ($texts as $row) {
            fputcsv($output, $row, ';');
        }
        fclose($output);
        exit;
    }

    if ($action === 'import_site_texts') {
        if (isset($_FILES['texts_csv']) && $_FILES['texts_csv']['error'] === UPLOAD_ERR_OK) {
            $rows = readCsvRows($_FILES['texts_csv']['tmp_name']);
            $updatedCount = 0;
            
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    if (isset($row['key'], $row['text'])) {
                        $config['seo'][$row['key']] = $row['text'];
                        $updatedCount++;
                    }
                }
                saveData('config', $config);
                $message = "Тексты сайта обновлены ($updatedCount записей).";
            } else {
                $error = "Файл пуст или имеет неверный формат.";
            }
        } else {
            $error = "Ошибка при загрузке файла.";
        }
    }
}

$seo = array_merge($defaultSeo, $config['seo'] ?? []);
$siteUrl = rtrim((string) ($config['site_url'] ?? baseSiteUrl()), '/');
$previewSitemap = is_file(__DIR__ . '/../sitemap.xml') ? (string) file_get_contents(__DIR__ . '/../sitemap.xml') : buildSitemapXml($siteUrl);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO | <?php echo e($config['site_name'] ?? 'VPN'); ?> Admin</title>
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
        .page-subtitle { margin: 0; color: var(--text-secondary); line-height: 1.7; max-width: 820px; }
        .grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(330px, 0.9fr); gap: 22px; }
        .card { background: var(--bg-secondary); border-radius: 20px; padding: 24px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 22px; }
        .card-title { margin: 0 0 14px; font-size: 20px; font-weight: 700; }
        .card-subtitle { margin: -4px 0 18px; color: var(--text-secondary); line-height: 1.7; font-size: 14px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-secondary); font-size: 14px; }
        .form-input, .form-textarea { width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px 14px; color: var(--text-main); font-family: inherit; box-sizing: border-box; }
        .form-textarea { min-height: 120px; }
        .code-box { width: 100%; min-height: 420px; background: #0d1117; color: #d0d7de; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 18px; box-sizing: border-box; font-family: 'Fira Code', monospace; font-size: 13px; line-height: 1.6; }
        .alert { padding: 15px 18px; border-radius: 14px; margin-bottom: 20px; font-weight: 600; background: rgba(0, 255, 100, 0.1); color: #00ff64; border: 1px solid rgba(0, 255, 100, 0.2); }
        .stats-list { display: grid; gap: 10px; }
        .stats-row { display: flex; justify-content: space-between; gap: 12px; padding: 12px 14px; border-radius: 14px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
        @media (max-width: 1180px) { .grid, .form-grid { grid-template-columns: 1fr; } }
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
            <a href="/" class="sidebar__logo"><?php echo e($config['site_name'] ?? 'VPN'); ?><span>Admin</span></a>
            <nav class="sidebar__nav">
                <ul>
                    <li><a href="index.php" class="sidebar__link">Дашборд</a></li>
                    <li><a href="edit-page.php" class="sidebar__link">Редактор страниц</a></li>
                    <li><a href="manage-blog.php" class="sidebar__link">Блог</a></li>
                    <li><a href="manage-knowledge.php" class="sidebar__link">База знаний</a></li>
                    <li><a href="seo.php" class="sidebar__link active">SEO</a></li>
                    <li><a href="settings.php" class="sidebar__link">Настройки</a></li>
                    <li><a href="logout.php" class="sidebar__link" style="margin-top: 40px; color: #ff4d4d;">Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="page-header">
                <div>
                    <h1 class="page-title">SEO</h1>
                    <p class="page-subtitle">Здесь можно управлять основными мета-тегами, robots.txt и вручную перегенерировать sitemap.xml, чтобы новые статьи из блога и базы знаний сразу попадали в карту сайта.</p>
                </div>
            </div>

            <?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert--error" style="background: rgba(255,77,77,0.1); color: #ff8d8d; border: 1px solid rgba(255,77,77,0.2); padding: 15px 18px; border-radius: 14px; margin-bottom: 20px; font-weight: 600;"><?php echo e($error); ?></div><?php endif; ?>

            <div class="grid">
                <div>
                    <form method="POST" class="card glass">
                        <input type="hidden" name="action" value="save_seo">
                        <h2 class="card-title">Мета-теги сайта</h2>
                        <p class="card-subtitle">Управление заголовками и описаниями для ключевых разделов сайта.</p>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Title главной</label>
                                <input type="text" name="home_title" value="<?php echo e($seo['home_title']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Keywords главной</label>
                                <input type="text" name="home_keywords" value="<?php echo e($seo['home_keywords']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Description главной</label>
                                <textarea name="home_description" class="form-textarea"><?php echo e($seo['home_description']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Title базы знаний</label>
                                <input type="text" name="knowledge_title" value="<?php echo e($seo['knowledge_title']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Description базы знаний</label>
                                <textarea name="knowledge_description" class="form-textarea"><?php echo e($seo['knowledge_description']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Title блога</label>
                                <input type="text" name="blog_title" value="<?php echo e($seo['blog_title']); ?>" class="form-input">
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Description блога</label>
                                <textarea name="blog_description" class="form-textarea"><?php echo e($seo['blog_description']); ?></textarea>
                            </div>
                        </div>

                        <h2 class="card-title" style="margin-top:30px;">robots.txt</h2>
                        <p class="card-subtitle">Содержимое файла сохраняется в конфиг и записывается в корневой robots.txt.</p>
                        <div class="form-group">
                            <textarea name="robots_txt" class="code-box"><?php echo e($seo['robots_txt']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn--primary">Сохранить SEO-настройки</button>
                    </form>
                </div>

                <div>
                    <section class="card glass">
                        <h2 class="card-title">Генерация sitemap.xml</h2>
                        <p class="card-subtitle">Карта сайта собирается по опубликованным страницам, статьям блога и статьям базы знаний.</p>
                        <div class="stats-list" style="margin-bottom:16px;">
                            <div class="stats-row"><span>Базовый домен</span><strong><?php echo e($siteUrl); ?></strong></div>
                            <div class="stats-row"><span>Путь к карте сайта</span><strong>/sitemap.xml</strong></div>
                            <div class="stats-row"><span>Статей в блоге</span><strong><?php echo number_format(count(array_filter(getData('blog'), static fn($item) => !empty($item['is_published']))), 0, ',', ' '); ?></strong></div>
                            <div class="stats-row"><span>Статей в базе знаний</span><strong><?php echo number_format(count(array_filter(getData('knowledge'), static fn($item) => !empty($item['is_published']))), 0, ',', ' '); ?></strong></div>
                        </div>
                        <form method="POST">
                            <button type="submit" name="action" value="generate_sitemap" class="btn btn--primary">Перегенерировать sitemap.xml</button>
                        </form>
                    </section>

                    <section class="card glass">
                        <h2 class="card-title">Перенос сайта (Тексты)</h2>
                        <p class="card-subtitle">Выгрузите все тексты для рерайта и загрузите обратно, чтобы избежать дублей при переносе на новый домен.</p>
                        <div style="display: flex; gap: 10px; flex-direction: column;">
                            <form method="POST">
                                <input type="hidden" name="action" value="export_site_texts">
                                <button type="submit" class="btn btn--secondary" style="width: 100%;">Выгрузить все тексты (CSV)</button>
                            </form>
                            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.05); margin: 10px 0;">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="import_site_texts">
                                <div class="form-group">
                                    <label>Загрузить обновленный CSV</label>
                                    <input type="file" name="texts_csv" accept=".csv" class="form-input">
                                </div>
                                <button type="submit" class="btn btn--primary" style="width: 100%;">Загрузить тексты обратно</button>
                            </form>
                        </div>
                    </section>

                    <section class="card glass">
                        <h2 class="card-title">Предпросмотр sitemap.xml</h2>
                        <p class="card-subtitle">Текущая версия файла карты сайта.</p>
                        <textarea class="code-box" readonly><?php echo e($previewSitemap); ?></textarea>
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
