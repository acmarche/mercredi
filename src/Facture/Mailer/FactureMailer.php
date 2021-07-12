<?php

namespace AcMarche\Mercredi\Facture\Mailer;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use function count;

final class FactureMailer
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;
    use PdfDownloaderTrait;

    private FactureFactory $factureFactory;

    public function __construct(
        FactureFactory $factureFactory
    ) {
        $this->factureFactory = $factureFactory;
    }

    public function init(?Facture $facture = null): array
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
     * @throws TransportExceptionInterface
     */
    public function sendFacture(Facture $facture, array $data): void
    {
        $data['to'] = 'jf@marche.be';
        $templatedEmail = (new TemplatedEmail())
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

        $html = $this->factureFactory->generateFullHtml($facture);
        $date = $facture->getFactureLe();
        $invoicepdf = $this->getPdf()->getOutputFromHtml($html);

        $templatedEmail->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');

        $this->sendMail($templatedEmail);
    }
}
