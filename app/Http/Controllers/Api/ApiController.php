<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Example for response examples value"
 * )
 */
/**
 * @OA\Info(
 *      version="1.0.",
 *      title="Laravel Api Documentation",
 *      description="L5 Swagger",
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Wishcare API"
 * )
 */

class ApiController extends Controller
{
    /**
     * Return generic json response with the given data.
     *
     * @param $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */

    protected function  respond($data, int $statusCode = 200, array $headers = [], string $message = ''): JsonResponse
    {
        $metaData = $data['meta'] ?? null;
        if ($metaData) {
            unset($data['meta']);
        }

        $responseData = [
            'statusCode' => $statusCode,
            'data' => $data['data'] ?? $data,
            'message' => $message,
        ];
        if (!empty($metaData)){
            $responseData['meta'] = $metaData;
        }
        return response()->json($responseData, $statusCode, $headers);
    }

    /**
     * Respond with success.
     *
     * @param $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function respondSuccess($data, int $statusCode = 200): JsonResponse
    {
        return $this->respond($data, $statusCode);
    }

    /**
     * Respond success without data.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondSuccessWithoutData(string $message = ""): JsonResponse
    {
        return response()->json(['status' => 200, 'message' => $message]);
    }

    /**
     * Respond with created.
     *
     * @param array $data
     * @return JsonResponse
     */
    protected function respondCreated(array $data = []): JsonResponse
    {
        $dataRes = $data['data'] ?? [];
        $message = $data['message'] ?? '';
        return $this->respond($dataRes, 201, message: $message);
    }

    /**
     * Respond with created.
     *
     * @param array $data
     * @return JsonResponse
     */
    protected function respondUpdated(array $data = []): JsonResponse
    {
        $dataRes = $data['data'] ?? [];
        $message = $data['message'] ?? '';
        return $this->respond($dataRes, 201, message: $message);
    }

    /**
     * Respond with no content.
     *
     * @return JsonResponse
     */
    protected function respondNoContent(): JsonResponse
    {
        return $this->respond(null, 204);
    }

    /**
     * Respond with error.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondError(string $message = "", $statusCode = 400): JsonResponse
    {
        $responseData = [
            'statusCode' => $statusCode,
            'message' => $message ? $message : __('messages.system_error'),
        ];

        return response()->json($responseData, $statusCode);
    }

    /**
     * Respond with error and data.
     *
     * @param $data
     * @param $statusCode
     *
     * @return JsonResponse
     */
    protected function respondErrorWithData($data, $statusCode): JsonResponse
    {
        array_unshift($data, ['status' => 'Error']);

        return $this->respond($data, $statusCode);
    }

    /**
     * Respond with unauthorized.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with forbidden.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondForbidden(string $message = 'You do not have access to this resource'): JsonResponse
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respond with not found.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with failed login.
     *
     * @return JsonResponse
     */
    protected function respondFailedLogin(): JsonResponse
    {
        return response()->json(['status' => 422, 'message' => __('auth.email_password_invalid')]);
    }

    /**
     * Respond with internal error.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondInternalError(string $message = 'Internal Error'): JsonResponse
    {
        return $this->respondError($message, 500);
    }
}
