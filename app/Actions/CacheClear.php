<?php


namespace App\Actions;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * Clear all the cache
 * Class CacheClear
 * @package App\Actions
 */
class CacheClear
{
    public static function execute()
    {
        Cache::flush();
        Artisan::call('cache:clear');
    }
}
