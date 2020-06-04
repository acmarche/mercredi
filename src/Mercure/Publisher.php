<?php

namespace AcMarche\Mercredi\Mercure;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class Publisher
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    public function __invoke()
    {
        $update = new Update(
            'http://hotton.local/books/1',
            json_encode(['status' => 'OutOfStock3'])
        );

        // The Publisher service is an invokable object
        $this->publisher($update);
    }
}
