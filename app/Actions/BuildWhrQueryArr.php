<?php


namespace App\Actions;


use Illuminate\Support\Arr;

/**
 * Build the array of where query which will be
 * used in where query
 * Class BuildWhereArray
 * @package App\Actions
 */
class BuildWhrQueryArr
{
    /**
     * @param array $query
     * @param array $searchInfo
     * @return array
     */
    public static function execute(array $query, array $searchInfo): array
    {
        /*
         * This is how the searchInfo array would look like
         * $searchInfo = [
            [
                'queryKey' => 'id',
                'dbColumn' => 'id',
                'queryVal' => 1
            ]
        *
         * if $query = ['id' => 1]
         * that means searching for id whose value is 1
         *
         * So, in that case the $searchInfo would look like this
         * if dbColumn is not provided, it would be equal to queryKey
         * if queryVal is not provided it would be $query['queryKey'] in this case $query['id']
        ];*/

        $where = [];
        foreach ($searchInfo as $item) {
            $queryKey = Arr::get($item, 'queryKey');
            if (is_null($queryKey)) continue;
            $dbColumn = Arr::get($item, 'dbColumn', $queryKey);
            $arr = [$dbColumn];
            $operator = Arr::get($item, 'operator');
            if ($operator) {
                $arr = array_merge($arr, ["$operator"]);
            }
            $queryVal = Arr::get($item, 'queryVal');
            if (is_null($queryVal)) $queryVal = Arr::get($query, 'queryKey');
            if (is_null($queryVal)) continue;
            $arr = array_merge($arr, [$operator == 'ilike' ? "%$queryVal%" : $queryVal]);

            $where[] = $arr;
        }

        /**
         * Example of where query would look like this
         * $where = [['id', 1], ['title', 'ilike', '%search title%']]
         */
        return $where;
    }
}
