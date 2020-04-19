<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Entity\User;
use AcMarche\Mercredi\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HottonCreateUserCommand extends Command
{
    protected static $defaultName = 'hotton:create:user';
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Création d\'un utilisateur')
            ->addArgument('name', InputArgument::REQUIRED, 'nom')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('Adresse email non valide');

            return 1;
        }

        if (strlen($name) < 1) {
            $io->error('Name minium 1');

            return 1;
        }
        if (null !== $this->userRepository->findOneBy(['email' => $email])) {
            $io->error('Un utilisateur existe déjà avec cette adresse email');

            return 1;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setNom($name);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $password));
        $user->addRole('ROLE_ADMINISTRATOR');

        $this->userRepository->insert($user);

        $io->success('Utilisateur créé.');

        return 0;
    }
}
