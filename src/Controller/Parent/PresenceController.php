<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Presence\Constraint\DeleteConstraint;
use AcMarche\Mercredi\Presence\Dto\PresenceSelectDays;
use AcMarche\Mercredi\Presence\Form\PresenceNewForParentType;
use AcMarche\Mercredi\Presence\Message\PresenceCreated;
use AcMarche\Mercredi\Presence\Message\PresenceDeleted;
use AcMarche\Mercredi\Presence\Repository\PresenceDaysProviderInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/presence')]
#[IsGranted('ROLE_MERCREDI_PARENT')]
final class PresenceController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private RelationUtils $relationUtils,
        private PresenceRepository $presenceRepository,
        private PresenceHandlerInterface $presenceHandler,
        private SanteChecker $santeChecker,
        private SanteHandler $santeHandler,
        private PresenceCalculatorInterface $presenceCalculator,
        private PresenceDaysProviderInterface $presenceDaysProvider,
        private FacturePresenceRepository $facturePresenceRepository,
        private MessageBusInterface $dispatcher,
    ) {}

    /**
     * Etape 1 select enfant.
     */
    #[Route(path: '/select/enfant', name: 'mercredi_parent_presence_select_enfant', methods: ['GET'])]
    public function selectEnfant(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/presence/select_enfant.html.twig',
            [
                'enfants' => $enfants,
            ],
        );
    }

    /**
     * Etape 2.
     */
    #[Route(path: '/select/jour/{uuid}', name: 'mercredi_parent_presence_select_jours', methods: ['GET', 'POST'])]
    #[IsGranted('enfant_edit', subject: 'enfant')]
    public function selectJours(Request $request, Enfant $enfant): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $santeFiche = $this->santeHandler->init($enfant);
        if (!$this->santeChecker->isComplete($santeFiche)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('mercredi_parent_sante_fiche_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }
        $presenceSelectDays = new PresenceSelectDays($enfant);
        $form = $this->createForm(PresenceNewForParentType::class, $presenceSelectDays);
        $dates = $this->presenceDaysProvider->getAllDaysToSubscribe($enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $days = $form->getData()->getJours();

            $this->presenceHandler->handleNew($this->tuteur, $enfant, $days);

            $this->dispatcher->dispatch(new PresenceCreated($days));

            return $this->redirectToRoute('mercredi_parent_enfant_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/presence/select_jours.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
                'dates' => $dates,
            ],
        );
    }

    #[Route(path: '/{uuid}', name: 'mercredi_parent_presence_show', methods: ['GET'])]
    #[IsGranted('presence_show', subject: 'presence')]
    public function show(Presence $presence): Response
    {
        $facturePresence = $this->facturePresenceRepository->findByPresence($presence, type: null);

        return $this->render(
            '@AcMarcheMercrediParent/presence/show.html.twig',
            [
                'presence' => $presence,
                'facturePresence' => $facturePresence,
                'enfant' => $presence->getEnfant(),
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_parent_presence_delete', methods: ['POST'])]
    #[IsGranted('presence_edit', subject: 'presence')]
    public function delete(Request $request, Presence $presence): RedirectResponse
    {
        $enfant = $presence->getEnfant();
        if ($this->isCsrfTokenValid('delete'.$presence->getId(), $request->request->get('_token'))) {
            if (!DeleteConstraint::canBeDeleted($presence)) {
                $this->addFlash('danger', 'Une présence passée ne peut être supprimée');

                return $this->redirectToRoute('mercredi_parent_enfant_show', [
                    'uuid' => $enfant->getUuid(),
                ]);
            }
            $presenceId = $presence->getId();
            $this->presenceRepository->remove($presence);
            $this->presenceRepository->flush();
            $this->dispatcher->dispatch(new PresenceDeleted($presenceId));
        }

        return $this->redirectToRoute('mercredi_parent_enfant_show', [
            'uuid' => $enfant->getUuid(),
        ]);
    }

    #[Route(path: '/non/payes', name: 'mercredi_parent_presence_non_payes', methods: ['POST', 'GET'])]
    public function nonPaye(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        $presences = $this->presenceRepository->findWithOutPaiement($this->tuteur);
        $presencesPlaines = $this->presenceRepository->findWithOutPaiementPlaine($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/presence/non_payes.html.twig',
            [
                'presences' => $presences,
                'presencesPlaines' => $presencesPlaines,
            ],
        );
    }
}
