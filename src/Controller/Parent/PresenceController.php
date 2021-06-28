<?php

namespace AcMarche\Mercredi\Controller\Parent;

use Symfony\Component\HttpFoundation\RedirectResponse;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Presence\Constraint\DeleteConstraint;
use AcMarche\Mercredi\Presence\Dto\PresenceSelectDays;
use AcMarche\Mercredi\Presence\Form\PresenceNewForParentType;
use AcMarche\Mercredi\Presence\Form\SearchPresenceType;
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
    /**
     * @var string
     */
    private const UUID = 'uuid';
    /**
     * @var string
     */
    private const MERCREDI_PARENT_ENFANT_SHOW = 'mercredi_parent_enfant_show';

    private PresenceRepository $presenceRepository;
    private PresenceHandler $presenceHandler;
    private RelationUtils $relationUtils;
    private SanteChecker $santeChecker;
    private SanteHandler $santeHandler;

    public function __construct(
        RelationUtils $relationUtils,
        PresenceRepository $presenceRepository,
        PresenceHandler $presenceHandler,
        SanteChecker $santeChecker,
        SanteHandler $santeHandler
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->presenceHandler = $presenceHandler;
        $this->relationUtils = $relationUtils;
        $this->santeChecker = $santeChecker;
        $this->santeHandler = $santeHandler;
    }

    /**
     * Route("/", name="mercredi_parent_presence_index", methods={"GET","POST"}).
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchPresenceType::class);
        $form->handleRequest($request);
        $data = [];
        $search = $displayRemarque = false;
        $jour = $remarques = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            /** @var Jour $jour */
            $jour = $dataForm['jour'];
            $displayRemarque = $dataForm['displayRemarque'];

            $search = true;
            $data = $this->presenceHandler->handleForGrouping($jour, $dataForm['ecole'], $displayRemarque);
        }

        return $this->render(
            '@AcMarcheMercrediParent/presence/index.html.twig',
            [
                'data' => $data,
                'form' => $form->createView(),
                'search' => $search,
                'jour' => $jour,
                'display_remarques' => $displayRemarque,
            ]
        );
    }

    /**
     * Etape 1 select enfant.
     *
     * @Route("/select/enfant", name="mercredi_parent_presence_select_enfant", methods={"GET"})
     */
    public function selectEnfant(): Response
    {
        if (($t = $this->hasTuteur()) !== null) {
            return $t;
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
        if (($t = $this->hasTuteur()) !== null) {
            return $t;
        }
        $santeFiche = $this->santeHandler->init($enfant);

        if (! $this->santeChecker->isComplete($santeFiche)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('mercredi_parent_sante_fiche_show', [self::UUID => $enfant->getUuid()]);
        }

        $presenceSelectDays = new PresenceSelectDays($enfant);
        $form = $this->createForm(PresenceNewForParentType::class, $presenceSelectDays);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $days = $form->getData()->getJours();

            $this->presenceHandler->handleNew($this->tuteur, $enfant, $days);

            $this->dispatchMessage(new PresenceCreated($days));

            return $this->redirectToRoute(self::MERCREDI_PARENT_ENFANT_SHOW, [self::UUID => $enfant->getUuid()]);
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
        return $this->render(
            '@AcMarcheMercrediParent/presence/show.html.twig',
            [
                'presence' => $presence,
                'enfant' => $presence->getEnfant(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_parent_presence_delete", methods={"DELETE"})
     * @IsGranted("presence_edit", subject="presence")
     */
    public function delete(Request $request, Presence $presence): RedirectResponse
    {
        $enfant = $presence->getEnfant();
        if ($this->isCsrfTokenValid('delete'.$presence->getId(), $request->request->get('_token'))) {
            if (! DeleteConstraint::canBeDeleted($presence)) {
                $this->addFlash('danger', 'Une présence passée ne peut être supprimée');

                return $this->redirectToRoute(self::MERCREDI_PARENT_ENFANT_SHOW, [self::UUID => $enfant->getUuid()]);
            }
            $presenceId = $presence->getId();
            $this->presenceRepository->remove($presence);
            $this->presenceRepository->flush();
            $this->dispatchMessage(new PresenceDeleted($presenceId));
        }

        return $this->redirectToRoute(self::MERCREDI_PARENT_ENFANT_SHOW, [self::UUID => $enfant->getUuid()]);
    }
}
