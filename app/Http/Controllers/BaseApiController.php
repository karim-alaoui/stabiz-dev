<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Extend every api controller from this controller
 * will add some helpful functions to this controller
 * soon in the future.
 * Use these methods instead of returning the data using the response function
 * This way in the future, if we need to change the structure in response everywhere,
 * we can do that easily.
 * Class BaseApiController
 * @package App\Http\Controllers
 */
class BaseApiController extends Controller
{
    /**
     * Send a success (200 by default) json response that contains a message
     * @param string $msg
     * @param int $code
     * @return JsonResponse
     */
    public function successMsg(string $msg = 'Successful', int $code = 200): JsonResponse
    {
        return response()->json(['message' => $msg], $code);
    }


    /**
     * @param $data
     * @param int $code
     * @return JsonResponse
     */
    public function success($data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * Usually returned after a resource is deleted
     * @param int $code
     * @return Response
     */
    public function noContent(int $code = 204): Response
    {
        return response()->noContent($code);
    }

    /**
     * @param null $msg
     * @param int $code
     * @return JsonResponse
     */
    public function errorMsg($msg = null, int $code = 400): JsonResponse
    {
        return response()->json(['message' => $msg], $code);
    }
}
