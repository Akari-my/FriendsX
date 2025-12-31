<?php

namespace Akari_my\FriendsX\util;

class TimeUtils {

    public static function formatDuration(int $seconds): string {
        $seconds = max(0, $seconds);

        $units = [
            "d" => 86400,
            "h" => 3600,
            "m" => 60,
            "s" => 1
        ];

        $parts = [];

        foreach ($units as $suffix => $length) {
            if ($seconds >= $length) {
                $value = intdiv($seconds, $length);
                $seconds %= $length;
                $parts[] = $value . $suffix;
            }
        }

        if (empty($parts)) {
            return "0s";
        }

        return implode(" ", $parts);
    }
}