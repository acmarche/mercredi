<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Ecole\Utils\EcoleUtils;
use AcMarche\Mercredi\Entity\Animateur;
use Symfony\Component\HttpFoundation\Response;

trait GetAnimateurTrait
{
    /**
     * @var Animateur
     */
    private $animateur;

    /**
     * @required
     */
    public function setEcoleUtils(EcoleUtils $ecoleUtils): void
    {
        $this->ecoleUtils = $ecoleUtils;
    }

    public function hasAnimateur(): ?Response
    {
        $user = $this->getUser();
        $this->animateur = $user->getAnimateur();

        if (!$this->animateur) {
            return $this->redirectToRoute('mercredi_animateur_nouveau');
        }

        return $this->denyAccessUnlessGranted('animateur_index', null);
    }
}
