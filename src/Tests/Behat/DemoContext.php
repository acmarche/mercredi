<?php

declare(strict_types=1);

namespace AcMarche\Mercredi\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class DemoContext implements Context
{
    private ?Response $response = null;
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @Then /^the "([^"]*)" response header exists$/
     */
    public function theResponseHeaderExists($arg1): void
    {
        $headers = $this->requestStack->getMasterRequest();
        var_dump($headers);

        if (null === $this->response) {
            throw new RuntimeException('No response received');
        }
        var_dump($arg1);
        var_dump($this->response->headers);
    }

    /**
     * @Then /^the "([^"]*)" response header is "([^"]*)"$/
     */
    public function theResponseHeaderIs($arg1, $arg2): void
    {
        var_dump($arg1, $arg2);

        throw new PendingException();
    }
}
