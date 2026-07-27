<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Security\Token\TokenManager;
use AcMarche\Mercredi\Security\Token\TokenRepository;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mercredi:token:generate-all',
    description: 'Régénère un token de connexion automatique pour tous les utilisateurs',
)]
class TokenGenerateCommand extends Command
{
    public function __construct(
        private readonly TokenManager $tokenManager,
        private readonly TokenRepository $tokenRepository,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'flush',
                null,
                InputOption::VALUE_NONE,
                'Enregistre les modifications en base de données (sinon dry-run)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $flush = (bool)$input->getOption('flush');

        $users = \count($this->userRepository->findAll());
        $tokens = \count($this->tokenRepository->findAll());

        $io->table(
            ['', 'Nombre'],
            [
                ['Utilisateurs', $users],
                ['Tokens existants', $tokens],
                ['Tokens à créer', $users - $tokens],
            ]
        );

        // Toutes les urls de connexion automatique déjà envoyées deviennent invalides.
        $io->warning('Les anciens liens de connexion automatique ne fonctionneront plus.');

        if (!$flush) {
            $io->note('Dry-run : aucune modification enregistrée. Utilisez --flush pour appliquer.');

            return Command::SUCCESS;
        }

        $this->tokenManager->createForAllUsers();

        $io->success(sprintf('%d token(s) en base.', \count($this->tokenRepository->findAll())));

        return Command::SUCCESS;
    }
}
