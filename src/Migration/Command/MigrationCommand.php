<?php

namespace AcMarche\Mercredi\Migration\Command;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Migration\Handler\EnfantImport;
use AcMarche\Mercredi\Migration\Handler\FactureImport;
use AcMarche\Mercredi\Migration\Handler\FicheSanteImport;
use AcMarche\Mercredi\Migration\Handler\FixImport;
use AcMarche\Mercredi\Migration\Handler\PaiementImport;
use AcMarche\Mercredi\Migration\Handler\ParametreImport;
use AcMarche\Mercredi\Migration\Handler\PlaineImport;
use AcMarche\Mercredi\Migration\Handler\PlainePresenceImport;
use AcMarche\Mercredi\Migration\Handler\PresenceImport;
use AcMarche\Mercredi\Migration\Handler\TuteurImport;
use AcMarche\Mercredi\Migration\Handler\UserImport;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'mercredi:migration')]
final class MigrationCommand extends Command
{
    public function __construct(
        private ParametreImport $parametreImport,
        private UserImport $userImport,
        private TuteurImport $tuteurImport,
        private EnfantImport $enfantImport,
        private FicheSanteImport $ficheSanteImport,
        private PresenceImport $presenceImport,
        private UserRepository $userRepository,
        private PlaineImport $plaineImport,
        private PlainePresenceImport $plainePresenceImport,
        private FactureImport $factureImport,
        private FixImport $fixImport,
        private UserPasswordHasherInterface $passwordHasher,
        private PaiementImport $paiementImport,
        ?string $name = null
    ) {
        parent::__construct($name);
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

        if ('all' === $input->getArgument('name')) {
            $this->parametreImport->importAll();
            $this->userImport->import($symfonyStyle);
            $this->tuteurImport->import($symfonyStyle);
            $this->enfantImport->import($symfonyStyle);
            $this->enfantImport->importRelation($symfonyStyle);
            $this->enfantImport->importNote($symfonyStyle);
            $this->ficheSanteImport->import($symfonyStyle);
            $this->ficheSanteImport->importReponse($symfonyStyle);
            $this->paiementImport->import($symfonyStyle);
            $this->presenceImport->import($symfonyStyle);
            $this->plaineImport->import($symfonyStyle);
            $this->plaineImport->importGroupe($symfonyStyle);
            $this->plaineImport->importJours($symfonyStyle);
            $this->plainePresenceImport->import($symfonyStyle);
            $this->organisation();

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
            // no break
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
            case 'oranisation':
                $this->organisation();

                return Command::SUCCESS;
            case 'facture':
                $this->factureImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'paiement':
                $this->paiementImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'fix':
                $this->fixImport->import($symfonyStyle);

                return Command::SUCCESS;
            case 'password':
                $user = $this->userRepository->findOneBy([
                    'username' => 'jfsenechal',
                ]);
                //    $user->setPassword($this->passwordHasher->hashPassword($user, 'homer'));
                //   $this->userRepository->flush();

                return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }

    private function organisation(): void
    {
        $oranisation = new Organisation();
        $oranisation->setNom('Espace parents enfants');
        $oranisation->setEmail('epe@marche.be');
        $oranisation->setCodePostal(6900);
        $oranisation->setInitiale('Epe');
        $oranisation->setRue('Rue Victor Libert 20');
        $oranisation->setLocalite('Marche');
        $this->userRepository->persist($oranisation);
        $this->userRepository->flush();
    }

    /***********************************************
     * A REGARDER !!!!!!!!!!!!!!!
     */

    /*
     * La table animateur
     * Lier ecole et user
     */
}
