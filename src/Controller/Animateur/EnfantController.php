<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Enfant\Form\SearchEnfantForAnimateurType;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
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
    use GetAnimateurTrait;

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
     * @var RelationRepository
     */
    private $relationRepository;

    public function __construct(
        EnfantRepository $enfantRepository,
        SanteHandler $santeHandler,
        SanteChecker $santeChecker,
        PresenceRepository $presenceRepository,
        RelationRepository $relationRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->presenceRepository = $presenceRepository;
        $this->relationRepository = $relationRepository;
    }

    /**
     * @Route("/", name="mercredi_animateur_enfant_index", methods={"GET", "POST"})
     * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
     */
    public function index(Request $request)
    {
        if ($t = $this->hasAnimateur()) {
            return $t;
        }

        $nom = $jour = null;
        $form = $this->createForm(
            SearchEnfantForAnimateurType::class,
            null,
            [
                'animateur' => $this->animateur,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $jour = $data['jour'];
        }

        $enfants = $this->enfantRepository->searchForAnimateur($this->animateur, $nom, $jour);

        return $this->render(
            '@AcMarcheMercrediAnimateur/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}", name="mercredi_animateur_enfant_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function show(Enfant $enfant)
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findPresencesByEnfant($enfant);
        $relations = $this->relationRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheMercrediAnimateur/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'relations' => $relations,
                'ficheSanteComplete' => $ficheSanteComplete,
            ]
        );
    }

}
