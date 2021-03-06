<?php

namespace App\Actions;

use App\Contracts\Selector;

class CardFirst extends AbstractAction
{
    public function apply(Selector $selector)
    {
        $buttons = $selector->getButtons();

        $cards = [];
        $wallets = [];
        $terminals = [];
        foreach ($buttons as $button) {
            switch ($button->getType()) {
                case 'card':
                    $cards[] = $button;
                    break;
                case 'wallet':
                    $wallets[] = $button;
                    break;
                case 'terminal':
                    $terminals[] = $button;
                    break;
            }
        }

        usort($cards, function ($a, $b) {
            if ($a->getOrder() == $b->getOrder()) { return 0;}
            return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
        });

        usort($wallets, function ($a, $b) {
            if ($a->getOrder() == $b->getOrder()) { return 0;}
            return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
        });

        usort($terminals, function ($a, $b) {
            if ($a->getOrder() == $b->getOrder()) { return 0;}
            return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
        });

        $selector->setButtons(array_merge($cards, $wallets, $terminals));
    }
}
