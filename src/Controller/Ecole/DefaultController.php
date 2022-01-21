<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class DefaultController extends AbstractController
{
    use GetEcolesTrait;
    use OrganisationPropertyInitTrait;

    #[Route(path: '/', name: 'mercredi_ecole_home')]
    #[IsGranted(data: 'ROLE_MERCREDI_ECOLE')]
    public function default(): Response
    {
        if (($response = $this->hasEcoles()) !== null) {
            return $response;
        }

        return $this->redirectToRoute('mercredi_ecole_ecole_index');
    }

    #[Route(path: '/nouveau', name: 'mercredi_ecole_nouveau')]
    #[IsGranted(data: 'ROLE_MERCREDI_ECOLE')]
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
