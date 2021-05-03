<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Form\AccueilType;
use AcMarche\Mercredi\Accueil\Form\SearchAccueilByDate;
use AcMarche\Mercredi\Accueil\Handler\AccueilHandler;
use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use AcMarche\Mercredi\Accueil\Message\AccueilDeleted;
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
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class AccueilController extends AbstractController
{
    /**
     * @var string
     */
    private const ENFANT = 'enfant';
    /**
     * @var string
     */
    private const ID = 'id';
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
     * @Route("/index", name="mercredi_admin_accueil_index", methods={"GET","POST"})
     *
     */
    public function index(Request $request)
    {
        $accueils = [];
        $form = $this->createForm(SearchAccueilByDate::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $date = $data['date_jour'];
            $heure = $data['heure'];
            $accueils = $this->accueilRepository->findByDateAndHeure($date, $heure);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/index.html.twig',
            [
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/list/{id}", name="mercredi_admin_accueil_show_enfant", methods={"GET","POST"})
     */
    public function enfant(Enfant $enfant): Response
    {
        $accueils = $this->accueilRepository->findByEnfant($enfant);
        $relations = $this->relationRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/enfant.html.twig',
            [
                'accueils' => $accueils,
                'relations' => $relations,
                self::ENFANT => $enfant,
            ]
        );
    }

    /**
     * @Route("/new/{tuteur}/{enfant}", name="mercredi_admin_accueil_new", methods={"GET","POST"})
     * @Entity("tuteur", expr="repository.find(tuteur)")
     * @Entity("enfant", expr="repository.find(enfant)")
     */
    public function new(Request $request, Tuteur $tuteur, Enfant $enfant): Response
    {
        $accueil = new Accueil($tuteur, $enfant);
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->accueilHandler->handleNew($enfant, $accueil);
            $this->dispatchMessage(new AccueilCreated($result->getId()));

            return $this->redirectToRoute('mercredi_admin_accueil_show', [self::ID => $result->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/new.html.twig',
            [
                self::ENFANT => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/show/{id}", name="mercredi_admin_accueil_show", methods={"GET"})
     */
    public function show(Accueil $accueil): Response
    {
        $enfant = $accueil->getEnfant();
        $cout = $this->accueilCalculator->calculate($accueil);
        $factureAccueil = $this->factureAccueilRepository->findByAccueil($accueil);

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                self::ENFANT => $enfant,
                'facture' => $factureAccueil,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_accueil_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Accueil $accueil): Response
    {
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accueilRepository->flush();

            $this->dispatchMessage(new AccueilUpdated($accueil->getId()));

            return $this->redirectToRoute('mercredi_admin_accueil_show', [self::ID => $accueil->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/edit.html.twig',
            [
                'accueil' => $accueil,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delete/{id}", name="mercredi_admin_accueil_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Accueil $accueil): Response
    {
        if ($this->isCsrfTokenValid('delete'.$accueil->getId(), $request->request->get('_token'))) {
            $id = $accueil->getId();
            $enfant = $accueil->getEnfant();
            $this->accueilRepository->remove($accueil);
            $this->accueilRepository->flush();
            $this->dispatchMessage(new AccueilDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_show', [self::ID => $enfant->getId()]);
    }
}
