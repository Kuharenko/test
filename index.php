<?php

require_once('vendor/autoload.php');

use App\Models\PaymentTypeSelector;

// Входные параметры, которые откуда-то приходят, неважно откуда
$productType        = $_GET['productType'] ?? null;     // book | reward | walletRefill (пополнение внутреннего кошелька)
$amount             = $_GET['amount'] ?? null;          // any float > 0
$lang               = $_GET['lang'] ?? null;            // only ru | en | es | uk
$countryCode        = $_GET['countryCode'] ?? null;     // any country code in ISO-3166 format
$userOs             = $_GET['userOs'] ?? null;          // android | ios | windows
$enableYooMoney     = $_GET['enableYooMoney'] ?? false; // true | false

// Вам нужно сделать логику класса PaymentTypeSelector (можете назвать иначе, если хотите)
$paymentTypeSelector = new PaymentTypeSelector($productType, $amount, $lang, $countryCode, $userOs);

if ($enableYooMoney) {
    $paymentTypeSelector->addPaymentAction(new \App\Actions\ClearAll()); // вызываем нужное нам действие, к примеру очистить кнопки
}

$paymentButtons = $paymentTypeSelector->generateButtons();

echo json_encode($paymentButtons);
die();
