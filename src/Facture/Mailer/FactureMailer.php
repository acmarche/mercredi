<?php

namespace AcMarche\Mercredi\Facture\Mailer;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Factory\FacturePdfFactory;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class FactureMailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;
    /**
     * @var \AcMarche\Mercredi\Entity\Organisation|null
     */
    private $organisation;
    /**
     * @var FacturePdfFactory
     */
    private $facturePdfFactory;
    /**
     * @var FactureFactory
     */
    private $factureFactory;

    public function __construct(
        MailerInterface $mailer,
        OrganisationRepository $organisationRepository,
        FactureFactory $factureFactory
    ) {
        $this->mailer = $mailer;
        $this->organisationRepository = $organisationRepository;
        $this->organisation = $organisationRepository->getOrganisation();
        $this->factureFactory = $factureFactory;
    }

    public function init(Facture $facture): array
    {
        $data = [];
        $data['from'] = $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';
        $tuteur = $facture->getTuteur();
        $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);
        $data['to'] = count($emails) > 0 ? $emails[0] : null;

        return $data;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendFacture(Facture $facture, array $data)
    {
        $message = (new TemplatedEmail())
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
        $message->attach($html, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');

        $this->mailer->send($message);
    }
}
