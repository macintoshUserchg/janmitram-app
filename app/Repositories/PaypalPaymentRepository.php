<?php

namespace App\Repositories;

use App\Models\PaypalPayment;
use App\Support\Repositories\Repository;

class PaypalPaymentRepository extends Repository
{
    public static function model()
    {
        return PaypalPayment::class;
    }
}
