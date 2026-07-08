<?php
namespace App\Repositories;

use App\Support\Repositories\Repository;
use App\Models\PaypalPayment;

class PaypalPaymentRepository extends Repository
{
    public static function model()
    {
        return PaypalPayment::class;    
    }
}