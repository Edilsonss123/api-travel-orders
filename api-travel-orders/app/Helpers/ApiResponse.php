<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * @param  array  $data
     * @param  string  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function response(array $data = [], string $message = "", int $statusCode = 200): JsonResponse
    {
        $successCodes = [ 200, 201, 202, 203, 204, 205, 206, 207, 208, 226 ];
        return response()->json([
            'message' => $message ?: __('apiResponse.defaultMessage'),
            'success' => in_array($statusCode, $successCodes),
            'result' => $data
        ], $statusCode);
    }

    /**
     * @param  string  $message
     * @param  array  $errors
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $message = "", array $errors = [], int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'message' => $message ?: __('apiResponse.exceptionMessageError'),
            'success' => false,
            'errors' => $errors
        ], $statusCode);
    }
}
