<?php

namespace AcMarche\Mercredi\Migration\Command;

use AcMarche\Mercredi\Migration\Handler\EnfantImport;
use AcMarche\Mercredi\Migration\Handler\FicheSanteImport;
use AcMarche\Mercredi\Migration\Handler\ParametreImport;
use AcMarche\Mercredi\Migration\Handler\PlaineImport;
use AcMarche\Mercredi\Migration\Handler\PlainePresenceImport;
use AcMarche\Mercredi\Migration\Handler\PresenceImport;
use AcMarche\Mercredi\Migration\Handler\TuteurImport;
use AcMarche\Mercredi\Migration\Handler\UserImport;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private FicheSanteImport $ficheSanteImport;
    private PresenceImport $presenceImport;
    private PlaineImport $plaineImport;
    private PlainePresenceImport $plainePresenceImport;

    public function __construct(
        ParametreImport $parametreImport,
        UserImport $userImport,
        TuteurImport $tuteurImport,
        EnfantImport $enfantImport,
        FicheSanteImport $ficheSanteImport,
        PresenceImport $presenceImport,
        UserRepository $userRepository,
        PlaineImport $plaineImport,
        PlainePresenceImport $plainePresenceImport,
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
        $this->ficheSanteImport = $ficheSanteImport;
        $this->presenceImport = $presenceImport;
        $this->plaineImport = $plaineImport;
        $this->plainePresenceImport = $plainePresenceImport;
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

        if ($input->getArgument('name') == 'all') {
            $this->parametreImport->importAll();
            $this->userImport->import($symfonyStyle);
            $this->tuteurImport->import($symfonyStyle);
            $this->enfantImport->import($symfonyStyle);
            $this->enfantImport->importRelation($symfonyStyle);
            $this->enfantImport->importNote($symfonyStyle);
            $this->ficheSanteImport->import($symfonyStyle);
            $this->ficheSanteImport->importReponse($symfonyStyle);
            $this->presenceImport->import($symfonyStyle);
            $this->plaineImport->import($symfonyStyle);
            $this->plainePresenceImport->import($symfonyStyle);

            return Command::SUCCESS;
        }

        switch ($input->getArgument('name')) {
            case 'parametre':
                $this->parametreImport->importAll();

                return Command::SUCCESS;
            case 'user':
                $this->userImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'tuteur':
                $this->tuteurImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'enfant':
                $this->enfantImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'relation':
                $this->enfantImport->importRelation($symfonyStyle);
                $this->enfantImport->importNote($symfonyStyle);

                return Command::SUCCESS;
            case 'sante':
                $this->ficheSanteImport->import($symfonyStyle);
                $this->ficheSanteImport->importReponse($symfonyStyle);
            case 'presence':
                $this->presenceImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'plaine':
                $this->plaineImport->import($symfonyStyle);
                $this->plaineImport->importGroupe($symfonyStyle);
                $this->plaineImport->importJours($symfonyStyle);

                return Command::SUCCESS;
            case 'plainepresence':
                $this->plainePresenceImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'password':
                $user = $this->userRepository->findOneBy(['username' => 'jfsenechal']);
                $user->setPassword($this->passwordHasher->hashPassword($user, 'homer'));
                $this->userRepository->flush();

                return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }

    /***********************************************
     * A REGARDER !!!!!!!!!!!!!!!
     */

    /**
     * La table animateur
     * Lier ecole et user
     */

}
