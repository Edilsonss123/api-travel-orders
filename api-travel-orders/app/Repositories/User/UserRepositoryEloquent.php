<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class UserRepositoryEloquent extends Repository implements IUserRepository
{
    protected function model(): Model
    {
        return new User();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }
}
