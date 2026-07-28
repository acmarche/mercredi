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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Envoie le texte de data/mail.txt aux utilisateurs ayant au moins un tuteur
 * possédant un numéro de registre national.
 *
 * Format du fichier :
 * - la première ligne non vide est le sujet du mail ;
 * - le reste est le corps, les paragraphes étant séparés par une ligne vide ;
 * - la syntaxe [libellé] link to 'nom_de_route' devient un lien vers cette route.
 *   Pour mercredi_front_migration le lien contient le token de connexion du destinataire.
 */
#[AsCommand(
    name: 'mercredi:migration-mail',
    description: 'Envoie le texte de data/mail.txt aux parents ayant un tuteur avec registre national'
)]
class SendMigrationMailCommand extends Command
{
    use OrganisationPropertyInitTrait;

    private const LINK_PATTERN = "/\[([^\]]+)\]\s*link to\s*'([a-zA-Z0-9_]+)'/";

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenManager $tokenManager,
        private readonly NotificationMailer $notificationMailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Fichier contenant le mail', 'data/mail.txt')
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

        $fileName = $input->getOption('file');
        $path = str_starts_with((string)$fileName, '/') ? $fileName : $this->projectDir.'/'.$fileName;

        if (!is_readable($path)) {
            $io->error('Fichier introuvable: '.$path);

            return Command::FAILURE;
        }

        [$subject, $body] = $this->parseFile((string)file_get_contents($path));

        if ('' === $subject || '' === $body) {
            $io->error('Le fichier doit contenir un sujet (première ligne) et un corps de message.');

            return Command::FAILURE;
        }

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
        $io->writeln($subject);
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
                ->subject($subject)
                ->from($this->getEmailSenderAddress())
                ->to($testEmail ?: $user->getEmail())
                ->htmlTemplate('@AcMarcheMercredi/email/admin/migration.html.twig')
                ->context([
                    'content' => $this->renderBody($body, $tokenUser ?? $user),
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
     * @return array{0: string, 1: string} sujet, corps
     */
    private function parseFile(string $content): array
    {
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $content);
        $subject = '';

        while ([] !== $lines) {
            $line = trim((string)array_shift($lines));
            if ('' !== $line) {
                $subject = $line;
                break;
            }
        }

        return [$subject, trim(implode("\n", $lines))];
    }

    /**
     * Transforme le texte brut en html, les liens étant résolus pour ce destinataire.
     */
    private function renderBody(string $body, User $user): string
    {
        $links = [];
        $body = preg_replace_callback(
            self::LINK_PATTERN,
            function (array $matches) use (&$links, $user): string {
                $placeholder = '@@LINK'.\count($links).'@@';
                $links[$placeholder] = sprintf(
                    '<a href="%s">%s</a>',
                    htmlspecialchars($this->generateUrl($matches[2], $user), ENT_QUOTES),
                    htmlspecialchars($matches[1], ENT_QUOTES)
                );

                return $placeholder;
            },
            $body
        );

        $html = '';
        foreach (preg_split("/\n\s*\n/", (string)$body) as $paragraph) {
            $paragraph = trim((string)$paragraph);
            if ('' === $paragraph) {
                continue;
            }
            $html .= '<p style="margin: 0 0 16px">'.nl2br(htmlspecialchars($paragraph, ENT_QUOTES)).'</p>';
        }

        return strtr($html, $links);
    }

    private function generateUrl(string $route, User $user): string
    {
        if ('mercredi_front_migration' === $route) {
            return $this->tokenManager->getLinkToMigration($user);
        }

        return $this->urlGenerator->generate($route, [], UrlGeneratorInterface::ABSOLUTE_URL);
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
