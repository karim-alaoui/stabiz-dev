<?php


namespace App\Actions;


/**
 * Helpful when you've a bunch of rules in create or storing of a resource
 * in the edit, you have basically the same
 * but all of them are optional. Right now, support only the string and array format of validation
 * Class MakeEditRules
 * @package App\Actions
 */
class MakeEditRules
{
    /**
     * @param array $rules
     * @param array $excludeFields
     * @return array
     */
    public static function execute(array $rules, array $excludeFields = []): array
    {
        $editRules = [];
        foreach ($rules as $paramName => $rule) {
            if (in_array($paramName, $excludeFields)) continue;

            if (gettype($rule) == 'string') {
                if (preg_match('/^(required)/', $rule)) {
                    $rule = str_replace('required', 'sometimes|required', $rule);
                }
            } elseif (gettype($rule) == 'array') {
                $key = array_search('required', $rule);
                if ($key !== false) {
                    array_splice($rule, $key, 1, ['sometimes', 'required']);
                }
            }
            $editRules[$paramName] = $rule;
        }
        return $editRules;
    }
}
