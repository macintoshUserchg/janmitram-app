<?php

namespace App\Repositories;

use App\Models\ShopUser;
use App\Support\Repositories\Repository;

class ShopUserRepository extends Repository
{
    public static function model()
    {
        return ShopUser::class;
    }
}
