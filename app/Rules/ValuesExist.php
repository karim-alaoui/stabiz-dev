<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Check if all these values exist on the column
 * of a particular table.
 * Eg - the values are 1,2,3 and column is id on users table,
 * then verify that all 3 values exist for id column.
 * If even one is missing, it should return error.
 * Class ValuesExist
 * @package App\Rules
 */
class ValuesExist implements Rule
{
    protected string $msg;

    /**
     * Create a new rule instance.
     * @param string $column
     * @param string|Model $modelClass - string representation of Model class. get_class((new ModelName()))
     * would return this value or just the model class like new User() // user is the model class here
     */
    public function __construct(protected string $column, protected string|Model $modelClass)
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value): bool
    {
        if (gettype($value) != 'array') {
            $msg = __('validation.array');
            /** @var string $msg */
            $this->msg = $msg;
            return false;
        }

        $value = array_unique($value);
        try {
            if ($this->modelClass instanceof Model) {
                $model = $this->modelClass;
            } else {
                $model = new $this->modelClass();
            }
        } catch (Exception) {
            throw new Exception('Invalid model class provided');
        }
        if (!$model instanceof Model) {
            $msg = $this->modelClass . ' is not any model class';
            throw new Exception($msg);
        }

        if (!in_array($this->column, Schema::getColumnListing($model->getTable()))) {
            $msg = sprintf('%s column does not exist in this model class', $this->column);
            throw new Exception($msg);
        }

        $valuesFound = $model->whereIn($this->column, $value)
            ->get([$this->column])
            ->pluck($this->column);

        if (count($valuesFound) != count($value)) {
            $diff = collect($value)
                ->diff($valuesFound)
                ->values()
                ->toArray();

            $msg = sprintf('%s %s invalid :attribute',
                implode(', ', $diff),
                count($diff) >= 2 ? 'are' : 'is',
            );
            $this->msg = $msg;
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        if ($this->msg) return $this->msg;
        return __('validation.value_exist');
    }
}
