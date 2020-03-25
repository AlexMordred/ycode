<?php

namespace App\Http;

class ApiResponse
{
    /**
     * Return a JSON response.
     *
     * @param array $payload
     * @param string $message
     * @param integer $status
     * @return mixed
     */
    public static function response(array $payload = [], string $message = 'OK', int $status = 200)
    {
        return response()->json([
            'message' => $message,
            'payload' => $payload,
        ], $status);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param array $payload
     * @param integer $status
     * @return mixed
     */
    public static function error(string $message = 'ERROR', array $payload = [], int $status = 422)
    {
        return response()->json([
            'message' => $message,
            'payload' => $payload,
        ], $status);
    }

    /**
     * Return an 'Item created' response.
     *
     * @param string $message
     * @param array $payload
     * @return mixed
     */
    public static function created(string $message = 'Item created', array $payload = [])
    {
        return response()->json([
            'message' => $message,
            'payload' => $payload,
        ], 201);
    }
}