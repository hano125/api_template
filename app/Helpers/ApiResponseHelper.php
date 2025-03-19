<?php

namespace App\Helpers;

class ApiResponseHelper
{
    public static function success($data = null, $message = "Success", $statusCode = 200)
    {
        return response()->json(
            [
                'status' => 'success',
                'message' => $message,
                'data' => $data
            ],
            $statusCode,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }

    public static function error($message = "Error", $statusCode = 400, $data = null)
    {
        return response()->json(
            [
                'status' => 'error',
                'message' => $message,
                'data' => $data
            ],
            $statusCode,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }
}
