<?php


namespace AcMarche\Mercredi\Facture\Utils;


use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class FactureUtils
{
    public SluggerInterface $slugger;
    private PresenceRepository $presenceRepository;

    public function __construct(SluggerInterface $slugger, PresenceRepository $presenceRepository)
    {
        $this->slugger = $slugger;
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * @return array|Enfant[]
     */
    public function getEnfants(Facture $facture): array
    {
        $enfants = [];
        foreach ($facture->getFacturePresences() as $facturePresence) {
            $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
            $slug = $this->slugger->slug($enfant);
            $enfants[$slug->toString()] = $enfant;
        }

        return $enfants;
    }

    /**
     * @return array|Ecole[]
     */
    public function getEcoles(Facture $facture): array
    {
        $ecoles = [];
        foreach ($facture->getFacturePresences() as $facturePresence) {
            if ($facturePresence->getObjectType() == FactureInterface::OBJECT_PRESENCE) {
                $presence = $this->presenceRepository->find($facturePresence->getPresenceId());
                $enfant = $presence->getEnfant();
                $ecole = $enfant->getEcole();
                $ecoles[$ecole->getId()] = $ecole;
            }
        }

        return $ecoles;
    }
}
