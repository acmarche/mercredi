<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\QrCode\QrCodeGenerator;
use Knp\DoctrineBehaviors\Exception\ShouldNotHappenException;
use Twig\Environment;

class FacturePdfPresenceMarche implements FacturePdfPresenceInterface
{
    public function __construct(
        private Environment $environment,
        private OrganisationRepository $organisationRepository,
        private FactureUtils $factureUtils,
        private FacturePresenceRepository $facturePresenceRepository,
        private FactureCalculatorInterface $factureCalculator,
        private QrCodeGenerator $qrCodeGenerator,
        private NotificationMailer $notificationMailer,
        private AdminEmailFactory $adminEmailFactory,
    ) {}

    public function render(FactureInterface $facture): string
    {
        $content = $this->prepareContent($facture);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/marche/pdf.html.twig',
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
            '@AcMarcheMercrediAdmin/facture/marche/pdf.html.twig',
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
                'mercredi' => 0,
            ];
        }

        $tuteur = $facture->getTuteur();
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE,
        );

        foreach ($facturePresences as $facturePresence) {
            $data = $this->groupPresences($facturePresence, $data);
        }

        foreach ($data['enfants'] as $enfant) {
            $data['cout'] += $enfant['cout'];
        }

        $dto = $this->factureCalculator->createDetail($facture);

        try {
            $imgQrcode = $this->qrCodeGenerator->generate($facture, $dto->total);
        } catch (ShouldNotHappenException|\Exception $e) {
            $message = $this->adminEmailFactory->messageToJf(
                'Error create qrcode marche',
                'facture id '.$facture->getId(),
            );
            $this->notificationMailer->sendAsEmailNotification($message);
            $imgQrcode = null;
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/marche/_presence_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'organisation' => $organisation,
                'data' => $data,
                'countPresences' => \count($facturePresences),
                'dto' => $dto,
                'imgQrcode' => $imgQrcode,
            ],
        );
    }

    private function groupPresences(FacturePresence $facturePresence, array $data): array
    {
        $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        if (!$facturePresence->isPedagogique()) {
            ++$data['enfants'][$slug->toString()]['mercredi'];
        }
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCoutCalculated();

        return $data;
    }
}
