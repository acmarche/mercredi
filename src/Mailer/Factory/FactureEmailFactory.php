<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Render\FactureRender;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
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
     */
    public function messageFacture(Facture $facture, array $data): NotificationEmail
    {
        $data['to'] = 'jf@marche.be';
        $message = NotificationEmail::asPublicEmail();
        $message
            ->subject($data['sujet'])
            ->from($data['from'])
            ->to($data['to'])
            ->textTemplate('@AcMarcheMercrediAdmin/facture/mail/_send.txt.twig')
            ->context(
                [
                    'texte' => $data['texte'],
                    'organisation' => $this->organisation,
                ]
            );

        $html = $this->factureRender->generateOneHtml($facture);
        $date = $facture->getFactureLe();
        $invoicepdf = $this->getPdf()->getOutputFromHtml($html);

        $message->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');

        return $message;
    }
}
