<?php
return [
    [
        "ID" => 1,
        "PaymentSystemID" => 1,
        "Name" => "Включенный способ оплаты",
        "Commission" => 2.5,
        "PayUrl" => "/pay/123",
        "ImageUrl" => "enabled_cards.jpg",
        "Order" => 1,
        "IsActive" => true,
        "Type" => "card",
        "DisplayConditions" => [],
        "ActionConditions" => []
    ],
    [
        "ID" => 2,
        "PaymentSystemID" => 1,
        "Name" => "Выключенный способ оплаты",
        "Commission" => 2.5,
        "PayUrl" => "/pay/123",
        "ImageUrl" => "disabled_cards.jpg",
        "Order" => 2,
        "IsActive" => false,
        "Type" => "card",
        "DisplayConditions" => [],
        "ActionConditions" => []
    ],
    [
        "ID" => 3,
        "PaymentSystemID" => 1,
        "Name" => "Доступна только в UA",
        "Commission" => 2.5,
        "PayUrl" => "/pay/123",
        "ImageUrl" => "cards.jpg",
        "Order" => 3,
        "IsActive" => true,
        "Type" => "card",
        "DisplayConditions" => [
            'Available' => ['countryCode' => 'UA'],
        ],
        "ActionConditions" => []
    ],
    [
        "ID" => 4,
        "PaymentSystemID" => 1,
        "Name" => "Доступна везде, кроме UA",
        "Commission" => 2.5,
        "PayUrl" => "/pay/123",
        "ImageUrl" => "cards.jpg",
        "Order" => 4,
        "IsActive" => true,
        "Type" => "card",
        "DisplayConditions" => [
            'NotAvailable' => ['countryCode' => 'UA'],
        ],
        "ActionConditions" => []
    ],
    [
        "ID" => 5,
        "PaymentSystemID" => 1,
        "Name" => "Paypal",
        "Commission" => 5,
        "PayUrl" => "/pay/123",
        "ImageUrl" => "paypal.jpg",
        "Order" => 1,
        "IsActive" => true,
        "Type" => "card",
        "DisplayConditions" => [
            'NotAvailable' => ['lang' => 'ru', 'amount' => ['less' => 30]],
        ],
        "ActionConditions" => []
    ],
    [
        "ID" => 6,
        "PaymentSystemID" => 1,
        "Name" => "Internal Wallet",
        "Commission" => 5,
        "PayUrl" => "/pay/123",
        "ImageUrl" => "paypal.jpg",
        "Order" => 7,
        "IsActive" => true,
        "Type" => "wallet",
        "DisplayConditions" => [],
        "ActionConditions" => [
            'onlyInternalWallet' => ['lang' => 'ru', 'productType' => 'Награда', 'amount' => ['less' => 10]]
        ]
    ],
    [
        "ID" => 6,
        "PaymentSystemID" => 1,
        "Name" => "Google Pay",
        "Commission" => 5,
        "PayUrl" => "/google-pay/123",
        "ImageUrl" => "google-pay.jpg",
        "Order" => 7,
        "IsActive" => true,
        "Type" => "card",
        "DisplayConditions" => [
            'Available' => ['userOs' => 'android'],
            'NotAvailable' => ['countryCode' => 'IN'],
        ],
        "ActionConditions" => []
    ],
    [
        "ID" => 7,
        "PaymentSystemID" => 1,
        "Name" => "Apple Pay",
        "Commission" => 5,
        "PayUrl" => "/apple-pay/123",
        "ImageUrl" => "apple-pay.jpg",
        "Order" => 7,
        "IsActive" => true,
        "Type" => "card",
        "DisplayConditions" => [
            'Available' => ['userOs' => 'iOS'],
        ],
        "ActionConditions" => []
    ],
    [
        "ID" => 8,
        "PaymentSystemID" => 1,
        "Name" => "Оплата Банковскими картами",
        "Commission" => 10,
        "PayUrl" => "/card-pay/123",
        "ImageUrl" => "card-pay.jpg",
        "Order" => 7,
        "IsActive" => true,
        "Type" => "card",
        "DisplayConditions" => [],
        "ActionConditions" => [
            'enablePrivatBank' => ['countryCode' => 'UA']
        ]
    ],
    [
        "ID" => 9,
        "PaymentSystemID" => 1,
        "Name" => "Пополнение кошелька",
        "Commission" => 0,
        "PayUrl" => "/refill/123",
        "ImageUrl" => "refill.jpg",
        "Order" => 7,
        "IsActive" => true,
        "Type" => "wallet",
        "DisplayConditions" => [],
        "ActionConditions" => [
            'onlyExternalPayments' => ['productType' => 'Пополнение кошелька']
        ]
    ],
];
