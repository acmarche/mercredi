<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Facture\FacturePdfPlaineInterface;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\QrCode\QrCodeGenerator;
use Knp\DoctrineBehaviors\Exception\ShouldNotHappenException;
use Twig\Environment;

class FacturePdfPlaineMarche implements FacturePdfPlaineInterface
{
    public function __construct(
        private OrganisationRepository $organisationRepository,
        private FactureCalculatorInterface $factureCalculator,
        private PlainePresenceRepository $plainePresenceRepository,
        private FacturePresenceRepository $facturePresenceRepository,
        private Environment $environment,
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

    private function prepareContent(FactureInterface $facture): string
    {
        $tuteur = $facture->getTuteur();
        $plaine = $facture->getPlaine();
        $facturePlaines = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PLAINE,
        );
        $dto = $this->factureCalculator->createDetail($facture);
        $organisation = $this->organisationRepository->getOrganisation();
        $enfants = $this->plainePresenceRepository->findEnfantsByPlaineAndTuteur($plaine, $tuteur);

        try {
            $imgQrcode = $this->qrCodeGenerator->generate($facture, $dto->total);
        } catch (ShouldNotHappenException|\Exception $e) {
            $message = $this->adminEmailFactory->messageToJf(
                'Error create qrcode Marche plaine',
                'facture id '.$facture->getId().' error: '.$e->getMessage(),
            );
            $this->notificationMailer->sendAsEmailNotification($message);
            $imgQrcode = null;
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/marche/_plaine_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'enfants' => $enfants,
                'facturePlaines' => $facturePlaines,
                'organisation' => $organisation,
                'dto' => $dto,
                'imgQrcode' => $imgQrcode,
                'plaine' => $plaine,
            ],
        );
    }
}
