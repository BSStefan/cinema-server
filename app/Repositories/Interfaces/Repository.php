<?php

namespace App\Repositories\Interfaces;

interface Repository
{
    public function all(array $columns);

    public function paginate(int $perPage, array $columns);

    public function save(array $data, int $id);

    public function delete(int $id);

    public function find(int $id, array $columns);

    public function findBy(string $field, string $value, array $columns);

    public function count();

    public function whereIn(array $array);
}