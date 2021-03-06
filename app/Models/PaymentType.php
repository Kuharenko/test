<?php

namespace App\Models;

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
            if ($this->checkConditions($selector, $conditions) && !$this->checkConditions($selector, $conditions, false)) {
                $selector->addPaymentType($this);
            }
        } elseif (isset($conditions['Available']) && !isset($conditions['NotAvailable'])) {
            if ($this->checkConditions($selector, $conditions)) {
                $selector->addPaymentType($this);
            }
        } elseif (!isset($conditions['Available']) && isset($conditions['NotAvailable'])) {
            if (!$this->checkConditions($selector, $conditions, false)) {
                $selector->addPaymentType($this);
            }
        } else {
            $selector->addPaymentType($this);
        }
    }

    public function checkConditions(Selector $selector, $conditions, $isAvailability = true): bool
    {
        $checkingResults = [];

        $type = $isAvailability ? 'Available' : 'NotAvailable';

        if (isset($conditions[$type])) {
            foreach ($conditions[$type] as $condition => $value) {
                $checkingResults[$condition] = false;

                switch (gettype($value)) {
                    case 'array':
                        if (isset($selector->$condition)) {
                            $operation = array_keys($value)[0];
                            $operationValue = $value[$operation];

                            switch ($operation) {
                                case 'in':
                                    $checkingResults[$condition] = in_array($selector->$condition, $operationValue);
                                    break;
                                case 'not_in':
                                    $checkingResults[$condition] = !in_array($selector->$condition, $operationValue);
                                    break;
                                case 'less':
                                    $checkingResults[$condition] = $selector->$condition < $operationValue;
                                    break;
                                case 'equal':
                                    $checkingResults[$condition] = $selector->$condition == $operationValue;
                                    break;
                                // todo: add other operations
                            }
                        } else {
                            $checkingResults[$condition] = $isAvailability;
                        }
                        break;
                    case 'string':
                        $checkingResults[$condition] = isset($selector->$condition) && $value === $selector->$condition;
                        break;
                }
            }
        }


        $status = true;
        foreach ($checkingResults as $condition => $value) {
            $status = $status && $value;
        }

        return $status;
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

    public function getOrder(): int
    {
        return $this->Order;
    }

    public function getId(): int
    {
        return $this->ID;
    }

    public function modify($attributes = [])
    {
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
            return null;
        }, $items));
    }
}
