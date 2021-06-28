<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
final class DefaultController extends AbstractController
{
    use GetEcolesTrait;
    use OrganisationPropertyInitTrait;

    /**
     * @Route("/", name="mercredi_ecole_home")
     * @IsGranted("ROLE_MERCREDI_ECOLE")
     */
    public function default(): Response
    {
        if (($response = $this->hasEcoles()) !== null) {
            return $response;
        }

        return $this->redirectToRoute('mercredi_ecole_enfant_index');
    }

    /**
     * @Route("/nouveau", name="mercredi_ecole_nouveau")
     * @IsGranted("ROLE_MERCREDI_ECOLE")
     */
    public function nouveau(): Response
    {
        return $this->render(
            '@AcMarcheMercrediEcole/default/nouveau.html.twig',
            [
                'organisation' => $this->organisation,
            ]
        );
    }
}
