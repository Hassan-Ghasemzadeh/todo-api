<?php

namespace App;

trait ApiResponder
{
    protected function successResponse($data = null, $message = 'Ok', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function errorResponse($message = 'Error', $code = 500, $errors = null)
    {
        $payload = [
            'success' => false,
            'message' => $message
        ];

        if (!is_null($errors)) {
            $payload['errors']  = $errors;
        }
        return response()->json([$payload, $code]);
    }
}
