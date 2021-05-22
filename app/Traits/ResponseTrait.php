<?php

namespace App\Traits;

trait ResponseTrait
{
    public function coreResponse($message, $data = null, $meta = [], $statusCode, $isSuccess = true)
    {
        /**
         * Check the params
         */
        if (!$message) {
            return response()->json(['message' => 'Message is required'], 500);
        }

        /**
         * Send the response
         */
        if ($isSuccess) {
            return response()->json([
                'message' => $message,
                'status' => 'success',
                'data' => $data,
                'meta' => $meta,
            ], $statusCode);
        }
        return response()->json([
            'message' => $message,
            'status' => 'error',
        ], $statusCode);

    }

    public function successResponse($data = [], $message = 'Response given successfully.', $meta = [], $statusCode = 200)
    {
        return $this->coreResponse($message, $data, $meta, $statusCode);
    }

    /**
     * This will return error response
     *
     * @param string $message
     * @param integer $statusCode
     * @return void
     */
    public function errorResponse($message = 'There is an error while processing your request.', $statusCode = 500)
    {
        return $this->coreResponse($message, null, [], $statusCode, false);
    }
}
