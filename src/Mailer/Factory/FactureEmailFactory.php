<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Render\FactureRender;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Notification\Notification;

class FactureEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;
    use PdfDownloaderTrait;

    private FactureRender $factureRender;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        FactureRender $factureRender,
        ParameterBagInterface $parameterBag
    ) {
        $this->factureRender = $factureRender;
        $this->parameterBag = $parameterBag;
    }

    public function initFromAndToForForm(?Facture $facture = null): array
    {
        $data = [];
        $data['from'] = null !== $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';
        if ($facture) {
            $tuteur = $facture->getTuteur();
            if ($emails = TuteurUtils::getEmailsOfOneTuteur($tuteur)) {
                $data['to'] = $emails[0];
            }
        }

        return $data;
    }

    /**
     * @param Facture $facture
     * @param string $from
     * @param string $sujet
     * @param string $body
     * @return \Symfony\Bridge\Twig\Mime\NotificationEmail
     */
    public function messageFacture(string $from, string $sujet, string $body): NotificationEmail
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject($sujet)
            ->from($from)
            ->htmlTemplate('@AcMarcheMercrediEmail/admin/facture_mail.html.twig')
            ->textTemplate('@AcMarcheMercrediEmail/admin/facture_mail.txt.twig')
            ->context(
                [
                    "importance" => Notification::IMPORTANCE_HIGH,
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
            $message->addBcc(new Address('cartourba@hotton.be', $email));
            $message->addBcc(new Address('jf@marche.be', $email));
            $message->addTo(new Address($email));
        }
    }

    /**
     * @throws \Exception
     */
    public function attachFactureFromPath(NotificationEmail $message, Facture $facture): void
    {
        $path = $this->parameterBag->get('kernel.project_dir').'/var/factures/';
        $factureFile = $path.'facture-'.$facture->getId().'.pdf';

        $date = $facture->getFactureLe();
        if (!is_readable($factureFile)) {
            throw new \Exception('Pdf non trouvé '.$factureFile);
        }
        $message->attachFromPath($factureFile, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }

    /**
     * acces refuse wget https://assetx
     * @param $facture
     * @param $message
     */
    public function attachFactureOnTheFly($facture, $message)
    {
        $htmlInvoice = $this->factureRender->generateOneHtml($facture);
        $invoicepdf = $this->getPdf()->getOutputFromHtml($htmlInvoice);

        $date = $facture->getFactureLe();
        $message->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }
}
