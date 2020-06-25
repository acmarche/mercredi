<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Contact\Form\ContactType;
use AcMarche\Mercredi\Contact\Mailer\ContactMailer;
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
class DefaultController extends AbstractController
{
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;
    /**
     * @var PageRepository
     */
    private $pageRepository;
    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var ContactMailer
     */
    private $contactMailer;

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
     * @Route("/contact", name="mercredi_front_contact")
     */
    public function contact(Request $request)
    {
        $page = $this->pageRepository->findContactPage();
        if (!$page) {
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
