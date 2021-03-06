<?php

use App\Models\PaymentTypeSelector;
use PHPUnit\Framework\TestCase;

class PaymentTypeSelectorTest extends TestCase
{
    public function initialize($params): array
    {
        $productType = $params['productType'];  // book | reward | walletRefill (пополнение внутреннего кошелька)
        $amount = $params['amount'];            // any float > 0
        $lang = $params['lang'];                // only ru | en | es | uk
        $countryCode = $params['countryCode'];  // any country code in ISO-3166 format
        $userOs = $params['userOs'];            // android | ios | windows

        $paymentTypeSelector = new PaymentTypeSelector($productType, $amount, $lang, $countryCode, $userOs);
        return $paymentTypeSelector->generateButtons();
    }

    public function testEnablePrivatBankAction(): void
    {
        // создаём запись способа оплаты
        $paymentType = new \App\Models\PaymentType([
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
        ]);

        // инициализируем селектор
        $paymentTypeSelector = new PaymentTypeSelector();

        // создаём Action, связываем его со Способом Оплаты
        $action = new \App\Actions\EnablePrivatBank();
        $action->joinPaymentType($paymentType);

        // добавляем в список доступных кнопок
        $paymentTypeSelector->setButtons([$paymentType]);

        $this->assertEquals(1, count($paymentTypeSelector->getButtons()));

        // применяем Action
        $action->apply($paymentTypeSelector);

        $this->assertEquals(2, count($paymentTypeSelector->getButtons()));

        // проверяем совпадают ли ID
        $equalID = 0;
        foreach ($paymentTypeSelector->getButtons() as $button) {
            if ($button->getId() === 8) {
                $equalID++;
            }
        }

        $this->assertEquals(2, $equalID);
    }

    public function testSelectWorks(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'windows'
        ]);

        $this->assertGreaterThan(1, count($result));
    }

    public function testInCountyFilter(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'PL',
            'userOs' => 'windows'
        ]);

        $found = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Карты \"МИР\"") {
                $found++;
            }
        }

        $this->assertEquals(1, $found);
    }

    public function testNotInCountyFilter(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'KZ',
            'userOs' => 'windows'
        ]);

        $found = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Карты \"МИР\"") {
                $found++;
            }
        }

        $this->assertEquals(0, $found);
    }

    public function testPaypalNotAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'windows'
        ]);

        $paypalCount = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Paypal") {
                $paypalCount++;
            }
        }

        $this->assertEquals(0, $paypalCount);
    }

    public function testPaypalAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 30,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'windows'
        ]);

        $paypalCount = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Paypal") {
                $paypalCount++;
            }
        }

        $this->assertEquals(1, $paypalCount);

        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'uk',
            'countryCode' => 'RU',
            'userOs' => 'windows'
        ]);

        $paypalCount = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Paypal") {
                $paypalCount++;
            }
        }

        $this->assertEquals(1, $paypalCount);
    }

    public function testExternalPaymentsNotAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'reward',
            'amount' => 9,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'windows'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Internal Wallet") {
                $needed++;
            }
        }

        $this->assertEquals(1, $needed);
        $this->assertEquals(1, count($result));
    }

    public function testExternalPaymentsIsAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'reward',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'windows'
        ]);

        $this->assertNotEquals(1, count($result));
    }

    public function testGooglePayIsAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'android'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Google Pay") {
                $needed++;
            }
        }

        $this->assertEquals(1, $needed);
    }

    public function testGooglePayIsNotAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'IN',
            'userOs' => 'android'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Google Pay") {
                $needed++;
            }
        }

        $this->assertEquals(0, $needed);
    }

    public function testApplePayIsNotAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'IN',
            'userOs' => 'android'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Apple Pay") {
                $needed++;
            }
        }

        $this->assertEquals(0, $needed);
    }

    public function testApplePayIsAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'IN',
            'userOs' => 'iOS'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Apple Pay") {
                $needed++;
            }
        }

        $this->assertEquals(1, $needed);
    }

    public function testPrivatBankIsAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'UA',
            'userOs' => 'iOS'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Оплата картой ПриватБанка" || $button['name'] == "Оплата Банковскими картами") {
                $needed++;
            }
        }

        $this->assertEquals(2, $needed);
    }

    public function testPrivatBankIsNotAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'iOS'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Оплата картой ПриватБанка" || $button['name'] == "Оплата Банковскими картами") {
                $needed++;
            }
        }

        $this->assertEquals(1, $needed);
    }

    public function testExternalWalletInNotAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'walletRefill',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'iOS'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Internal Wallet") {
                $needed++;
            }
        }

        $this->assertEquals(0, $needed);
    }

    public function testExternalWalletIsAvailable(): void
    {
        $result = $this->initialize([
            'productType' => 'book',
            'amount' => 10,
            'lang' => 'ru',
            'countryCode' => 'RU',
            'userOs' => 'iOS'
        ]);

        $needed = 0;
        foreach ($result as $button) {
            if ($button['name'] == "Internal Wallet") {
                $needed++;
            }
        }

        $this->assertEquals(1, $needed);
    }
}
