<?php


namespace App\Traits;


use App\Models\IncomeRange;
use App\Rules\ValidateAge;
use App\Rules\ValidDate;
use Exception;
use Illuminate\Validation\Rule;

/**
 * Helpful validation related methods
 * Trait ValidationTrait
 * @package App\Traits
 */
trait ValidationTrait
{
    /**
     * @param bool $required
     * @return string[]
     */
    public function passwordValidation(bool $required = true): array
    {
        $validation = [];
        if ($required) $validation[] = 'required';
        return array_merge($validation, [
            'min:10',
            'max:64',
//            'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{10,64}$/'
        ]);
    }

    /**
     * @param string $requestKey
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function passwordValidationMsg(string $requestKey = 'password'): array
    {
        return [
            "$requestKey.regex" => __('exception.password_alphanumeric')
        ];
    }

    /**
     * Common validation rules which can be used for both when updating
     * founder and entrepreneur
     * @throws Exception
     */
    public function commonUserValidationRules(): array
    {
        return [
            'dob' => ['sometimes', 'required', new ValidDate(), new ValidateAge(18)],
            'gender' => ['sometimes', 'required', Rule::in(['male', 'female', 'other'])],
            'first_name' => ['sometimes', 'required', 'max:100'],
            'last_name' => ['sometimes', 'required', 'max:100'],
            'last_name_cana' => ['sometimes', 'required', 'max:100'],
            'first_name_cana' => ['sometimes', 'required', 'max:100'],
            'income_range_id' => ['sometimes', 'required', 'integer', 'exists:' . (new IncomeRange())->getTable() . ',id']
        ];
    }
}
