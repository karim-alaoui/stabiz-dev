<?php


namespace App\Actions;


/**
 * Common validation used for pagination
 * Class PaginationRules
 * @package App\Actions
 */
class PaginationRules
{
    /**
     * @return string[][]
     */
    public static function execute(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1']
        ];
    }
}
