<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Form\AccueilParentType;
use AcMarche\Mercredi\Accueil\Form\AccueilType;
use AcMarche\Mercredi\Accueil\Handler\AccueilHandler;
use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use AcMarche\Mercredi\Accueil\Message\AccueilDeleted;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Constraint\DeleteConstraint;
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
 * @Route("/accueil")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
class AccueilController extends AbstractController
{
    use GetTuteurTrait;

    /**
     * @var AccueilRepository
     */
    private $accueilRepository;
    /**
     * @var AccueilHandler
     */
    private $accueilHandler;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var RelationUtils
     */
    private $relationUtils;
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;
    /**
     * @var SanteChecker
     */
    private $santeChecker;
    /**
     * @var SanteHandler
     */
    private $santeHandler;
    /**
     * @var AccueilCalculatorInterface
     */
    private $accueilCalculator;

    public function __construct(
        RelationUtils $relationUtils,
        TuteurUtils $tuteurUtils,
        AccueilRepository $accueilRepository,
        JourRepository $jourRepository,
        AccueilHandler $accueilHandler,
        SanteChecker $santeChecker,
        SanteHandler $santeHandler,
        AccueilCalculatorInterface $accueilCalculator
    ) {
        $this->accueilRepository = $accueilRepository;
        $this->accueilHandler = $accueilHandler;
        $this->jourRepository = $jourRepository;
        $this->relationUtils = $relationUtils;
        $this->tuteurUtils = $tuteurUtils;
        $this->santeChecker = $santeChecker;
        $this->santeHandler = $santeHandler;
        $this->accueilCalculator = $accueilCalculator;
    }

    /**
     * Etape 1 select enfant.
     *
     * @Route("/select/enfant", name="mercredi_parent_accueil_select_enfant", methods={"GET"})
     */
    public function selectEnfant()
    {
        $this->hasTuteur();

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/accueil/select_enfant.html.twig',
            [
                'enfants' => $enfants,
            ]
        );
    }

    /**
     * Etape 2.
     *
     * @Route("/select/jour/{uuid}", name="mercredi_parent_accueil_select_jours", methods={"GET","POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function selectJours(Request $request, Enfant $enfant)
    {
        $this->hasTuteur();
        $santeFiche = $this->santeHandler->init($enfant);

        if (!$this->santeChecker->isComplete($santeFiche)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('mercredi_parent_sante_fiche_show', ['uuid' => $enfant->getUuid()]);
        }

        $accueil = new Accueil($this->tuteur, $enfant);
        $form = $this->createForm(AccueilParentType::class, $accueil);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->accueilHandler->handleNew($enfant, $accueil);
            $this->dispatchMessage(new AccueilCreated($result->getId()));

            return $this->redirectToRoute('mercredi_parent_accueil_show', ['uuid' => $result->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/accueil/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}", name="mercredi_parent_accueil_show", methods={"GET"})
     * @IsGranted("accueil_show", subject="accueil")
     */
    public function show(Accueil $accueil): Response
    {
        $cout = $this->accueilCalculator->calculate($accueil);

        return $this->render(
            '@AcMarcheMercrediParent/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                'enfant' => $accueil->getEnfant(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_parent_accueil_delete", methods={"DELETE"})
     * @IsGranted("accueil_edit", subject="accueil")
     */
    public function delete(Request $request, Accueil $accueil): Response
    {
        $enfant = $accueil->getEnfant();
        if ($this->isCsrfTokenValid('delete'.$accueil->getId(), $request->request->get('_token'))) {
            if (!DeleteConstraint::accueilCanBeDeleted($accueil)) {
                $this->addFlash('danger', 'Un accueil passé ne peut être supprimé');

                return $this->redirectToRoute('mercredi_parent_enfant_show', ['uuid' => $enfant->getUuid()]);
            }
            $accueilId = $accueil->getId();
            $this->accueilRepository->remove($accueil);
            $this->accueilRepository->flush();
            $this->dispatchMessage(new AccueilDeleted($accueilId));
        }

        return $this->redirectToRoute('mercredi_parent_enfant_show', ['uuid' => $enfant->getUuid()]);
    }
}
