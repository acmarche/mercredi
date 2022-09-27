<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
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
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Security;
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
     * @return void
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws Exception
     */
    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant, iterable $jours = []): void
    {
        if (!$this->security->isGranted('ROLE_MERCREDI_ADMIN')) {
            $jour = $plaine->getFirstDay();
            $age = $enfant->getAge($jour->getDateJour(), true);
            $groupe = $this->grouping->findGroupeScolaireByAge($age);
            $plaineGroupe = $this->plaineGroupeRepository->findOneByPlaineAndGroupe($plaine, $groupe);
            if ($plaineGroupe) {
                $enfants = $this->plainePresenceRepository->findEnfantsByPlaine($plaine);
                $data = $this->grouping->groupEnfantsForPlaine($plaine, $enfants);
                if (isset($data[$groupe->id])) {
                    $inscrits = $data[$groupe->id]['enfants'];
                    if (count($inscrits) > $plaineGroupe->getInscriptionMaximum()) {
                        throw new Exception(
                            "$enfant n' a pas pu être inscrit, il n'y a plus de place pour cette catégorie d'âge"
                        );
                    }
                }
            }
        }
        $this->presenceHandler->handleNew($tuteur, $enfant, $jours);
    }

    public function handleEditPresences(
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $newJours
    ): void {
        $enMoins = array_diff($currentJours, $newJours->toArray());
        $enPlus = array_diff($newJours->toArray(), $currentJours);

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
}
