<?php

namespace App\Cache\Owner;
    
use Illuminate\Support\Facades\Cache;
use App\Models\Court;
use App\Cache\CacheProvider;
use Illuminate\Support\Facades\Log;


class CourtCache extends CacheProvider
{
    public function __construct($user_id,$ttl = 1000)
    {
        $key = 'court_cache_user_' . $user_id;

        parent::__construct($key, $ttl);
    }

    public static function getCourts($user_id)
    {
        $ins = new static($user_id);

        if(!$ins->hasData()){
            $courts = Court::where('user_id', $user_id)->get();
            $ins->setData($courts);
        }
        return $ins->getData();
    }

    public static function clearCourts($user_id)
    {
        $ins = new static($user_id);
        $ins->clearData();
    }   
}

