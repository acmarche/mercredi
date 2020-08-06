<?php

namespace AcMarche\Mercredi\User\Command;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\MercrediSecurity;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function strlen;

final class CreateUserCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'mercredi:create-user';
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var string
     */
    private const EMAIL = 'email';

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Création d\'un utilisateur')
            ->addArgument('name', InputArgument::REQUIRED, 'nom')
            ->addArgument(self::EMAIL, InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $email = $input->getArgument(self::EMAIL);
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $symfonyStyle->error('Adresse email non valide');

            return 1;
        }

        if (strlen($name) < 1) {
            $symfonyStyle->error('Name minium 1');

            return 1;
        }
        if (null !== $this->userRepository->findOneBy([self::EMAIL => $email])) {
            $symfonyStyle->error('Un utilisateur existe déjà avec cette adresse email');

            return 1;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setNom($name);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $password));
        $user->addRole(MercrediSecurity::ROLE_ADMIN);

        $this->userRepository->insert($user);

        $symfonyStyle->success('User crée.');

        return 0;
    }
}
