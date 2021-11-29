<?php


namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Reduction;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Entity\Scolaire\AnneeScolaire;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class ParametreImport
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

    public function setIo(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    public function importAll()
    {
        $this->importEcole();
        $this->importJour();
        $this->importQuestions();
        $this->importReduction();
        $this->importGroupes();
        $this->importAnneeScolaire();

        $this->enfantRepository->flush();
    }

    public function importEcole()
    {
        $pdo = new MercrediPdo();
        $enfants = $pdo->getAll('ecole');
        foreach ($enfants as $data) {
            $this->io->writeln($data->nom);
            $ecole = new Ecole();
            $ecole->setNom($data->nom);
            $ecole->setEmail($data->email);
            $ecole->setRemarque($data->remarques);
            $ecole->setRue($data->adresse);
            $ecole->setCodePostal($data->code_postal);
            $ecole->setLocalite($data->localite);
            $ecole->setTelephone($data->telephone);
            $ecole->setGsm($data->gsm);
            $this->enfantRepository->persist($ecole);
        }
    }

    public function importReduction()
    {
        $pdo = new MercrediPdo();
        $enfants = $pdo->getAll('reduction');
        foreach ($enfants as $data) {
            $this->io->writeln($data->nom);
            $reduction = new Reduction();
            $reduction->setNom($data->nom);
            $reduction->setRemarque($data->remarques);
            $reduction->setPourcentage($data->pourcentage);
            $this->enfantRepository->persist($reduction);
        }
    }

    public function importJour()
    {
        $pdo = new MercrediPdo();
        $enfants = $pdo->getAll('jour');
        foreach ($enfants as $data) {
            $this->io->writeln($data->date_jour);
            $jour = new Jour();
            $jour->setDateJour(\DateTime::createFromFormat('Y-m-d', $data->date_jour));
            $jour->setRemarque($data->remarques);
            $jour->setColor($data->color);
            $jour->setArchived($data->archive);
            $jour->setPrix1($data->prix1);
            $jour->setPrix2($data->prix2);
            $jour->setPrix3($data->prix3);
            $jour->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
            $jour->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
            $this->enfantRepository->persist($jour);
        }
    }

    public function importQuestions()
    {
        $pdo = new MercrediPdo();
        $enfants = $pdo->getAll('sante_question');
        foreach ($enfants as $data) {
            $this->io->writeln($data->intitule);
            $reduction = new SanteQuestion();
            $reduction->setNom($data->intitule);
            $reduction->setCategorie($data->categorie);
            $reduction->setComplement((bool)$data->complement);
            $reduction->setComplementLabel($data->complement_label);
            $reduction->setDisplayOrder($data->display_order);
            $this->enfantRepository->persist($reduction);
        }
    }

    private function importAnneeScolaire()
    {
        $annees = ['PM', '1M', '2M', '3M', '1P', '2P', '3P', '4P', '5P', '6P'];
        $i = 0;
        foreach ($annees as $annee) {
            $anneeScolaire = new AnneeScolaire();
            $anneeScolaire->setNom($annee);
            $anneeScolaire->setOrdre($i);
            $groupeScolaire = $this->getGroupeScolaire($anneeScolaire);
            $anneeScolaire->setGroupeScolaire($groupeScolaire);
            $this->enfantRepository->persist($anneeScolaire);
            $i++;
        }
    }

    private function getGroupeScolaire(AnneeScolaire $anneeScolaire): GroupeScolaire
    {
        $groupeName = 'grands';
        if (in_array($anneeScolaire->getNom(), ['PM', '1M', '2M'])) {
            $groupeName = 'petits';
        }

        if (in_array($anneeScolaire->getNom(), ['3M', '1P', '2P'])) {
            $groupeName = 'moyens';
        }

        return $this->migrationRepository->getGroupeScolaire($groupeName);
    }

    private function importGroupes()
    {
        $groupes = ['premats', 'petits', 'moyens', 'grands'];
        foreach ($groupes as $data) {
            $groupe = new GroupeScolaire();
            $groupe->setOrdre(0);
            $groupe->setNom($data);
            $this->enfantRepository->persist($groupe);
        }

        $groupes = ['petits plaine', 'moyens plaine', 'grands plaine'];
        foreach ($groupes as $data) {
            $groupe = new GroupeScolaire();
            $groupe->setOrdre(0);
            $groupe->setNom($data);
            $groupe->setIsPlaine(true);
            $this->enfantRepository->persist($groupe);
        }

        $this->enfantRepository->flush();
    }
}
