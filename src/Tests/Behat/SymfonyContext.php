<?php

declare(strict_types=1);

namespace AcMarche\Mercredi\Tests\Behat;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use Behat\Behat\Context\Context;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class SymfonyContext implements Context
{
    private KernelInterface $kernel;

    private ?Response $response = null;
    private EnfantRepository $enfantRepository;

    public function __construct(KernelInterface $kernel, EnfantRepository $enfantRepository)
    {
        $this->kernel = $kernel;
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * When a demo scenario sends a request to :path.
     */
    public function aDemoScenarioSendsARequestTo(string $path): void
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * Then the response should be received.
     */
    public function theResponseShouldBeReceived(): void
    {
        if (null === $this->response) {
            throw new RuntimeException('No response received');
        }
//        var_dump($this->response->getContent());
    }

    /**
     * Then I should see :arg1.
     */
    public function iShouldSee($arg1): void
    {
        if (null === $this->response) {
            throw new RuntimeException('No response received');
        }

        if (!$this->response->isRedirection()) {
            throw new RuntimeException('Response is not redirect');
        }
    }

    /**
     * When /^i am login with user "([^"]*)" and password "([^"]*)"$/.
     */
    public function iAmLoginWithUserAndPassword($arg1, $arg2): void
    {
        if (null === $arg1) {
            throw new RuntimeException('No user received');
        }
    }
}
