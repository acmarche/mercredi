<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Form\AccueilType;
use AcMarche\Mercredi\Accueil\Handler\AccueilHandler;
use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use AcMarche\Mercredi\Accueil\Message\AccueilUpdated;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/accueil")
 * @IsGranted("ROLE_MERCREDI_ECOLE")
 */
final class AccueilController extends AbstractController
{
    /**
     * @var string
     */
    private const ENFANT = 'enfant';
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;
    /**
     * @var AccueilHandler
     */
    private $accueilHandler;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var AccueilCalculatorInterface
     */
    private $accueilCalculator;
    /**
     * @var FactureAccueilRepository
     */
    private $factureAccueilRepository;

    public function __construct(
        AccueilRepository $accueilRepository,
        AccueilHandler $accueilHandler,
        RelationRepository $relationRepository,
        AccueilCalculatorInterface $accueilCalculator,
        FactureAccueilRepository $factureAccueilRepository
    ) {
        $this->accueilRepository = $accueilRepository;
        $this->accueilHandler = $accueilHandler;
        $this->relationRepository = $relationRepository;
        $this->accueilCalculator = $accueilCalculator;
        $this->factureAccueilRepository = $factureAccueilRepository;
    }

    /**
     * @Route("/new/{tuteur}/{enfant}", name="mercredi_ecole_accueil_new", methods={"GET","POST"})
     * @Entity("tuteur", expr="repository.find(tuteur)")
     * @Entity("enfant", expr="repository.find(enfant)")
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function new(Request $request, Tuteur $tuteur, Enfant $enfant): Response
    {
        $accueil = new Accueil($tuteur, $enfant);
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->accueilHandler->handleNew($enfant, $accueil);
            $this->dispatchMessage(new AccueilCreated($result->getId()));

            return $this->redirectToRoute('mercredi_ecole_accueil_show', ['uuid' => $result->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediEcole/accueil/new.html.twig',
            [
                self::ENFANT => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}", name="mercredi_ecole_accueil_show", methods={"GET"})
     * @IsGranted("accueil_show", subject="accueil")
     */
    public function show(Accueil $accueil): Response
    {
        $enfant = $accueil->getEnfant();
        $cout = $this->accueilCalculator->calculate($accueil);
        $factureAccueil = $this->factureAccueilRepository->findByAccueil($accueil);

        return $this->render(
            '@AcMarcheMercrediEcole/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                self::ENFANT => $enfant,
                'facture' => $factureAccueil,
            ]
        );
    }

    /**
     * @Route("/{uuid}/edit", name="mercredi_ecole_accueil_edit", methods={"GET","POST"})
     * @IsGranted("accueil_edit", subject="accueil")
     */
    public function edit(Request $request, Accueil $accueil): Response
    {
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accueilRepository->flush();

            $this->dispatchMessage(new AccueilUpdated($accueil->getId()));

            return $this->redirectToRoute('mercredi_ecole_accueil_show', ['uuid' => $accueil->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediEcole/accueil/edit.html.twig',
            [
                'accueil' => $accueil,
                'form' => $form->createView(),
            ]
        );
    }
}
