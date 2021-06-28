<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Form\AccueilType;
use AcMarche\Mercredi\Accueil\Form\InscriptionsType;
use AcMarche\Mercredi\Accueil\Form\SearchAccueilByDate;
use AcMarche\Mercredi\Accueil\Handler\AccueilHandler;
use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use AcMarche\Mercredi\Accueil\Message\AccueilUpdated;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Utils\DateUtils;
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
    use GetEcolesTrait;

    /**
     * @var string
     */
    private const ENFANT = 'enfant';
    private AccueilRepository $accueilRepository;
    private AccueilHandler $accueilHandler;
    private RelationRepository $relationRepository;
    private AccueilCalculatorInterface $accueilCalculator;
    private FactureAccueilRepository $factureAccueilRepository;
    private EnfantRepository $enfantRepository;
    private DateUtils $dateUtils;

    public function __construct(
        AccueilRepository $accueilRepository,
        AccueilHandler $accueilHandler,
        RelationRepository $relationRepository,
        AccueilCalculatorInterface $accueilCalculator,
        FactureAccueilRepository $factureAccueilRepository,
        EnfantRepository $enfantRepository,
        DateUtils $dateUtils
    ) {
        $this->accueilRepository = $accueilRepository;
        $this->accueilHandler = $accueilHandler;
        $this->relationRepository = $relationRepository;
        $this->accueilCalculator = $accueilCalculator;
        $this->factureAccueilRepository = $factureAccueilRepository;
        $this->enfantRepository = $enfantRepository;
        $this->dateUtils = $dateUtils;
    }

    /**
     * @Route("/index", name="mercredi_ecole_accueils_index", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ECOLE")
     */
    public function index(Request $request): Response
    {
        if (($response = $this->hasEcoles()) !== null) {
            return $response;
        }

        $accueils = [];
        $form = $this->createForm(SearchAccueilByDate::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $date = $data['date_jour'];
            $heure = $data['heure'];
            $ecoles = $this->ecoles;
            $accueils = $this->accueilRepository->findByDateAndHeureAndEcoles($date, $heure, $ecoles);
        }

        return $this->render(
            '@AcMarcheMercrediEcole/accueil/index.html.twig',
            [
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
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
     * @Route("/{uuid}/show", name="mercredi_ecole_accueil_show", methods={"GET"})
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

    /**
     * @Route("/inscriptions/{id}/year/{year}/month/{month}/week/{week}/heure/{heure}", name="mercredi_ecole_accueil_inscriptions", methods={"GET","POST"})
     * @param Request $request
     * @param Ecole $ecole
     * @param int $year
     * @param int $month
     * @param string $heure
     * @param int $week
     * @return Response
     */
    public function inscriptions(
        Request $request,
        Ecole $ecole,
        int $year,
        int $month,
        string $heure,
        int $week = 0
    ): Response {
        if ($week !== 0) {
            $date = $this->dateUtils->createDateImmutableFromYearWeek($year, $week);
        } else {
            $date = $this->dateUtils->createDateImmutableFromYearMonth($year, $month);
        }

        $data = [];
        $enfants = $this->enfantRepository->findByEcolesForInscription($ecole);
        foreach ($enfants as $enfant) {
            $accueils = $this->accueilRepository->findByEnfantAndHeure($enfant, $heure);
            $rows = ['accueils' => [], 'tuteurs' => []];
            foreach ($accueils as $accueil) {
                $rows['accueils'][$accueil->getDateJour()->format('Y-m-d')] = $accueil->getDuree();
                $rows['tuteur'] = $accueil->getTuteur()->getId();
                $data[$enfant->getId()] = $rows;
            }
        }

        $form = $this->createForm(InscriptionsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accueils = $request->request->get('accueils');
            $tuteurs = $request->request->get('tuteurs');

            $this->accueilHandler->handleCollections($accueils, $tuteurs, $heure);

            $this->addFlash('success', "Les acceuils ont bien été ajoutés");

            return $this->redirectToRoute('mercredi_ecole_ecole_show', ['id' => $ecole->getId()]);
        }

        $weekPeriod = $this->dateUtils->getWeekByNumber($date, $week);
        $calendar = $this->dateUtils->renderMonth($ecole, $heure, $week, $date);

        return $this->render(
            '@AcMarcheMercrediEcole/accueil/inscriptions.html.twig',
            [
                'ecole' => $ecole,
                'enfants' => $enfants,
                'week' => $weekPeriod,
                'date' => $date,
                'heure' => $heure,
                'form' => $form->createView(),
                'calendar' => $calendar,
                'data' => $data,
            ]
        );
    }
}
