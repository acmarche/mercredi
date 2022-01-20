<?php

namespace AcMarche\Mercredi\Tests\Behat;

use AcMarche\Mercredi\Fixture\FixtureLoader;
use Behat\Behat\Context\Context;

class DatabaseContext implements Context
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    /**
     * @BeforeScenario
     */
    public function loadFixtures(): void
    {
        $this->fixtureLoader->load();
    }

    /**
     * @AfterScenario
     */
    public function rollbackPostgreSqlTransaction(): void
    {
    }
}