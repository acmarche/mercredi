<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Facture\FactureRenderInterface;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use AcMarche\Mercredi\Facture\Repository\FactureDecompteRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureReductionRepository;
use Twig\Environment;

class FactureRenderMarche implements FactureRenderInterface
{
    public function __construct(
        private FacturePresenceRepository $facturePresenceRepository,
        private FactureReductionRepository $factureReductionRepository,
        private FactureComplementRepository $factureComplementRepository,
        private FactureCalculatorInterface $factureCalculator,
        private FactureDecompteRepository $factureDecompteRepository,
        private Environment $environment
    ) {
    }

    public function render(FactureInterface $facture): string
    {
        if ($facture->getPlaineNom()) {
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
            '@AcMarcheMercrediAdmin/facture/marche/_show_presence.html.twig',
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
            '@AcMarcheMercrediAdmin/facture/marche/_show_plaine.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'facturePlaines' => $facturePlaines,
                'dto' => $dto,
            ]
        );
    }
}
