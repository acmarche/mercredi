<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Enfant\Form\EnfantEditForParentType;
use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public function __construct(
        EnfantRepository $enfantRepository,
        TuteurUtils $tuteurUtils,
        SanteHandler $santeHandler,
        RelationUtils $relationUtils,
        SanteChecker $santeChecker,
        PresenceRepository $presenceRepository,
        PlainePresenceRepository $plainePresenceRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->tuteurUtils = $tuteurUtils;
        $this->relationUtils = $relationUtils;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->presenceRepository = $presenceRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
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
     * @Route("/{uuid}", name="mercredi_parent_enfant_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function show(Enfant $enfant)
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findPresencesByEnfant($enfant);
        $plaines = $this->plainePresenceRepository->findPlainesByEnfant($enfant);

        return $this->render(
            '@AcMarcheMercrediParent/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'plaines' => $plaines,
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
