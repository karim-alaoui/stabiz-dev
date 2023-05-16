<?php


namespace App\Actions;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use function response;

/**
 * Send JSON error response
 * Class ErrorRes
 * @package App\Actions
 */
class ErrorRes
{
    /**
     * @param $msg
     * @param int $code
     * @param array $extra
     * @return JsonResponse
     * @throws Exception
     */
    public static function execute($msg, int $code = 400, array $extra = []): JsonResponse
    {
        $data = [
            'message' => $msg,
        ];
        if (count($extra) && !Arr::isAssoc($extra)) {
            throw new Exception('extra param must be an associative array');
        }

        if (Arr::isAssoc($extra)) {
            $data = array_merge($data, $extra);
        }

        return response()->json($data, $code);
    }
}
