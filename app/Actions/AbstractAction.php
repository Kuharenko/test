<?php

namespace App\Actions;

use App\Contracts\Action;
use App\Contracts\Selector;
use App\Models\PaymentType;

abstract class AbstractAction implements Action
{
    protected $paymentType;

    public function joinPaymentType(PaymentType $paymentType)
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    public function getPaymentType()
    {
        return $this->paymentType;
    }
}
