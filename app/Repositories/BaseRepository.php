<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $_model;

    /**
     * EloquentRepository constructor.
     *
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * get model
     *
     * @return string
     */
    abstract public function getModel(): string;

    /**
     * Set model
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function setModel(): void
    {
        $this->_model = app()->make(
            $this->getModel()
        );
    }

    /**
     * Get All
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->_model->all();
    }

    /**
     * Get one
     *
     * @param      $id
     * @param bool $checkSoftDelete
     *
     * @return mixed
     */
    public function find($id, bool $checkSoftDelete = false): mixed
    {
        return $checkSoftDelete ? $this->_model->withTrashed()->find($id) : $this->_model->find($id);
    }

    /**
     * Create
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes): mixed
    {
        return $this->_model->create($attributes);
    }

    /**
     * Update
     *
     * @param       $id
     * @param array $attributes
     *
     * @return bool|mixed
     */
    public function update($id, array $attributes): mixed
    {
        $response = $this->find($id);
        if ($response) {
            $response->update($attributes);
            return $response;
        }
        return false;
    }

    /**
     * Delete
     *
     * @param $id
     *
     * @return bool
     */
    public function delete($id): bool
    {
        $response = $this->find($id);
        if ($response) {
            $response->delete();
            return true;
        }
        return false;
    }

    /**
     *  Get data by params
     *
     * @param array   $params
     * @param string  $orderBy
     * @param string  $order
     * @param boolean $checkSoftDelete
     *
     * @return mixed
     */
    public function getByParams(array $params = [], string $orderBy = 'id', string $order = 'ASC', bool $checkSoftDelete = false): mixed
    {
        $operators = ['=', '>', '<', 'like'];
        $response = $this->_model;
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    $operator = $value[1] ?? '';
                    $val = $value[0] ?? '';
                    if (in_array($operator, $operators)) {
                        if ($val != '') {
                            if ($operator == 'like') {
                                $response = $response->where($key, 'LIKE', "%{$val}%");
                            } else {
                                $response = $response->where($key, $operator, $val);
                            }
                        }
                    }
                } else {
                    $response = $response->where($key, $value);
                }
            }
        }
        if ($checkSoftDelete) {
            $response = $response->withTrashed();
        }

        return $response->orderBy($orderBy, $order)->get();
    }

    /**
     *  Restore soft delete
     *
     * @param $id
     *
     * @return mixed
     */
    public function restore($id): mixed
    {
        $response = $this->find($id, true);
        if ($response) {
            $response->restore();
            return $response;
        }
        return false;
    }


    /**
     * Pagination
     *
     * @param int $perPage
     */
    public function paginate(int $perPage = DEFAULT_RECORDS_PER_PAGE)
    {
        return $this->_model->paginate($perPage);
    }

    /**
     * get data with query
     * conditions like:
     * b is =, >, <, like,...
     * ['a', 'b', 'c']
     * [['a', 'b', 'c'], ['a', 'b', 'c']]
     * equal
     * ['a' => 'b']
     * ['a' => 'b', 'c' => 'd']
     *
     * @param array  $columns
     * @param array  $conditions
     * @param string $orderBy
     * @param int    $perPage
     * @param bool   $softDelete
     *
     * @return mixed
     */
    public function getData(
        array $columns = ['*'],
        array $conditions = [],
        array $orderBy = ['created_at' => 'desc'],
        int $perPage = GET_ALL_ITEMS,
        bool $withTrashed = false
    ): mixed
    {
        $query = $this->_model->select($columns);
        if ($withTrashed) {
            $query = $query->withTrashed();
        }

        $query = $this->checkCondition($query, $conditions);
        foreach ($orderBy as $key => $value) {
            $query = $query->orderBy($key, $value);
        }

        if ($perPage !== GET_ALL_ITEMS) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    /**
     * conditions like:
     * b is =, >, <, like,...
     * ['a', 'b', 'c']
     * [['a', 'b', 'c'], ['a', 'b', 'c']]
     * equal
     * ['a' => 'b']
     * ['a' => 'b', 'c' => 'd']
     * @param array $conditions
     * @param array $columns
     *
     * @return mixed
     */
    public function getFirstRow(array $conditions, array $columns = ['*'], bool $withTrashed = false): mixed
    {
        $query = $this->_model;
        $query = $this->checkCondition($query, $conditions);
        if ($withTrashed) {
            $query = $query->withTrashed();
        }
        return $query->select($columns)->first();
    }

    /**
     * @param $query
     * @param $conditions
     *
     * @return mixed
     */
    private function checkCondition($query, $conditions): mixed
    {
        if (!empty($conditions)) {
            foreach ($conditions as $key => $condition) {
                if (is_array($condition)) {
                    $query = $this->checkArrayCondition($query, $condition);
                } else if (!is_numeric($key)) {
                    $query = $query->where($key, $condition);
                } else {
                    $query = $this->checkArrayCondition($query, $conditions);
                    break;
                }
            }
        }
        return $query;
    }

    /**
     * @param $query
     * @param $condition
     *
     * @return mixed
     */
    private function checkArrayCondition($query, $condition): mixed
    {
        if (count($condition) === 2) {
            $query = $query->where($condition[0], $condition[1]);
        }
        if (count($condition) === 3) {
            $query = $query->where($condition[0], $condition[1], $condition[2]);
        }
        return $query;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function bulkInsert($data): mixed
    {
        return $this->_model->insert($data);
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function bulkCreate($data): array
    {
        $item = new $this->_model;
        $response = [];
        foreach ($data as $datum) {
            $item->fill($datum);
            $item->save();
            $response[] = $item;
        }
        return $response;
    }
}
