<?php


namespace AcMarche\Mercredi\Migration\Handler;


use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class EnfantImport
{
    private SymfonyStyle $io;
    private EnfantRepository $enfantRepository;
    private MigrationRepository $migrationRepository;

    public function __construct(
        EnfantRepository $enfantRepository,
        MigrationRepository $migrationRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->migrationRepository = $migrationRepository;
    }

    public function import(SymfonyStyle $io)
    {
        $this->io = $io;
        $pdo = new MercrediPdo();
        $enfants = $pdo->getAll('enfant');
        foreach ($enfants as $data) {
            $this->io->writeln($data->nom);
            $enfant = new Enfant();
            $enfant->generateUuid();
            $enfant->generateSlug();
            $enfant->setNom($data->nom);
            $enfant->setPrenom($data->prenom);
            $enfant->setBirthday($data->birthday);
            $anneeScolaire = $this->migrationRepository->getAnneeScolaire($data);
            $enfant->setAnneeScolaire($anneeScolaire);
            $enfant->setArchived($data->archive);
            $groupeScolaire = $this->migrationRepository->getGroupeScolaire($data);
            $enfant->setGroupeScolaire($groupeScolaire);
            $enfant->setOrdre($data->ordre);
            $enfant->setPhotoName($data->image_name);
            $enfant->setPhotoAutorisation($data->photo_autorisation);
            $enfant->setRemarque($data->remarque);
            $enfant->setSexe($data->sexe);
            $user = $this->migrationRepository->getUser($data->user_add_id);
            $enfant->setUserAdd($user->getUserIdentifier());
            $enfant->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s',$data->updated));
            $enfant->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s',$data->created));
            $this->enfantRepository->persist($enfant);
        }

    }

}
