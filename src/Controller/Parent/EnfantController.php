<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Form\EnfantEditForParentType;
use AcMarche\Mercredi\Enfant\Handler\EnfantHandler;
use AcMarche\Mercredi\Enfant\Message\EnfantCreated;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/enfant')]
final class EnfantController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private EnfantRepository $enfantRepository,
        private SanteHandler $santeHandler,
        private RelationUtils $relationUtils,
        private SanteChecker $santeChecker,
        private PresenceRepository $presenceRepository,
        private PlainePresenceRepository $plainePresenceRepository,
        private AccueilRepository $accueilRepository,
        private EnfantHandler $enfantHandler,
        private AdminEmailFactory $adminEmailFactory,
        private NotificationMailer $notifcationMailer,
        private MessageBusInterface $dispatcher,
    ) {
    }

    #[Route(path: '/', name: 'mercredi_parent_enfant_index', methods: ['GET'])]
    #[IsGranted('ROLE_MERCREDI_PARENT')]
    public function index(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $this->santeChecker->isCompleteForEnfants($enfants);

        return $this->render(
            '@AcMarcheMercrediParent/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'year' => date('Y'),
            ],
        );
    }

    #[Route(path: '/new', name: 'mercredi_parent_enfant_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MERCREDI_PARENT')]
    public function new(Request $request): Response
    {
        if ($this->getParameter('mercredi.add_enfant') < 1) {
            $this->addFlash('danger', 'L\'ajout d\'un enfant n\'est pas autorisé');

            return $this->redirectToRoute('mercredi_parent_home');
        }
        $this->hasTuteur();
        $enfant = new Enfant();
        $form = $this->createForm(EnfantEditForParentType::class, $enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantHandler->newHandle($enfant, $this->tuteur);
            $this->dispatcher->dispatch(new EnfantCreated($enfant->getId()));
            $enfant->setPhoto(null); //bug serialize
            $message = $this->adminEmailFactory->messageEnfantCreated($this->getUser(), $enfant);
            $this->notifcationMailer->sendAsEmailNotification($message);

            return $this->redirectToRoute('mercredi_parent_enfant_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/enfant/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{uuid}', name: 'mercredi_parent_enfant_show', methods: ['GET'])]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function show(#[MapEntity(expr: 'repository.findOneByUuid(uuid)')] Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findWithoutPlaineByEnfant($enfant);
        $plaines = $this->plainePresenceRepository->findPlainesByEnfant($enfant);
        $accueils = $this->accueilRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheMercrediParent/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'plaines' => $plaines,
                'accueils' => $accueils,
                'ficheSanteComplete' => $ficheSanteComplete,
            ],
        );
    }
}
