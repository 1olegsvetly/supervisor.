<?php

declare(strict_types=1);

function getJsonPath(string $file): string {
    return __DIR__ . '/data/' . $file . '.json';
}

function requestScheme(): string {
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        return strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https' ? 'https' : 'http';
    }

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return 'https';
    }

    if ((int) ($_SERVER['SERVER_PORT'] ?? 80) === 443) {
        return 'https';
    }

    return 'http';
}

function requestHost(): string {
    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost'));
    if ($host === '') {
        return 'localhost';
    }

    return preg_replace('/:\d+$/', '', $host) ?? 'localhost';
}

function requestBaseUrl(): string {
    return requestScheme() . '://' . requestHost();
}

function normalizeSiteUrl(?string $url): string {
    $value = trim((string) $url);
    if ($value === '') {
        return requestBaseUrl();
    }

    if (!preg_match('~^https?://~i', $value)) {
        $value = requestScheme() . '://' . ltrim($value, '/');
    }

    $parts = parse_url($value);
    if (!is_array($parts) || empty($parts['host'])) {
        return requestBaseUrl();
    }

    $scheme = strtolower((string) ($parts['scheme'] ?? requestScheme()));
    $host = strtolower((string) $parts['host']);
    $port = isset($parts['port']) ? ':' . (int) $parts['port'] : '';

    return rtrim($scheme . '://' . $host . $port, '/');
}

function extractHost(?string $url): string {
    $normalized = normalizeSiteUrl($url);
    return (string) (parse_url($normalized, PHP_URL_HOST) ?: requestHost());
}

function getConfig(): array {
    $defaults = [
        'site_name' => 'VPN Service',
        'site_url' => requestBaseUrl(),
        'tg_bot_link' => 'https://t.me/your_bot',
        'bot_username' => '@your_bot',
        'support_email' => 'support@' . requestHost(),
        'support_telegram' => '@support',
        'admin_login' => 'admin',
        'admin_password' => 'admin1',
        'colors' => [
            'bg_main' => '#0A0F1C',
            'bg_secondary' => '#111827',
            'accent_cyan' => '#00F0FF',
            'accent_purple' => '#7000FF',
            'text_main' => '#F8FAFC',
            'text_secondary' => '#94A3B8'
        ]
    ];

    $configPath = getJsonPath('config');
    if (!file_exists($configPath)) {
        return $defaults;
    }

    $config = json_decode((string) file_get_contents($configPath), true);
    if (!is_array($config)) {
        return $defaults;
    }

    $config = array_replace_recursive($defaults, $config);
    $config['site_name'] = trim((string) ($config['site_name'] ?? $defaults['site_name'])) ?: $defaults['site_name'];
    $config['site_url'] = normalizeSiteUrl((string) ($config['site_url'] ?? $defaults['site_url']));

    $supportEmail = trim((string) ($config['support_email'] ?? ''));
    if ($supportEmail === '') {
        $supportEmail = 'support@' . extractHost($config['site_url']);
    }
    $config['support_email'] = $supportEmail;

    $supportTelegram = trim((string) ($config['support_telegram'] ?? ''));
    $config['support_telegram'] = $supportTelegram !== '' ? $supportTelegram : '@support';

    return $config;
}

function getData(string $file): array {
    if ($file === 'blog') {
        return getJsonDataWithShards($file);
    }

    $path = getJsonPath($file);
    if (!file_exists($path)) {
        return [];
    }

    $data = json_decode((string) file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function getJsonDataWithShards(string $file): array {
    $paths = [];
    $mainPath = getJsonPath($file);
    if (is_file($mainPath)) {
        $paths[] = $mainPath;
    }

    $shards = glob(__DIR__ . '/data/' . $file . '-*.json') ?: [];
    natsort($shards);
    foreach ($shards as $path) {
        if (is_file($path)) {
            $paths[] = $path;
        }
    }

    $merged = [];
    $seen = [];
    foreach ($paths as $path) {
        $data = json_decode((string) file_get_contents($path), true);
        if (!is_array($data)) {
            continue;
        }
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }
            $key = (string) ($item['slug'] ?? ($item['id'] ?? md5(json_encode($item))));
            if ($key !== '' && isset($seen[$key])) {
                continue;
            }
            if ($key !== '') {
                $seen[$key] = true;
            }
            $merged[] = $item;
        }
    }

    return $merged;
}

function saveData(string $file, array $data): bool {
    if ($file === 'blog') {
        return saveJsonDataWithShards($file, $data);
    }

    $path = getJsonPath($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    return file_put_contents(
        $path,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    ) !== false;
}

function saveJsonDataWithShards(string $file, array $data, int $criticalBytes = 1800000): bool {
    $path = getJsonPath($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($encoded === false) {
        return false;
    }

    foreach (glob(__DIR__ . '/data/' . $file . '-*.json') ?: [] as $oldShard) {
        if (is_file($oldShard)) {
            @unlink($oldShard);
        }
    }

    if (strlen($encoded) <= $criticalBytes) {
        return file_put_contents($path, $encoded) !== false;
    }

    $chunks = [];
    $current = [];
    foreach ($data as $item) {
        $candidate = array_merge($current, [$item]);
        $candidateEncoded = json_encode($candidate, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($current !== [] && $candidateEncoded !== false && strlen($candidateEncoded) > $criticalBytes) {
            $chunks[] = $current;
            $current = [$item];
        } else {
            $current = $candidate;
        }
    }
    if ($current !== []) {
        $chunks[] = $current;
    }

    if ($chunks === []) {
        $chunks = [[]];
    }

    $ok = true;
    foreach ($chunks as $index => $chunk) {
        $target = $index === 0 ? $path : __DIR__ . '/data/' . $file . '-' . ($index + 1) . '.json';
        $chunkEncoded = json_encode($chunk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($chunkEncoded === false || file_put_contents($target, $chunkEncoded) === false) {
            $ok = false;
        }
    }

    return $ok;
}

function e(?string $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function t(string $defaultText, ?string $key = null): string {
    global $config;
    $key = $key ?? md5(trim($defaultText));
    if (isset($config['seo'][$key])) {
        return $config['seo'][$key];
    }
    return $defaultText;
}

function currentPath(): string {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return $path ?: '/';
}

function baseSiteUrl(): string {
    global $config;
    $siteUrl = rtrim((string) ($config['site_url'] ?? requestBaseUrl()), '/');
    return $siteUrl !== '' ? $siteUrl : requestBaseUrl();
}

function canonicalUrl(?string $path = null): string {
    $resolvedPath = $path ?? currentPath();
    if ($resolvedPath === '') {
        $resolvedPath = '/';
    }
    if ($resolvedPath !== '/' && str_ends_with($resolvedPath, '/')) {
        $resolvedPath = rtrim($resolvedPath, '/');
    }
    return baseSiteUrl() . $resolvedPath;
}

function isActiveNav(string $path): bool {
    $current = currentPath();
    if ($path === '/') {
        return $current === '/';
    }
    $aliases = [
        '/blog.php' => '/blog',
        '/knowledge.php' => '/knowledge',
    ];
    $paths = [$path];
    if (isset($aliases[$path])) {
        $paths[] = $aliases[$path];
    }
    foreach ($paths as $candidate) {
        if ($current === $candidate || str_starts_with($current, rtrim($candidate, '/') . '/')) {
            return true;
        }
    }
    return false;
}

function normalizeTelegramUsername(?string $username): string {
    $value = trim((string) $username);
    $value = preg_replace('/\s+/', '', $value) ?? '';
    $value = ltrim($value, '@');
    $value = preg_replace('/[^a-zA-Z0-9_]/', '', $value) ?? '';
    return $value;
}

function telegramBaseLink(): string {
    global $config;
    return (string) ($config['tg_bot_link'] ?? 'https://t.me/your_bot');
}

function buildTelegramUrl(array $params = []): string {
    $base = telegramBaseLink();
    if (!$params) {
        return $base;
    }
    $separator = str_contains($base, '?') ? '&' : '?';
    return $base . $separator . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}

function telegramDisplayName(): string {
    global $config;
    $configured = trim((string) ($config['bot_username'] ?? ''));
    if ($configured !== '') {
        return str_starts_with($configured, '@') ? $configured : '@' . $configured;
    }
    $path = parse_url(telegramBaseLink(), PHP_URL_PATH);
    $username = trim((string) $path, '/');
    if ($username === '') {
        return '@your_bot';
    }
    return str_starts_with($username, '@') ? $username : '@' . $username;
}

function slugify(string $text): string {
    $text = mb_strtolower(trim($text));
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''
    ];
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text) ?? '';
    $text = preg_replace('/-+/', '-', $text) ?? '';
    return trim($text, '-');
}

function normalizeBoolean(mixed $value, bool $default = false): bool {
    if (is_bool($value)) {
        return $value;
    }
    $str = strtolower(trim((string) $value));
    if (in_array($str, ['1', 'true', 'yes', 'on', 'да'], true)) {
        return true;
    }
    if (in_array($str, ['0', 'false', 'no', 'off', 'нет', ''], true)) {
        return false;
    }
    return $default;
}

function readCsvRows(string $filePath): array {
    if (!is_readable($filePath)) {
        return [];
    }
    $content = (string) file_get_contents($filePath);
    // Strip UTF-8 BOM if present
    if (str_starts_with($content, "\xEF\xBB\xBF")) {
        $content = substr($content, 3);
    }
    return parseCsvContent($content);
}

function parseCsvContent(string $content): array {
    $content = trim($content);
    if ($content === '') {
        return [];
    }
    $tmpHandle = fopen('php://temp', 'r+');
    if (!$tmpHandle) {
        return [];
    }
    fwrite($tmpHandle, $content);
    rewind($tmpHandle);
    $firstLine = fgets($tmpHandle);
    if ($firstLine === false) {
        fclose($tmpHandle);
        return [];
    }
    rewind($tmpHandle);
    $delimiter = detectCsvDelimiter($firstLine);
    $header = fgetcsv($tmpHandle, 0, $delimiter);
    if (!is_array($header)) {
        fclose($tmpHandle);
        return [];
    }
    $header = array_map(static fn($value) => trim(trim((string) $value), "\xEF\xBB\xBF"), $header);
    $rows = [];
    while (($row = fgetcsv($tmpHandle, 0, $delimiter)) !== false) {
        if (!$row || count(array_filter($row, static fn($value) => trim((string) $value) !== '')) === 0) {
            continue;
        }
        if (count($row) > count($header)) {
            $row = array_slice($row, 0, count($header));
        } else if (count($row) < count($header)) {
            $row = array_pad($row, count($header), '');
        }
        $rowData = @array_combine($header, $row);
        if ($rowData === false) {
            continue;
        }
        $rows[] = array_map(static fn($value) => trim((string) $value), $rowData);
    }
    fclose($tmpHandle);
    return $rows;
}

function detectCsvDelimiter(string $line): string {
    $delimiters = [',' => 0, ';' => 0, "\t" => 0, '|' => 0];
    foreach ($delimiters as $d => &$count) {
        $count = count(explode($d, $line));
    }
    arsort($delimiters);
    return (string) key($delimiters);
}

function renderDynamicContent(?string $content): string {
    global $config;
    $value = trim((string) $content);
    if ($value === '') {
        return '';
    }
    $replacements = [
        '<?php echo $config["site_name"]; ?>' => e((string) ($config['site_name'] ?? 'VPN Service')),
        '<?php echo $config[\'site_name\']; ?>' => e((string) ($config['site_name'] ?? 'VPN Service')),
        '<?php echo $config["support_email"]; ?>' => e((string) ($config['support_email'] ?? 'support@' . extractHost(baseSiteUrl()))),
        '<?php echo $config[\'support_email\']; ?>' => e((string) ($config['support_email'] ?? 'support@' . extractHost(baseSiteUrl()))),
        '<?php echo parse_url(baseSiteUrl(), PHP_URL_HOST); ?>' => e(extractHost(baseSiteUrl())),
        '<?php echo "api." . parse_url(baseSiteUrl(), PHP_URL_HOST); ?>' => e('api.' . extractHost(baseSiteUrl())),
    ];
    $value = strtr($value, $replacements);
    if (strpos($value, '<?php') !== false) {
        ob_start();
        try {
            eval('?>' . $value);
        } catch (Throwable $e) {
            ob_end_clean();
            return $value;
        }
        return addLazyLoadingToImages((string) ob_get_clean());
    }
    return addLazyLoadingToImages($value);
}

function addLazyLoadingToImages(string $html): string {
    if (stripos($html, '<img') === false) {
        return $html;
    }
    $html = preg_replace_callback('/<img\b[^>]*>/i', static function (array $matches): string {
        $tag = $matches[0];
        if (!preg_match('/\sloading\s*=/i', $tag)) {
            $tag = preg_replace('/<img\b/i', '<img loading="lazy"', $tag, 1) ?? $tag;
        }
        if (!preg_match('/\sdecoding\s*=/i', $tag)) {
            $tag = preg_replace('/<img\b/i', '<img decoding="async"', $tag, 1) ?? $tag;
        }
        return $tag;
    }, $html);
    return $html ?? '';
}

function buildMigrationSummary(array $oldConfig, array $newConfig): array {
    return [
        'previous_site_name' => trim((string) ($oldConfig['site_name'] ?? '')),
        'previous_site_url' => normalizeSiteUrl((string) ($oldConfig['site_url'] ?? requestBaseUrl())),
        'new_site_name' => trim((string) ($newConfig['site_name'] ?? '')),
        'new_site_url' => normalizeSiteUrl((string) ($newConfig['site_url'] ?? requestBaseUrl())),
        'migrated_at' => date('c'),
    ];
}

function resetSiteFootprints(): void {
    saveData('stats', ['visits' => 0, 'clicks_tg_direct' => 0, 'clicks_tg_test' => 0]);
    saveData('activity_log', []);
    $rootFiles = [__DIR__ . '/sitemap.xml', __DIR__ . '/robots.txt'];
    foreach ($rootFiles as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }
    $sessionDir = session_save_path() ?: sys_get_temp_dir();
    if ($sessionDir && is_dir($sessionDir)) {
        $files = glob($sessionDir . '/sess_*') ?: [];
        $currentSession = session_id();
        foreach ($files as $file) {
            if (is_file($file) && strpos($file, $currentSession) === false) {
                @unlink($file);
            }
        }
    }
}

function blogUrl(?string $slug = null, array $params = []): string {
    $path = '/blog';
    $slug = trim((string) $slug);
    if ($slug !== '') {
        $path .= '/' . rawurlencode($slug);
    }
    $params = array_filter($params, static fn($value) => $value !== null && $value !== '' && $value !== 1 && $value !== '1');
    return $params ? $path . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : $path;
}

function knowledgeUrl(?string $slug = null): string {
    $path = '/knowledge';
    $slug = trim((string) $slug);
    return $slug !== '' ? $path . '/' . rawurlencode($slug) : $path;
}

function normalizeLastmod(?string $date = null): string {
    $value = trim((string) $date);
    $timestamp = $value !== '' ? strtotime($value) : false;
    if ($timestamp === false || $timestamp > time()) {
        return date('Y-m-d');
    }
    return date('Y-m-d', $timestamp);
}

function plainTextFromHtml(?string $html): string {
    $text = strip_tags(renderDynamicContent((string) $html));
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text) ?? '';
    return trim($text);
}

function findRelatedItems(array $current, array $items, int $limit = 3): array {
    $currentSlug = (string) ($current['slug'] ?? '');
    $category = mb_strtolower(trim((string) ($current['category'] ?? '')));
    $keywords = array_filter(array_map('trim', explode(',', mb_strtolower((string) ($current['keywords'] ?? '')))));
    $scored = [];
    foreach ($items as $item) {
        if (empty($item['is_published']) || (string) ($item['slug'] ?? '') === $currentSlug) {
            continue;
        }
        $score = 0;
        if ($category !== '' && mb_strtolower(trim((string) ($item['category'] ?? ''))) === $category) {
            $score += 5;
        }
        $haystack = mb_strtolower((string) (($item['title'] ?? '') . ' ' . ($item['excerpt'] ?? '') . ' ' . ($item['keywords'] ?? '')));
        foreach ($keywords as $keyword) {
            if ($keyword !== '' && str_contains($haystack, $keyword)) {
                $score += 2;
            }
        }
        $score += strtotime((string) ($item['updated_at'] ?? '')) ?: 0;
        $scored[] = ['score' => $score, 'item' => $item];
    }
    usort($scored, static fn($a, $b) => $b['score'] <=> $a['score']);
    return array_map(static fn($row) => $row['item'], array_slice($scored, 0, $limit));
}

function interlinkContentHtml(string $content, array $targets, string $currentSlug, int $limit = 4): string {
    if (str_contains($content, 'internal-links-block')) {
        return $content;
    }
    $links = [];
    foreach ($targets as $target) {
        if (empty($target['is_published']) || empty($target['slug']) || (string) $target['slug'] === $currentSlug) {
            continue;
        }
        $title = trim(strip_tags((string) ($target['title'] ?? '')));
        $url = (string) ($target['_url'] ?? '#');
        if ($title === '' || $url === '#') {
            continue;
        }
        $links[] = '<li><a href="' . e($url) . '">' . e($title) . '</a></li>';
        if (count($links) >= $limit) {
            break;
        }
    }
    if (!$links) {
        return $content;
    }
    return rtrim($content) . "\n\n<section class=\"internal-links-block\"><h2>Читайте также</h2><ul>" . implode('', $links) . '</ul></section>';
}

function convertLocalImagesToWebp(string $baseDir): array {
    $allowed = ['jpg', 'jpeg', 'png'];
    $converted = 0;
    $skipped = 0;
    $errors = [];
    $paths = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile()) {
            continue;
        }
        $ext = strtolower($fileInfo->getExtension());
        if (!in_array($ext, $allowed, true)) {
            continue;
        }
        if (str_contains($fileInfo->getFilename(), '_original')) {
            $skipped++;
            continue;
        }
        $source = $fileInfo->getPathname();
        $target = preg_replace('/\.(jpe?g|png)$/i', '.webp', $source);
        if ($target === null || $target === $source) {
            $skipped++;
            continue;
        }
        if (is_file($target) && filemtime($target) >= filemtime($source)) {
            $skipped++;
            continue;
        }
        if (!function_exists('imagewebp')) {
            $errors[] = 'На сервере недоступна функция imagewebp().';
            break;
        }
        $image = $ext === 'png' ? @imagecreatefrompng($source) : @imagecreatefromjpeg($source);
        if (!$image) {
            $errors[] = 'Не удалось открыть: ' . str_replace($baseDir, '', $source);
            continue;
        }
        if (@imagewebp($image, $target, 82)) {
            $converted++;
            $paths['/' . ltrim(str_replace($baseDir, '', $source), '/')] = '/' . ltrim(str_replace($baseDir, '', $target), '/');
        } else {
            $errors[] = 'Не удалось сохранить WebP: ' . str_replace($baseDir, '', $target);
        }
        imagedestroy($image);
    }
    return ['converted' => $converted, 'skipped' => $skipped, 'errors' => $errors, 'paths' => $paths];
}

function replaceImagePathsInContent(string $content, array $paths): string {
    return $paths ? strtr($content, $paths) : $content;
}

function generateArticleSchema(array $article, string $type = 'BlogPosting'): string {
    global $config;
    $siteUrl = baseSiteUrl();
    $url = ($type === 'BlogPosting') ? $siteUrl . blogUrl($article['slug'] ?? '') : $siteUrl . knowledgeUrl($article['slug'] ?? '');

    // Description: prefer meta_description, then excerpt, then first 160 chars of content
    $description = trim((string) ($article['meta_description'] ?? ''));
    if ($description === '') {
        $description = trim((string) ($article['excerpt'] ?? ''));
    }
    if ($description === '') {
        $description = mb_substr(strip_tags((string) ($article['content_html'] ?? '')), 0, 160);
    }

    // datePublished: prefer created_at, fall back to updated_at, then today
    $datePublished = '';
    if (!empty($article['created_at'])) {
        $ts = strtotime((string) $article['created_at']);
        $datePublished = $ts ? date('c', $ts) : '';
    }
    if ($datePublished === '' && !empty($article['updated_at'])) {
        $ts = strtotime((string) $article['updated_at']);
        $datePublished = $ts ? date('c', $ts) : '';
    }
    if ($datePublished === '') {
        $datePublished = date('c');
    }

    // dateModified
    $dateModified = '';
    if (!empty($article['updated_at'])) {
        $ts = strtotime((string) $article['updated_at']);
        $dateModified = $ts ? date('c', $ts) : '';
    }
    if ($dateModified === '') {
        $dateModified = $datePublished;
    }

    // Author: use article author field (Person), fall back to site Organization
    $authorName = trim((string) ($article['author'] ?? ''));
    if ($authorName !== '' && $authorName !== 'Администратор') {
        $author = [
            '@type' => 'Person',
            'name'  => $authorName,
            'url'   => $siteUrl . '/'
        ];
    } else {
        $author = [
            '@type' => 'Organization',
            'name'  => $config['site_name'] ?? 'VPN',
            'url'   => $siteUrl . '/'
        ];
    }

    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => $type,
        'headline'         => trim((string) ($article['title'] ?? '')),
        'description'      => $description,
        'author'           => $author,
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => $config['site_name'] ?? 'VPN',
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => $siteUrl . '/img/hero-globe.png'
            ]
        ],
        'datePublished'    => $datePublished,
        'dateModified'     => $dateModified,
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
        'inLanguage'       => 'ru',
    ];

    if (!empty($article['image'])) {
        $imgUrl = (string) $article['image'];
        // Make absolute if relative
        if (!str_starts_with($imgUrl, 'http')) {
            $imgUrl = $siteUrl . '/' . ltrim($imgUrl, '/');
        }
        $schema['image'] = [
            '@type' => 'ImageObject',
            'url'   => $imgUrl
        ];
    }

    if (!empty($article['keywords'])) {
        $schema['keywords'] = trim((string) $article['keywords']);
    }

    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>' . PHP_EOL;
}

$config = getConfig();
?>
