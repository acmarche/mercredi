<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Handler\FacturePlaineHandler;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PlaineHandlerHotton implements PlaineHandlerInterface
{
    private PlainePresenceRepository $plainePresenceRepository;
    private FacturePlaineHandler $facturePlaineHandler;
    private FactureEmailFactory $factureEmailFactory;
    private NotificationMailer $notificationMailer;
    private AdminEmailFactory $adminEmailFactory;
    private PresenceHandlerInterface $presenceHandler;

    public function __construct(
        PlainePresenceRepository $plainePresenceRepository,
        FacturePlaineHandler $facturePlaineHandler,
        FactureEmailFactory $factureEmailFactory,
        NotificationMailer $notificationMailer,
        AdminEmailFactory $adminEmailFactory,
        PresenceHandlerInterface $presenceHandler
    ) {
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->facturePlaineHandler = $facturePlaineHandler;
        $this->factureEmailFactory = $factureEmailFactory;
        $this->notificationMailer = $notificationMailer;
        $this->adminEmailFactory = $adminEmailFactory;
        $this->presenceHandler = $presenceHandler;
    }

    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant): void
    {
        $jours = $plaine->getJours();
        $this->presenceHandler->handleNew($tuteur, $enfant, $jours);
    }

    public function handleEditPresences(
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $collection
    ): void {
        $enMoins = array_diff($currentJours, $collection->toArray());
        $enPlus = array_diff($collection->toArray(), $currentJours);

        foreach ($enPlus as $jour) {
            $presence = new Presence($tuteur, $enfant, $jour);
            $this->plainePresenceRepository->persist($presence);
        }

        foreach ($enMoins as $jour) {
            $presence = $this->plainePresenceRepository->findOneByEnfantJour($enfant, $jour);
            if (null !== $presence) {
                $this->plainePresenceRepository->remove($presence);
            }
        }

        $this->plainePresenceRepository->flush();
    }

    public function removeEnfant(Plaine $plaine, Enfant $enfant): void
    {
        $presences = $this->plainePresenceRepository->findByPlaineAndEnfant($plaine, $enfant);
        foreach ($presences as $presence) {
            $this->plainePresenceRepository->remove($presence);
        }
        $this->plainePresenceRepository->flush();
    }

    public function isRegistrationFinalized(Plaine $plaine, Tuteur $tuteur): bool
    {
        return [] !== $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur, true);
    }

    /**
     * @throws Exception
     */
    public function confirm(Plaine $plaine, Tuteur $tuteur): void
    {
        $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        foreach ($inscriptions as $inscription) {
            $inscription->setConfirmed(true);
        }
        $this->plainePresenceRepository->flush();

        $facture = $this->facturePlaineHandler->newInstance($plaine, $tuteur);
        $this->plainePresenceRepository->persist($facture);
        $this->plainePresenceRepository->flush();

        $this->facturePlaineHandler->handleManually($facture, $plaine);

        $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);
        if (\count($emails) < 1) {
            $error = 'Pas de mail pour la facture plaine: '.$facture->getId();
            $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
            $this->notificationMailer->sendAsEmailNotification($message);
            throw new Exception($error);
        }

        $from = $this->factureEmailFactory->getEmailAddressOrganisation();
        $message = $this->factureEmailFactory->messageFacture($from, 'Inscription à la plaine', 'Coucou');
        $this->factureEmailFactory->setTos($message, $emails);
        $this->factureEmailFactory->attachFactureOnTheFly($facture, $message);

        try {
            $this->notificationMailer->sendMail($message);
        } catch (TransportExceptionInterface $e) {
            $error = 'Facture plaine num '.$facture->getId().' '.$e->getMessage();
            $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture plaine', $error);
            $this->notificationMailer->sendAsEmailNotification($message);
        }

        $this->notificationMailer->sendAsEmailNotification($message);
        $facture->setEnvoyeA(implode(',', $emails));
        $facture->setEnvoyeLe(new DateTime());
        $this->plainePresenceRepository->flush();
    }
}
