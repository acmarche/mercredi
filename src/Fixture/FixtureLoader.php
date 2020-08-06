<?php

namespace AcMarche\Mercredi\Fixture;

use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class FixtureLoader
{
    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        LoaderInterface $loader,
        ParameterBagInterface $parameterBag
    ) {
        $this->loader = $loader;
        $this->parameterBag = $parameterBag;
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
            $path.'facture.yaml',
            $path.'facture_presence.yaml',
            $path.'facture_accueil.yaml',
        ];

        $this->loader->load($files);
    }
}
