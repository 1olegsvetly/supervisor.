<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../functions.php';
require_once '../stats.php';

$stats = StatsManager::getStats();
$recentActivity = $stats['recent_activity'] ?? StatsManager::getRecentActivity(20);
$dailyRows = $stats['daily'] ?? [];
$topPages = $stats['top_pages'] ?? [];
$topIps = $stats['top_ips'] ?? [];
$topUserAgents = $stats['top_user_agents'] ?? [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд | <?php echo e($config['site_name'] ?? 'VPN'); ?> Admin</title>
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
            max-width: 820px;
        }

        .header-meta {
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card {
            padding: 24px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.06);
            background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
        }

        .stat-card__label {
            color: var(--text-secondary);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .stat-card__value {
            font-size: 34px;
            font-weight: 800;
            color: var(--accent-cyan);
            line-height: 1.1;
        }

        .stat-card__note {
            margin-top: 10px;
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(320px, 0.9fr);
            gap: 22px;
            margin-bottom: 24px;
        }

        .card {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .card + .card {
            margin-top: 22px;
        }

        .card-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
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

        .status-table {
            width: 100%;
            border-collapse: collapse;
        }

        .status-table th {
            text-align: left;
            padding: 14px 12px;
            color: var(--text-secondary);
            font-weight: 500;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            font-size: 13px;
        }

        .status-table td {
            padding: 14px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 14px;
            vertical-align: top;
        }

        .badge {
            display: inline-flex;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge--visit {
            background: rgba(0, 240, 255, 0.1);
            color: var(--accent-cyan);
        }

        .badge--click {
            background: rgba(112, 0, 255, 0.12);
            color: #b39bff;
        }

        .mini-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .mini-stat {
            padding: 16px 18px;
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .mini-stat__label {
            color: var(--text-secondary);
            font-size: 13px;
            margin-bottom: 8px;
        }

        .mini-stat__value {
            font-size: 24px;
            font-weight: 800;
        }

        .list-table {
            width: 100%;
            border-collapse: collapse;
        }

        .list-table td {
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            vertical-align: top;
        }

        .list-table td:last-child {
            text-align: right;
            color: var(--accent-cyan);
            font-weight: 700;
            padding-left: 16px;
            white-space: nowrap;
        }

        .muted {
            color: var(--text-secondary);
        }

        .mono {
            font-family: 'Fira Code', 'Courier New', monospace;
            font-size: 12px;
            word-break: break-word;
        }

        @media (max-width: 1180px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .admin-layout {
                flex-direction: column;
            }

            .admin-main {
                margin-left: 0;
                padding: 22px;
            }

            .page-header {
                flex-direction: column;
            }

            .mini-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <a href="/" class="sidebar__logo"><?php echo e($config['site_name'] ?? 'VPN'); ?><span>Admin</span></a>
            <nav class="sidebar__nav">
                <ul>
                    <li><a href="index.php" class="sidebar__link active">Дашборд</a></li>
                    <li><a href="edit-page.php" class="sidebar__link">Редактор страниц</a></li>
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
                    <h1 class="page-title">Дашборд</h1>
                    <p class="page-subtitle">Панель показывает реальные показатели сайта по журналу событий: посещаемость, клики по Telegram, заявки на тест, конверсию, популярные страницы и последние действия посетителей.</p>
                </div>
                <div class="header-meta">Обновлено: <?php echo date('d.m.Y H:i'); ?></div>
            </div>

            <section class="stats-grid">
                <article class="stat-card glass">
                    <div class="stat-card__label">Всего визитов</div>
                    <div class="stat-card__value"><?php echo number_format((int) ($stats['visits'] ?? 0), 0, ',', ' '); ?></div>
                    <div class="stat-card__note">Учитываются фактические посещения из журнала активности сайта.</div>
                </article>
                <article class="stat-card glass">
                    <div class="stat-card__label">Уникальные IP</div>
                    <div class="stat-card__value"><?php echo number_format((int) ($stats['unique_visitors'] ?? 0), 0, ',', ' '); ?></div>
                    <div class="stat-card__note">Приближённая оценка уникальных посетителей по IP-адресам.</div>
                </article>
                <article class="stat-card glass">
                    <div class="stat-card__label">Переходы в Telegram</div>
                    <div class="stat-card__value"><?php echo number_format((int) ($stats['tg_direct'] ?? 0), 0, ',', ' '); ?></div>
                    <div class="stat-card__note">Клики по кнопкам перехода на бота и CTA-блокам.</div>
                </article>
                <article class="stat-card glass">
                    <div class="stat-card__label">Заявки на тест</div>
                    <div class="stat-card__value"><?php echo number_format((int) ($stats['tg_test'] ?? 0), 0, ',', ' '); ?></div>
                    <div class="stat-card__note">Отправки формы «Введите ваш @username в Telegram / Получить тест 3 дня».</div>
                </article>
                <article class="stat-card glass">
                    <div class="stat-card__label">Заявки с username</div>
                    <div class="stat-card__value"><?php echo number_format((int) ($stats['tg_test_with_username'] ?? 0), 0, ',', ' '); ?></div>
                    <div class="stat-card__note">Сколько пользователей указали username перед переходом в Telegram.</div>
                </article>
                <article class="stat-card glass">
                    <div class="stat-card__label">Общая конверсия</div>
                    <div class="stat-card__value"><?php echo rtrim(rtrim(number_format((float) ($stats['conversion_total'] ?? 0), 2, '.', ''), '0'), '.'); ?>%</div>
                    <div class="stat-card__note">Сумма переходов в Telegram и заявок на тест относительно всех визитов.</div>
                </article>
            </section>

            <div class="dashboard-grid">
                <div>
                    <section class="card glass">
                        <h2 class="card-title">Воронка и оперативные показатели</h2>
                        <p class="card-subtitle">Здесь собраны ключевые цифры по привлечению и конверсии, которые помогают быстро оценить эффективность сайта и точек перехода в Telegram.</p>
                        <div class="mini-grid">
                            <div class="mini-stat">
                                <div class="mini-stat__label">Сегодня визитов</div>
                                <div class="mini-stat__value"><?php echo number_format((int) ($stats['today_visits'] ?? 0), 0, ',', ' '); ?></div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat__label">Вчера визитов</div>
                                <div class="mini-stat__value"><?php echo number_format((int) ($stats['yesterday_visits'] ?? 0), 0, ',', ' '); ?></div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat__label">Конверсия в прямой клик TG</div>
                                <div class="mini-stat__value"><?php echo rtrim(rtrim(number_format((float) ($stats['conversion_direct'] ?? 0), 2, '.', ''), '0'), '.'); ?>%</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat__label">Конверсия в тест</div>
                                <div class="mini-stat__value"><?php echo rtrim(rtrim(number_format((float) ($stats['conversion_test'] ?? 0), 2, '.', ''), '0'), '.'); ?>%</div>
                            </div>
                        </div>
                    </section>

                    <section class="card glass">
                        <h2 class="card-title">Динамика по дням</h2>
                        <p class="card-subtitle">Последние 14 дней по визитам, прямым переходам в Telegram и отправкам формы тестового доступа.</p>
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Визиты</th>
                                    <th>Клики TG</th>
                                    <th>Тест 3 дня</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_reverse($dailyRows) as $row): ?>
                                    <tr>
                                        <td><?php echo e(date('d.m.Y', strtotime((string) ($row['date'] ?? 'now')))); ?></td>
                                        <td><?php echo number_format((int) ($row['visits'] ?? 0), 0, ',', ' '); ?></td>
                                        <td><?php echo number_format((int) ($row['tg_direct'] ?? 0), 0, ',', ' '); ?></td>
                                        <td><?php echo number_format((int) ($row['tg_test'] ?? 0), 0, ',', ' '); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($dailyRows)): ?>
                                    <tr><td colspan="4" class="muted">Пока нет данных по дням.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </section>

                    <section class="card glass">
                        <h2 class="card-title">Последняя активность</h2>
                        <p class="card-subtitle">Живой журнал посещений и действий посетителей. Помогает увидеть, какие страницы посещают и какие события происходят чаще всего.</p>
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th>Время</th>
                                    <th>Событие</th>
                                    <th>Детали</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $log): ?>
                                    <tr>
                                        <td><?php echo e(date('d.m H:i', strtotime((string) ($log['timestamp'] ?? 'now')))); ?></td>
                                        <td>
                                            <span class="badge <?php echo str_starts_with((string) ($log['type'] ?? ''), 'click') ? 'badge--click' : 'badge--visit'; ?>">
                                                <?php echo e((string) ($log['type'] ?? '')); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div><?php echo e((string) ($log['details'] ?? '')); ?></div>
                                            <?php if (!empty($log['path'])): ?>
                                                <div class="muted mono"><?php echo e((string) $log['path']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="mono"><?php echo e((string) ($log['ip'] ?? 'unknown')); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentActivity)): ?>
                                    <tr><td colspan="4" class="muted">Активности пока нет.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </section>
                </div>

                <div>
                    <section class="card glass">
                        <h2 class="card-title">Топ страниц</h2>
                        <p class="card-subtitle">Какие URL чаще всего посещают пользователи.</p>
                        <table class="list-table">
                            <tbody>
                                <?php foreach ($topPages as $row): ?>
                                    <tr>
                                        <td class="mono"><?php echo e((string) ($row['path'] ?? '/')); ?></td>
                                        <td><?php echo number_format((int) ($row['visits'] ?? 0), 0, ',', ' '); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($topPages)): ?>
                                    <tr><td class="muted">Статистика страниц пока пуста.</td><td></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </section>

                    <section class="card glass">
                        <h2 class="card-title">Топ IP</h2>
                        <p class="card-subtitle">Список IP, с которых чаще всего были посещения.</p>
                        <table class="list-table">
                            <tbody>
                                <?php foreach ($topIps as $row): ?>
                                    <tr>
                                        <td class="mono"><?php echo e((string) ($row['ip'] ?? 'unknown')); ?></td>
                                        <td><?php echo number_format((int) ($row['visits'] ?? 0), 0, ',', ' '); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($topIps)): ?>
                                    <tr><td class="muted">IP-данных пока нет.</td><td></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </section>

                    <section class="card glass">
                        <h2 class="card-title">Популярные User-Agent</h2>
                        <p class="card-subtitle">Помогает понять, откуда приходят пользователи: браузер, бот, устройство или служебный запрос.</p>
                        <table class="list-table">
                            <tbody>
                                <?php foreach ($topUserAgents as $row): ?>
                                    <tr>
                                        <td class="mono"><?php echo e((string) ($row['ua'] ?? 'unknown')); ?></td>
                                        <td><?php echo number_format((int) ($row['visits'] ?? 0), 0, ',', ' '); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($topUserAgents)): ?>
                                    <tr><td class="muted">Данных по User-Agent пока нет.</td><td></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
