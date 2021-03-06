<?php

namespace App\Models;

use App\Contracts\Action;
use App\Contracts\Selector;

class PaymentType
{
    private int $ID;
    private int $PaymentSystemID;
    private string $Name;
    private float $Commission;
    private string $PayUrl;
    private string $ImageUrl;
    private int $Order;
    private bool $IsActive;
    private string $Type; // карта, терминал, кошелёк
    private array $DisplayConditions;
    private array $ActionConditions;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists(self::class, $key)) {
                $this->$key = $value;
            }
        }
    }

    public static function findAllByID($ID): array
    {
        return array_filter(self::getAll(), function (PaymentType $item) use ($ID) {
            return $item->PaymentSystemID === $ID;
        });
    }

    public function checkDisplayConditions(Selector $selector)
    {
        $conditions = $this->DisplayConditions;

        if (isset($conditions['Available']) && isset($conditions['NotAvailable'])) {
            if ($this->checkAvailability($selector, $conditions) && !$this->checkUnavailability($selector, $conditions)) {
                $selector->addPaymentType($this);
            }
        } elseif (isset($conditions['Available']) && !isset($conditions['NotAvailable'])) {
            if ($this->checkAvailability($selector, $conditions)) {
                $selector->addPaymentType($this);
            }
        } elseif (!isset($conditions['Available']) && isset($conditions['NotAvailable'])) {
            if (!$this->checkUnavailability($selector, $conditions)) {
                $selector->addPaymentType($this);
            }
        } else {
            $selector->addPaymentType($this);
        }
    }

    public function checkAvailability(Selector $selector, $conditions): bool
    {
        $enabled = true;
        $type = 'Available';
        if (isset($conditions[$type])) {
            foreach ($conditions[$type] as $condition => $value) {
                switch (gettype($value)) {
                    case 'array':
                        if (isset($selector->$condition)) {
                            $operation = array_keys($value)[0];
                            $operationValue = $value[$operation];

                            switch ($operation) {
                                case 'in':
                                    $enabled = in_array($selector->$condition, $operationValue);
                                    break;
                                case 'not_in':
                                    $enabled = !in_array($selector->$condition, $operationValue);
                                    break;
                                case 'less':
                                    $enabled = $selector->$condition < $operationValue;
                                    break;
                                case 'equal':
                                    $enabled = $selector->$condition == $operationValue;
                                    break;
                                // todo: add other operations
                            }
                        } else {
                            $enabled = false;
                        }
                        break;
                    case 'string':
                        if (isset($selector->$condition) && $value === $selector->$condition) {
                            $enabled = true;
                        } else {
                          $enabled = false;
                        }
                        break;
                }
            }
        }

        return $enabled;
    }

    public function checkUnavailability(Selector $selector, $conditions): bool
    {
        $enabled = false;
        $type = 'NotAvailable';
        if (isset($conditions[$type])) {
            foreach ($conditions[$type] as $condition => $value) {
                switch (gettype($value)) {
                    case 'array':
                        if (isset($selector->$condition)) {
                            $operation = array_keys($value)[0];
                            $operationValue = $value[$operation];

                            switch ($operation) {
                                case 'in':
                                    $enabled = in_array($selector->$condition, $operationValue);
                                    break;
                                case 'not_in':
                                    $enabled = !in_array($selector->$condition, $operationValue);
                                    break;
                                case 'less':
                                    $enabled = $selector->$condition < $operationValue;
                                    break;
                                case 'equal':
                                    $enabled = $selector->$condition == $operationValue;
                                    break;
                                // todo: add other operations
                            }
                        } else {
                            $enabled = true;
                        }
                        break;
                    case 'string':
                        if (isset($selector->$condition) && $value === $selector->$condition) {
                            $enabled = true;
                        } else {
                            $enabled = false;
                        }
                        break;
                }
            }
        }

        return $enabled;
    }

    public function collectActions(Selector $selector)
    {
        if ($this->ActionConditions) {
            foreach ($this->ActionConditions as $class => $conditions) {
                if ($this->checkActionConditions($selector, $conditions)) {
                    $className = 'App\\Actions\\' . ucfirst($class);
                    if (class_exists($className)) {
                        $selector->addPaymentAction((new $className())->joinPaymentType($this));
                    }
                }
            }
        }
    }

    public function checkActionConditions(Selector $selector, array $conditions): bool
    {
        $canBeApply = false;
        foreach ($conditions as $condition => $value) {
            switch (gettype($value)) {
                case 'array':
                    if (isset($selector->$condition)) {
                        $operation = array_keys($value)[0];
                        $operationValue = $value[$operation];

                        switch ($operation) {
                            case 'in':
                                $canBeApply = in_array($selector->$condition, $operationValue);
                                break;
                            case 'not_in':
                                $canBeApply = !in_array($selector->$condition, $operationValue);
                                break;
                            case 'less':
                                $canBeApply = $selector->$condition < $operationValue;
                                break;
                            case 'equal':
                                $canBeApply = $selector->$condition == $operationValue;
                                break;
                            // todo: add other operations
                        }
                    } else {
                        return false;
                    }
                    break;
                case 'string':
                    if (isset($selector->$condition) && $value == $selector->$condition) {
                        $canBeApply = true;
                    } else {
                        return false;
                    }
                    break;
            }
        }

        return $canBeApply;
    }

    public function getType()
    {
        return $this->Type;
    }

    public function getOrder() : int
    {
        return $this->Order;
    }

    public function modify($attributes = []) {
        foreach ($attributes as $attribute => $value) {
            if (isset($this->$attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    public function toArray()
    {
        return [
            'name' => $this->Name,
            'comission' => $this->Commission,
            'imageUrl' => $this->ImageUrl,
            'payUrl' => $this->PayUrl
        ];
    }

    // emulate database
    public static function getAll(): array
    {
        $items = include './app/Database/paymentTypeTable.php';

        return array_filter(array_map(function ($attributes) {
            if ($attributes['IsActive']) {
                return new PaymentType($attributes);
            }
            return  null;
        }, $items));
    }
}
