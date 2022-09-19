<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Facture\Handler\FacturePlaineHandler;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mercredi:fix',
)]
class FixCommand extends Command
{
    public function __construct(
        private FactureRepository $factureRepository,
        private FacturePlaineHandler $facturePlaineHandler,
        private PlaineRepository $plaineRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $plaine = $this->plaineRepository->find(32);
        // $facture = $this->factureRepository->find(8);

        foreach ($this->factureRepository->findAll() as $facture) {
            $this->facturePlaineHandler->handleManually($facture, $plaine);
        }

        return Command::SUCCESS;
    }
}
