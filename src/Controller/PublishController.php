<?php

namespace AcMarche\Mercredi\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PublishController extends AbstractController
{
    /**
     * @param PublisherInterface $publisher
     * @return Response
     * @Route("/publish")
     */
    public function publish(PublisherInterface $publisher): Response
    {
        $update = new Update(
            'http://hotton.local/books/1',
            json_encode(['status' => 'OutOfStock3'])
        );
        HttpClientInterface::OPTIONS_DEFAULTS;
        // The Publisher service is an invokable object
        $publisher($update);

        return new Response('published!');
    }

}
