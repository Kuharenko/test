<?php

namespace App\Contracts;

interface Selector
{
    public function setButtons(array $data);
    public function getButtons() : array;
    public function generateButtons() : array;
    public function getFormattedResult() : array;
    public function addPaymentAction(Action $action);
}
