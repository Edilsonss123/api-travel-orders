<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    protected Model $model;
    public function __construct()
    {
        $this->model = $this->model();
    }
    abstract protected function model(): Model;
}
