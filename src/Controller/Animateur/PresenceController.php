<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Animateur\Utils\AnimateurUtils;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 */
final class PresenceController extends AbstractController
{
    use GetAnimateurTrait;

    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var AnimateurUtils
     */
    private $animateurUtils;

    public function __construct(
        PresenceRepository $presenceRepository,
        AnimateurUtils $animateurUtils
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->animateurUtils = $animateurUtils;
    }

    /**
     * @Route("/", name="mercredi_animateur_presence_index", methods={"GET","POST"}).
     */
    public function index(Request $request): Response
    {
        if ($t = $this->hasAnimateur()) {
            return $t;
        }

        $jours = $this->animateurUtils->getAllJours($this->animateur);

        return $this->render(
            '@AcMarcheMercrediAnimateur/presence/index.html.twig',
            [
                'jours' => $jours,
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_animateur_presence_show", methods={"GET","POST"}).
     */
    public function show(Jour $jour): Response
    {
        $this->denyAccessUnlessGranted('jour_show', $jour);

        $presences = $this->presenceRepository->findPresencesByJours([$jour]);
        $enfants = PresenceUtils::extractEnfants($presences);

        return $this->render(
            '@AcMarcheMercrediAnimateur/presence/show.html.twig',
            [
                'enfants' => $enfants,
                'jour' => $jour,
            ]
        );
    }
}
