<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Enfant\Form\SearchEnfantForAnimateurType;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/enfant')]
final class EnfantController extends AbstractController
{
    use GetAnimateurTrait;

    public function __construct(
        private EnfantRepository $enfantRepository,
        private SanteHandler $santeHandler,
        private SanteChecker $santeChecker,
        private PresenceRepository $presenceRepository,
        private RelationRepository $relationRepository,
    ) {}

    #[Route(path: '/', name: 'mercredi_animateur_enfant_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MERCREDI_ANIMATEUR')]
    public function index(Request $request): Response
    {
        if (($hasAnimateur = $this->hasAnimateur()) !== null) {
            return $hasAnimateur;
        }
        $nom = null;
        $form = $this->createForm(
            SearchEnfantForAnimateurType::class,
            null,
            [
                'animateur' => $this->animateur,
            ],
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
        }
        $enfants = $this->enfantRepository->searchForAnimateur($this->animateur, $nom);

        return $this->render(
            '@AcMarcheMercrediAnimateur/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{uuid}', name: 'mercredi_animateur_enfant_show', methods: ['GET'])]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findWithoutPlaineByEnfant($enfant);
        $relations = $this->relationRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheMercrediAnimateur/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'relations' => $relations,
                'ficheSanteComplete' => $ficheSanteComplete,
            ],
        );
    }
}
