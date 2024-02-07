<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
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
        private FacturePresenceRepository $facturePresenceRepository,
        private EnfantRepository $enfantRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        //id: 2674 => enfantId= 1455
        foreach ($this->facturePresenceRepository->findAll() as $facturePresence) {
            if (!$facturePresence->enfantId) {

                if ($this->nohamCollas($facturePresence)) {
                    continue;
                }

                if ($this->rojaMustafta($facturePresence)) {
                    continue;
                }
                if ($this->aureliaMowack($facturePresence)) {
                    continue;
                }

                $enfant = $this->enfantRepository->findOneBy(
                    ['nom' => $facturePresence->getNom(), 'prenom' => $facturePresence->getPrenom()]
                );

                if ($enfant) {
                    $facturePresence->enfantId = $enfant->getId();
                } else {

                    $this->error($facturePresence);

                }
            }
        }

        $this->facturePresenceRepository->flush();

        return Command::SUCCESS;
    }

    private function aureliaMowack(FacturePresence $facturePresence): bool
    {
        if (in_array($facturePresence->getFacture()->getId(), [1656])) {
            if ($facturePresence->getPrenom() == 'Arthur') {
                $enfant = $this->enfantRepository->findOneBy(
                    ['nom' => 'GHEYS', 'prenom' => $facturePresence->getPrenom()]
                );
                if ($enfant) {
                    $facturePresence->enfantId = $enfant->getId();
                } else {
                    $this->error($facturePresence);
                }
            }
            if ($facturePresence->getPrenom() == 'Aurélia') {
                $enfant = $this->enfantRepository->findOneBy(
                    ['nom' => 'NOWAK', 'prenom' => $facturePresence->getPrenom()]
                );
                if ($enfant) {
                    $facturePresence->enfantId = $enfant->getId();
                } else {
                    $this->error($facturePresence);
                }

            }

            return true;
        }

        return false;
    }

    private function rojaMustafta(FacturePresence $facturePresence): bool
    {
        if (in_array($facturePresence->getFacture()->getId(), [304])) {
            $enfant = $this->enfantRepository->findOneBy(
                ['nom' => 'MUSTAFTAPUR', 'prenom' => $facturePresence->getPrenom()]
            );
            if ($enfant) {
                $facturePresence->enfantId = $enfant->getId();
            } else {
                $this->error($facturePresence);
            }

            return true;
        }

        return false;
    }

    private function nohamCollas(FacturePresence $facturePresence): bool
    {
        if (in_array($facturePresence->getFacture()->getId(), [128, 234, 372, 479, 591, 751])) {
            $enfant = $this->enfantRepository->findOneBy(
                ['nom' => 'GASMI', 'prenom' => $facturePresence->getPrenom()]
            );
            if ($enfant) {
                $facturePresence->enfantId = $enfant->getId();
            } else {
                $this->error($facturePresence);
            }

            return true;
        }

        return false;
    }

    private function error(FacturePresence $facturePresence)
    {
        $this->io->error(
            'pas trouve '.$facturePresence->getFacture()->getId().' '.$facturePresence->getNom(
            ).' '.$facturePresence->getPrenom()
        );

    }
}
