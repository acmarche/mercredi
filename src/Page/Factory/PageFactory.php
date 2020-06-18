<?php


namespace AcMarche\Mercredi\Page\Factory;


use AcMarche\Mercredi\Entity\Page;
use AcMarche\Mercredi\Page\Repository\PageRepository;

class PageFactory
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function createHomePage(): ?Page
    {
        $page = new Page();
        $page->setNom('Accueil');
        $page->setContent('Contenu à modifier');
        $page->setSlugSystem('home');

        $this->pageRepository->persist($page);
        $this->pageRepository->flush();

        return $page;
    }

    public function createContactPage(): ?Page
    {
        $page = new Page();
        $page->setNom('Nous contacter');
        $page->setContent('Contenu à modifier');
        $page->setSlugSystem('contact');

        $this->pageRepository->persist($page);
        $this->pageRepository->flush();

        return $page;
    }
}
