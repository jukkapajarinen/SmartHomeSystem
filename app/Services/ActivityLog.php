<?php

namespace App\Services;

class ActivityLog
{
    /**
     * Scan the Laravel log from the end and collect, for each needle, the
     * most recent log lines whose message contains it.
     *
     * @param  array<string>  $needles
     * @return array<string, array<string>> needle => formatted lines, newest first
     */
    public static function latestEntriesFor(array $needles, int $limit = 5): array
    {
        $results = array_fill_keys($needles, []);

        $path = storage_path('logs/laravel.log');

        if (! is_file($path)) {
            return $results;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $pending = count($needles);

        for ($i = count($lines) - 1; $i >= 0 && $pending > 0; $i--) {
            if (! preg_match('/^\[(?<ts>[^\]]+)] \S+: (?<message>.*)$/', $lines[$i], $match)) {
                continue;
            }

            foreach ($needles as $needle) {
                if (count($results[$needle]) >= $limit) {
                    continue;
                }

                if (str_contains($match['message'], $needle)) {
                    $results[$needle][] = "{$match['ts']} — " . rtrim($match['message']);

                    if (count($results[$needle]) >= $limit) {
                        $pending--;
                    }

                    break;
                }
            }
        }

        return $results;
    }
}
