<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../functions.php';

$rootDir = realpath(__DIR__ . '/..');
$message = '';
$error = '';

$allowedExtensions = ['php', 'css', 'js', 'json', 'txt', 'md'];
$excludedTopDirs = ['img', 'data'];
$importantFiles = [
    'index.php',
    'pricing.php',
    'setup.php',
    'knowledge.php',
    'blog.php',
    'contacts.php',
    'about.php',
    'header.php',
    'footer.php',
    'css/style.css',
    'js/main.js',
];

$displayNames = [
    'index.php' => 'Главная страница',
    'pricing.php' => 'Тарифы',
    'setup.php' => 'Инструкции',
    'knowledge.php' => 'База знаний',
    'blog.php' => 'Блог',
    'contacts.php' => 'Поддержка',
    'about.php' => 'О сервисе',
    'header.php' => 'Шапка сайта',
    'footer.php' => 'Подвал сайта',
    'css/style.css' => 'Основные стили',
    'js/main.js' => 'Основной JavaScript',
    'robots.txt' => 'robots.txt',
    'sitemap.xml' => 'sitemap.xml',
];

$editableFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }

    $fullPath = $file->getPathname();
    $relativePath = ltrim(str_replace($rootDir, '', $fullPath), DIRECTORY_SEPARATOR);
    $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
    $topDir = explode('/', $relativePath)[0] ?? '';

    if (in_array($topDir, $excludedTopDirs, true)) {
        continue;
    }

    $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
    if ($extension !== '' && !in_array($extension, $allowedExtensions, true)) {
        continue;
    }

    if (str_starts_with($relativePath, 'admin/') && !in_array($relativePath, ['admin/login.php', 'admin/logout.php'], true)) {
        $displayNames[$relativePath] = 'Админка: ' . basename($relativePath);
    }

    $editableFiles[$relativePath] = [
        'path' => $relativePath,
        'name' => $displayNames[$relativePath] ?? $relativePath,
        'extension' => $extension ?: 'file',
        'size' => $file->getSize(),
    ];
}

uksort($editableFiles, static function ($a, $b) use ($importantFiles) {
    $aPriority = array_search($a, $importantFiles, true);
    $bPriority = array_search($b, $importantFiles, true);
    $aWeight = $aPriority === false ? 999 : $aPriority;
    $bWeight = $bPriority === false ? 999 : $bPriority;

    if ($aWeight === $bWeight) {
        return strcmp($a, $b);
    }

    return $aWeight <=> $bWeight;
});

$currentPage = trim((string) ($_GET['page'] ?? 'index.php'));
if (!isset($editableFiles[$currentPage])) {
    $currentPage = array_key_first($editableFiles) ?: 'index.php';
}

$currentFilePath = $rootDir . '/' . $currentPage;
$content = is_file($currentFilePath) ? (string) file_get_contents($currentFilePath) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['page'], $_POST['content'])) {
    $submittedPage = trim((string) $_POST['page']);

    if (!isset($editableFiles[$submittedPage])) {
        $error = 'Выбранный файл недоступен для редактирования.';
    } else {
        $savePath = $rootDir . '/' . $submittedPage;
        $saveDir = dirname($savePath);

        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0775, true);
        }

        $newContent = (string) $_POST['content'];
        if (file_put_contents($savePath, $newContent) !== false) {
            $message = 'Файл успешно сохранён.';
            $currentPage = $submittedPage;
            $currentFilePath = $savePath;
            $content = $newContent;
        } else {
            $error = 'Не удалось сохранить файл. Проверьте права доступа.';
        }
    }
}

$currentMeta = $editableFiles[$currentPage] ?? [
    'path' => $currentPage,
    'name' => $currentPage,
    'extension' => strtolower(pathinfo($currentPage, PATHINFO_EXTENSION)),
    'size' => strlen($content),
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактор страниц | SuperVisor Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        :root {
            --sidebar-width: 260px;
        }

        body {
            background: var(--bg-main);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            margin: 0;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            border-right: 1px solid rgba(255,255,255,0.05);
            position: fixed;
            height: 100vh;
            padding: 30px 20px;
            box-sizing: border-box;
        }

        .sidebar__logo {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 40px;
            display: block;
            color: var(--text-main);
            text-decoration: none;
        }

        .sidebar__logo span {
            color: var(--accent-cyan);
        }

        .sidebar__nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar__nav li {
            margin-bottom: 8px;
        }

        .sidebar__link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
        }

        .sidebar__link:hover,
        .sidebar__link.active {
            background: rgba(0, 240, 255, 0.1);
            color: var(--accent-cyan);
        }

        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 36px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 28px;
        }

        .page-title {
            font-size: 30px;
            font-weight: 800;
            margin: 0 0 8px;
        }

        .page-subtitle {
            margin: 0;
            color: var(--text-secondary);
            line-height: 1.7;
            max-width: 760px;
        }

        .editor-layout {
            display: grid;
            grid-template-columns: minmax(280px, 320px) minmax(0, 1fr);
            gap: 22px;
        }

        .card {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .card-title {
            margin: 0 0 16px;
            font-size: 20px;
            font-weight: 700;
        }

        .card-subtitle {
            margin: -4px 0 18px;
            color: var(--text-secondary);
            line-height: 1.7;
            font-size: 14px;
        }

        .file-list {
            display: grid;
            gap: 10px;
            max-height: calc(100vh - 240px);
            overflow: auto;
            padding-right: 6px;
        }

        .file-link {
            display: block;
            padding: 12px 14px;
            border-radius: 14px;
            text-decoration: none;
            color: var(--text-secondary);
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            transition: 0.25s ease;
        }

        .file-link:hover,
        .file-link.active {
            color: var(--text-main);
            border-color: rgba(0, 240, 255, 0.18);
            background: rgba(0, 240, 255, 0.08);
        }

        .file-link small {
            display: block;
            margin-top: 6px;
            color: var(--text-secondary);
        }

        .editor-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        .editor-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .editor-meta span {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            color: var(--text-secondary);
            font-size: 13px;
        }

        .code-editor {
            width: 100%;
            min-height: 72vh;
            background: #0d1117;
            color: #d0d7de;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 20px;
            font-family: 'Fira Code', 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.65;
            resize: vertical;
            outline: none;
            box-sizing: border-box;
            tab-size: 4;
        }

        .alert {
            padding: 15px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .alert--success {
            background: rgba(0, 255, 100, 0.1);
            color: #00ff64;
            border: 1px solid rgba(0, 255, 100, 0.2);
        }

        .alert--error {
            background: rgba(255, 77, 77, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(255, 77, 77, 0.2);
        }

        @media (max-width: 1100px) {
            .editor-layout {
                grid-template-columns: 1fr;
            }

            .file-list {
                max-height: none;
            }
        }

        @media (max-width: 900px) {
            .admin-layout {
                flex-direction: column;
            }

            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .admin-main {
                margin-left: 0;
                padding: 22px;
            }

            .page-header {
                flex-direction: column;
            }
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
                    <li><a href="edit-page.php" class="sidebar__link active">Редактор страниц</a></li>
                    <li><a href="manage-blog.php" class="sidebar__link">Блог</a></li>
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
                    <h1 class="page-title">Редактор страниц</h1>
                    <p class="page-subtitle">Теперь здесь доступно реальное редактирование файлов сайта: публичные страницы, шаблоны, стили, скрипты и служебные файлы. Изменения сохраняются сразу в рабочий проект.</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert--success"><?php echo e($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert--error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <div class="editor-layout">
                <section class="card glass">
                    <h2 class="card-title">Файлы проекта</h2>
                    <p class="card-subtitle">Список формируется автоматически по реальным файлам проекта, а не по имитации страниц.</p>
                    <div class="file-list">
                        <?php foreach ($editableFiles as $file): ?>
                            <a href="?page=<?php echo urlencode($file['path']); ?>" class="file-link <?php echo $currentPage === $file['path'] ? 'active' : ''; ?>">
                                <strong><?php echo e($file['name']); ?></strong>
                                <small><?php echo e($file['path']); ?> · <?php echo e(strtoupper((string) $file['extension'])); ?> · <?php echo number_format((int) $file['size'], 0, ',', ' '); ?> байт</small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="card glass">
                    <form method="POST">
                        <input type="hidden" name="page" value="<?php echo e($currentPage); ?>">
                        <div class="editor-toolbar">
                            <div>
                                <h2 class="card-title" style="margin-bottom: 8px;"><?php echo e($currentMeta['name']); ?></h2>
                                <div class="editor-meta">
                                    <span>Файл: <?php echo e($currentMeta['path']); ?></span>
                                    <span>Тип: <?php echo e(strtoupper((string) $currentMeta['extension'])); ?></span>
                                    <span>Размер: <?php echo number_format((int) $currentMeta['size'], 0, ',', ' '); ?> байт</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary">Сохранить изменения</button>
                        </div>
                        <textarea name="content" class="code-editor" spellcheck="false"><?php echo e($content); ?></textarea>
                    </form>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
