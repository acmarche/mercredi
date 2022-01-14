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

/**
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 */
final class PresenceController extends AbstractController
{
    use GetAnimateurTrait;

    private PresenceRepository $presenceRepository;
    private EnfantRepository $enfantRepository;
    private JourRepository $jourRepository;

    public function __construct(
        PresenceRepository $presenceRepository,
        JourRepository $jourRepository,
        EnfantRepository $enfantRepository
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->enfantRepository = $enfantRepository;
        $this->jourRepository = $jourRepository;
    }

    /**
     * @Route("/", name="mercredi_animateur_presence_index", methods={"GET", "POST"}).
     */
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

    /**
     * @Route("/{id}", name="mercredi_animateur_presence_show", methods={"GET", "POST"}).
     */
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
