<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Contrat\AccueilInterface;
use AcMarche\Mercredi\Accueil\Form\AccueilRetardTpe;
use AcMarche\Mercredi\Accueil\Form\AccueilType;
use AcMarche\Mercredi\Accueil\Form\InscriptionsType;
use AcMarche\Mercredi\Accueil\Form\SearchAccueilByDate;
use AcMarche\Mercredi\Accueil\Handler\AccueilHandler;
use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use AcMarche\Mercredi\Accueil\Message\AccueilUpdated;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Utils\DateUtils;
use DateTime;
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

    private AccueilRepository $accueilRepository;
    private AccueilHandler $accueilHandler;
    private RelationRepository $relationRepository;
    private AccueilCalculatorInterface $accueilCalculator;
    private EnfantRepository $enfantRepository;
    private DateUtils $dateUtils;
    private FacturePresenceRepository $facturePresenceRepository;

    public function __construct(
        AccueilRepository $accueilRepository,
        AccueilHandler $accueilHandler,
        RelationRepository $relationRepository,
        AccueilCalculatorInterface $accueilCalculator,
        EnfantRepository $enfantRepository,
        DateUtils $dateUtils,
        FacturePresenceRepository $facturePresenceRepository
    ) {
        $this->accueilRepository = $accueilRepository;
        $this->accueilHandler = $accueilHandler;
        $this->relationRepository = $relationRepository;
        $this->accueilCalculator = $accueilCalculator;
        $this->enfantRepository = $enfantRepository;
        $this->dateUtils = $dateUtils;
        $this->facturePresenceRepository = $facturePresenceRepository;
    }

    /**
     * @Route("/index", name="mercredi_ecole_accueils_index", methods={"GET", "POST"})
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
     * @Route("/new/{tuteur}/{enfant}", name="mercredi_ecole_accueil_new", methods={"GET", "POST"})
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
                'enfant' => $enfant,
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
        $factureAccueil = $this->facturePresenceRepository->findByAccueil($accueil);

        return $this->render(
            '@AcMarcheMercrediEcole/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                'enfant' => $enfant,
                'facture' => $factureAccueil,
            ]
        );
    }

    /**
     * @Route("/{uuid}/edit", name="mercredi_ecole_accueil_edit", methods={"GET", "POST"})
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
     * @Route("/inscriptions/{id}/year/{year}/month/{month}/week/{week}/heure/{heure}", name="mercredi_ecole_accueil_inscriptions", methods={"GET", "POST"})
     */
    public function inscriptions(
        Request $request,
        Ecole $ecole,
        int $year,
        int $month,
        string $heure,
        int $week = 0
    ): Response {
        if (0 !== $week) {
            $date = $this->dateUtils->createDateImmutableFromYearWeek($year, $week);
            $weekSelected = $week;
        } else {
            $date = $this->dateUtils->createDateImmutableFromYearMonth($year, $month);
            $weekSelected = $date->format('W');
        }

        $weekPeriod = $this->dateUtils->getWeekByNumber($date, $week);
        $data = [];
        $enfants = $this->enfantRepository->findByEcolesForInscription($ecole);

        foreach ($enfants as $enfant) {
            $tuteurSelected = 0;
            $accueils = $this->accueilRepository->findByEnfantAndDaysAndHeure($enfant, $weekPeriod, $heure);
            $rows = ['accueils' => [], 'tuteurs' => []];
            foreach ($accueils as $accueil) {
                $rows['accueils'][$accueil->getDateJour()->format('Y-m-d')] = [
                    'duree' => $accueil->getDuree(),
                    'tuteur' => $accueil->getTuteur()->getId(),
                ];
                $weekAccueil = $accueil->getDateJour()->format('W');
                if ($weekSelected == $weekAccueil) {
                    $tuteurSelected = $accueil->getTuteur()->getId();
                }
            }
            $rows['tuteurSelected'] = $tuteurSelected;
            $data[$enfant->getId()] = $rows;
        }

        $form = $this->createForm(InscriptionsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accueils = $request->request->get('accueils');
            $tuteurs = $request->request->get('tuteurs');

            $this->accueilHandler->handleCollections($accueils, $tuteurs, $heure);
            $this->addFlash('success', 'Les acceuils ont bien été sauvegardés');

            return $this->redirectToRoute('mercredi_ecole_accueil_inscriptions', [
                'id' => $ecole->getId(),
                'year' => $year,
                'month' => $month,
                'heure' => $heure,
                'week' => $week,
            ]);
        }

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

    /**
     * @Route("/{uuid}/retard", name="mercredi_ecole_accueil_retard", methods={"GET", "POST"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function retard(Request $request, Enfant $enfant): Response
    {
        $args = ['date_retard' => new DateTime(), 'heure_retard' => new DateTime()];
        $form = $this->createForm(AccueilRetardTpe::class, $args);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dateRetard = $data['date_retard'];
            $heureRetard = $data['heure_retard'];
            if (($accueil = $this->accueilRepository->findOneByEnfantAndDayAndHour(
                $enfant,
                $dateRetard,
                AccueilInterface::SOIR
            )) === null) {
                $this->addFlash('danger', 'Aucun accueil encodé pour ce jour là. Veuillez d\'abord ajouté un accueil');
            } else {
                $dateRetard->setTime($heureRetard->format('H'), $heureRetard->format('i'));
                $accueil->setHeureRetard($dateRetard);
                $this->accueilRepository->flush();
                $this->addFlash('success', 'Le retard a bien été ajouté');
            }

            return $this->redirectToRoute('mercredi_ecole_enfant_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediEcole/accueil/retard.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
