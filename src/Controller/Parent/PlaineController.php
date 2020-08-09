<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Plaine\Handler\PlainePresenceHandler;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plaine")
 */
final class PlaineController extends AbstractController
{
    use GetTuteurTrait;

    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var RelationUtils
     */
    private $relationUtils;
    /**
     * @var PlainePresenceHandler
     */
    private $plainePresenceHandler;
    /**
     * @var SanteHandler
     */
    private $santeHandler;
    /**
     * @var SanteChecker
     */
    private $santeChecker;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;

    public function __construct(
        PlaineRepository $plaineRepository,
        RelationUtils $relationUtils,
        PlainePresenceHandler $plainePresenceHandler,
        SanteHandler $santeHandler,
        SanteChecker $santeChecker,
        PlainePresenceRepository $plainePresenceRepository
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->relationUtils = $relationUtils;
        $this->plainePresenceHandler = $plainePresenceHandler;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->plainePresenceRepository = $plainePresenceRepository;
    }

    /**
     * @Route("/open", name="mercredi_parent_plaine_open")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function open()
    {
        $plaine = $this->plaineRepository->findPlaineOpen();

        return $this->render(
            '@AcMarcheMercrediParent/plaine/_open.html.twig',
            [
                'plaine' => $plaine,
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_parent_plaine_show")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function show(Plaine $plaine)
    {
        if ($t = $this->hasTuteur()) {
            return $t;
        }
        $enfants = $this->plainePresenceRepository->findEnfantsByPlaineAndTuteur($plaine, $this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/plaine/show.html.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfants,
            ]
        );
    }

    /**
     * Etape 1 select enfant.
     *
     * @Route("/select/enfant", name="mercredi_parent_plaine_select_enfant", methods={"GET"})
     */
    public function selectEnfant()
    {
        $this->hasTuteur();

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/plaine/select_enfant.html.twig',
            [
                'enfants' => $enfants,
            ]
        );
    }

    /**
     * @Route("/confirmation/{uuid}", name="mercredi_parent_plaine_presence_confirmation", methods={"GET","POST"})
     */
    public function confirmation(Enfant $enfant): Response
    {
        $this->hasTuteur();
        $plaine = $this->plaineRepository->findPlaineOpen();

        $santeFiche = $this->santeHandler->init($enfant);

        if (! $this->santeChecker->isComplete($santeFiche)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('mercredi_parent_sante_fiche_show', ['uuid' => $enfant->getUuid()]);
        }

        if (null !== $plaine) {
            $this->plainePresenceHandler->handleAddEnfant($plaine, $this->tuteur, $enfant);
            $this->addFlash('success', 'Votre enfant a bien été inscrits à la plaine');
        }

        return $this->redirectToRoute(
            'mercredi_parent_plaine_show',
            [
                'id' => $plaine->getId(),
            ]
        );
    }
}
