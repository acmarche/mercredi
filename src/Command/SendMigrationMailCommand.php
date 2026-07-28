<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Security\Token\TokenManager;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Envoie l'annonce de fusion des plateformes aux utilisateurs ayant au moins un tuteur
 * possédant un numéro de registre national.
 *
 * Le corps du mail se trouve dans @AcMarcheMercredi/email/front/_migration.html.twig,
 * la variable link étant le lien de migration propre à chaque destinataire.
 */
#[AsCommand(
    name: 'mercredi:migration-mail',
    description: 'Envoie l\'annonce de migration aux parents ayant un tuteur avec registre national'
)]
class SendMigrationMailCommand extends Command
{
    use OrganisationPropertyInitTrait;

    private const SUBJECT = 'Fusion du site mercredi.marche.be et du site enfance-jeunesse.marche.be';

    private const TEMPLATE = '@AcMarcheMercredi/email/admin/migration.html.twig';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenManager $tokenManager,
        private readonly NotificationMailer $notificationMailer,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('send', null, InputOption::VALUE_NONE, 'Envoie réellement les mails (sinon simulation)')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limite le nombre de destinataires')
            ->addOption(
                'test-email',
                null,
                InputOption::VALUE_REQUIRED,
                'Envoie tout à cette adresse au lieu des destinataires réels'
            )
            ->addOption(
                'test-user',
                null,
                InputOption::VALUE_REQUIRED,
                'Email du compte dont le lien de migration est utilisé en test '.
                '(par défaut le compte de --test-email, sinon le destinataire réel)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findWithTuteurHavingRegistreNational();
        $users = $this->filterWithEmail($users, $io);

        if ($limit = $input->getOption('limit')) {
            $users = \array_slice($users, 0, (int)$limit);
        }

        if ([] === $users) {
            $io->warning('Aucun destinataire trouvé.');

            return Command::SUCCESS;
        }

        $send = (bool)$input->getOption('send');
        $testEmail = $input->getOption('test-email');
        $tokenUser = $this->resolveTokenUser($input, $io);

        if ($testEmail && null === $tokenUser) {
            $io->warning(
                'Le lien du mail de test connectera le destinataire réel. '.
                'Utilisez --test-user pour choisir le compte.'
            );
        }

        $io->section('Sujet');
        $io->writeln(self::SUBJECT);
        $io->section(\count($users).' destinataire(s)');

        if (!$send) {
            $io->listing(array_map(static fn(User $user): string => $user->getEmail(), $users));
            $io->note('Simulation, aucun mail envoyé. Ajoutez --send pour envoyer.');

            return Command::SUCCESS;
        }

        $sent = 0;
        foreach ($users as $user) {
            $message = new TemplatedEmail();
            $message
                ->subject(self::SUBJECT)
                ->from($this->getEmailSenderAddress())
                ->to($testEmail ?: $user->getEmail())
                ->htmlTemplate(self::TEMPLATE)
                ->context([
                    'link' => $this->tokenManager->getLinkToMigration($tokenUser ?? $user),
                    'organisation' => $this->organisation,
                ]);

            try {
                $this->notificationMailer->sendMail($message);
                ++$sent;
            } catch (TransportExceptionInterface $transportException) {
                $io->error(
                    'Envoi impossible pour '.$user->getEmail().' : '.$transportException->getMessage()
                );
            }
        }

        $io->success($sent.' mail(s) envoyé(s).');

        return Command::SUCCESS;
    }

    /**
     * Compte dont le lien de migration est utilisé lors d'un test, pour ne pas envoyer
     * le token d'un vrai parent. Null en envoi réel : chacun reçoit son propre lien.
     */
    private function resolveTokenUser(InputInterface $input, SymfonyStyle $io): ?User
    {
        $email = $input->getOption('test-user') ?: $input->getOption('test-email');

        if (!$email) {
            return null;
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $user && $input->getOption('test-user')) {
            $io->warning('Aucun compte pour '.$email.', le lien du destinataire réel sera utilisé.');
        }

        return $user;
    }

    /**
     * @param User[] $users
     *
     * @return User[]
     */
    private function filterWithEmail(array $users, SymfonyStyle $io): array
    {
        $withEmail = [];
        foreach ($users as $user) {
            if (!$user->getEmail()) {
                $io->warning('Pas de mail pour l\'utilisateur '.$user->getId());
                continue;
            }
            $withEmail[] = $user;
        }

        return $withEmail;
    }
}
