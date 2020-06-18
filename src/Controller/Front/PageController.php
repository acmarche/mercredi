<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Page;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/page")
 */
class PageController extends AbstractController
{
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(
        OrganisationRepository $organisationRepository,
        PageRepository $pageRepository
    ) {
        $this->organisationRepository = $organisationRepository;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @Route("/{slug}", name="mercredi_front_page_show")
     */
    public function page(Page $page): Response
    {
        if ('home' === $page->getSlugSystem()) {
            return $this->redirectToRoute('mercredi_front_home');
        }

        if ('contact' === $page->getSlugSystem()) {
            return $this->redirectToRoute('mercredi_front_contact');
        }

        return $this->render(
            '@AcMarcheMercredi/front/page.html.twig',
            [
                'page' => $page,
            ]
        );
    }

}
