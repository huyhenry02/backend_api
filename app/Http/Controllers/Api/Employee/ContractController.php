<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Employee\Repositories\Interfaces\ContractAllowanceInterface;
use App\Modules\Employee\Repositories\Interfaces\ContractInsuranceProcessedHistoryInterface;
use App\Modules\Employee\Repositories\Interfaces\ContractInterface;
use App\Modules\Employee\Repositories\Interfaces\ContractWorkingHistoryInterface;
use App\Modules\Employee\Requests\GetContractDetailRequest;
use App\Modules\Employee\Requests\UpdateContractRequest;
use App\Modules\Employee\Transformers\ContractTransformer;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use App\Modules\RawMediaUpload\Repositories\Interfaces\RawMediaUploadInterface;
use App\Traits\MediaTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ContractController extends ApiController
{
    use MediaTrait;

    private ContractInterface $contractRepo;
    private ContractWorkingHistoryInterface $contractWorkingHistoryRepo;
    private ContractAllowanceInterface $contractAllowanceRepo;
    protected ContractInsuranceProcessedHistoryInterface $contractInsuranceProcessedHistoryRepo;
    private array $contractCollectionNames = [
        RawMediaUploadCollectionConstants::CONTRACT_HEALTH_RECORDS,
        RawMediaUploadCollectionConstants::CONTRACT_FILES,
    ];
    private array $contractWorkingHistoryCollectionNames = [
        RawMediaUploadCollectionConstants::JOB_TRANSFER_PROOFS,
    ];

    public function __construct(
        ContractInterface                          $contractRepo,
        RawMediaUploadInterface                    $rawMediaUploadRepo,
        ContractWorkingHistoryInterface            $contractWorkingHistoryRepo,
        ContractInsuranceProcessedHistoryInterface $contractInsuranceProcessedHistoryRepo,
        ContractAllowanceInterface                 $contractAllowanceRepo,
    )
    {
        $this->contractRepo = $contractRepo;
        $this->contractWorkingHistoryRepo = $contractWorkingHistoryRepo;
        $this->contractAllowanceRepo = $contractAllowanceRepo;
        $this->contractInsuranceProcessedHistoryRepo = $contractInsuranceProcessedHistoryRepo;
    }

    /**
     * @OA\Get(
     *      path="/api/employee/contract/detail",
     *     tags={"Employee"},
     *     summary="get detail information of contract",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="employee_id",
     *         in="query",
     *         description="employee id",
     *         required=true,
     *         example="b8a4ff19-6723-4ae4-a07d-9be9ca3b5027",
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="include",
     *         example="department,position,employment,contract_type",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="get detail successfully",
     *       )
     * )
     */
    public function getContractDetail(GetContractDetailRequest $request): JsonResponse
    {
        $employeeId = $request->query('employee_id');
        $include = $request->validated('include');

        $contract = $this->contractRepo->getContractDetail($employeeId);
        if ($contract) {
            $contract['contract_files'] = $contract->getMedia(RawMediaUploadCollectionConstants::CONTRACT_FILES);
            $contract['contract_health_records'] = $contract->getMedia(RawMediaUploadCollectionConstants::CONTRACT_HEALTH_RECORDS);
        }
        $respContractData = fractal($contract, new ContractTransformer());
        if (!empty($include)) {
            $include = explode(',', $include);

            $respContractData->parseIncludes(array_map(static function ($item) {
                return $item;
            }, $include));
        }

        return $this->respondSuccess($respContractData->toArray());
    }

    /**
     *
     * @OA\PUT (
     *      path="/api/employee/contract/update",
     *      operationId="update",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Update Contract",
     *      description="Update Contract",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"id","contract_type_id", "contract_files", "contract_health_records"},
     *                  @OA\Property(
     *                      property="id",
     *                      type="uuid",
     *                      example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                  ),
     *                  @OA\Property(
     *                      property="contract_type_id",
     *                      type="uuid",
     *                      example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                  ),
     *                  @OA\Property(
     *                      property="contract_files",
     *                      type="uuid",
     *                      example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                  ),
     *                  @OA\Property(
     *                      property="department_id",
     *                      type="uuid",
     *                      example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                  ),
     *                  @OA\Property(
     *                      property="position_id",
     *                      type="uuid",
     *                      example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                  ),
     *                  @OA\Property(
     *                      property="function",
     *                      type="string",
     *                      example="funcion example"
     *                  ),
     *                  @OA\Property(
     *                      property="rank",
     *                      type="string",
     *                      example="rank example"
     *                  ),
     *                  @OA\Property(
     *                      property="skill_coefficient",
     *                      type="number",
     *                      example="1.3"
     *                  ),
     *                  @OA\Property(
     *                      property="workplace",
     *                      type="uuid",
     *                      example="824b1d21-50d2-44d6-9245-ae98ce29013a // id of cities table"
     *                  ),
     *                  @OA\Property(
     *                      property="employment_type_id",
     *                      type="uuid",
     *                      example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                  ),
     *                  @OA\Property(
     *                      property="effective_date",
     *                      type="date",
     *                      example="2000-10-12"
     *                  ),
     *                  @OA\Property(
     *                      property="signed_date",
     *                      type="date",
     *                      example="2000-10-12"
     *                  ),
     *                  @OA\Property(
     *                      property="signer",
     *                      type="string",
     *                      example="Nguyễn Văn A"
     *                  ),
     *                  @OA\Property(
     *                      property="digital_signature",
     *                      type="string",
     *                      example="active"
     *                  ),
     *                  @OA\Property(
     *                      property="apply_from_date",
     *                      type="date",
     *                      example="2000-10-12"
     *                  ),
     *                  @OA\Property(
     *                      property="note",
     *                      type="string",
     *                      example="note example"
     *                  ),
     *                  @OA\Property(
     *                      property="payment_type",
     *                      type="string",
     *                      example="payment type example"
     *                  ),
     *                  @OA\Property(
     *                      property="salary",
     *                      type="numeric",
     *                      example="1000000"
     *                  ),
     *                  @OA\Property(
     *                      property="insurance_book_number",
     *                      type="string",
     *                      example="125478AC"
     *                  ),
     *                  @OA\Property(
     *                      property="insurance_book_status",
     *                      type="string",
     *                      example="active"
     *                  ),
     *                  @OA\Property(
     *                      property="insurers",
     *                      type="string",
     *                      example="Nguyễn Văn B"
     *                  ),
     *                  @OA\Property(
     *                      property="insurance_card_number",
     *                      type="string",
     *                      example="125478AC"
     *                  ),
     *                  @OA\Property(
     *                      property="insurance_city_code",
     *                      type="string",
     *                      example="Hà Nội"
     *                  ),
     *                  @OA\Property(
     *                      property="medical_examination_place",
     *                      type="string",
     *                      example="Bệnh viện A"
     *                  ),
     *                  @OA\Property(
     *                      property="card_received_date",
     *                      type="date",
     *                      example="2000-10-12"
     *                  ),
     *                  @OA\Property(
     *                      property="card_returned_date",
     *                      type="date",
     *                      example="2000-10-12"
     *                  ),
     *                  @OA\Property(
     *                      property="contract_working_histories",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="id",
     *                              type="uuid",
     *                              example=""
     *                          ),
     *                          @OA\Property(
     *                              property="worked_from_date",
     *                              type="date",
     *                              example="2000-10-12"
     *                          ),
     *                          @OA\Property(
     *                              property="worked_to_date",
     *                              type="date",
     *                              example="2000-10-13"
     *                          ),
     *                         @OA\Property(
     *                              property="from_department",
     *                              type="uuid",
     *                              example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                          ),
     *                         @OA\Property(
     *                              property="to_department",
     *                              type="uuid",
     *                              example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                          ),
     *                         @OA\Property(
     *                              property="reason",
     *                              type="string",
     *                              example="reason example"
     *                          ),
     *                         @OA\Property(
     *                              property="is_deleted",
     *                              type="boolean",
     *                              example="true/false"
     *                          ),
     *                          @OA\Property(
     *                              property="media",
     *                              type="array",
     *                              @OA\Items(
     *                                 @OA\Property(
     *                                     property="new",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         @OA\Property(
     *                                             property="job_transfer_proofs",
     *                                             type="uuid",
     *                                             example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                             description="raw_media_id"
     *                                         ),
     *                                     ),
     *                                 ),
     *                                 @OA\Property(
     *                                     property="delete",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         @OA\Property(
     *                                             property="job_transfer_proofs",
     *                                             type="array",
     *                                             @OA\Items(
     *                                                 type="uuid",
     *                                                 example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                                 description="list delete media_ids"
     *                                             )
     *                                         ),
     *                                     ),
     *                                 ),
     *                              ),
     *                          ),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="contract_allowances",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="id",
     *                              type="uuid",
     *                              example=""
     *                          ),
     *                          @OA\Property(
     *                              property="allowance_id",
     *                              type="uuid",
     *                              example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                          ),
     *                          @OA\Property(
     *                              property="benefit",
     *                              type="numeric",
     *                              example="1000000"
     *                          ),
     *                          @OA\Property(
     *                              property="is_deleted",
     *                              type="boolean",
     *                              example="true/false"
     *                          ),
     *                          description="add new"
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                       property="contract_insurance_processed_histories",
     *                       type="array",
     *                       @OA\Items(
     *                           @OA\Property(
     *                               property="id",
     *                               type="uuid",
     *                               example="85ad7cc0-d3ae-419e-ab12-f6735050cac8"
     *                           ),
     *                           @OA\Property(
     *                               property="insurance_policy_id",
     *                               type="uuid",
     *                               example="85ad7cc0-d3ae-419e-ab12-f6735050cac8"
     *                           ),
     *                           @OA\Property(
     *                               property="refund_amount",
     *                               type="numeric",
     *                               example="190.999"
     *                           ),
     *                               @OA\Property(
     *                                property="completed_date",
     *                                type="date",
     *                                example="2020-10-12"
     *                            ),
     *                                @OA\Property(
     *                                property="received_date",
     *                                type="date",
     *                                example="2020-10-12"
     *                            ),
     *                                @OA\Property(
     *                                property="refunded_date",
     *                                type="date",
     *                                example="2020-10-12"
     *                            ),
     *                                @OA\Property(
     *                                property="is_deleted",
     *                                type="boolean",
     *                                example="true/false"
     *                            ),
     *                           description="add new"
     *                       ),
     *                   ),
     *                  @OA\Property(
     *                      property="media",
     *                      type="array",
     *                      @OA\Items(
     *                         @OA\Property(
     *                             property="new",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(
     *                                     property="contract_health_records",
     *                                     type="uuid",
     *                                     example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                     description="raw_media_id"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="contract_files",
     *                                     type="uuid",
     *                                     example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                     description="raw_media_id"
     *                                 ),
     *                             ),
     *                         ),
     *                         @OA\Property(
     *                             property="delete",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(
     *                                     property="contract_health_records",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="uuid",
     *                                         example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                         description="list delete media_ids"
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="contract_files",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="uuid",
     *                                         example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                         description="list delete media_ids"
     *                                     )
     *                                 ),
     *                             ),
     *                         ),
     *                     ),
     *                  ),
     *              ),
     *          ),
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
    public function updateContract(UpdateContractRequest $request): JsonResponse
    {
        $data = $request->validated();
        $updateContractWorkingHistory = [];
        $newContractWorkingHistory = [];

        DB::beginTransaction();
        try {
            $contract = $this->contractRepo->find($data['id']);
            $originalData = fractal($contract, new ContractTransformer())
                ->parseIncludes([
                    'contract_type',
                    'department',
                    'position',
                    'employment',
                ])->toArray();
            if (!empty($data['media'])) {
                checkChangeFileRequired($contract, $data['media'], $this->contractCollectionNames);
            }
            if (!empty($data['contract_working_histories'])) {
                foreach ($data['contract_working_histories'] as $contractWorkingHistory) {
                    if (!empty($contractWorkingHistory['id'])) {
                        $contractWorkingHistoryModel = $contract->contractWorkingHistories->find($contractWorkingHistory['id']);
                        $updateContractWorkingHistory[] = [
                            'data' => $contractWorkingHistory,
                            'model' => $contractWorkingHistoryModel
                        ];
                    } else {
                        $contractWorkingHistory['contract_id'] = $data['id'];
                        $newContractWorkingHistory[] = $contractWorkingHistory;
                    }
                }
            }
            $contract->fill($data);
            $contract->save();
            if (!empty($data['media'])) {
                $this->updateMedia($contract, $data['media'], $this->contractCollectionNames);
            }

            foreach ($newContractWorkingHistory as $item) {
                $workingHistory = $this->contractWorkingHistoryRepo->create($item);
                if (!empty($item['job_transfer_proofs'])) {
                    $this->moveRawMediaToMedia($item['job_transfer_proofs'], $workingHistory);
                }
            }

            $this->updateContractWorkingHistories($updateContractWorkingHistory);
            if (!empty($data['contract_allowances'])) {
                $this->updateContractAllowances($data['id'], $data['contract_allowances']);
            }
            if (!empty($data['contract_insurance_processed_histories'])) {
                $this->updateContractInsuranceProcessedHistories($data['id'], $data['contract_insurance_processed_histories']);
            }
            $contract = $this->contractRepo->find($data['id']);
            $respContractData = fractal($contract, new ContractTransformer())
                ->parseIncludes([
                    'contract_type',
                    'department',
                    'position',
                    'employment',
                ]);
            $newData = $respContractData->toArray();
            handleLogUpdateData($originalData, $newData, $contract);
            $response = $this->respondSuccess(['data' => $respContractData]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->respondError($e->getMessage() . ' - ' . $e->getLine() . ' - ' . $e->getFile());
        }
        return $response;
    }


    /**
     * @param array $workingHistories
     *
     * @return void
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    private function updateContractWorkingHistories(array $workingHistories): void
    {
        foreach ($workingHistories as $workingHistory) {
            if (!empty($workingHistory['data']['is_deleted'])) {
                $workingHistory['model']->delete();
            } else {
                $this->contractWorkingHistoryRepo->update($workingHistory['data']['id'], $workingHistory['data']);
                if (!empty($workingHistory['data']['media'])) {
                    $this->updateMedia($workingHistory['model'], $workingHistory['data']['media'], $this->contractWorkingHistoryCollectionNames);
                }
            }
        }
    }

    /**
     * @param string $contractId
     * @param array $contractAllowances
     *
     * @return void
     */
    private function updateContractAllowances(string $contractId, array $contractAllowances): void
    {
        foreach ($contractAllowances as $contractAllowance) {
            if (!empty($contractAllowance['id'])) {
                if ($contractAllowance['is_deleted']) {
                    $this->contractAllowanceRepo->delete($contractAllowance['id']);
                } else {
                    $this->contractAllowanceRepo->update($contractAllowance['id'], $contractAllowance);
                }
            } else {
                $contractAllowance['contract_id'] = $contractId;
                $this->contractAllowanceRepo->create($contractAllowance);
            }
        }
    }

    /**
     * @param string $contractId
     * @param array $contractInsuranceProcessedHistories
     * @return void
     */
    private function updateContractInsuranceProcessedHistories(string $contractId, array $contractInsuranceProcessedHistories): void
    {
        foreach ($contractInsuranceProcessedHistories as $contractInsuranceProcessedHistory) {
            if (!empty($contractInsuranceProcessedHistory['id'])) {
                if ($contractInsuranceProcessedHistory['is_deleted']) {
                    $this->contractInsuranceProcessedHistoryRepo->delete($contractInsuranceProcessedHistory['id']);
                } else {
                    $this->contractInsuranceProcessedHistoryRepo->update($contractInsuranceProcessedHistory['id'], $contractInsuranceProcessedHistory);
                }
            } else {
                $contractInsuranceProcessedHistory['contract_id'] = $contractId;
                $this->contractInsuranceProcessedHistoryRepo->create($contractInsuranceProcessedHistory);
            }
        }
    }
}
