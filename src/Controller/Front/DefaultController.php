<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Facture\Handler\FactureCronHandler;
use AcMarche\Mercredi\Page\Factory\PageFactory;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use PHPUnit\TextUI\Exception;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Worker;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository,
        private PageFactory $pageFactory,
        private FactureCronHandler $factureCronHandler,
        private MessageBusInterface $bus,
        #[Autowire(service: 'messenger.receiver_locator')]
        private ContainerInterface $receiverLocator
    ) {
    }

    #[Route(path: '/', name: 'mercredi_front_home')]
    public function index(): Response
    {
        $homePage = $this->pageRepository->findHomePage();
        if (null === $homePage) {
            $homePage = $this->pageFactory->createHomePage();
        }

        return $this->render(
            '@AcMarcheMercredi/default/index.html.twig',
            [
                'page' => $homePage,
            ],
        );
    }

    #[Route(path: '/menu/front', name: 'mercredi_front_menu_page')]
    public function menu(): Response
    {
        $pages = $this->pageRepository->findToDisplayMenu();

        return $this->render(
            '@AcMarcheMercredi/front/_menu_top.html.twig',
            [
                'pages' => $pages,
            ],
        );
    }

    #[Route(path: '/cron/launch', name: 'mercredi_front_cron')]
    public function cron(): JsonResponse
    {
        try {
            $result = $this->factureCronHandler->execute();
        } catch (Exception|TransportExceptionInterface $exception) {
            $result[] = ['error' => $exception->getMessage()];
        }

        if (count($result) > 0) {
            try {
                $this->factureCronHandler->sendResult($result);
            } catch (Exception|TransportExceptionInterface $exception) {
                $result[] = ['error' => $exception->getMessage()];
            }
        }


        return $this->json($result);
    }

    public function consume(): Response
    {
        $eventDispatcher = new EventDispatcher();

        $receiver = $this->receiverLocator->get('async');

        // Stop after processing 10 messages
        $eventDispatcher->addSubscriber(
            new StopWorkerOnMessageLimitListener(10)
        );

        $worker = new Worker(
            [$receiver],
            $this->bus,
            $eventDispatcher
        );

        // This will block the HTTP request!
        $worker->run();

        return new Response('Messages consumed');
    }


}
