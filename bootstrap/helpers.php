<?php

use Carbon\Carbon;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

if (!function_exists('format_date')) {
    /**
     * Format the date to have application's date format.
     * This is used sometimes when you create some file and date has
     * be formatted. Apart from that, datetimes are always formatted in
     * the front end and there's no need to use it.
     * @param DateTime|Carbon|string|null $date
     * @return DateTime|string|null
     */
    function format_date(DateTime|Carbon|string $date = null): DateTime|string|null
    {
        if ($date) {
            return Carbon::parse($date)
                ->format(config('other.jp_date_format_php'));
        }
        return $date;
    }
}


if (!function_exists('db_bool_val')) {
    /**
     * PHP8 with Laravel 8 has problem for postgreSQL 13 boolean field
     * If you just provide 'boolean_column' => true/false it would not work
     * and return an Exception since php converts the value to int of either 0 for false and 1 for true.
     * However, if you provide DB::raw('true/false') it would
     * work. To workaround that problem, as of now, creating this helper function
     *
     * Edit - Use php 8.0.8 or above and don't use this function anymore.
     * @param bool|int|null $value
     * @return Expression
     */
    function db_bool_val(bool|null|int $value): Expression
    {
        if ($value === false) return DB::raw('false');
        elseif ($value === true) return DB::raw('true');
        elseif (is_null($value)) return DB::raw('null');
        else return DB::raw("'$value'::bool");
    }
}


if (!function_exists('bool_convert')) {
    /**
     * Convert any value provided into boolean
     * This is exactly how $request->boolean() works
     * https://laravel.com/docs/8.x/requests#retrieving-boolean-input-values
     * @param $value
     * @return bool
     */
    function bool_convert($value): bool
    {
        return $value == 1 || $value == 'true' || $value === true || strtolower($value) == 'on';
    }
}

if (!function_exists('model_index')) {
    /**
     * Generate model index name used for Laravel scout based on env
     * @param string $modelName
     * @return string
     */
    function model_index(string $modelName): string
    {
        $modelName = strtolower($modelName);
        if (app()->environment('production')) {
            return sprintf('%s_%s_index', $modelName, app()->environment());
        }
        /**
         * On local, changes are if multiple devs work, they would use the
         * same Meilisearch server (in case they are on Windows where setting up is quite a task)
         * it would collide with each other index since the index name would be the same
         * To make the index name unique for each of them, this way is used.
         */
        $key = preg_replace('/[^a-z]*/i', '', config('app.key')); // replace non alphabets
        //take the last 8 digits to keep it short and at the same time, maintain uniqueness since
        // beginning always start with base64:...
        $key = substr($key, -8);
        return sprintf('%s_%s_%s_index', $modelName, app()->environment(), $key);
    }
}
