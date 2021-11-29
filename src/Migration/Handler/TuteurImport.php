<?php


namespace AcMarche\Mercredi\Migration\Handler;

use DateTime;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class TuteurImport
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
        $enfants = $pdo->getAll('tuteur');
        foreach ($enfants as $data) {
            $this->io->writeln($data->nom);
            $tuteur = new Tuteur();
            $tuteur->setNom($data->nom);
            $tuteur->setPrenom($data->prenom);
            $tuteur->setArchived($data->archive);
            $tuteur->setRemarque($data->remarques);
            $tuteur->setEmail($data->email);
            $tuteur->setRue($data->adresse);
            $tuteur->setCodePostal($data->code_postal);
            $tuteur->setLocalite($data->localite);
            $tuteur->setNomConjoint($data->nom_conjoint);
            $tuteur->setPrenomConjoint($data->prenom_conjoint);
            $tuteur->setTelephone($data->telephone);
            $tuteur->setTelephoneBureau($data->telephone_bureau);
            $tuteur->setTelephoneBureauConjoint($data->telephone_bureau_conjoint);
            $tuteur->setTelephoneConjoint($data->telephone_conjoint);
            $tuteur->setGsm($data->gsm);
            $tuteur->setGsmConjoint($data->gsm_conjoint);
            $tuteur->setEmailConjoint($data->email_conjoint);
            $tuteur->setSexe($data->sexe);
            $user = $this->migrationRepository->getUser($data->user_add_id);
            $tuteur->setUserAdd($user->getUserIdentifier());
            $this->addUser($tuteur, $data->user_id);
            $tuteur->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
            $tuteur->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
            $tuteur->setSlug($data->slugname);
            $this->tuteurRepository->persist($tuteur);
        }
        $this->tuteurRepository->flush();
    }

    private function addUser(Tuteur $tuteur, ?int $userId): void
    {
        if ($userId) {
            $user = $this->migrationRepository->getUser($userId);
            $user->addTuteur($tuteur);
        }
    }
}
