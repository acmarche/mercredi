<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Security\Token\TokenManager;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;

#[AsCommand(
    name: 'mercredi:rappel',
    description: 'Envoie rappel par mail'
)]
class SendRappelCommand extends Command
{
    use OrganisationPropertyInitTrait;

    public function __construct(
        private PresenceRepository $presenceRepository,
        private readonly TokenManager $tokenManager,
        private NotificationMailer $notificationMailer,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $presences = $this->presenceRepository->findByYear(2023);
        $missing = [];
        foreach ($presences as $presence) {
            $enfant = $presence->getEnfant();
            if (!$enfant->getRegistreNational()) {
                $tuteur = $presence->getTuteur();
                $missing[$tuteur->getId()] = $tuteur;
            }
        }

        $messageBase = NotificationEmailJf::asPublicEmailJf();

        foreach ($missing as $tuteur) {

            $users = $tuteur->getUsers();
            $url = 'https://mercredi.marche.be/login';

            if (count($users) > 0) {
                $url = $this->tokenManager->getLinkToConnect($users[0]);
            }

            $messageBase
                ->subject('Attestations fiscales, nous avons besoin du numéro national')
                ->from($this->getEmailSenderAddress())
                ->htmlTemplate('@AcMarcheMercredi/email/admin/rappel.html.twig')
                ->context(
                    [
                        'urlLogin' => $url,
                    ]
                );

            $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);

            if (\count($emails) < 1) {
                $error = 'Pas de mail pour  '.$tuteur->getId();
                $io->error($error);
                continue;
            }

            $messageBase->bcc(new Address('jf@marche.be', join(',', $emails)));
            $messageBase->to(...$emails);
            $messageBase->bcc($this->getEmailSenderAddress());

            try {
                $this->notificationMailer->sendMail($messageBase);
            } catch (TransportExceptionInterface $e) {
                $error = 'send error '.$tuteur->getId().' '.$e->getMessage();
                $io->error($error);
                continue;
            }

        }


        return Command::SUCCESS;
    }
}
