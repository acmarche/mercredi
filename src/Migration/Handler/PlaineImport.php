<?php

namespace AcMarche\Mercredi\Migration\Handler;

use DateTime;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class PlaineImport
{
    private SymfonyStyle $io;
    private TuteurRepository $tuteurRepository;
    private MigrationRepository $migrationRepository;

    public function __construct(
        TuteurRepository $tuteurRepository,
        MigrationRepository $migrationRepository
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->migrationRepository = $migrationRepository;
    }

    public function import(SymfonyStyle $io): void
    {
        $this->io = $io;
        $pdo = new MercrediPdo();
        $plaines = $pdo->getAll('plaine');
        foreach ($plaines as $data) {
            $plaine = new Plaine();
            $plaine->setNom($data->intitule);
            $plaine->setSlug($data->slugname);
            $plaine->setRemarque($data->remarques);
            $plaine->setArchived($data->archive);
            $plaine->setPrematernelle($data->premat);
            $plaine->setPrix1($data->prix1);
            $plaine->setPrix2($data->prix2);
            $plaine->setPrix3($data->prix3);
            $this->tuteurRepository->persist($plaine);
            $io->writeln($plaine->getNom());
        }
        $this->tuteurRepository->flush();
    }

    public function importGroupe(SymfonyStyle $io): void
    {
        $this->io = $io;
        $pdo = new MercrediPdo();
        $jours = $pdo->getAll('plaine_max');
        foreach ($jours as $data) {
            $plaine = $this->migrationRepository->getPlaine($data->plaine_id);
            $groupeScolaire = $this->migrationRepository->getGroupeScolaire($data->groupe . ' plaine');
            $plaineGroupe = new PlaineGroupe($plaine, $groupeScolaire);
            $plaineGroupe->setInscriptionMaximum($data->maximum);
            $this->tuteurRepository->persist($plaineGroupe);
        }
        $this->tuteurRepository->flush();
    }

    public function importJours(SymfonyStyle $io): void
    {
        $this->io = $io;
        $pdo = new MercrediPdo();
        $jours = $pdo->getAll('plaine_jours');
        foreach ($jours as $data) {
            $plaine = $this->migrationRepository->getPlaine($data->plaine_id);
            $jourDate = DateTime::createFromFormat('Y-m-d', $data->date_jour);
            $jour = new Jour();
            $jour->setDateJour($jourDate);
            $jour->setPlaine($plaine);
            $this->tuteurRepository->persist($jour);
        }
        $this->tuteurRepository->flush();
    }
}
