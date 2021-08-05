<?php

namespace AcMarche\Mercredi\Controller\Front;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Contact\Form\ContactType;
use AcMarche\Mercredi\Mailer\ContactMailer;
use AcMarche\Mercredi\Entity\Page;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Page\Factory\PageFactory;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
final class PageController extends AbstractController
{
    private OrganisationRepository $organisationRepository;
    private PageRepository $pageRepository;
    private PageFactory $pageFactory;
    private ContactMailer $contactMailer;

    public function __construct(
        OrganisationRepository $organisationRepository,
        PageRepository $pageRepository,
        PageFactory $pageFactory,
        ContactMailer $contactMailer
    ) {
        $this->organisationRepository = $organisationRepository;
        $this->pageRepository = $pageRepository;
        $this->pageFactory = $pageFactory;
        $this->contactMailer = $contactMailer;
    }

    /**
     * @Route("/page/{slug}", name="mercredi_front_page_show")
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

    /**
     * @Route("/contact", name="mercredi_front_contact")
     */
    public function contact(Request $request): Response
    {
        $page = $this->pageRepository->findContactPage();
        if (null === $page) {
            $page = $this->pageFactory->createContactPage();
        }

        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $email = $data['email'];
            $body = $data['texte'];

            try {
                $this->contactMailer->sendContactForm($email, $nom, $body);
                $this->addFlash('success', 'Le message a bien été envoyé.');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Le message n\'a pas pu être envoyé !'.$e->getMessage());
            }

            return $this->redirectToRoute('mercredi_front_contact');
        }

        return $this->render(
            '@AcMarcheMercredi/front/contact.html.twig',
            [
                'page' => $page,
                'organisation' => $this->organisationRepository->getOrganisation(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/modalite", name="mercredi_front_modalite")
     */
    public function modalite(): Response
    {
        $page = $this->pageRepository->findModalitePage();
        if (null === $page) {
            $page = $this->pageFactory->createModalitePage();
        }

        return $this->render(
            '@AcMarcheMercredi/front/page.html.twig',
            [
                'page' => $page,
            ]
        );
    }
}
