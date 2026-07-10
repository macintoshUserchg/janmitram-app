<?php

namespace App\Repositories;

use App\Models\LegalPage;
use App\Support\Repositories\Repository;

class LegalPageRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return LegalPage::class;
    }
}
