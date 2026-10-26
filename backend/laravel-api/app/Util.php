<?php

namespace App;

class Util
{
    public static function api_response(bool $success, string $errorCode, string $message, mixed $data, int $statusCode = 200)
    {
        $flag = $success ? "success" : "fail";

        $res = response()->json(['status' => $flag, 'code' => $errorCode, 'message' => $message, 'data' => $data], $statusCode, [
            'Content-Type' => 'application/json',
            'Charset' => 'utf-8'
        ]);

        return $res;
    }
}
