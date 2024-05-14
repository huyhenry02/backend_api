<?php

namespace App\Http\Controllers\Api\Example;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Example\Repositories\Interfaces\ExampleInterface;
use App\Modules\Example\Requests\CreateExampleRequest;
use App\Modules\Example\Transformmers\ExampleTransformer;
use App\Modules\RawMediaUpload\Repositories\Interfaces\RawMediaUploadInterface;
use App\Traits\MediaTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Spatie\Fractalistic\ArraySerializer;

class ExampleController extends ApiController
{
    use AuthorizesRequests;
    use MediaTrait;
    protected ExampleInterface $exampleRepo;
    protected RawMediaUploadInterface $rawMediaUpload;

    public function __construct
    (
        ExampleInterface        $example,
        RawMediaUploadInterface $rawMediaUpload,
    )
    {
        $this->exampleRepo = $example;
        $this->rawMediaUpload = $rawMediaUpload;
    }

    /**
     * Example API
     *
     * @OA\Get(
     *      path="/api/example",
     *      operationId="example",
     *      tags={"Example"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function example()
    {
        $example = $this->exampleRepo->getAll();
        $data = fractal($example, new ExampleTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    /**
     *
     * @OA\GET(
     *      path="/api/example-with-passport",
     *      operationId="exampleWithPasspost",
     *      tags={"Example"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list of users",
     *      description="Returns list of users",
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
    public function exampleWithPassport()
    {
        $example = $this->exampleRepo->getAll();
        $data = fractal($example, new ExampleTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    public function createExampleWithFiles(CreateExampleRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $example = $this->exampleRepo->create($data);
            if ($request->files_id) {
                $rawMedia = $this->rawMediaUpload->find($request->files_id);
                if ($rawMedia){
                    $this->addMediaToCollection($example, $rawMedia);
                }
            }
            $files = $example->getMedia('example');
            $response = [];
            $response['data'] = fractal($example, new ExampleTransformer(), new ArraySerializer());
            $response['message'] = 'ok';
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $response['message'] = 'err001';
        }
        return $this->respondSuccess($response);

    }

}
