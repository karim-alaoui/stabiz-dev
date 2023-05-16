<?php

namespace App\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class MatchEitherDateFormat
 * @package App\Rules
 */
class MatchEitherDateFormat implements Rule
{
    private array $formats;

    /**
     * Create a new rule instance.
     *
     * @param array $formats
     */
    public function __construct(array $formats)
    {
        $this->formats = $formats;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $valid = false;
        foreach ($this->formats as $format) {
            try {
                Carbon::createFromFormat($format, $value);
                $valid = true;
            } catch (Exception) {

            }
        }
        return $valid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        $phpToJSFormatMapping = [
            'Y-m-d H:i' /*PHP format*/ => 'YYYY-MM-DD HH:mm', /*JavaScript format*/
            'Y-m-d H:i:s' => 'YYYY-MM-DD HH:mm:ss',
            'Y-n-d H:i:s' => 'YYYY-M-DD HH:mm:ss',
            'H:i:s' => 'HH:mm:ss',
            'H:i' => 'HH:mm',
            'Y-m-d' => 'YYYY-MM-DD',
            'Y-m-j' => 'YYYY-MM-D',
            'Y-n-d' => 'YYYY-M-DD',
            'Y-n-j' => 'YYYY-M-D',
            'Y-m' => 'YYYY-M',
        ];

        $formats = [];
        foreach ($this->formats as $format) {
            try {
                $formats[] = $phpToJSFormatMapping[$format];
            } catch (Exception) {
                $formats[] = $format;
            }
        }

        return __('validation.match_either_format', ['formats' => implode(', ', $formats)]);
    }
}
