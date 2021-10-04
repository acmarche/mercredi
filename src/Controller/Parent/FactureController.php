<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Calculator\FactureCalculatorInterface;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_PARENT")
 * @Route("/facture")
 */
final class FactureController extends AbstractController
{
    use GetTuteurTrait;

    private FactureRepository $factureRepository;
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureCalculatorInterface $factureCalculator;

    public function __construct(
        FactureRepository $factureRepository,
        FacturePresenceRepository $facturePresenceRepository,
        FactureCalculatorInterface $factureCalculator
    ) {
        $this->factureRepository = $factureRepository;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureCalculator = $factureCalculator;
    }

    /**
     * @Route("/", name="mercredi_parent_facture_index", methods={"GET","POST"})
     */
    public function index(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $factures = $this->factureRepository->findFacturesByTuteurWhoIsSend($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $this->tuteur,
            ]
        );
    }

    /**
     * @Route("/{uuid}/show", name="mercredi_parent_facture_show", methods={"GET"})
     */
    public function show(Facture $facture): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
        );
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL
        );
        $facturePlaines = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PLAINE
        );

        $tuteur = $this->tuteur;

        $dto = $this->factureCalculator->createDetail($facture);

        return $this->render(
            '@AcMarcheMercrediParent/facture/show.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'facturePresences' => $facturePresences,
                'factureAccueils' => $factureAccueils,
                'facturePlaines' => $facturePlaines,
                'dto' => $dto,
            ]
        );
    }
}
