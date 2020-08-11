<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Search\Form\SearchNameType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/enfant")
 */
final class EnfantController extends AbstractController
{
    use GetEcolesTrait;

    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
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
     * @var AccueilRepository
     */
    private $accueilRepository;
    /**
     * @var RelationRepository
     */
    private $relationRepository;

    public function __construct(
        EnfantRepository $enfantRepository,
        SanteHandler $santeHandler,
        SanteChecker $santeChecker,
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        RelationRepository $relationRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->presenceRepository = $presenceRepository;
        $this->accueilRepository = $accueilRepository;
        $this->relationRepository = $relationRepository;
    }

    /**
     * @Route("/", name="mercredi_ecole_enfant_index", methods={"GET", "POST"})
     * @IsGranted("ROLE_MERCREDI_ECOLE")
     */
    public function index(Request $request)
    {
        if ($t = $this->hasEcoles()) {
            return $t;
        }

        $nom = null;
        $form = $this->createForm(SearchNameType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
        }

        $enfants = $this->enfantRepository->searchForEcole($this->ecoles, $nom);

        return $this->render(
            '@AcMarcheMercrediEcole/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}", name="mercredi_ecole_enfant_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function show(Enfant $enfant)
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findPresencesByEnfant($enfant);
        $accueils = $this->accueilRepository->findByEnfant($enfant);
        $relations = $this->relationRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheMercrediEcole/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'accueils' => $accueils,
                'relations' => $relations,
                'ficheSanteComplete' => $ficheSanteComplete,
            ]
        );
    }

}
