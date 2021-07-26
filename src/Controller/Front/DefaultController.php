<?php

namespace AcMarche\Mercredi\Controller\Front;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Page\Factory\PageFactory;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
final class DefaultController extends AbstractController
{
    private PageRepository $pageRepository;
    private PageFactory $pageFactory;

    public function __construct(
        PageRepository $pageRepository,
        PageFactory $pageFactory
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @Route("/", name="mercredi_front_home")
     */
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

    /**
     * @Route("/menu/front", name="mercredi_front_menu_page")
     */
    public function menu(): Response
    {
        $pages = $this->pageRepository->findAll();

        return $this->render(
            '@AcMarcheMercredi/front/_menu_top.html.twig',
            [
                'pages' => $pages,
            ]
        );
    }
}
