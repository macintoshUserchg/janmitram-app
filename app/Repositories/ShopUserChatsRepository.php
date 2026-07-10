<?php

namespace App\Repositories;

use App\Models\ShopUserChats;
use App\Support\Repositories\Repository;

class ShopUserChatsRepository extends Repository
{
    public static function model()
    {
        return ShopUserChats::class;
    }
}
