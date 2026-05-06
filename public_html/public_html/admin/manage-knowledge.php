<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../functions.php';

$message = '';
$error = '';
$categories = ['Материалы базы знаний', 'Настройка', 'Диагностика', 'Оплата', 'Безопасность', 'Протоколы', 'FAQ'];
$items = getData('knowledge');

function knowledgeFindIndex(array $items, string $slug): ?int {
    foreach ($items as $index => $item) {
        if (($item['slug'] ?? '') === $slug) {
            return $index;
        }
    }
    return null;
}

function normalizeKnowledgeItem(array $input, ?array $existing = null): array {
    $title = trim((string) ($input['title'] ?? ''));
    $slug = trim((string) ($input['slug'] ?? ''));
    $slug = $slug !== '' ? slugify($slug) : slugify($title);
    $published = $input['is_published'] ?? false;

    return [
        'id' => (int) ($existing['id'] ?? $input['id'] ?? time()),
        'title' => $title,
        'slug' => $slug,
        'category' => trim((string) ($input['category'] ?? 'Материалы базы знаний')) ?: 'Материалы базы знаний',
        'meta_title' => trim((string) ($input['meta_title'] ?? $title)),
        'meta_description' => trim((string) ($input['meta_description'] ?? '')),
        'keywords' => trim((string) ($input['keywords'] ?? '')),
        'excerpt' => trim((string) ($input['excerpt'] ?? '')),
        'image' => trim((string) ($input['image'] ?? '')),
        'content_html' => trim((string) ($input['content_html'] ?? '')),
        'updated_at' => trim((string) ($input['updated_at'] ?? $existing['updated_at'] ?? date('Y-m-d'))),
        'is_published' => normalizeBoolean($published, true),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save') {
        $existingIndex = knowledgeFindIndex($items, trim((string) ($_POST['slug'] ?? '')));
        $existingItem = $existingIndex !== null ? $items[$existingIndex] : null;
        $payload = normalizeKnowledgeItem($_POST, $existingItem);

        if ($payload['title'] === '' || $payload['content_html'] === '') {
            $error = 'Заполните заголовок и содержание статьи.';
        } else {
            if ($existingIndex !== null) {
                $payload['id'] = $items[$existingIndex]['id'] ?? $payload['id'];
                $items[$existingIndex] = array_merge($items[$existingIndex], $payload);
            } else {
                $payload['id'] = count($items) ? (max(array_column($items, 'id')) + 1) : 1;
                $items[] = $payload;
            }

            usort($items, static fn($a, $b) => strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? '')));
            saveData('knowledge', $items);
            $message = 'Статья базы знаний сохранена.';
        }
    }

    if ($action === 'delete') {
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $items = array_values(array_filter($items, static fn($item) => ($item['slug'] ?? '') !== $slug));
        saveData('knowledge', $items);
        $message = 'Статья удалена.';
    }

    if ($action === 'delete_all') {
        saveData('knowledge', []);
        $items = [];
        $message = 'Все статьи базы знаний удалены.';
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
                    $payload = normalizeKnowledgeItem($rowData, null);
                    if ($payload['title'] === '' || $payload['content_html'] === '') {
                        continue;
                    }

                    $existingIndex = knowledgeFindIndex($items, $payload['slug']);
                    if ($existingIndex !== null) {
                        $payload['id'] = $items[$existingIndex]['id'] ?? $payload['id'];
                        $items[$existingIndex] = array_merge($items[$existingIndex], $payload);
                    } else {
                        $payload['id'] = count($items) ? (max(array_column($items, 'id')) + 1) : 1;
                        $items[] = $payload;
                    }
                    $imported++;
                }

                usort($items, static fn($a, $b) => strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? '')));
                saveData('knowledge', $items);
                $message = "Импортировано $imported статей.";
            }
        }
    }
}

$items = getData('knowledge');
if (!is_array($items)) {
    $items = [];
}
usort($items, static function ($a, $b) {
    return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
});

$categorySummary = [];
foreach ($items as $item) {
    $category = trim((string) ($item['category'] ?? 'Материалы базы знаний')) ?: 'Материалы базы знаний';
    $categorySummary[$category] = ($categorySummary[$category] ?? 0) + 1;
}
ksort($categorySummary);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>База знаний | SuperVisor Admin</title>
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

        .top-grid { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr); gap: 22px; margin-bottom: 22px; }
        .content-grid { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(330px, 0.85fr); gap: 22px; }
        .card { background: var(--bg-secondary); border-radius: 20px; padding: 24px; border: 1px solid rgba(255,255,255,0.05); }
        .card-title { margin: 0 0 14px; font-size: 20px; font-weight: 700; }
        .card-subtitle { margin: -4px 0 18px; color: var(--text-secondary); line-height: 1.7; font-size: 14px; }

        .metrics { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .metric { padding: 18px; border-radius: 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
        .metric__label { color: var(--text-secondary); font-size: 13px; margin-bottom: 10px; }
        .metric__value { font-size: 28px; font-weight: 800; color: var(--accent-cyan); }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-secondary); font-size: 14px; }
        .form-input, .form-select, .form-textarea { width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px 14px; color: var(--text-main); font-family: inherit; box-sizing: border-box; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .form-textarea { min-height: 380px; font-family: 'Fira Code', monospace; font-size: 14px; line-height: 1.6; }
        .actions-row { display: flex; gap: 12px; flex-wrap: wrap; }
        .checkbox-row { display: flex; align-items: center; gap: 10px; margin: 8px 0 22px; }

        .item-grid { display: grid; gap: 12px; max-height: 800px; overflow-y: auto; }
        .knowledge-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 18px; }
        .knowledge-card h3 { margin: 0 0 8px; font-size: 18px; }
        .knowledge-card p { font-size: 14px; color: var(--text-secondary); line-height: 1.65; margin: 0 0 14px; }
        .knowledge-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
        .knowledge-meta span { padding: 6px 10px; border-radius: 999px; background: rgba(255,255,255,0.04); color: var(--text-secondary); font-size: 12px; }

        .badge { display: inline-flex; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .badge--published { background: rgba(0,255,100,0.12); color: #00ff88; }
        .badge--draft { background: rgba(255,188,92,0.14); color: #ffbc5c; }
        .btn-danger { background: rgba(255, 77, 77, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 77, 77, 0.2); padding: 10px 18px; border-radius: 10px; cursor: pointer; transition: 0.3s; }
        .btn-danger:hover { background: #ff4d4d; color: white; }
        .category-list { display: grid; gap: 10px; }
        .category-row { display: flex; justify-content: space-between; gap: 14px; padding: 12px 14px; border-radius: 14px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }

        .modal { display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(3, 10, 20, 0.82); backdrop-filter: blur(8px); padding: 30px; overflow-y: auto; }
        .modal.is-open { display: block; }
        .modal-content { max-width: 1100px; margin: 20px auto; background: var(--bg-secondary); border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 28px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 20px; }
        .modal-close { background: rgba(255,255,255,0.06); color: var(--text-main); border: none; border-radius: 12px; width: 42px; height: 42px; cursor: pointer; font-size: 22px; }

        .alert { padding: 15px 18px; border-radius: 14px; margin-bottom: 20px; font-weight: 600; }
        .alert--success { background: rgba(0, 255, 100, 0.1); color: #00ff64; border: 1px solid rgba(0, 255, 100, 0.2); }
        .alert--error { background: rgba(255, 77, 77, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 77, 77, 0.2); }

        @media (max-width: 1180px) {
            .top-grid, .content-grid, .metrics, .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 900px) {
            .admin-layout { flex-direction: column; }
            .sidebar { position: static; width: 100%; height: auto; }
            .admin-main { margin-left: 0; padding: 22px; }
            .page-header { flex-direction: column; }
            .modal { padding: 16px; }
            .modal-content { padding: 20px; }
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
                    <li><a href="manage-blog.php" class="sidebar__link">Блог</a></li>
                    <li><a href="manage-knowledge.php" class="sidebar__link active">База знаний</a></li>
                    <li><a href="seo.php" class="sidebar__link">SEO</a></li>
                    <li><a href="settings.php" class="sidebar__link">Настройки</a></li>
                    <li><a href="logout.php" class="sidebar__link" style="margin-top: 40px; color: #ff4d4d;">Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="page-header">
                <div>
                    <h1 class="page-title">База знаний</h1>
                    <p class="page-subtitle">Раздел оформлен в более удобном формате: карточки материалов, массовый импорт из CSV, быстрое удаление и редактирование статьи во всплывающем окне без перехода на другую страницу.</p>
                </div>
                <button type="button" onclick="openModal()" class="btn btn--primary">+ Добавить статью</button>
            </div>

            <?php if ($message): ?><div class="alert alert--success"><?php echo e($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert--error"><?php echo e($error); ?></div><?php endif; ?>

            <section class="top-grid">
                <div class="card glass">
                    <h2 class="card-title">Сводка по базе знаний</h2>
                    <p class="card-subtitle">Краткие показатели по опубликованным материалам и структуре разделов.</p>
                    <div class="metrics">
                        <div class="metric">
                            <div class="metric__label">Всего статей</div>
                            <div class="metric__value"><?php echo number_format(count($items), 0, ',', ' '); ?></div>
                        </div>
                        <div class="metric">
                            <div class="metric__label">Опубликовано</div>
                            <div class="metric__value"><?php echo number_format(count(array_filter($items, static fn($item) => !empty($item['is_published']))), 0, ',', ' '); ?></div>
                        </div>
                        <div class="metric">
                            <div class="metric__label">Категорий</div>
                            <div class="metric__value"><?php echo number_format(count($categorySummary), 0, ',', ' '); ?></div>
                        </div>
                    </div>
                </div>

                <div class="card glass">
                    <h2 class="card-title">Массовая загрузка CSV</h2>
                    <p class="card-subtitle">Можно загрузить до <strong>5 статей за раз</strong>. Поддерживаются поля: <strong>title, slug, category, meta_title, meta_description, keywords, excerpt, image, updated_at, is_published, content_html</strong>.</p>
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
                    <form method="POST" onsubmit="return confirm('Удалить все статьи базы знаний?');" style="margin-top:12px;">
                        <input type="hidden" name="action" value="delete_all">
                        <button type="submit" class="btn-danger">Удалить все статьи</button>
                    </form>
                </div>
            </section>

            <section class="content-grid">
                <div class="card glass">
                    <h2 class="card-title">Список статей</h2>
                    <p class="card-subtitle">Нажмите «Редактировать», чтобы открыть статью во всплывающем окне и изменить все поля без перехода на отдельную страницу.</p>
                    <div class="item-grid">
                        <?php foreach ($items as $item): ?>
                            <article class="knowledge-card">
                                <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:10px;">
                                    <h3><?php echo e((string) ($item['title'] ?? 'Без названия')); ?></h3>
                                    <span class="badge <?php echo !empty($item['is_published']) ? 'badge--published' : 'badge--draft'; ?>"><?php echo !empty($item['is_published']) ? 'Опубликовано' : 'Черновик'; ?></span>
                                </div>
                                <div class="knowledge-meta">
                                    <span><?php echo e((string) (($item['category'] ?? '') ?: 'Материалы базы знаний')); ?></span>
                                    <span><?php echo e((string) ($item['updated_at'] ?? '')); ?></span>
                                    <span>#<?php echo e((string) ($item['slug'] ?? '')); ?></span>
                                </div>
                                <p><?php echo e((string) ($item['excerpt'] ?? '')); ?></p>
                                <div class="actions-row">
                                    <button type="button" class="btn btn--secondary" onclick='editItem(<?php echo json_encode($item, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>Редактировать</button>
                                    <a href="<?php echo e(knowledgeUrl((string) ($item['slug'] ?? ''))); ?>" target="_blank" rel="noopener" class="btn btn--secondary" style="text-decoration:none;">Открыть</a>
                                    <form method="POST" onsubmit="return confirm('Удалить статью «<?php echo e((string) ($item['title'] ?? '')); ?>»?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="slug" value="<?php echo e((string) ($item['slug'] ?? '')); ?>">
                                        <button type="submit" class="btn-danger">Удалить</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                            <article class="knowledge-card"><p style="margin:0;">Статьи базы знаний пока не добавлены.</p></article>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card glass">
                    <h2 class="card-title">Категории</h2>
                    <p class="card-subtitle">Распределение материалов по разделам базы знаний.</p>
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
                </div>
            </section>
        </main>
    </div>

    <div id="editModal" class="modal" aria-hidden="true">
        <div class="modal-content glass">
            <div class="modal-header">
                <div>
                    <h2 id="modalTitle" class="card-title" style="margin:0;">Новая статья</h2>
                    <p class="card-subtitle" style="margin:6px 0 0;">Заполните заголовок, SEO-поля, категорию и HTML-контент материала.</p>
                </div>
                <button type="button" class="modal-close" onclick="closeModal()">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="itemId" value="0">
                <input type="hidden" name="slug" id="itemSlug" value="">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Заголовок</label>
                        <input type="text" name="title" id="itemTitle" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Категория</label>
                        <select name="category" id="itemCategory" class="form-select">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" id="itemMetaTitle" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Дата обновления</label>
                        <input type="date" name="updated_at" id="itemUpdatedAt" class="form-input" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <input type="text" name="meta_description" id="itemMetaDescription" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Изображение</label>
                        <input type="text" name="image" id="itemImage" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label>Ключевые слова</label>
                    <input type="text" name="keywords" id="itemKeywords" class="form-input">
                </div>

                <div class="form-group">
                    <label>Краткое описание</label>
                    <textarea name="excerpt" id="itemExcerpt" class="form-input" style="min-height:100px;"></textarea>
                </div>

                <div class="checkbox-row">
                    <input type="checkbox" name="is_published" id="itemPublished" value="1" checked>
                    <label for="itemPublished" style="margin:0;">Опубликовать статью</label>
                </div>

                <div class="form-group">
                    <label>Контент (HTML)</label>
                    <textarea name="content_html" id="itemContent" class="form-textarea"></textarea>
                </div>

                <div class="actions-row">
                    <button type="submit" class="btn btn--primary">Сохранить</button>
                    <button type="button" onclick="closeModal()" class="btn btn--secondary">Отмена</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('editModal');

        function openModal() {
            document.getElementById('itemId').value = '0';
            document.getElementById('itemSlug').value = '';
            document.getElementById('itemTitle').value = '';
            document.getElementById('itemCategory').value = 'Материалы базы знаний';
            document.getElementById('itemMetaTitle').value = '';
            document.getElementById('itemMetaDescription').value = '';
            document.getElementById('itemKeywords').value = '';
            document.getElementById('itemExcerpt').value = '';
            document.getElementById('itemImage').value = '';
            document.getElementById('itemUpdatedAt').value = '<?php echo date('Y-m-d'); ?>';
            document.getElementById('itemContent').value = '';
            document.getElementById('itemPublished').checked = true;
            document.getElementById('modalTitle').innerText = 'Новая статья';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        }

        function editItem(item) {
            document.getElementById('itemId').value = item.id || 0;
            document.getElementById('itemSlug').value = item.slug || '';
            document.getElementById('itemTitle').value = item.title || '';
            document.getElementById('itemCategory').value = item.category || 'Материалы базы знаний';
            document.getElementById('itemMetaTitle').value = item.meta_title || '';
            document.getElementById('itemMetaDescription').value = item.meta_description || '';
            document.getElementById('itemKeywords').value = item.keywords || '';
            document.getElementById('itemExcerpt').value = item.excerpt || '';
            document.getElementById('itemImage').value = item.image || '';
            document.getElementById('itemUpdatedAt').value = item.updated_at || '<?php echo date('Y-m-d'); ?>';
            document.getElementById('itemContent').value = item.content_html || '';
            document.getElementById('itemPublished').checked = Boolean(item.is_published);
            document.getElementById('modalTitle').innerText = 'Редактировать статью';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        }

        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
