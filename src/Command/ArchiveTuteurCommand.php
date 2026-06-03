<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mercredi:archive-tuteur',
    description: 'Archive les tuteurs qui n\'ont aucun enfant âgé de 13 ans ou moins',
)]
class ArchiveTuteurCommand extends Command
{
    /**
     * Âge maximum (inclus) pour qu'un enfant soit considéré comme actif.
     */
    private const AGE_MAX = 13;

    private SymfonyStyle $io;

    public function __construct(
        private readonly TuteurRepository $tuteurRepository
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
        $this->io = new SymfonyStyle($input, $output);
        $flush = (bool)$input->getOption('flush');

        $tuteurs = $this->tuteurRepository->findBy(['archived' => false], ['nom' => 'ASC']);

        $rows = [];
        $toArchive = [];

        foreach ($tuteurs as $tuteur) {
            $enfants = $this->getEnfants($tuteur);
            $hasYoung = false;
            $enfantRows = [];

            if ([] === $enfants) {
                $enfantRows[] = [(string)$tuteur, '— aucun enfant —', ''];
            }

            foreach ($enfants as $index => $enfant) {
                $age = $enfant->getAge();
                // getAge() renvoie un float "années.mois" (13.11 = 13 ans 11 mois),
                // on ne compare donc que les années révolues : "13 ans ou moins" = pas encore 14 ans.
                $isYoung = null !== $age && (int)$age <= self::AGE_MAX;
                if ($isYoung) {
                    $hasYoung = true;
                }

                $enfantRows[] = [
                    0 === $index ? (string)$tuteur : '',
                    (string)$enfant,
                    null !== $age ? number_format($age, 1, ',', '') : '?',
                ];
            }

            // On n'affiche que les tuteurs qui seront archivés (aucun enfant de 13 ans ou moins).
            if ($hasYoung) {
                continue;
            }

            $toArchive[] = $tuteur;
            $tuteur->setArchived(true);

            foreach ($enfantRows as $enfantRow) {
                $rows[] = $enfantRow;
            }
            $rows[] = new TableSeparator();
        }

        $this->io->table(
            ['Tuteur', 'Enfant', 'Âge'],
            $rows
        );

        $this->io->writeln(sprintf('Tuteurs analysés : %d', \count($tuteurs)));
        $this->io->writeln(sprintf('Tuteurs à archiver : %d', \count($toArchive)));

        foreach ($toArchive as $tuteur) {
            $this->io->writeln(' - '.$tuteur);
        }

        if ($flush) {
            if ([] !== $toArchive) {
                $this->tuteurRepository->flush();
            }
            $this->io->success(sprintf('%d tuteur(s) archivé(s).', \count($toArchive)));
        } else {
            $this->io->note('Dry-run : aucune modification enregistrée. Utilisez --flush pour appliquer.');
        }

        return Command::SUCCESS;
    }

    /**
     * @return \AcMarche\Mercredi\Entity\Enfant[]
     */
    private function getEnfants(Tuteur $tuteur): array
    {
        $enfants = [];
        foreach ($tuteur->getRelations() as $relation) {
            $enfant = $relation->getEnfant();
            if (null !== $enfant) {
                $enfants[] = $enfant;
            }
        }

        return $enfants;
    }
}
