<?php

namespace AcMarche\Mercredi\Message\Handler;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Message\Factory\EmailFactory;
use AcMarche\Mercredi\Message\Repository\MessageRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineGroupeRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Component\HttpFoundation\RequestStack;

final class MessageHandler
{
    use InitMailerTrait;

    public function __construct(
        private MessageRepository $messageRepository,
        private EmailFactory $emailFactory,
        private NotificationMailer $notificationMailer,
        private PlainePresenceRepository $plainePresenceRepository,
        private GroupingInterface $grouping,
        private PlaineGroupeRepository $plaineGroupeRepository,
        private TuteurUtils $tuteurUtils,
        RequestStack $requestStack
    ) {

    }

    public function handle(Message $message): void
    {
        $templatedEmail = $this->emailFactory->create($message);

        foreach ($message->getDestinataires() as $addressEmail) {
            $templatedEmail->to($addressEmail);
            $this->notificationMailer->sendAsEmailNotification($templatedEmail, $addressEmail);
        }

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();
    }

    /**
     * @param Plaine $plaine
     * @param Message $message
     * @param array|Presence[] $presences
     * @param bool $attachCourrier
     * @return void
     */
    public function handleFromPlaine(Plaine $plaine, Message $message, bool $attachCourrier): void
    {
        $recipients = $this->recipientsForPlaine($plaine);
        $templatedBase = $this->emailFactory->createForPlaine($message);

        foreach ($recipients as $recipient) {
            $templatedEmail = clone($templatedBase);
            $emails = $recipient['emails'];
            if (!$emails) {
                $emails = ['jf@marche.be'];
            } elseif (count($emails) == 0) {
                $emails = ['jf@marche.be'];
            }
            if ($attachCourrier) {
                if (count($recipient['groupes']) > 0) {
                    $this->emailFactory->attachmentsForPlaine($templatedEmail, $recipient['groupes']);
                }
            }
            $templatedEmail->to(...$emails);
            $this->notificationMailer->sendAsEmailNotification($templatedEmail);
            unset($templatedEmail);
        }

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();
    }

    private function recipientsForPlaine(Plaine $plaine)
    {
        $presences = $this->plainePresenceRepository->findByPlaine($plaine);
        $recipients = [];

        foreach ($presences as $presence) {
            $tuteur = $presence->getTuteur();
            $enfant = $presence->getEnfant();
            $emails = $this->tuteurUtils->getEmailsOfOneTuteur($tuteur);
            $groupe = $this->grouping->findGroupeScolaire($enfant);
            if (!$groupe) {
                $recipients[$tuteur->getId()]['groupes'] = [];
                continue;
            }
            $recipients[$tuteur->getId()] = ['emails' => $emails];

            $plaineGroupe = $this->plaineGroupeRepository->findOneByPlaineAndGroupe($plaine, $groupe);
            if ($plaineGroupe) {
                if (isset($recipients[$tuteur->getId()]['groupes'])) {
                    $recipients[$tuteur->getId()]['groupes'][] = $plaineGroupe;
                } else {
                    $recipients[$tuteur->getId()]['groupes'] = [$plaineGroupe];
                }
            } else {
                $recipients[$tuteur->getId()]['groupes'] = [];
            }
        }

        return $recipients;
    }

}
