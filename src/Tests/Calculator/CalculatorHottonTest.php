<?php

namespace AcMarche\Mercredi\Tests\Calculator;

use PHPUnit\Framework\TestCase;

class CalculatorHottonTest extends TestCase
{
    public function testSettingCustomerFirstName()
    {
        $customer = new Customer();
        $firstName = 'John';

        $customer->setFirstName($firstName);

        $this->assertEquals($firstName, $customer->getFirstName());
    }

    public function testSettingCustomerLastName()
    {
        $customer = new Customer();
        $lastName = 'Doe';

        $customer->setLastName($lastName);

        $this->assertEquals($lastName, $customer->getLastName());
    }

    public function testReturnsCustomerFullName()
    {
        $customer = new Customer();
        $customer->setFirstName('John');
        $customer->setLastName('Deo');

        $fullName = $customer->getFirstName().''.$customer->getLastName();

        $this->assertSame($fullName, $customer->getCustomerFullName());
    }
}
