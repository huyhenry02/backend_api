<?php

namespace App\Http\Controllers\Api\MasterData;

use App\Http\Controllers\Api\ApiController;
use App\Modules\MasterData\Allowance\Repositories\Interfaces\AllowanceInterface;
use App\Modules\MasterData\City\Repositories\Interfaces\CityInterface;
use App\Modules\MasterData\ContractType\Repositories\Interfaces\ContractTypeInterface;
use App\Modules\MasterData\SalaryType\Repositories\Interfaces\SalaryTypeInterface;
use App\Modules\MasterData\Employment\Repositories\Interfaces\EmploymentInterface;
use App\Modules\MasterData\InsurancePolicy\Repositories\Interfaces\InsurancePolicyInterface;
use App\Modules\MasterData\MasterDataTransformer;
use App\Modules\MasterData\Nationality\Repositories\Interfaces\NationalityInterface;
use App\Modules\MasterData\Position\Repositories\Interfaces\PositionInterface;
use App\Modules\MasterData\Religion\Repositories\Interfaces\ReligionInterface;
use App\Modules\MasterData\Requests\GetListMasterDataRequest;
use App\Modules\MasterData\Requests\GetListMultiKeyMasterDataRequest;
use App\Modules\MasterData\Title\Repositories\Interfaces\TitleInterface;
use App\Modules\MasterData\UnitLevel\Repositories\Interfaces\UnitLevelInterface;
use Spatie\Fractalistic\ArraySerializer;


class MasterDataController extends ApiController
{
    protected AllowanceInterface $allowanceRepo;
    protected CityInterface $cityRepo;
    protected EmploymentInterface $employmentRepo;
    protected InsurancePolicyInterface $insurancePolicyRepo;
    protected NationalityInterface $nationalityRepo;
    protected PositionInterface $positionRepo;
    protected UnitLevelInterface $unitLevelRepo;
    protected ReligionInterface $religionRepo;
    protected TitleInterface $titleRepo;
    protected ContractTypeInterface $contractTypeRepo;
    protected SalaryTypeInterface $salaryTypeRepo;

    protected array $repos;
    public function __construct(
        AllowanceInterface       $allowance,
        CityInterface            $city,
        EmploymentInterface      $employment,
        InsurancePolicyInterface $insurancePolicy,
        NationalityInterface     $nationality,
        PositionInterface        $position,
        UnitLevelInterface       $unitLevel,
        ReligionInterface        $religionRepo,
        TitleInterface           $titleRepo,
        ContractTypeInterface    $contractTypeRepo,
        SalaryTypeInterface      $salaryTypeRepo,
    )
    {
        $this->allowanceRepo = $allowance;
        $this->cityRepo = $city;
        $this->employmentRepo = $employment;
        $this->insurancePolicyRepo = $insurancePolicy;
        $this->nationalityRepo = $nationality;
        $this->positionRepo = $position;
        $this->unitLevelRepo = $unitLevel;
        $this->religionRepo = $religionRepo;
        $this->titleRepo = $titleRepo;
        $this->contractTypeRepo = $contractTypeRepo;
        $this->salaryTypeRepo = $salaryTypeRepo;
        $this->repos = [
            "position" => $this->positionRepo,
            "employment" => $this->employmentRepo,
            "insurance_policy" => $this->insurancePolicyRepo,
            "nationality" => $this->nationalityRepo,
            "unit_level" => $this->unitLevelRepo,
            "allowance" => $this->allowanceRepo,
            "city" => $this->cityRepo,
            "title" => $this->titleRepo,
            "religion" => $this->religionRepo,
            "contract_type" => $this->contractTypeRepo,
            "salary_type" => $this->salaryTypeRepo,
        ];
    }

    /**
     *
     * @OA\Get(
     *      path="/api/master-data/list",
     *      operationId="getListMasterData",
     *      tags={"Master data"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list of master data",
     *      description="Get list of master data",
     *      @OA\Parameter(
     *           name="key",
     *           in="query",
     *           description="key",
     *           required=true,
     *           example="position",
     *           @OA\Schema(
     *               type="string"
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
    function getListMasterData(GetListMasterDataRequest $request)
    {
        $table = $request->key;
        $result = $this->repos[$table]->getData();
        $response = fractal($result, new MasterDataTransformer())->toArray();
        return $this->respondSuccess($response);
    }

    /**
     *
     * @OA\Get(
     *      path="/api/master-data/list-multi-key",
     *      operationId="getListMultiKeyMasterData",
     *      tags={"Master data"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list of master data",
     *      description="Get list of master data",
     *      @OA\Parameter(
     *           name="keys[]",
     *           in="query",
     *           description="keys",
     *           required=true,
     *           @OA\Schema(
     *              type="array",
     *              @OA\Items(type="string"),
     *           ),
     *              style="form",
     *              explode="true",
     *              example="keys[]=position&$keys[]=contract-type",
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
    function getListMultiKeyMasterData(GetListMultiKeyMasterDataRequest $request)
    {
        $tables = $request->keys;
        $result = [];
        foreach ($tables as $table) {
            if (!($result[$table] ?? false)){
                $result[$table] = fractal($this->repos[$table]->getData(), new MasterDataTransformer(), new ArraySerializer());
            }
        }
        return $this->respondSuccess(['data' => $result]);
    }
}

