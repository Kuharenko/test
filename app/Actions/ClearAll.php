<?php

namespace App\Actions;

use App\Contracts\Selector;

class ClearAll extends AbstractAction
{
    public function apply(Selector $selector)
    {
        $selector->setButtons([]);
    }
}
