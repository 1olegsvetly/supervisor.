<?php
declare(strict_types=1);

class StatsManager
{
    private static function getStatsPath(): string
    {
        return __DIR__ . '/data/stats.json';
    }

    private static function getActivityLogPath(): string
    {
        return __DIR__ . '/data/activity_log.json';
    }

    private static function loadStats(): array
    {
        $path = self::getStatsPath();
        if (!file_exists($path)) {
            return ['visits' => 0, 'clicks_tg_direct' => 0, 'clicks_tg_test' => 0];
        }
        $data = json_decode((string) file_get_contents($path), true);
        return is_array($data) ? $data : ['visits' => 0, 'clicks_tg_direct' => 0, 'clicks_tg_test' => 0];
    }

    private static function saveStats(array $data): void
    {
        file_put_contents(self::getStatsPath(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private static function loadActivityLog(): array
    {
        $path = self::getActivityLogPath();
        if (!file_exists($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }

    private static function saveActivityLog(array $logs): void
    {
        file_put_contents(self::getActivityLogPath(), json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function logVisit(): void
    {
        $stats = self::loadStats();
        $stats['visits'] = ($stats['visits'] ?? 0) + 1;
        self::saveStats($stats);
    }

    public static function logClick(string $type, string $details = ''): void
    {
        $stats = self::loadStats();
        $key = 'clicks_' . $type;
        $stats[$key] = ($stats[$key] ?? 0) + 1;
        self::saveStats($stats);

        $log = self::loadActivityLog();
        $log[] = [
            'type' => $type,
            'details' => $details,
            'timestamp' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        // Keep only last 1000 entries
        $log = array_slice($log, -1000);
        self::saveActivityLog($log);
    }

    public static function getStats(): array
    {
        $stats = self::loadStats();
        $activity = self::loadActivityLog();

        // Daily stats (last 30 days)
        $daily = [];
        $now = time();
        for ($i = 29; $i >= 0; $i--) {
            $dayStart = strtotime('today -' . $i . ' days');
            $dayEnd = $dayStart + 86400;
            $dayKey = date('Y-m-d', $dayStart);

            $visits = 0;
            $clicksDirect = 0;
            $clicksTest = 0;

            foreach ($activity as $entry) {
                $ts = $entry['timestamp'] ?? 0;
                if ($ts >= $dayStart && $ts < $dayEnd) {
                    if (($entry['type'] ?? '') === 'visit') {
                        $visits++;
                    } elseif (($entry['type'] ?? '') === 'tg_direct') {
                        $clicksDirect++;
                    } elseif (($entry['type'] ?? '') === 'tg_test') {
                        $clicksTest++;
                    }
                }
            }

            $daily[] = [
                'date' => $dayKey,
                'visits' => $visits,
                'clicks_tg_direct' => $clicksDirect,
                'clicks_tg_test' => $clicksTest
            ];
        }

        // Top pages
        $pageCounts = [];
        foreach ($activity as $entry) {
            if (($entry['type'] ?? '') === 'visit') {
                $page = $entry['details'] ?? '/';
                $pageCounts[$page] = ($pageCounts[$page] ?? 0) + 1;
            }
        }
        arsort($pageCounts);
        $topPages = array_slice($pageCounts, 0, 10, true);

        // Top IPs
        $ipCounts = [];
        foreach ($activity as $entry) {
            $ip = $entry['ip'] ?? 'unknown';
            $ipCounts[$ip] = ($ipCounts[$ip] ?? 0) + 1;
        }
        arsort($ipCounts);
        $topIps = array_slice($ipCounts, 0, 10, true);

        // Top User Agents
        $uaCounts = [];
        foreach ($activity as $entry) {
            $ua = $entry['user_agent'] ?? 'unknown';
            $uaCounts[$ua] = ($uaCounts[$ua] ?? 0) + 1;
        }
        arsort($uaCounts);
        $topUserAgents = array_slice($uaCounts, 0, 10, true);

        return [
            'total_visits' => $stats['visits'] ?? 0,
            'total_clicks_tg_direct' => $stats['clicks_tg_direct'] ?? 0,
            'total_clicks_tg_test' => $stats['clicks_tg_test'] ?? 0,
            'daily' => $daily,
            'top_pages' => $topPages,
            'top_ips' => $topIps,
            'top_user_agents' => $topUserAgents,
            'recent_activity' => array_slice(array_reverse($activity), 0, 50)
        ];
    }

    public static function getRecentActivity(int $limit = 20): array
    {
        $activity = self::loadActivityLog();
        return array_slice(array_reverse($activity), 0, $limit);
    }
}
