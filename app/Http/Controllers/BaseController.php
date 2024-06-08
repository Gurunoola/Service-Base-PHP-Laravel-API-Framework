<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class BaseController extends Controller
{
    protected function sendResponse($result, $message = '', $code = Response::HTTP_OK)
    {
        return response()->json(['data' => $result, 'message' => $message], $code);
    }

    protected function sendError($message, $code = Response::HTTP_BAD_REQUEST, $errors = [])
    {
        return response()->json(['message' => $message, 'errors' => $errors], $code);
    }
}