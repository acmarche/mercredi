<?php


namespace AcMarche\Mercredi\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

/**
 * https://symfony.com/doc/current/messenger.html#in-memory-transport
 * Class MessagageTest
 * @package AcMarche\Mercredi\Tests\Unit
 */
class MessagageTest extends WebTestCase
{
    public function testSomething()
    {
        $client = static::createClient();
        // ...

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        /* @var InMemoryTransport $transport */
        $transport = self::$container->get('messenger.transport.async_priority_normal');
        $this->assertCount(1, $transport->getSent());
    }


}
