<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Facture\Handler\FactureCronHandler;
use AcMarche\Mercredi\Page\Factory\PageFactory;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class DefaultController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository,
        private PageFactory $pageFactory,
        private FactureCronHandler $factureCronHandler
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
            ]
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
            ]
        );
    }

    #[Route(path: '/cron/launch', name: 'mercredi_front_cron')]
    public function cron(): Response
    {
        $this->factureCronHandler->execute();

        return $this->render(
            '@AcMarcheMercredi/default/cron.html.twig',
            [

            ]
        );
    }

}
