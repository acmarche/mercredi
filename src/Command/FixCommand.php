<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
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
    private SymfonyStyle $io;

    public function __construct(
        private FactureRepository $factureRepository,
        private JourRepository $jourRepository,
        private PresenceRepository $presenceRepository,
        private FactureHandler $factureHandler,
        private FacturePresenceRepository $facturePresenceRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        /**
         * janvier 2019 => juin 2022
         */
        $rows = $this->presenceRepository->findWithOutPaiement();
        $grouped = PresenceUtils::groupByTuteur($rows);
        foreach ($grouped as $data) {
            $tuteur = $data['tuteur'];
            $presences = $data['presences'];
            foreach ($presences as $key => $presence) {
                if ($this->facturePresenceRepository->findByPresence($presence)) {
                  /*  $this->io->error(
                        $presence->getId().' '.$presence->getJour()->getDateJour()->format(
                            'Y-m-d'
                        ).' '.$presence->getEnfant()->getPrenom()
                    );*/
                    unset($presences[$key]);
                }
            }
            if (count($presences) > 0) {
                $facture = $this->factureHandler->newFacture($tuteur);
                $facture->setMois('2022-06');
                $this->factureHandler->handleManuallyNotResolved($facture, $presences, []);
            }
        }
        $this->factureRepository->flush();

        return Command::SUCCESS;
    }


    private function attach31Aout()
    {
        $jour = $this->jourRepository->find(448);//31 aout 2022
        $presences = $this->presenceRepository->findByDay($jour);

        foreach ($presences as $presence) {
            $facture = $this->factureRepository->findFacturesByTuteurAndMonth($presence->getTuteur(), '09-2022');
            if ($facture) {
                $this->io->success($presence->getEnfant()->getNom().' '.$presence->getEnfant()->getPrenom());
                if (!$this->facturePresenceRepository->findByPresence($presence)) {
                    $this->factureHandler->attachPresences($facture, [$presence]);
                    $this->factureRepository->flush();
                }
            } else {
                //ne sont venu que le 31 aout et pas en septembre
                $this->io->error($presence->getEnfant()->getNom().' '.$presence->getEnfant()->getPrenom());
            }
        }
    }
}
