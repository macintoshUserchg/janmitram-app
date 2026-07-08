<?php

namespace App\Repositories;

use App\Support\Repositories\Repository;
use App\Models\LegalPage;

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
