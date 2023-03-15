<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Handler\FacturePlaineHandler;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Plaine\Repository\PlaineGroupeRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Environment;

class PlaineHandlerMarche implements PlaineHandlerInterface
{
    public function __construct(
        private PlainePresenceRepository $plainePresenceRepository,
        private FacturePlaineHandler $facturePlaineHandler,
        private FactureEmailFactory $factureEmailFactory,
        private NotificationMailer $notificationMailer,
        private AdminEmailFactory $adminEmailFactory,
        private PresenceHandlerInterface $presenceHandler,
        private Environment $environment,
        private GroupingInterface $grouping,
        private PlaineGroupeRepository $plaineGroupeRepository,
        private Security $security
    ) {
    }

    /**
     * @param Plaine $plaine
     * @param Tuteur $tuteur
     * @param Enfant $enfant
     * @param array|Jour[] $jours
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant, iterable $jours = []): array
    {
        $daysFull = [];
        if (!$this->security->isGranted('ROLE_MERCREDI_ADMIN')) {
            $result = $this->removeFullDays($plaine, $enfant, $jours);
            $jours = $result[0];
            $daysFull = $result[1];
        }

        $this->presenceHandler->handleNew($tuteur, $enfant, $jours);

        return $daysFull;
    }

    /**
     * @param Plaine $plaine
     * @param Tuteur $tuteur
     * @param Enfant $enfant
     * @param array $currentJours
     * @param Collection $newJours
     * @return array|Jour[]
     * @throws Exception
     */
    public function handleEditPresences(
        Plaine $plaine,
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $newJours
    ): array {
        $enMoins = array_diff($currentJours, $newJours->toArray());
        $enPlus = array_diff($newJours->toArray(), $currentJours);

        $result = $this->removeFullDays($plaine, $enfant, $enPlus);

        $enPlus = $result[0];
        $daysFull = $result[1];
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
        //?todo for currentJours set confirmed false ?
        $this->plainePresenceRepository->flush();

        return $daysFull;
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

        $body = $this->environment->render('@AcMarcheMercrediAdmin/message/_plaine_facture_text.html.twig');
        $from = $this->factureEmailFactory->getEmailAddressOrganisation();
        $message = $this->factureEmailFactory->messageFacture($from, 'Votre inscription à '.$plaine->getNom(), $body);
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

    /**
     * @param Plaine $plaine
     * @param Enfant $enfant
     * @param array $jours
     * @return array //freeDays,fullDays
     */
    private function removeFullDays(Plaine $plaine, Enfant $enfant, iterable $jours): array
    {
        $groupeScolaire = $this->getGroupeScolaire($enfant, $plaine);
        if ($groupeScolaire->getId() == 0) {
            if ($jours instanceof Collection) {
                $jours = $jours->toArray();
            }

            return [[], $jours];
        }

        $plaineGroupe = $this->plaineGroupeRepository->findOneByPlaineAndGroupe($plaine, $groupeScolaire);
        if (!$plaineGroupe) {
            if ($jours instanceof Collection) {
                $jours = $jours->toArray();
            }
        }

        $daysFull = [];
        foreach ($jours as $key => $jour) {
            if ($this->isDayFull($plaine, $jour, $groupeScolaire, $plaineGroupe)) {
                unset($jours[$key]);
                $daysFull[] = $jour;
            }
        }

        return [$jours, $daysFull];
    }

    private function isDayFull(
        Plaine $plaine,
        Jour $jour,
        GroupeScolaire $groupeScolaireReferent,
        PlaineGroupe $plaineGroupe
    ): bool {
        $enfantsByDay = $this->plainePresenceRepository->findEnfantsByPlaineAndJour($plaine, $jour);
        $enfants = array_filter(
            $enfantsByDay,
            function ($enfant) use ($plaine, $groupeScolaireReferent) {
                $groupeScolaire = $this->getGroupeScolaire($enfant, $plaine);

                return $groupeScolaireReferent->getId() == $groupeScolaire->getId();
            }
        );

        return count($enfants) > $plaineGroupe->getInscriptionMaximum();
    }

    /**
     * Groupe scolaire suivant l'année scolaire
     * @param Enfant $enfant
     * @param Plaine $plaine
     * @return GroupeScolaire
     */
    public function getGroupeScolaire(Enfant $enfant, Plaine $plaine): GroupeScolaire
    {
        return $this->grouping->findGroupeScolaire($enfant);
    }
}
