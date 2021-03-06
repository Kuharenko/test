<?php

namespace App\Contracts;

use App\Models\PaymentType;

interface Action
{
    public function apply(Selector $selector);
    public function joinPaymentType(PaymentType $paymentType);
    public function getPaymentType();
}
