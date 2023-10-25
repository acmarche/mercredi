<?php

namespace AcMarche\Mercredi\User\Command;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'mercredi:create-user',
    description: 'Création d\'un utilisateur'
)]
final class CreateUserCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'mercredi:create-user';

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'nom')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $symfonyStyle->error('Adresse email non valide');

            return Command::FAILURE;
        }

        if (\strlen($name) < 1) {
            $symfonyStyle->error('Name minium 1');

            return Command::FAILURE;
        }
        if (null !== $this->userRepository->findOneBy([
            'email' => $email,
        ])) {
            $symfonyStyle->error('Un utilisateur existe déjà avec cette adresse email');

            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setNom($name);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->addRole(MercrediSecurityRole::ROLE_ADMIN);

        $this->userRepository->persist($user);
        $this->userRepository->flush();

        $symfonyStyle->success('User crée.');

        return Command::SUCCESS;
    }
}
