<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Form\SearchAccueilByDate;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/accueil_ecoles")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class AccueilEcoleController extends AbstractController
{
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;
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
        AccueilCalculatorInterface $accueilCalculator,
        FactureAccueilRepository $factureAccueilRepository
    ) {
        $this->accueilRepository = $accueilRepository;
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
            '@AcMarcheMercrediAdmin/accueil_ecole/index.html.twig',
            [
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}/show", name="mercredi_admin_accueil_show", methods={"GET"})
     *
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
                'enfant' => $enfant,
                'facture' => $factureAccueil,
            ]
        );
    }

}
