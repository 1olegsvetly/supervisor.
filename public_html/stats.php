<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

class StatsManager {
    private static string $statsFile = 'stats';
    private static string $logFile = 'activity_log';

    private static function clientIp(): string {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }

    private static function clientUserAgent(): string {
        return (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
    }

    private static function currentPathInfo(): string {
        return (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    private static function normalizeUsername(string $value): string {
        $value = trim($value);
        $value = ltrim($value, '@');
        return preg_replace('/[^a-zA-Z0-9_]/', '', $value) ?? '';
    }

    public static function logVisit(): void {
        if (!empty($_SESSION['visited'])) {
            return;
        }

        $ua = self::clientUserAgent();
        $bots = ['bot', 'spider', 'crawler', 'slurp', 'google', 'yandex', 'bing', 'lighthouse'];
        foreach ($bots as $bot) {
            if (stripos($ua, $bot) !== false) {
                return;
            }
        }

        $stats = getData(self::$statsFile);
        $stats['visits'] = (int) ($stats['visits'] ?? 0) + 1;
        saveData(self::$statsFile, $stats);

        $_SESSION['visited'] = true;
        self::logActivity('visit', self::currentPathInfo());
    }

    public static function logClick(string $type, string $details = ''): void {
        $stats = getData(self::$statsFile);
        $key = 'clicks_' . $type;
        $stats[$key] = (int) ($stats[$key] ?? 0) + 1;
        saveData(self::$statsFile, $stats);

        self::logActivity('click_' . $type, $details);
    }

    public static function logActivity(string $type, string $details = ''): void {
        $logs = getData(self::$logFile);
        array_unshift($logs, [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'details' => $details,
            'path' => self::currentPathInfo(),
            'ip' => self::clientIp(),
            'ua' => self::clientUserAgent(),
        ]);
        if (count($logs) > 500) {
            $logs = array_slice($logs, 0, 500);
        }
        saveData(self::$logFile, $logs);
    }

    public static function getStats(): array {
        $logs = getData(self::$logFile);
        $statsFile = getData(self::$statsFile);
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $summary = [
            'visits' => 0,
            'unique_visitors' => 0,
            'today_visits' => 0,
            'yesterday_visits' => 0,
            'tg_direct' => 0,
            'tg_test' => 0,
            'tg_test_with_username' => 0,
            'conversion_direct' => 0.0,
            'conversion_test' => 0.0,
            'conversion_total' => 0.0,
            'top_pages' => [],
            'top_ips' => [],
            'top_user_agents' => [],
            'daily' => [],
            'recent_activity' => array_slice($logs, 0, 20),
            'sources' => [
                'stats_file_visits' => (int) ($statsFile['visits'] ?? 0),
                'log_entries' => count($logs),
            ],
        ];

        $uniqueIps = [];
        $visitsByDay = [];
        $pageCounts = [];
        $ipCounts = [];
        $uaCounts = [];

        foreach ($logs as $log) {
            $type = (string) ($log['type'] ?? '');
            $ip = (string) ($log['ip'] ?? 'unknown');
            $ua = trim((string) ($log['ua'] ?? 'unknown')) ?: 'unknown';
            $path = trim((string) ($log['details'] ?? ''));
            if ($path === '' || !str_starts_with($path, '/')) {
                $path = trim((string) ($log['path'] ?? '/')) ?: '/';
            }

            $date = substr((string) ($log['timestamp'] ?? ''), 0, 10);
            if ($date !== '') {
                $visitsByDay[$date] = $visitsByDay[$date] ?? [
                    'date' => $date,
                    'visits' => 0,
                    'tg_direct' => 0,
                    'tg_test' => 0,
                ];
            }

            if ($type === 'visit') {
                $summary['visits']++;
                $uniqueIps[$ip] = true;
                $pageCounts[$path] = ($pageCounts[$path] ?? 0) + 1;
                $ipCounts[$ip] = ($ipCounts[$ip] ?? 0) + 1;
                $uaCounts[$ua] = ($uaCounts[$ua] ?? 0) + 1;

                if ($date === $today) {
                    $summary['today_visits']++;
                }
                if ($date === $yesterday) {
                    $summary['yesterday_visits']++;
                }
                if ($date !== '') {
                    $visitsByDay[$date]['visits']++;
                }
            }

            if ($type === 'click_tg_direct') {
                $summary['tg_direct']++;
                if ($date !== '') {
                    $visitsByDay[$date]['tg_direct']++;
                }
            }

            if ($type === 'click_tg_test') {
                $summary['tg_test']++;
                if (self::normalizeUsername((string) ($log['details'] ?? '')) !== '') {
                    $summary['tg_test_with_username']++;
                }
                if ($date !== '') {
                    $visitsByDay[$date]['tg_test']++;
                }
            }
        }

        if ($summary['visits'] === 0) {
            $summary['visits'] = (int) ($statsFile['visits'] ?? 0);
        }

        $summary['unique_visitors'] = count($uniqueIps);
        $summary['conversion_direct'] = $summary['visits'] > 0 ? round(($summary['tg_direct'] / $summary['visits']) * 100, 2) : 0.0;
        $summary['conversion_test'] = $summary['visits'] > 0 ? round(($summary['tg_test'] / $summary['visits']) * 100, 2) : 0.0;
        $summary['conversion_total'] = $summary['visits'] > 0 ? round((($summary['tg_direct'] + $summary['tg_test']) / $summary['visits']) * 100, 2) : 0.0;

        arsort($pageCounts);
        arsort($ipCounts);
        arsort($uaCounts);
        ksort($visitsByDay);

        $summary['top_pages'] = array_map(static function ($path, $count) {
            return ['path' => $path, 'visits' => $count];
        }, array_keys(array_slice($pageCounts, 0, 8, true)), array_values(array_slice($pageCounts, 0, 8, true)));

        $summary['top_ips'] = array_map(static function ($ip, $count) {
            return ['ip' => $ip, 'visits' => $count];
        }, array_keys(array_slice($ipCounts, 0, 8, true)), array_values(array_slice($ipCounts, 0, 8, true)));

        $summary['top_user_agents'] = array_map(static function ($ua, $count) {
            return ['ua' => $ua, 'visits' => $count];
        }, array_keys(array_slice($uaCounts, 0, 5, true)), array_values(array_slice($uaCounts, 0, 5, true)));

        $summary['daily'] = array_values(array_slice($visitsByDay, -14, 14, true));

        return $summary;
    }

    public static function getRecentActivity(int $limit = 10): array {
        $logs = getData(self::$logFile);
        return array_slice($logs, 0, $limit);
    }
}

if (!str_contains((string) ($_SERVER['REQUEST_URI'] ?? ''), '/admin/')) {
    StatsManager::logVisit();
}
?>
