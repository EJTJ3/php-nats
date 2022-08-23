<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Util;

use DateInterval;
use Exception;

final class NatsDateUtil
{
    /**
     * @throws Exception
     */
    public static function createDateInterval(string $interval): DateInterval
    {
        $interval = str_replace('D', 'DT', strtoupper($interval));

        // Check if T exist in date interval
        if (strpos($interval, 'T') === false) {
            $interval = 'T' . $interval;
        }

        return new DateInterval('P' . $interval);
    }
}
