<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Plaine\Repository\PlaineGroupeRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
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
    private GroupeScolaire $grands;
    private GroupeScolaire $moyens;
    private GroupeScolaire $petits;

    public function __construct(
        private PlaineRepository $plaineRepository,
        private PlaineGroupeRepository $plaineGroupeRepository,
        private GroupeScolaireRepository $groupeScolaireRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->petits = $this->groupeScolaireRepository->find(2);
        $this->moyens = $this->groupeScolaireRepository->find(3);
        $this->grands = $this->groupeScolaireRepository->find(4);

        foreach ($this->plaineRepository->findAll() as $plaine) {
            foreach ($this->plaineGroupeRepository->findByPlaine($plaine) as $groupePlaine) {
                $groupe = $this->findGroupe($groupePlaine);
                if (!$groupe) {
                    $this->io->error($groupePlaine->getGroupeScolaire()->getNom());
                    $this->io->error($groupePlaine->getGroupeScolaire()->getId());
                    continue;
                }
                if (!$this->plaineGroupeRepository->findOneByPlaineAndGroupe($plaine, $groupe)) {
                    $plaineGroupe = new PlaineGroupe($plaine, $groupe);
                    $plaineGroupe->setInscriptionMaximum($groupePlaine->getInscriptionMaximum());
                    $this->plaineGroupeRepository->persist($plaineGroupe);
                }
            }
        }

        $this->plaineGroupeRepository->flush();

        return Command::SUCCESS;
    }

    private function findGroupe(PlaineGroupe $plaineGroupe): ?GroupeScolaire
    {
        if (str_contains($plaineGroupe->getGroupeScolaire()->getNom(), 'petits')) {
            return $this->petits;
        }
        if (str_contains($plaineGroupe->getGroupeScolaire()->getNom(), 'Moyens')) {
            return $this->moyens;
        }
        if (str_contains($plaineGroupe->getGroupeScolaire()->getNom(), 'grands')) {
            return $this->grands;
        }
        if ($plaineGroupe->getGroupeScolaire()->getId() == 6) {
            return $this->moyens;
        }
        if ($plaineGroupe->getGroupeScolaire()->getId() == 4) {
            return $this->grands;
        }
        if ($plaineGroupe->getGroupeScolaire()->getId() == 2) {
            return $this->petits;
        }

        return null;
    }

}
