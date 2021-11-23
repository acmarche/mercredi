<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Facture\Calculator\FactureCalculatorInterface;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use AcMarche\Mercredi\Facture\Repository\FactureDecompteRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureReductionRepository;
use Twig\Environment;

class FactureRenderHotton implements FactureRenderInterface
{
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureReductionRepository $factureReductionRepository;
    private FactureComplementRepository $factureComplementRepository;
    private FactureCalculatorInterface $factureCalculator;
    private FactureDecompteRepository $factureDecompteRepository;
    private Environment $environment;

    public function __construct(
        FacturePresenceRepository $facturePresenceRepository,
        FactureReductionRepository $factureReductionRepository,
        FactureComplementRepository $factureComplementRepository,
        FactureCalculatorInterface $factureCalculator,
        FactureDecompteRepository $factureDecompteRepository,
        Environment $environment
    ) {

        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureReductionRepository = $factureReductionRepository;
        $this->factureComplementRepository = $factureComplementRepository;
        $this->factureCalculator = $factureCalculator;
        $this->factureDecompteRepository = $factureDecompteRepository;
        $this->environment = $environment;
    }

    public function render(FactureInterface $facture): string
    {
        if ($facture->getPlaine()) {
            return $this->renderForPlaine($facture);
        }

        return $this->renderForPresence($facture);
    }

    public function renderForPresence(FactureInterface $facture): string
    {
        $tuteur = $facture->getTuteur();
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
        );
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL
        );

        $factureReductions = $this->factureReductionRepository->findByFacture($facture);
        $factureComplements = $this->factureComplementRepository->findByFacture($facture);
        $factureDecomptes = $this->factureDecompteRepository->findByFacture($facture);

        $dto = $this->factureCalculator->createDetail($facture);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/_show_presence.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'facturePresences' => $facturePresences,
                'factureAccueils' => $factureAccueils,
                'factureReductions' => $factureReductions,
                'factureComplements' => $factureComplements,
                'factureDecomptes' => $factureDecomptes,
                'dto' => $dto,
            ]
        );
    }

    public function renderForPlaine(FactureInterface $facture): string
    {
        $tuteur = $facture->getTuteur();
        $facturePlaines = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PLAINE
        );
        $dto = $this->factureCalculator->createDetail($facture);
         return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/_show_plaine.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'facturePlaines' => $facturePlaines,
                'dto' => $dto,
            ]
        );
    }
}
