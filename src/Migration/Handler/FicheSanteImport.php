<?php


namespace AcMarche\Mercredi\Migration\Handler;

use DateTime;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteReponse;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class FicheSanteImport
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

    public function import(SymfonyStyle $io): void
    {
        $this->io = $io;
        $fiches = $this->pdo->getAll('sante_fiche');
        foreach ($fiches as $data) {
            $enfant = $this->migrationRepository->getEnfant($data->enfant_id);
            $io->writeln($enfant->getPrenom());
            $ficheSante = new SanteFiche($enfant);
            $ficheSante->setIdOld($data->id);
            $ficheSante->setRemarque($data->remarques);
            $ficheSante->setMedecinNom($data->medecin_nom);
            $ficheSante->setMedecinTelephone($data->medecin_telephone);
            $ficheSante->setPersonneUrgence($data->personne_urgence);
            $ficheSante->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->updated_at));
            $ficheSante->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->created_at));
            $this->enfantRepository->persist($ficheSante);
        }
        $this->enfantRepository->flush();
    }

    public function importReponse(SymfonyStyle $io): void
    {
        $this->io = $io;
        $fiches = $this->pdo->getAll('sante_reponse');
        foreach ($fiches as $data) {
            $santeFiche = $this->migrationRepository->getSanteFiche($data->sante_fiche_id);
            $santeQuestion = $this->migrationRepository->getSanteQuestion($data->question_id);

            $santeReponse = new SanteReponse($santeFiche, $santeQuestion);
            $santeReponse->setIdOld($data->id);
            $santeReponse->setReponse((bool)$data->reponse);
            $santeReponse->setRemarque($data->remarque);
            $this->enfantRepository->persist($santeReponse);
        }
        $this->enfantRepository->flush();
    }
}
