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

    public function initFromAndTo(?Facture $facture = null): array
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
     * @param array $data
     * @return \Symfony\Bridge\Twig\Mime\NotificationEmail
     */
    public function messageFacture(Facture $facture, array $data): NotificationEmail
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject($data['sujet'])
            ->from($data['from'])
            ->htmlTemplate('@AcMarcheMercrediEmail/admin/facture_mail.html.twig')
            ->textTemplate('@AcMarcheMercrediEmail/admin/facture_mail.txt.twig')
            ->context(
                [
                    "importance" => Notification::IMPORTANCE_HIGH,
                    'texte' => $data['texte'],
                    'organisation' => $this->organisation,
                    'footer_text' => 'orga',
                ]
            );

        foreach ($data['tos'] as $email) {
            $message->addTo(new Address('jf@marche.be', $email));
        }
        $path = $this->parameterBag->get('kernel.project_dir').'/var/factures/';
        $factureFile = $path.'facture-'.$facture->getId().'.pdf';

        $date = $facture->getFactureLe();
        $message->attachFromPath($factureFile, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');

        return $message;
    }

    /**
     * bug sur ovh si genere depuis console
     * acces refuse wget https://assetx
     * @param $facture
     * @param $message
     */
    private function genereatAlavolee($facture, $message)
    {
        $htmlInvoice = $this->factureRender->generateOneHtml($facture);
        $invoicepdf = $this->getPdf()->getOutputFromHtml($htmlInvoice);

        $date = $facture->getFactureLe();
        $message->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }
}
