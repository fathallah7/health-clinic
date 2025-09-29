<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data = null, string $message = '', int $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }


    protected function error(string $message = '', array $errors = [], int $code = 422)
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }
}
