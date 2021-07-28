<?php

namespace AcMarche\Mercredi\Fixture;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class FixtureLoader
{
    private LoaderInterface $loader;
    private ParameterBagInterface $parameterBag;
    private TuteurRepository $tuteurRepository;
    private PresenceRepository $presenceRepository;
    private EnfantRepository $enfantRepository;
    private AccueilRepository $accueilRepository;

    public function __construct(
        LoaderInterface $loader,
        ParameterBagInterface $parameterBag,
        TuteurRepository $tuteurRepository,
        PresenceRepository $presenceRepository,
        EnfantRepository $enfantRepository,
        AccueilRepository $accueilRepository
    ) {
        $this->loader = $loader;
        $this->parameterBag = $parameterBag;
        $this->tuteurRepository = $tuteurRepository;
        $this->presenceRepository = $presenceRepository;
        $this->enfantRepository = $enfantRepository;
        $this->accueilRepository = $accueilRepository;
    }

    public function getPath(): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/src/AcMarche/Mercredi/src/Fixture/Files/';
    }

    public function load(): void
    {
        $path = $this->getPath();

        $files = [
            $path.'groupe_scolaire.yaml',
            $path.'annee_scolaire.yaml',
            $path.'document.yaml',
            $path.'ecole.yaml',
            $path.'tuteur.yaml',
            $path.'animateur.yaml',
            $path.'enfant.yaml',
            $path.'relation.yaml',
            $path.'user.yaml',
            $path.'jour.yaml',
            $path.'organisation.yaml',
            $path.'presence.yaml',
            $path.'reduction.yaml',
            $path.'question.yaml',
            $path.'reponse.yaml',
            $path.'sante_fiche.yaml',
            $path.'page.yaml',
            $path.'sante_reponse.yaml',
            $path.'plaine.yaml',
            $path.'plaine_groupe.yaml',
            $path.'plaine_jour.yaml',
            $path.'accueil.yaml',
            $path.'note.yaml',
            //   $path.'facture.yaml',
            //     $path.'facture_presence.yaml',
            //      $path.'facture_accueil.yaml',
        ];

        $this->loader->load($files);
        $tuteurSimposn = $this->tuteurRepository->findOneBy(['prenom' => 'Homer']);
        $enfant = $this->enfantRepository->findOneBy(['prenom' => 'Bart']);
        $presence = $this->presenceRepository->findOneBy(['tuteur' => $tuteurSimposn, 'enfant' => $enfant]);
        $acceuil = $this->accueilRepository->findOneBy(['tuteur' => $tuteurSimposn, 'enfant' => $enfant]);

        $this->loader->load(
            [
                $path.'facture.yaml',
            ]
            ,
            [],
            [
                'tuteur_Simpson' => $tuteurSimposn,
                'presence_bart_06_05' => $presence,
                'acceuil__bart_09_12' => $acceuil,
            ],
            PurgeMode::createNoPurgeMode()
        );

    }
}
