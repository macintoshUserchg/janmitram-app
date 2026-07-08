<?php
namespace App\Repositories;

use App\Support\Repositories\Repository;
use App\Models\ShopUserChats;

class ShopUserChatsRepository extends Repository
{
    public static function model()
    {
        return ShopUserChats::class;    
    }
}