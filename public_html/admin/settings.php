<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../functions.php';

$message = '';
$error = '';
$migrationInfo = $config['migration'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'clear_cache') {
        resetSiteFootprints();
        $message = 'Кэш, статистика, служебная история и карта сайта очищены. Следы старого домена удалены, сайт готов к индексации как новый проект.';
    }

    if ($action === 'save_settings' || $action === 'migrate') {
        $oldConfig = $config;

        $postedSiteName = trim((string) ($_POST['site_name'] ?? ($config['site_name'] ?? '')));
        $postedSiteUrl = normalizeSiteUrl((string) ($_POST['site_url'] ?? ($config['site_url'] ?? '')));
        $config['site_name'] = $postedSiteName !== '' ? $postedSiteName : ($config['site_name'] ?? 'VPN Service');
        $config['site_url'] = $postedSiteUrl;
        $config['tg_bot_link'] = trim((string) ($_POST['tg_bot_link'] ?? ($config['tg_bot_link'] ?? '')));
        $config['bot_username'] = trim((string) ($_POST['bot_username'] ?? ($config['bot_username'] ?? '')));

        $postedSupportEmail = trim((string) ($_POST['support_email'] ?? ($config['support_email'] ?? '')));
        $oldHost = extractHost((string) ($oldConfig['site_url'] ?? requestBaseUrl()));
        $newHost = extractHost($postedSiteUrl);
        if ($action === 'migrate' && ($postedSupportEmail === '' || str_ends_with(strtolower($postedSupportEmail), '@' . strtolower($oldHost)))) {
            $postedSupportEmail = 'support@' . $newHost;
        }
        $config['support_email'] = $postedSupportEmail !== '' ? $postedSupportEmail : ('support@' . $newHost);
        $config['support_telegram'] = trim((string) ($_POST['support_telegram'] ?? ($config['support_telegram'] ?? '')));
        $config['analytics_code'] = $_POST['analytics_code'] ?? ($config['analytics_code'] ?? '');

        if (isset($_POST['bg_main'])) {
            $config['colors'] = [
                'bg_main' => trim((string) ($_POST['bg_main'] ?? '#0A0F1C')),
                'bg_secondary' => trim((string) ($_POST['bg_secondary'] ?? '#111827')),
                'accent_cyan' => trim((string) ($_POST['accent_cyan'] ?? '#00F0FF')),
                'accent_purple' => trim((string) ($_POST['accent_purple'] ?? '#7000FF')),
                'text_main' => trim((string) ($_POST['text_main'] ?? '#F8FAFC')),
                'text_secondary' => trim((string) ($_POST['text_secondary'] ?? '#94A3B8')),
            ];
        }

        if (!empty($_POST['admin_login'])) {
            $config['admin_login'] = trim((string) $_POST['admin_login']);
        }

        if (!empty($_POST['admin_password'])) {
            $config['admin_password'] = trim((string) $_POST['admin_password']);
        }

        if ($action === 'migrate') {
            $config['migration'] = buildMigrationSummary($oldConfig, $config);
            resetSiteFootprints();
            $message = 'Переезд настроен: новое название и домен применены по сайту, служебные следы старого домена очищены. При необходимости обновите robots.txt и sitemap в разделе SEO.';
        } else {
            $message = 'Настройки сайта сохранены.';
        }

        saveData('config', $config);
        $migrationInfo = $config['migration'] ?? null;
    }
}

$colors = $config['colors'] ?? [
    'bg_main' => '#0A0F1C',
    'bg_secondary' => '#111827',
    'accent_cyan' => '#00F0FF',
    'accent_purple' => '#7000FF',
    'text_main' => '#F8FAFC',
    'text_secondary' => '#94A3B8',
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки и Переезд | <?php echo e($config['site_name'] ?? 'VPN'); ?> Admin</title>
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
        .page-subtitle { margin: 0; color: var(--text-secondary); line-height: 1.7; max-width: 860px; }
        .grid { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr); gap: 22px; }
        .card { background: var(--bg-secondary); border-radius: 20px; padding: 24px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 22px; }
        .card--migration { border: 1px solid rgba(112, 0, 255, 0.4); background: linear-gradient(145deg, var(--bg-secondary), rgba(112, 0, 255, 0.06)); box-shadow: 0 18px 44px rgba(112, 0, 255, 0.08); }
        .card-title { margin: 0 0 14px; font-size: 20px; font-weight: 700; }
        .card-subtitle { margin: -4px 0 18px; color: var(--text-secondary); line-height: 1.7; font-size: 14px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-secondary); font-size: 14px; }
        .form-input { width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px 14px; color: var(--text-main); font-family: inherit; box-sizing: border-box; }
        .alert, .alert--error { padding: 15px 18px; border-radius: 14px; margin-bottom: 20px; font-weight: 600; }
        .alert { background: rgba(0, 255, 100, 0.1); color: #00ff64; border: 1px solid rgba(0, 255, 100, 0.2); }
        .alert--error { background: rgba(255,77,77,0.1); color: #ff8d8d; border: 1px solid rgba(255,77,77,0.2); }
        .btn-danger { background: rgba(255, 77, 77, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 77, 77, 0.2); padding: 12px 18px; border-radius: 12px; cursor: pointer; transition: 0.3s; font-weight: 700; }
        .btn-danger:hover { background: #ff4d4d; color: white; }
        .btn--migration { background: linear-gradient(135deg, #7000FF, #9458ff); color: white; border: none; padding: 12px 24px; border-radius: 12px; cursor: pointer; font-weight: 700; transition: 0.3s; }
        .btn--migration:hover { opacity: 0.94; transform: translateY(-1px); }
        .migration-badges { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }
        .migration-badge { padding: 8px 12px; border-radius: 999px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); color: var(--text-secondary); font-size: 12px; }
        .migration-checklist { margin: 14px 0 0; padding-left: 18px; color: var(--text-secondary); line-height: 1.7; }
        .status-list { display: grid; gap: 10px; font-size: 14px; }
        .status-row { display:flex; justify-content:space-between; gap:16px; }
        @media (max-width: 1180px) { .grid, .form-grid { grid-template-columns: 1fr; } }
        @media (max-width: 900px) {
            .admin-layout { flex-direction: column; }
            .sidebar { position: static; width: 100%; height: auto; }
            .admin-main { margin-left: 0; padding: 22px; }
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
                    <li><a href="seo.php" class="sidebar__link">SEO</a></li>
                    <li><a href="settings.php" class="sidebar__link active">Настройки</a></li>
                    <li><a href="logout.php" class="sidebar__link" style="margin-top: 40px; color: #ff4d4d;">Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Настройки и Переезд</h1>
                    <p class="page-subtitle">Управляйте названием сайта, доменом, контактами и технической очисткой. Блок переезда обновляет ключевые доменные параметры по всему сайту, а служебная очистка убирает артефакты предыдущего запуска.</p>
                </div>
            </div>

            <?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert--error"><?php echo e($error); ?></div><?php endif; ?>

            <div class="grid">
                <div>
                    <section class="card card--migration glass">
                        <h2 class="card-title" style="color:#b998ff;">Переезд</h2>
                        <p class="card-subtitle">Используйте этот блок при переносе сайта на новый домен. Поля <strong>Название сайта</strong> и <strong>Домен сайта</strong> становятся основными значениями для всего проекта: метатегов, canonical, Open Graph, sitemap и служебных ссылок.</p>
                        <div class="migration-badges">
                            <span class="migration-badge">Текущий домен: <?php echo e(extractHost($config['site_url'] ?? requestBaseUrl())); ?></span>
                            <span class="migration-badge">Название: <?php echo e($config['site_name'] ?? 'VPN Service'); ?></span>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="migrate">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Название сайта</label>
                                    <input type="text" name="site_name" value="<?php echo e($config['site_name'] ?? ''); ?>" class="form-input" placeholder="Например: Nova VPN">
                                </div>
                                <div class="form-group">
                                    <label>Домен сайта</label>
                                    <input type="text" name="site_url" value="<?php echo e($config['site_url'] ?? ''); ?>" class="form-input" placeholder="https://new-domain.com">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Email поддержки</label>
                                    <input type="text" name="support_email" value="<?php echo e($config['support_email'] ?? ''); ?>" class="form-input" placeholder="support@new-domain.com">
                                </div>
                                <div class="form-group">
                                    <label>Telegram поддержки</label>
                                    <input type="text" name="support_telegram" value="<?php echo e($config['support_telegram'] ?? ''); ?>" class="form-input" placeholder="@support">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label>Коды аналитики (Google Analytics, Yandex Metrica и др.)</label>
                                <textarea name="analytics_code" class="form-input" style="min-height: 100px; font-family: monospace;"><?php echo e($config['analytics_code'] ?? ''); ?></textarea>
                                <p style="font-size: 12px; color: var(--text-secondary); margin-top: 5px;">Код будет вставлен перед закрывающим тегом &lt;/body&gt;.</p>
                            </div>
                            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-top:10px;">
                                <button type="submit" class="btn--migration">Применить переезд</button>
                                <span style="font-size:12px; color:var(--text-secondary);">После применения служебный кэш, статистика и sitemap очищаются автоматически.</span>
                            </div>
                        </form>
                        <ul class="migration-checklist">
                            <li>обновляется домен сайта для canonical, Open Graph и структурированных данных;</li>
                            <li>обновляется основное название проекта для интерфейса и SEO;</li>
                            <li>по умолчанию подставляется новый email поддержки на новом домене;</li>
                            <li>сбрасываются статистика, логи и старая карта сайта.</li>
                        </ul>
                    </section>

                    <form method="POST" class="card glass">
                        <input type="hidden" name="action" value="save_settings">
                        <h2 class="card-title">Общие настройки</h2>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Название сайта</label>
                                <input type="text" name="site_name" value="<?php echo e($config['site_name'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Домен сайта</label>
                                <input type="text" name="site_url" value="<?php echo e($config['site_url'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Ссылка на Telegram-бота</label>
                                <input type="text" name="tg_bot_link" value="<?php echo e($config['tg_bot_link'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Username бота</label>
                                <input type="text" name="bot_username" value="<?php echo e($config['bot_username'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Email поддержки</label>
                                <input type="text" name="support_email" value="<?php echo e($config['support_email'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Telegram поддержки</label>
                                <input type="text" name="support_telegram" value="<?php echo e($config['support_telegram'] ?? ''); ?>" class="form-input">
                            </div>
                        </div>

                        <h2 class="card-title" style="margin-top:30px;">Цвета интерфейса</h2>
                        <div class="form-grid">
                            <div class="form-group"><label>Фон основной</label><input type="text" name="bg_main" value="<?php echo e($colors['bg_main']); ?>" class="form-input"></div>
                            <div class="form-group"><label>Фон вторичный</label><input type="text" name="bg_secondary" value="<?php echo e($colors['bg_secondary']); ?>" class="form-input"></div>
                            <div class="form-group"><label>Акцент Cyan</label><input type="text" name="accent_cyan" value="<?php echo e($colors['accent_cyan']); ?>" class="form-input"></div>
                            <div class="form-group"><label>Акцент Purple</label><input type="text" name="accent_purple" value="<?php echo e($colors['accent_purple']); ?>" class="form-input"></div>
                            <div class="form-group"><label>Текст основной</label><input type="text" name="text_main" value="<?php echo e($colors['text_main']); ?>" class="form-input"></div>
                            <div class="form-group"><label>Текст вторичный</label><input type="text" name="text_secondary" value="<?php echo e($colors['text_secondary']); ?>" class="form-input"></div>
                        </div>

                        <h2 class="card-title" style="margin-top:30px;">Доступ</h2>
                        <div class="form-grid">
                            <div class="form-group"><label>Логин админа</label><input type="text" name="admin_login" value="<?php echo e($config['admin_login'] ?? ''); ?>" class="form-input"></div>
                            <div class="form-group"><label>Новый пароль</label><input type="password" name="admin_password" class="form-input" placeholder="Оставьте пустым"></div>
                        </div>

                        <button type="submit" class="btn btn--primary">Сохранить все настройки</button>
                    </form>
                </div>

                <div>
                    <section class="card glass">
                        <h2 class="card-title">Очистить кеш</h2>
                        <p class="card-subtitle">Кнопка удаляет статистику, логи активности, служебные сессии и старую карту сайта. Используйте после переноса, если хотите убрать остаточные следы старого домена и начать индексацию с чистого состояния.</p>
                        <form method="POST" onsubmit="return confirm('Это действие очистит статистику, историю и служебные следы старого домена. Продолжить?');">
                            <input type="hidden" name="action" value="clear_cache">
                            <button type="submit" class="btn-danger" style="width:100%;">Очистить кеш</button>
                        </form>
                    </section>

                    <section class="card glass">
                        <h2 class="card-title">Статус системы</h2>
                        <div class="status-list">
                            <div class="status-row"><span>Версия PHP:</span><strong><?php echo e(phpversion()); ?></strong></div>
                            <div class="status-row"><span>Домен в конфиге:</span><strong><?php echo e($config['site_url'] ?? requestBaseUrl()); ?></strong></div>
                            <div class="status-row"><span>Host сайта:</span><strong><?php echo e(extractHost($config['site_url'] ?? requestBaseUrl())); ?></strong></div>
                            <div class="status-row"><span>Sitemap:</span><strong><?php echo file_exists(__DIR__ . '/../sitemap.xml') ? 'Присутствует' : 'Будет создан заново'; ?></strong></div>
                            <div class="status-row"><span>Конфиг:</span><strong>Доступен</strong></div>
                        </div>
                    </section>

                    <?php if (is_array($migrationInfo)): ?>
                    <section class="card glass">
                        <h2 class="card-title">Последний переезд</h2>
                        <div class="status-list">
                            <div class="status-row"><span>Старое название:</span><strong><?php echo e($migrationInfo['previous_site_name'] ?? ''); ?></strong></div>
                            <div class="status-row"><span>Старый домен:</span><strong><?php echo e($migrationInfo['previous_site_url'] ?? ''); ?></strong></div>
                            <div class="status-row"><span>Новое название:</span><strong><?php echo e($migrationInfo['new_site_name'] ?? ''); ?></strong></div>
                            <div class="status-row"><span>Новый домен:</span><strong><?php echo e($migrationInfo['new_site_url'] ?? ''); ?></strong></div>
                            <div class="status-row"><span>Дата:</span><strong><?php echo e($migrationInfo['migrated_at'] ?? ''); ?></strong></div>
                        </div>
                    </section>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
