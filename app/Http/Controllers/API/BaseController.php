<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
/**
 * @OA\Info(
 *     title=" Backend API",
 *     version="1.0.0",
 *     description="API documentation",
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Enter token in format: Bearer {token}",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="Passport",
 *     securityScheme="passport"
 * )
 */

class BaseController extends Controller
{
     protected $language;

    public function __construct(Request $request)
    {
        $this->language = $request->input('language', 'EN');
    }
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message, $code = 200)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];


        return response()->json($response, $code);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if (!empty($errorMessages)) {
            $response['error'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}
