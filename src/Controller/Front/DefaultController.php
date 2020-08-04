<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Page\Factory\PageFactory;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController
{
    /**
     * @var PageRepository
     */
    private $pageRepository;
    /**
     * @var PageFactory
     */
    private $pageFactory;

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
    public function index()
    {
        $homePage = $this->pageRepository->findHomePage();
        if (!$homePage) {
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
    public function menu()
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
