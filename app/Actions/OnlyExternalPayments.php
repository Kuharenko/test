<?php

namespace App\Actions;

use App\Contracts\Selector;
use App\Models\PaymentType;

class OnlyExternalPayments extends AbstractAction
{
    public function apply(Selector $selector)
    {
        $buttons = [];
        /**
         * @var PaymentType $button
         */
        foreach ($selector->getButtons() as $button) {
            if ($button->getType() !== 'wallet') {
                $buttons[] = $button;
            }
        }

        $selector->setButtons($buttons);
    }
}
