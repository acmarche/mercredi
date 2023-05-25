<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Contrat\Facture\FacturePdfPlaineInterface;
use AcMarche\Mercredi\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Factory\FacturePdfFactoryTrait;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Exception;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Notification\Notification;

class FactureEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;
    use PdfDownloaderTrait;

    public function __construct(
        private FacturePdfPresenceInterface $facturePdfPresence,
        private FacturePdfPlaineInterface $facturePdfPlaine,
        private FactureFactory $factureFactory,
        private FacturePdfFactoryTrait $facturePdfFactory,
        private ParameterBagInterface $parameterBag
    ) {
    }

    public function initFromAndToForForm(?Facture $facture = null): array
    {
        $data = [];
        $data['from'] = $this->getEmailAddressOrganisation();
        if (null !== $facture) {
            $tuteur = $facture->getTuteur();
            if ($emails = TuteurUtils::getEmailsOfOneTuteur($tuteur)) {
                $data['to'] = $emails[0];
            }
        }

        return $data;
    }

    /**
     * @param Facture $from
     */
    public function messageFacture(string $from, string $sujet, string $body): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject($sujet)
            ->from($from)
            ->htmlTemplate('@AcMarcheMercrediEmail/admin/facture_mail.html.twig')
            ->textTemplate('@AcMarcheMercrediEmail/admin/facture_mail.txt.twig')
            ->context(
                [
                    'importance' => Notification::IMPORTANCE_HIGH,
                    'texte' => $body,
                    'organisation' => $this->organisation,
                    'footer_text' => 'orga',
                ]
            );

        return $message;
    }

    public function setTos(NotificationEmail $message, array $tos): void
    {
        foreach ($tos as $email) {
            $message->addTo(new Address("jf@marche.be",$email));
        }
        if ($this->parameterBag->has(Option::EMAILS_FACTURE)) {
            $copies = explode(',', $this->parameterBag->get(Option::EMAILS_FACTURE));
            if (\is_array($copies)) {
                foreach ($copies as $copy) {
                    if (filter_var($copy, FILTER_VALIDATE_EMAIL)) {
                        $message->addBcc(new Address($copy));
                    }
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function attachFactureFromPath(NotificationEmail $message, Facture $facture): void
    {
        $path = $this->factureFactory->getBasePathFacture($facture->getMois());
        $factureFile = $path.'facture-'.$facture->getId().'.pdf';

        $date = $facture->getFactureLe();
        if (!is_readable($factureFile)) {
            throw new Exception('Pdf non trouvé '.$factureFile);
        }
        $message->attachFromPath($factureFile, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }

    /**
     * acces refuse wget https://assetx en console.
     */
    public function attachFactureOnTheFly(FactureInterface $facture, Email $message): void
    {
        $htmlInvoice = $this->factureFactory->createHtml($facture);
        $invoicepdf = $this->getPdf()->getOutputFromHtml($htmlInvoice);

        $date = $facture->getFactureLe();
        $message->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }

    public function attachFacturePlaineOnTheFly(FactureInterface $facture, Email $message): void
    {
        $htmlInvoice = $this->facturePdfPlaine->render($facture);
        $invoicepdf = $this->getPdf()->getOutputFromHtml($htmlInvoice);

        $date = $facture->getFactureLe();
        $message->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }
}
