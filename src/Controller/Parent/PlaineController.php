<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Contrat\Plaine\FacturePlaineHandlerInterface;
use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Plaine\Form\PlaineConfirmationType;
use AcMarche\Mercredi\Plaine\Form\SelectEnfantType;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plaine")
 */
final class PlaineController extends AbstractController
{
    use GetTuteurTrait;

    private PlaineRepository $plaineRepository;
    private RelationUtils $relationUtils;
    private PlaineHandlerInterface $plaineHandler;
    private SanteHandler $santeHandler;
    private SanteChecker $santeChecker;
    private PlainePresenceRepository $plainePresenceRepository;
    private FacturePlaineHandlerInterface $facturePlaineHandler;
    private FactureEmailFactory $factureEmailFactory;
    private NotificationMailer $notificationMailer;
    private AdminEmailFactory $adminEmailFactory;
    private FactureRepository $factureRepository;

    public function __construct(
        PlaineRepository $plaineRepository,
        RelationUtils $relationUtils,
        SanteHandler $santeHandler,
        SanteChecker $santeChecker,
        PlainePresenceRepository $plainePresenceRepository,
        FacturePlaineHandlerInterface $facturePlaineHandler,
        FactureEmailFactory $factureEmailFactory,
        NotificationMailer $notificationMailer,
        AdminEmailFactory $adminEmailFactory,
        PlaineHandlerInterface $plaineHandler,
        FactureRepository $factureRepository
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->relationUtils = $relationUtils;
        $this->plaineHandler = $plaineHandler;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->facturePlaineHandler = $facturePlaineHandler;
        $this->factureEmailFactory = $factureEmailFactory;
        $this->notificationMailer = $notificationMailer;
        $this->adminEmailFactory = $adminEmailFactory;
        $this->factureRepository = $factureRepository;
    }

    /**
     * @Route("/open", name="mercredi_parent_plaine_open")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function open(): Response
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
    public function show(Plaine $plaine): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        $tuteur = $this->tuteur;
        $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        $enfantsInscrits = PresenceUtils::extractEnfants($inscriptions);
        $enfants = $this->relationUtils->findEnfantsByTuteur($tuteur);

        $resteEnfant = \count($enfantsInscrits) !== \count($enfants);

        $facture = null;
        if ($this->plaineHandler->isRegistrationFinalized($plaine, $tuteur)) {
            $facture = $this->factureRepository->findByTuteurAndPlaine($tuteur, $plaine);
        }

        return $this->render(
            '@AcMarcheMercrediParent/plaine/show.html.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfantsInscrits,
                'inscriptions' => $inscriptions,
                'resteEnfants' => $resteEnfant,
                'facture' => $facture,
            ]
        );
    }

    /**
     * Etape 1 select enfant.
     *
     * @Route("/select/enfant", name="mercredi_parent_plaine_select_enfant", methods={"GET", "POST"})
     */
    public function selectEnfant(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $form = $this->createForm(SelectEnfantType::class, null, ['enfants' => $enfants]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaine = $this->plaineRepository->findPlaineOpen();
            $enfantsSelected = $form->get('enfants')->getData();
            foreach ($enfantsSelected as $enfant) {
                $santeFiche = $this->santeHandler->init($enfant);

                if (!$this->santeChecker->isComplete($santeFiche)) {
                    $this->addFlash('danger', 'La fiche santé de '.$enfant.' doit être complétée');

                    continue;
                }

                if (null !== $plaine) {
                    $this->plaineHandler->handleAddEnfant($plaine, $this->tuteur, $enfant);
                    $this->addFlash('success', $enfant.' a bien été inscrits à la plaine');
                }
            }

            return $this->redirectToRoute(
                'mercredi_parent_plaine_presence_confirmation',
                [
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediParent/plaine/select_enfant.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/confirmation", name="mercredi_parent_plaine_presence_confirmation", methods={"GET", "POST"})
     */
    public function confirmation(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        $tuteur = $this->tuteur;
        $plaine = $this->plaineRepository->findPlaineOpen();

        if ($this->plaineHandler->isRegistrationFinalized($plaine, $tuteur)) {
            return $this->redirectToRoute('mercredi_parent_plaine_show', ['id' => $plaine->getId()]);
        }

        $enfantsInscrits = $this->plainePresenceRepository->findEnfantsByPlaineAndTuteur($plaine, $tuteur);
        $enfants = $this->relationUtils->findEnfantsByTuteur($tuteur);

        $form = $this->createForm(PlaineConfirmationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->plaineHandler->confirm($plaine, $tuteur);
                $this->addFlash('success', 'La facture a bien été générée et envoyée sur votre mail');
            } catch (Exception $e) {
                $this->addFlash('danger', 'Erreur survenue: '.$e->getMessage());
            }

            return $this->redirectToRoute('mercredi_parent_plaine_show', ['id' => $plaine->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/plaine/confirmation.twig',
            [
                'plaine' => $plaine,
                'enfantsInscrits' => $enfantsInscrits,
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }
}
