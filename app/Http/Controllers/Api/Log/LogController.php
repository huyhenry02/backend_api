<?php

namespace App\Http\Controllers\Api\Log;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Log\Repositories\Interfaces\LogInterface;
use App\Modules\Log\Request\GetLogRequest;
use App\Modules\Log\Transformers\GetLogTransformer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Spatie\Fractalistic\ArraySerializer;

class LogController extends ApiController
{

    use AuthorizesRequests;

    public LogInterface $logRepo;

    /**
     * @param LogInterface $logRepo
     */
    public function __construct(
        LogInterface              $logRepo
    )
    {
        $this->logRepo = $logRepo;
    }
    /**
     *
     * @OA\Get(
     *      path="/api/log/get",
     *      operationId="getLogData",
     *      tags={"Audit log"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list log data",
     *      description="Get list log data",
     *      @OA\Parameter(
     *           name="key",
     *           in="query",
     *           description="key",
     *           required=true,
     *           example="asset",
     *           @OA\Schema(
     *               type="string"
     *           )
     *      ),
     *          @OA\Parameter(
     *           name="id",
     *           in="query",
     *           description="id",
     *           required=true,
     *           example="08c07dad-51df-4e08-8a3b-f12b4acd6c61",
     *           @OA\Schema(
     *               type="string",
     *              format="uuid"
     *           )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function getLogData(GetLogRequest $request): JsonResponse
    {
        $model_type = $request->get('key');
        $id = $request->get('id');
        $data = $this->logRepo->getLogData($model_type, $id);
        $respond = [];
        if ($data) {
            $respond['data'] = fractal($data, new GetLogTransformer(), new ArraySerializer());
            $respond['data'] = $respond['data']->parseIncludes('user')->toArray();
        }
        return $this->respondSuccess($respond);
    }
}
