<?php

namespace App\Models;


class PaymentSystem
{

    private int $ID;
    private string $Name;
    private bool $IsActive;
    private array $Settings;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists(self::class, $key)) {
                $this->$key = $value;
            }
        }
    }

    public static function findOne($ID) : PaymentSystem
    {
        $elements = array_filter(self::getAll(), function (PaymentSystem $item) use ($ID) {
            return $item->ID === $ID;
        });

        return $elements[0];
    }

    public function paymentTypes(): array
    {
        return PaymentType::findAllByID($this->ID);
    }

    // emulate database
    public static function getAll(): array
    {
        $items = include './app/Database/paymentSystemTable.php';

        return array_filter(array_map(function ($attributes) {
            if ($attributes['IsActive']) {
                return new PaymentSystem($attributes);
            }
            return null;
        }, $items));
    }
}
