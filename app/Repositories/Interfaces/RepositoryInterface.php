<?php

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    /**
     * Get all
     */
    public function getAll();

    /**
     * Get one
     * @param $id
     * @param bool $checkSoftDelete
     */

    public function find($id, bool $checkSoftDelete = false);

    /**
     * Create
     * @param array $attributes
     */
    public function create(array $attributes);

    /**
     * Update
     * @param $id
     * @param array $attributes
     */
    public function update($id, array $attributes);

    /**
     * Delete
     * @param $id
     */
    public function delete($id);

    /**
     * get data by params
     * @param array $params
     * Example array ['key' => ['value', operator(>, <, =, like)]]
     */
    public function getByParams(array $params, string $orderBy = 'id', string $order = 'ASC', bool $checkSoftDelete = false);

    /**
     * restore deleted record
     * @param $id
     */
    public function restore($id);

    /**
     * Pagination
     * @param int $perPage
     */
    public function paginate(int $perPage = DEFAULT_RECORDS_PER_PAGE);

    /**
     * @param array  $columns
     * @param array  $conditions
     * @param array $orderBy
     * @param int    $perPage
     * @param bool   $withTrashed
     *
     * @return mixed
     */
    public function getData(
        array $columns = ['*'],
        array $conditions = [],
        array $orderBy = ['created_at' => 'desc'],
        int $perPage = GET_ALL_ITEMS,
        bool $withTrashed = false
    ): mixed;

    /**
     * get first by condition
     * @param array $conditions
     * @param array $columns
     */
    public function getFirstRow(array $conditions, array $columns = ['*'], bool $withTrashed = false);

    /**
     * @param $data
     *
     * @return mixed
     */
    public function bulkInsert($data): mixed;

    /**
     * @param $data
     *
     * @return mixed
     */
    public function bulkCreate($data): array;
}
