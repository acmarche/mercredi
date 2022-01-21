<?php

namespace AcMarche\Mercredi\Facture\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Facture\Facture;
use Symfony\Component\String\Slugger\SluggerInterface;

class FactureUtils
{
    public function __construct(
        public SluggerInterface $slugger
    ) {
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
}
