<?php

namespace App\Repositories;

use App\Models\BlogView;
use App\Support\Repositories\Repository;

class BlogViewRepository extends Repository
{
    public static function model()
    {
        return BlogView::class;
    }
}
