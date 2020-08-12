<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 */
final class DefaultController extends AbstractController
{
    use OrganisationPropertyInitTrait;
    use GetAnimateurTrait;

    /**
     * @Route("/", name="mercredi_animateur_home")
     *
     */
    public function default()
    {
        if ($t = $this->hasAnimateur()) {
            return $t;
        }

        return $this->render(
            '@AcMarcheMercrediAnimateur/default/index.html.twig',
            [
                'animateur' => $this->animateur,
            ]
        );
    }

    /**
     * @Route("/nouveau", name="mercredi_animateur_nouveau")
     */
    public function nouveau()
    {
        return $this->render(
            '@AcMarcheMercrediAnimateur/default/nouveau.html.twig',
            [
                'organisation' => $this->organisation,
            ]
        );
    }
}
