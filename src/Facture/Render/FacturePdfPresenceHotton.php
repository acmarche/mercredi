<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use AcMarche\Mercredi\Facture\Repository\FactureDecompteRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureReductionRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\QrCode\QrCodeGenerator;
use Knp\DoctrineBehaviors\Exception\ShouldNotHappenException;
use Twig\Environment;

class FacturePdfPresenceHotton implements FacturePdfPresenceInterface
{
    public function __construct(
        private Environment $environment,
        private OrganisationRepository $organisationRepository,
        private FactureUtils $factureUtils,
        private FacturePresenceRepository $facturePresenceRepository,
        private FactureReductionRepository $factureReductionRepository,
        private FactureComplementRepository $factureComplementRepository,
        private FactureCalculatorInterface $factureCalculator,
        private FactureDecompteRepository $factureDecompteRepository,
        private QrCodeGenerator $qrCodeGenerator,
        private NotificationMailer $notificationMailer,
        private AdminEmailFactory $adminEmailFactory,
    ) {
    }

    public function render(FactureInterface $facture): string
    {
        $content = $this->prepareContent($facture);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/pdf.html.twig',
            [
                'content' => $content,
            ],
        );
    }

    public function renderMultiple(array $factures): string
    {
        $content = '';
        foreach ($factures as $facture) {
            $content .= $this->prepareContent($facture);
            $content .= '<div class="page-breaker"></div>';
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/pdf.html.twig',
            [
                'content' => $content,
            ],
        );
    }

    private function prepareContent(FactureInterface $facture): string
    {
        $organisation = $this->organisationRepository->getOrganisation();
        $data = [
            'enfants' => [],
            'cout' => 0,
        ];
        //init
        foreach ($this->factureUtils->getEnfants($facture) as $slug => $enfant) {
            $data['enfants'][$slug] = [
                'enfant' => $enfant,
                'cout' => 0,
                'peda' => 0,
                'mercredi' => 0,
                'accueils' => [
                    'Soir' => [
                        'nb' => 0,
                        'cout' => 0,
                    ],
                    'Matin' => [
                        'nb' => 0,
                        'cout' => 0,

                    ],
                ],
            ];
        }

        $tuteur = $facture->getTuteur();
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE,
        );
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL,
        );

        foreach ($facturePresences as $facturePresence) {
            $data = $this->groupPresences($facturePresence, $data);
        }

        foreach ($factureAccueils as $factureAccueil) {
            $data = $this->groupAccueils($factureAccueil, $data);
        }

        foreach ($data['enfants'] as $enfant) {
            $data['cout'] += $enfant['cout'];
        }

        $factureReductions = $this->factureReductionRepository->findByFacture($facture);
        $factureComplements = $this->factureComplementRepository->findByFacture($facture);
        $factureDecomptes = $this->factureDecompteRepository->findByFacture($facture);

        $dto = $this->factureCalculator->createDetail($facture);

        try {
            $imgQrcode = $this->qrCodeGenerator->generateForFacture($facture, $dto->total);
        } catch (ShouldNotHappenException|\Exception $e) {
            $message = $this->adminEmailFactory->messageToJf(
                'Error create qrcode hotton',
                'facture id '.$facture->getId(),
            );
            $this->notificationMailer->sendAsEmailNotification($message);
            $imgQrcode = null;
        }


        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/_presence_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'organisation' => $organisation,
                'data' => $data,
                'countAccueils' => \count($factureAccueils),
                'countPresences' => \count($facturePresences),
                'factureReductions' => $factureReductions,
                'factureComplements' => $factureComplements,
                'factureDecomptes' => $factureDecomptes,
                'dto' => $dto,
                'imgQrcode' => $imgQrcode,
            ],
        );
    }

    private function groupAccueils(FacturePresence $facturePresence, array $data): array
    {
        $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        $heure = $facturePresence->getHeure();
        $duree = $facturePresence->getDuree();
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCoutCalculated();
        $data['enfants'][$slug->toString()]['accueils'][$heure]['nb'] += $duree;

        return $data;
    }

    private function groupPresences(FacturePresence $facturePresence, array $data): array
    {
        $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        if ($facturePresence->isPedagogique()) {
            ++$data['enfants'][$slug->toString()]['peda'];
        }
        if (!$facturePresence->isPedagogique()) {
            ++$data['enfants'][$slug->toString()]['mercredi'];
        }
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCoutCalculated();

        return $data;
    }
}
