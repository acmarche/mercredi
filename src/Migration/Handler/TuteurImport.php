<?php


namespace AcMarche\Mercredi\Migration\Handler;


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

    public function import(SymfonyStyle $io)
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
            $tuteur->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:s',$data->updated));
            $tuteur->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:s',$data->created));
            $tuteur->generateSlug();
            $this->tuteurRepository->persist($tuteur);
        }

    }

}
