<?php

namespace App\Cache;

use App\Models\CourtSchedule;

class CourtScheduleCache extends CacheProvider
{
    public function __construct($key, $expiration = 60, $store = null)
    {
        parent::__construct($key, $expiration, $store);
    }

    public static function getSchedules($courtId, $day)
    {
        $key = sprintf('court_schedule_%s_%s', $courtId, $day);
        $instance = new static($key, 60);

        return $instance->rememberData(function () use ($courtId, $day) {
            return CourtSchedule::where('court_id', $courtId)
                ->where('day', $day)
                ->get();
        });
    }

    public static function clearSchedules($courtId, $day)
    {
        $key = sprintf('court_schedule_%s_%s', $courtId, $day);
        $instance = new static($key, 60);
        $instance->clearData();
    }
}
