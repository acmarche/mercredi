<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Contact\Form\ContactType;
use AcMarche\Mercredi\Contact\Mailer\ContactMailer;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Page\Factory\PageFactory;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use AcMarche\Mercredi\Security\MercrediSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        /**
         * @var User
         */
        $user = $this->getUser();

        if ($user) {
            $roles = MercrediSecurity::getRolesForProfile($user);

            if (count($roles) > 1) {
                return $this->redirectToRoute('mercredi_front_select_profile');
            }

            if ($user->hasRole('ROLE_MERCREDI_PARENT')) {
                return $this->redirectToRoute('mercredi_parent_home');
            }

            if ($user->hasRole('ROLE_MERCREDI_ECOLE')) {
                //return $this->redirectToRoute('mercredi_ecole_home');
            }

            if ($user->hasRole('ROLE_MERCREDI_ANIMATEUR')) {
                //  return $this->redirectToRoute('home_animateur');
            }

            if ($user->hasRole('ROLE_MERCREDI_ADMIN') or $user->hasRole('ROLE_MERCREDI_READ')) {
                return $this->redirectToRoute('mercredi_admin_home');
            }
        }

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
     * @Route("/select/profile", name="mercredi_front_select_profile")
     * @IsGranted("ROLE_MERCREDI")
     */
    public function selectProfile()
    {
        return $this->render(
            '@AcMarcheMercredi/front/select_profile.html.twig',
            [
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

    /**
     * @Route("/organisation/show", name="mercredi_organisation_show")
     */
    public function organisation()
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/default/_organisation.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }

    /**
     * @Route("/organisation/title", name="mercredi_organisation_title")
     */
    public function organisationTitle()
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/default/_organisation_title.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }
}
