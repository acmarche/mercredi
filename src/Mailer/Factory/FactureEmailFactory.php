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

class FactureEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;
    use PdfDownloaderTrait;

    private FactureRender $factureRender;

    public function __construct(
        FactureRender $factureRender
    ) {
        $this->factureRender = $factureRender;
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
        $data['to'] = 'jf@marche.be';
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject($data['sujet'])
            ->from($data['from'])
            ->to($data['to'])
            ->htmlTemplate('@AcMarcheMercrediEmail/admin/facture_mail.html.twig')
            ->context(
                [
                    "importance" => "low",
                    'texte' => $data['texte'],
                    'organisation' => $this->organisation,
                    'footer_text' => 'orga',
                ]
            );

        $htmlInvoice = $this->factureRender->generateOneHtml($facture);
        $invoicepdf = $this->getPdf()->getOutputFromHtml($htmlInvoice);
        $date = $facture->getFactureLe();

        $message->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');

        return $message;
    }
}
