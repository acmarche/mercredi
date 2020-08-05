<?php

namespace AcMarche\Mercredi\Tests\Calculator;

use PHPUnit\Framework\TestCase;

class CalculatorHottonTest extends TestCase
{
    public function testSettingCustomerFirstName(): void
    {
        $customer = new Customer();
        $firstName = 'John';

        $customer->setFirstName($firstName);

        $this->assertSame($firstName, $customer->getFirstName());
    }

    public function testSettingCustomerLastName(): void
    {
        $customer = new Customer();
        $lastName = 'Doe';

        $customer->setLastName($lastName);

        $this->assertSame($lastName, $customer->getLastName());
    }

    public function testReturnsCustomerFullName(): void
    {
        $customer = new Customer();
        $customer->setFirstName('John');
        $customer->setLastName('Deo');

        $fullName = $customer->getFirstName().''.$customer->getLastName();

        $this->assertSame($fullName, $customer->getCustomerFullName());
    }
}
