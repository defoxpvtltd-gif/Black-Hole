<?php
declare(strict_types=1);

namespace App\Security;

final class RateLimiter
{
    private const SESSION_KEY = '__rate_limiter';

    public static function allow(string $bucket, int $maxRequests, int $windowSeconds): bool
    {
        if ($maxRequests <= 0 || $windowSeconds <= 0) {
            return true;
        }

        $now = time();
        $bucketKey = self::SESSION_KEY . ':' . $bucket;
        $events = $_SESSION[$bucketKey] ?? [];
        if (!is_array($events)) {
            $events = [];
        }

        $cutoff = $now - $windowSeconds;
        $events = array_values(
            array_filter(
                $events,
                static fn ($timestamp): bool => is_int($timestamp) && $timestamp >= $cutoff
            )
        );

        if (count($events) >= $maxRequests) {
            $_SESSION[$bucketKey] = $events;
            return false;
        }

        $events[] = $now;
        $_SESSION[$bucketKey] = $events;
        return true;
    }
}
