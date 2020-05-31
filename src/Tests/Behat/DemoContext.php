<?php

declare(strict_types=1);

namespace AcMarche\Mercredi\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class DemoContext implements Context
{
    /** @var Response|null */
    private $response;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @Then /^the "([^"]*)" response header exists$/
     */
    public function theResponseHeaderExists($arg1)
    {
        $headers = $this->requestStack->getMasterRequest();
        var_dump($headers);

        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
        var_dump($arg1);
        var_dump($this->response->headers);
    }

    /**
     * @Then /^the "([^"]*)" response header is "([^"]*)"$/
     */
    public function theResponseHeaderIs($arg1, $arg2)
    {
        var_dump($arg1, $arg2);
        throw new PendingException();
    }

}
