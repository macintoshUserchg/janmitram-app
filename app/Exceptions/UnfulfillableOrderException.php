<?php

namespace App\Exceptions;

use Exception;

class UnfulfillableOrderException extends Exception
{
    public array $unfulfillable;

    public function __construct(array $unfulfillable)
    {
        parent::__construct(__('Some items cannot be delivered to your area'));
        $this->unfulfillable = $unfulfillable;
    }
}
