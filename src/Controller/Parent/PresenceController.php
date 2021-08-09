<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Presence\Constraint\DeleteConstraint;
use AcMarche\Mercredi\Presence\Dto\PresenceSelectDays;
use AcMarche\Mercredi\Presence\Form\PresenceNewForParentType;
use AcMarche\Mercredi\Presence\Handler\PresenceHandler;
use AcMarche\Mercredi\Presence\Message\PresenceCreated;
use AcMarche\Mercredi\Presence\Message\PresenceDeleted;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
final class PresenceController extends AbstractController
{
    use GetTuteurTrait;

    private PresenceRepository $presenceRepository;
    private PresenceHandler $presenceHandler;
    private RelationUtils $relationUtils;
    private SanteChecker $santeChecker;
    private SanteHandler $santeHandler;
    private FacturePresenceRepository $facturePresenceRepository;

    public function __construct(
        RelationUtils $relationUtils,
        PresenceRepository $presenceRepository,
        PresenceHandler $presenceHandler,
        SanteChecker $santeChecker,
        SanteHandler $santeHandler,
        FacturePresenceRepository $facturePresenceRepository
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->presenceHandler = $presenceHandler;
        $this->relationUtils = $relationUtils;
        $this->santeChecker = $santeChecker;
        $this->santeHandler = $santeHandler;
        $this->facturePresenceRepository = $facturePresenceRepository;
    }

    /**
     * Etape 1 select enfant.
     *
     * @Route("/select/enfant", name="mercredi_parent_presence_select_enfant", methods={"GET"})
     */
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
            ]
        );
    }

    /**
     * Etape 2.
     *
     * @Route("/select/jour/{uuid}", name="mercredi_parent_presence_select_jours", methods={"GET","POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function selectJours(Request $request, Enfant $enfant): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $santeFiche = $this->santeHandler->init($enfant);

        if (!$this->santeChecker->isComplete($santeFiche)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('mercredi_parent_sante_fiche_show', ['uuid' => $enfant->getUuid()]);
        }

        $presenceSelectDays = new PresenceSelectDays($enfant);
        $form = $this->createForm(PresenceNewForParentType::class, $presenceSelectDays);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $days = $form->getData()->getJours();

            $this->presenceHandler->handleNew($this->tuteur, $enfant, $days);

            $this->dispatchMessage(new PresenceCreated($days));

            return $this->redirectToRoute('mercredi_parent_enfant_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/presence/select_jours.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}", name="mercredi_parent_presence_show", methods={"GET"})
     * @IsGranted("presence_show", subject="presence")
     */
    public function show(Presence $presence): Response
    {
        $facturePresence = $this->facturePresenceRepository->findByPresence($presence);

        return $this->render(
            '@AcMarcheMercrediParent/presence/show.html.twig',
            [
                'presence' => $presence,
                'facturePresence' => $facturePresence,
                'enfant' => $presence->getEnfant(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_parent_presence_delete", methods={"POST"})
     * @IsGranted("presence_edit", subject="presence")
     */
    public function delete(Request $request, Presence $presence): Response
    {
        $enfant = $presence->getEnfant();
        if ($this->isCsrfTokenValid('delete'.$presence->getId(), $request->request->get('_token'))) {
            if (!DeleteConstraint::canBeDeleted($presence)) {
                $this->addFlash('danger', 'Une présence passée ne peut être supprimée');

                return $this->redirectToRoute('mercredi_parent_enfant_show', ['uuid' => $enfant->getUuid()]);
            }
            $presenceId = $presence->getId();
            $this->presenceRepository->remove($presence);
            $this->presenceRepository->flush();
            $this->dispatchMessage(new PresenceDeleted($presenceId));
        }

        return $this->redirectToRoute('mercredi_parent_enfant_show', ['uuid' => $enfant->getUuid()]);
    }
}
