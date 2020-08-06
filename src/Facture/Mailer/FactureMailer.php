<?php

namespace AcMarche\Mercredi\Facture\Mailer;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use function count;

final class FactureMailer
{
    use InitMailerTrait;

    /**
     * @var FactureFactory
     */
    private $factureFactory;

    public function __construct(
        FactureFactory $factureFactory
    ) {
        $this->factureFactory = $factureFactory;
    }

    public function init(Facture $facture): array
    {
        $data = [];
        $data['from'] = $this->organisation !== null ? $this->organisation->getEmail() : 'nomail@domain.be';
        $tuteur = $facture->getTuteur();
        $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);
        $data['to'] = count($emails) > 0 ? $emails[0] : null;

        return $data;
    }

    /**
     * @param Facture $facture
     * @param array $data
     */
    public function sendFacture(Facture $facture, array $data): void
    {
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

        $date = $facture->getFactureLe();
        $html = $this->factureFactory->generateFullHtml($facture);
        $templatedEmail->attach($html, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');

        $this->sendMail($templatedEmail);
    }
}
