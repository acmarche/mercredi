<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/presence')]
#[IsGranted(data: 'ROLE_MERCREDI_ANIMATEUR')]
final class PresenceController extends AbstractController
{
    use GetAnimateurTrait;

    public function __construct(
        private PresenceRepository $presenceRepository,
        private JourRepository $jourRepository,
        private EnfantRepository $enfantRepository
    ) {
    }

    #[Route(path: '/', name: 'mercredi_animateur_presence_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        if (($response = $this->hasAnimateur()) !== null) {
            return $response;
        }
        $jours = $this->jourRepository->findByAnimateur($this->animateur);

        return $this->render(
            '@AcMarcheMercrediAnimateur/presence/index.html.twig',
            [
                'jours' => $jours,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_animateur_presence_show', methods: ['GET', 'POST'])]
    public function show(Jour $jour): Response
    {
        $this->denyAccessUnlessGranted('jour_show', $jour);
        if (($response = $this->hasAnimateur()) !== null) {
            return $response;
        }
        $enfants = $this->enfantRepository->searchForAnimateur($this->animateur, null, $jour);

        return $this->render(
            '@AcMarcheMercrediAnimateur/presence/show.html.twig',
            [
                'enfants' => $enfants,
                'jour' => $jour,
            ]
        );
    }
}
