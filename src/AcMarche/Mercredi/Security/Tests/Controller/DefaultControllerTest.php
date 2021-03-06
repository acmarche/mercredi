<?php

namespace AcMarche\Mercredi\Security\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class DefaultControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->anonyme->request('GET', '/login');

        $this->assertTrue($crawler->filter('html:contains("Authentification")')->count() > 0);
    }
}
