<?php

use Illuminate\Support\Facades\Validator;

if (!function_exists('sendResponse')) {
    function sendResponse($message, $data = [], $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => [],
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return $response;
    }
}

if (!function_exists('sendError')) {

    function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'data' => [],
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('validateRules')) {

    function validateRules($rules, $messages = [])
    {
        return Validator::make(request()->all(), $rules, $messages);

    }
}

if (!function_exists('sendUnauthorized')) {
    function sendUnauthorized($error, $errorMessages = [], $code = 401)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('sendForbidden')) {
    function sendForbidden($error, $errorMessages = [], $code = 403)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('sendBadRequest')) {

    function sendBadRequest($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('sendInternalError')) {
    function sendInternalError($error, $errorMessages = [], $code = 500)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('sendValidationError')) {

    function sendValidationError($errorMessages, $code = 422)
    {
        $response = [
            'success' => false,
            'message' => 'Validation Error',
            'data' => $errorMessages,
        ];

        return response()->json($response, $code);
    }
}

if (!function_exists('sendNotFound')) {

    function sendNotFound($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('sendNoContent')) {

    function sendNoContent($message, $code = 204)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
}

if (!function_exists('sendCreated')) {

    function sendCreated($message, $data = [], $code = 201)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('sendAccepted')) {

    function sendAccepted($message, $data = [], $code = 202)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}

?>
