<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Form\EnfantEditForParentType;
use AcMarche\Mercredi\Enfant\Handler\EnfantHandler;
use AcMarche\Mercredi\Enfant\Message\EnfantCreated;
use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Notification\Mailer\NotifcationMailer;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/enfant")
 */
class EnfantController extends AbstractController
{
    use GetTuteurTrait;

    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;
    /**
     * @var RelationUtils
     */
    private $relationUtils;
    /**
     * @var SanteHandler
     */
    private $santeHandler;
    /**
     * @var SanteChecker
     */
    private $santeChecker;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;
    /**
     * @var EnfantHandler
     */
    private $enfantHandler;
    /**
     * @var NotifcationMailer
     */
    private $notifcationMailer;

    public function __construct(
        EnfantRepository $enfantRepository,
        TuteurUtils $tuteurUtils,
        SanteHandler $santeHandler,
        RelationUtils $relationUtils,
        SanteChecker $santeChecker,
        PresenceRepository $presenceRepository,
        PlainePresenceRepository $plainePresenceRepository,
        AccueilRepository $accueilRepository,
        EnfantHandler $enfantHandler,
        NotifcationMailer $notifcationMailer
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->tuteurUtils = $tuteurUtils;
        $this->relationUtils = $relationUtils;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->presenceRepository = $presenceRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->accueilRepository = $accueilRepository;
        $this->enfantHandler = $enfantHandler;
        $this->notifcationMailer = $notifcationMailer;
    }

    /**
     * @Route("/", name="mercredi_parent_enfant_index", methods={"GET"})
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function index()
    {
        $this->hasTuteur();

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $this->santeChecker->isCompleteForEnfants($enfants);

        return $this->render(
            '@AcMarcheMercrediParent/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'year' => date('Y'),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_parent_enfant_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function new(Request $request): Response
    {
        $this->hasTuteur();
        $enfant = new Enfant();
        $form = $this->createForm(EnfantEditForParentType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantHandler->newHandle($enfant, $this->tuteur);
            $this->dispatchMessage(new EnfantCreated($enfant->getId()));

            $this->notifcationMailer->sendMessagEnfantCreated($this->getUser(), $enfant);

            return $this->redirectToRoute('mercredi_parent_enfant_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/enfant/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}", name="mercredi_parent_enfant_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function show(Enfant $enfant)
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findPresencesByEnfant($enfant);
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
            ]
        );
    }

    /**
     * @Route("/{uuid}/edit", name="mercredi_parent_enfant_edit", methods={"GET","POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function edit(Request $request, Enfant $enfant)
    {
        $form = $this->createForm(EnfantEditForParentType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatchMessage(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('mercredi_parent_enfant_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
