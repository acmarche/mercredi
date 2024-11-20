<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('ROLE_MERCREDI_ANIMATEUR')]
final class DefaultController extends AbstractController
{
    use OrganisationPropertyInitTrait;
    use GetAnimateurTrait;

    #[Route(path: '/', name: 'mercredi_animateur_home')]
    public function default(): Response
    {
        if (($response = $this->hasAnimateur()) !== null) {
            return $response;
        }

        return $this->redirectToRoute('mercredi_animateur_enfant_index');
    }

    #[Route(path: '/nouveau', name: 'mercredi_animateur_nouveau')]
    public function nouveau(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAnimateur/default/nouveau.html.twig',
            [
                'organisation' => $this->organisation,
            ],
        );
    }
}
