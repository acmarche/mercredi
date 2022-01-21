<?php

namespace AcMarche\Mercredi\Tests\Behat;

use AcMarche\Mercredi\Fixture\FixtureLoader;
use Behat\Behat\Context\Context;

class DatabaseContext implements Context
{
    public function __construct(
        private FixtureLoader $fixtureLoader
    ) {
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
