<?php


namespace App\Traits;


/**
 * Some models has very common class that we load at multiple places
 * Class RelationshipTrait
 * @package App\Traits
 */
trait RelationshipTrait
{
    /**
     * Common entrepreneur profile relationships
     * @return string[]
     */
    public static function entrProfileRelations(): array
    {
        return [
            'lang',
            'eduBg',
            'workingStatus',
            'presentPost',
            'occupation',
            'langAbility',
            'engLangAbility',
            'expectedIncome',
            'industriesExp',
            'prefecturesPfd',
            'industriesPfd',
            'prefecture',
            'area',
            'managementExp',
            'positionsPfd'
        ];
    }

    /**
     * Common founder profile relationships
     * @return string[]
     */
    public static function fdrProfileRelations(): array
    {
        return [
            'area',
            'prefecture',
            'affiliatedCompanies',
            'majorStockHolders',
            'pfdPrefectures' => function ($q) {
                $q->with(['prefecture' => fn($query) => $query->select('id', 'name_ja')]);
            },
            'pfdIndustries.industry',
            'companyIndustries.industry',
            'pfdPositions.position',
            'offeredIncome'
        ];
    }

    /**
     * Relationships loaded for article
     * @return array
     */
    public static function articleRelations(): array
    {
        return ['audiences', 'tags', 'categories.category', 'industries.industry'];
    }
}
