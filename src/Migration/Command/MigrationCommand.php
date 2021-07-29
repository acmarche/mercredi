<?php

namespace AcMarche\Mercredi\Migration\Command;

use AcMarche\Mercredi\Migration\Handler\EnfantImport;
use AcMarche\Mercredi\Migration\Handler\ParametreImport;
use AcMarche\Mercredi\Migration\Handler\TuteurImport;
use AcMarche\Mercredi\Migration\Handler\UserImport;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class MigrationCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'mercredi:migration';
    private ParametreImport $parametreImport;
    private EnfantImport $enfantImport;
    private TuteurImport $tuteurImport;
    private UserImport $userImport;
    /**
     * @var \AcMarche\Mercredi\User\Repository\UserRepository
     */
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ParametreImport $parametreImport,
        UserImport $userImport,
        TuteurImport $tuteurImport,
        EnfantImport $enfantImport,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->parametreImport = $parametreImport;
        $this->enfantImport = $enfantImport;
        $this->tuteurImport = $tuteurImport;
        $this->userImport = $userImport;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Migration uuid')
            ->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $this->parametreImport->setIo($symfonyStyle);

        switch ($input->getArgument('name')) {
            case 'user':
                $this->userImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'tuteur':
                $this->tuteurImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'parametre':
                $this->parametreImport->importAll();

                return Command::SUCCESS;
            case 'enfant':
                $this->enfantImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'relation':
                $this->enfantImport->importRelation($symfonyStyle);

                return Command::SUCCESS;
            case 'password':
                $user = $this->userRepository->findOneBy(['username' => 'jfsenechal']);
               $user->setPassword($this->passwordHasher->hashPassword($user, 'homer'));
                $this->userRepository->flush();

                return Command::SUCCESS;

        }

        return Command::SUCCESS;
    }

}
