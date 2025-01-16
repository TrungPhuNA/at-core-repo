<?php
/**
 * Created By PhpStorm
 * Code By : trungphuna
 * Date: 1/16/25
 */

namespace AtCore\CoreRepo\Repositories\Eloquent;

use AtCore\CoreRepo\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Lấy toàn bộ bản ghi (ko phân trang)
     */
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    /**
     * @param  array  $filters
     * @param  int  $perPage
     * @param $columns
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters = [], $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->model->query();

        $page = isset($filters['page']) ? (int) $filters['page'] : 1;
        $pageSize = isset($filters['page_size']) ? (int) $filters['page_size'] : 10;

        // Áp dụng các filters trong `filters`
        if (isset($filters['filters']) && is_array($filters['filters'])) {
            $query = $this->applyFilters($query, $filters['filters']);
        }

        return $query->orderBy('id', 'DESC')->paginate($pageSize, $columns, 'page', $page);
    }

    /**
     * Tìm 1 record theo ID
     */
    public function find($id, $relations = [], $columns = ['*'])
    {
        $query = $this->model->newQuery();
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->find($id, $columns);
    }

    public function findOrFail($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Tạo mới
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Cập nhật
     */
    public function update($id, array $attributes)
    {
        $instance = $this->find($id);
        $instance->update($attributes);
        return $instance;
    }

    /**
     * Xoá
     */
    public function delete($id)
    {
        $instance = $this->find($id);
        return $instance->delete();
    }

    protected function applyFilters($query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if (is_null($value) || $value === '') {
                continue;
            }

            switch (true) {
                // Search LIKE
                case str_ends_with($key, '_like'):
                    $query->whereRaw("LOWER({$this->cleanColumnName($key)}) LIKE ?", ['%'.strtolower($value).'%']);
                    break;

                // Search WHERE IN
                case str_ends_with($key, '_in'):
                    $query->whereIn($this->cleanColumnName($key), (array) $value);
                    break;

                // Search bằng ngày
                case str_ends_with($key, '_date'):
                    $query->whereDate($this->cleanColumnName($key), '=', $value);
                    break;

                // Search BETWEEN
                case str_ends_with($key, '_between'):
                    if (isset($value['start']) && isset($value['end'])) {
                        $query->whereBetween($this->cleanColumnName($key), [$value['start'], $value['end']]);
                    }
                    break;

                // Search mặc định (EQUAL)
                default:
                    $query->where($this->cleanColumnName($key), '=', $value);
            }
        }

        return $query;
    }

    /**
     * Làm sạch tên cột để tránh lỗi SQL injection
     */
    protected function cleanColumnName($key)
    {
        return preg_replace('/(_like|_in|_date|_between)$/', '', $key);
    }
}