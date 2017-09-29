<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\Repository as RepositoryInterface;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;

abstract class BasicRepository implements RepositoryInterface
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $modelClass = null;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * @return Model
     * @throws \Exception
     */
    public function makeModel() : Model
    {
        if(!$this->modelClass) {
            throw new \Exception('Model class must be set');
        }
        $model = $this->app->make($this->modelClass);
        if(!$model instanceof Model) {
            throw new \Exception(
                'Class ' .$this->modelClass. ' must be an instance of Illuminate\\Databse\\Eloquent\\MOdel'
            );
        }
        return $this->model = $model;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all(array $columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate(int $perPage = 10, array $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function save(array $data, int $id = 0)
    {
        $item = $this->makeModel();
        if($id) {
            $item = $this->model->find($id);
        }
        foreach ($data as $k => $v) {
            $item[$k] = $v;
        }
        if($item->save()) {
            return $item;
        }
        return null;
    }

    /**
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function find(int $id, array $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @param array $columns
     * @return mixed
     */
    public function findBy(string $field, string $value, array $columns = ['*'])
    {
        return $this->model->where($field, '=', $value)->firstOrFail($columns);
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function delete(int $id)
    {
        return $this->find($id)->delete();
    }

    /**
     * @param string $field
     * @param string $value
     * @param array $columns
     * @return mixed
     */
    public function findWhere(string $field, string $value, array $columns = ['*'])
    {
        return $this->model->where($field, '=', $value)->get($columns);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return $this->model->all()->count();
    }

    /**
     * @param array $array
     * @return mixed
     */
    public function whereIn(array $array)
    {
        return $this->model->whereIn('id', $array)->get();
    }
}