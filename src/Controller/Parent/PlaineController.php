<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Contrat\Plaine\FacturePlaineHandlerInterface;
use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Plaine\Form\PlaineConfirmationType;
use AcMarche\Mercredi\Plaine\Form\SelectEnfantType;
use AcMarche\Mercredi\Plaine\Form\SelectJourType;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/plaine')]
final class PlaineController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private PlaineRepository $plaineRepository,
        private RelationUtils $relationUtils,
        private SanteHandler $santeHandler,
        private SanteChecker $santeChecker,
        private PlainePresenceRepository $plainePresenceRepository,
        private EnfantRepository $enfantRepository,
        private FacturePlaineHandlerInterface $facturePlaineHandler,
        private FactureEmailFactory $factureEmailFactory,
        private NotificationMailer $notificationMailer,
        private AdminEmailFactory $adminEmailFactory,
        private PlaineHandlerInterface $plaineHandler,
        private FactureRepository $factureRepository
    ) {
    }

    /**
     * Render from layout !
     * @return Response
     */
    #[Route(path: '/open', name: 'mercredi_parent_plaine_open')]
    #[IsGranted('ROLE_MERCREDI_PARENT')]
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

    #[Route(path: '/{id}/show', name: 'mercredi_parent_plaine_show')]
    #[IsGranted('ROLE_MERCREDI_PARENT')]
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
     */
    #[Route(path: '/select/enfant', name: 'mercredi_parent_plaine_select_enfant', methods: ['GET', 'POST'])]
    public function selectEnfant(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $form = $this->createForm(SelectEnfantType::class, null, [
            'enfants' => $enfants,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plaine = $this->plaineRepository->findPlaineOpen();
            if (!$plaine) {
                $this->addFlash('danger', 'Aucune plaine ouverte aux inscriptions n\'a été trouvée');

                return $this->redirectToRoute('mercredi_parent_home');
            }
            $enfantsSelected = $form->get('enfants')->getData();
            foreach ($enfantsSelected as $enfant) {
                $santeFiche = $this->santeHandler->init($enfant);

                if (!$this->santeChecker->isComplete($santeFiche)) {
                    $this->addFlash('danger', 'La fiche santé de '.$enfant.' doit être complétée');

                    return $this->redirectToRoute('mercredi_parent_plaine_show', [
                        'id' => $plaine->getId(),
                    ]);
                }
            }

            $session = $request->getSession();
            $ids = [];
            foreach ($enfantsSelected as $enfant) {
                $ids[] = $enfant->getId();
            }
            $session->set('enfants', $ids);

            return $this->redirectToRoute('mercredi_parent_plaine_select_jour');
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
     * Etape 2 select jours.
     */
    #[Route(path: '/select/jours', name: 'mercredi_parent_plaine_select_jour', methods: ['GET', 'POST'])]
    public function selectJours(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $plaine = $this->plaineRepository->findPlaineOpen();
        if (!$plaine) {
            $this->addFlash('danger', 'Aucune plaine ouverte aux inscriptions n\'a été trouvée');

            return $this->redirectToRoute('mercredi_parent_home');
        }

        $form = $this->createForm(SelectJourType::class, null, [
            'plaine' => $plaine,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jours = $form->get('jours')->getData();
            $session = $request->getSession();
            $enfantIds = $session->get('enfants');
            if (count($enfantIds) === 0) {
                $this->addFlash('danger', 'Aucun enfant sélectionné');

                return $this->redirectToRoute('mercredi_parent_plaine_show', [
                    'id' => $plaine->getId(),
                ]);
            }

            $enfants = $this->enfantRepository->findBy(['id' => $enfantIds]);
            foreach ($enfants as $enfant) {
                $daysFull = $this->plaineHandler->handleAddEnfant($plaine, $this->tuteur, $enfant, $jours);
                if (count($daysFull) > 0) {
                    $text = "Attention $enfant n'a pas pu être inscrit aux dates suivantes, il n'y a plus de place pour cette catégorie d'âge: <ul>";
                    foreach ($daysFull as $day) {
                        $text .= '<li>'.$day->getDateJour()->format('d-m').'</li>';
                    }
                    $text .= "</ul>";
                    $this->addFlash('danger', $text);
                } else {
                    $this->addFlash('success', $enfant.' a bien été inscrits à la plaine');
                }
            }

            return $this->redirectToRoute('mercredi_parent_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/plaine/select_jour.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/confirmation', name: 'mercredi_parent_plaine_presence_confirmation', methods: ['GET', 'POST'])]
    public function confirmation(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $tuteur = $this->tuteur;
        $plaine = $this->plaineRepository->findPlaineOpen();
        if (!$plaine) {
            $this->addFlash('danger', 'Aucune plaine ouverte aux inscriptions n\'a été trouvée');

            return $this->redirectToRoute('mercredi_parent_home');
        }
        if ($this->plaineHandler->isRegistrationFinalized($plaine, $tuteur)) {
            $this->addFlash('danger', 'Tout est finalisé');

            return $this->redirectToRoute('mercredi_parent_plaine_show', [
                'id' => $plaine->getId(),
            ]);
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

            return $this->redirectToRoute('mercredi_parent_plaine_show', [
                'id' => $plaine->getId(),
            ]);
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
