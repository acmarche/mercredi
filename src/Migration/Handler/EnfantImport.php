<?php


namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Note;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class EnfantImport
{
    private SymfonyStyle $io;
    private EnfantRepository $enfantRepository;
    private MigrationRepository $migrationRepository;
    private MercrediPdo $pdo;

    public function __construct(
        EnfantRepository $enfantRepository,
        MigrationRepository $migrationRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->migrationRepository = $migrationRepository;
        $this->pdo = new MercrediPdo();
    }

    public function import(SymfonyStyle $io)
    {
        $this->io = $io;
        $enfants = $this->pdo->getAll('enfant');
        foreach ($enfants as $data) {
            $this->io->writeln($data->nom);
            $enfant = new Enfant();
            $enfant->setNom($data->nom);
            $enfant->setPrenom($data->prenom);
            if ($birthday = \DateTime::createFromFormat('Y-m-d', $data->birthday)) {
                $enfant->setBirthday($birthday);
            }
            $anneeScolaire = $this->migrationRepository->getAnneeScolaire($data->annee_scolaire);
            $enfant->setAnneeScolaire($anneeScolaire);
            if ($data->ecole_id) {
                $ecole = $this->migrationRepository->getEcole($data->ecole_id);
                $enfant->setEcole($ecole);
            }
            $enfant->setArchived($data->archive);
            if ($data->groupe_scolaire) {
                $groupeScolaire = $this->migrationRepository->getGroupeScolaire($data->groupe_scolaire);
                $enfant->setGroupeScolaire($groupeScolaire);
            }
            $enfant->setOrdre($data->ordre);
            $enfant->setPhotoName($data->image_name);
            $enfant->setPhotoAutorisation($data->photo_autorisation);
            $enfant->setRemarque($data->remarques);
            $enfant->setSexe($data->sexe);
            $user = $this->migrationRepository->getUser($data->user_add_id);
            $enfant->setUserAdd($user->getUserIdentifier());
            $enfant->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
            $enfant->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
            $enfant->generateUuid();
            $enfant->setSlug($data->slugname);
            $this->enfantRepository->persist($enfant);
        }
        $this->enfantRepository->flush();
    }

    public function importRelation(SymfonyStyle $io)
    {
        $relations = $this->pdo->getAll('enfant_tuteur');
        foreach ($relations as $data) {
            $tuteur = $this->migrationRepository->getTuteur($data->tuteur_id);
            $enfant = $this->migrationRepository->getEnfant($data->enfant_id);

            $relation = new Relation($tuteur, $enfant);
            $relation->setType($data->relation);
            $relation->setOrdre($data->ordre);
            $this->enfantRepository->persist($relation);
        }
        $this->enfantRepository->flush();
    }

    public function importNote(SymfonyStyle $io)
    {
        $enfants = $this->pdo->getAll('note');
        foreach ($enfants as $data) {
            $enfant = $this->migrationRepository->getEnfant($data->enfant_id);

            $note = new Note($enfant);
            $note->setRemarque($data->contenu);
            $note->setArchived($data->cloture);
            $user = $this->migrationRepository->getUser($data->user_add_id);
            $note->setUserAdd($user->getUserIdentifier());
            $note->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
            $note->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
            $this->enfantRepository->persist($note);
        }
        $this->enfantRepository->flush();
    }
}
