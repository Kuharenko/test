<?php

namespace App\Actions;

use App\Contracts\Selector;
use App\Models\PaymentType;

class EnablePrivatBank extends AbstractAction
{
    public function apply(Selector $selector)
    {
        $privatBankCard = clone $this->getPaymentType();
        $privatBankCard->modify([
            "Name" => "Оплата картой ПриватБанка",
            "ImageUrl" => "privat-bank-pay.jpg"
        ]);

        $selector->setButtons(array_merge($selector->getButtons(), [$privatBankCard]));
    }
}
