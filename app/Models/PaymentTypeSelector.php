<?php

namespace App\Models;

use App\Actions\CardFirst;
use App\Actions\ClearAll;
use App\Contracts\Action;
use App\Contracts\Selector;

class PaymentTypeSelector implements Selector
{
    public $productType;
    public $amount;
    public $lang;
    public $countryCode;
    public $userOs;

    private $buttons = [];
    private $actions = [];

    public function __construct($productType = null, $amount = null, $lang = null, $countryCode = null, $userOs = null)
    {
        $this->productType = $productType;
        $this->amount = $amount;
        $this->lang = $lang;
        $this->countryCode = $countryCode;
        $this->userOs = $userOs;
    }

    public function generateButtons(): array
    {
        /**
         * @var PaymentSystem $system
         * @var Action $action
         * @var PaymentType $paymentType
         **/

        $systems = PaymentSystem::getAll();

        foreach ($systems as $system) {
            foreach ($system->paymentTypes() as $paymentType) {
                $paymentType->checkDisplayConditions($this);
            }
        }

        foreach ($this->buttons as $paymentType) {
            $paymentType->collectActions($this);
        }

        $this->addPaymentAction(new CardFirst()); // сортируем способы оплаты уже в конце
        foreach ($this->actions as $action) {
            $action->apply($this);
        }

        return $this->getFormattedResult();
    }

    public function addPaymentType(PaymentType $paymentType)
    {
        $this->buttons[] = $paymentType;
    }

    public function addPaymentAction(Action $action)
    {
        $this->actions[] = $action;
    }

    public function setButtons(array $buttons = [])
    {
        $this->buttons = $buttons;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function getFormattedResult(): array
    {
        /**
         * @var PaymentType $button
         */
        $result = [];
        foreach ($this->buttons as $button) {
            $result[] = $button->toArray();
        }
        return $result;
    }
}
