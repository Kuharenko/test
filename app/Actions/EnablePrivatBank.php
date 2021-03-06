<?php

namespace App\Actions;

use App\Contracts\Selector;

class EnablePrivatBank extends AbstractAction
{
    public function apply(Selector $selector)
    {
        $buttonInArray = false;
        foreach ($selector->getButtons() as $button) {
            if($button->getId() == $this->getPaymentType()->getId()) {
                $buttonInArray = true;
            }
        }

        if ($buttonInArray) {
            $privatBankCard = clone $this->getPaymentType();
            $privatBankCard->modify([
                "Name" => "Оплата картой ПриватБанка",
                "ImageUrl" => "privat-bank-pay.jpg"
            ]);

            $selector->setButtons(array_merge($selector->getButtons(), [$privatBankCard]));
        }
    }
}
