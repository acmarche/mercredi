<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Search\Form\SearchEnfantEcoleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/enfant')]
final class EnfantController extends AbstractController
{
    use GetEcolesTrait;

    public function __construct(
        private EnfantRepository $enfantRepository,
        private SanteHandler $santeHandler,
        private SanteChecker $santeChecker,
        private PresenceRepository $presenceRepository,
        private AccueilRepository $accueilRepository,
        private RelationRepository $relationRepository,
        private SanteQuestionRepository $santeQuestionRepository,
        private OrganisationRepository $organisationRepository,
        private MessageBusInterface $dispatcher,
    ) {
    }

    #[Route(path: '/', name: 'mercredi_ecole_enfant_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MERCREDI_ECOLE')]
    public function index(Request $request): Response
    {
        if (($response = $this->hasEcoles()) !== null) {
            return $response;
        }
        $nom = null;
        $accueil = true;
        $form = $this->createForm(SearchEnfantEcoleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $accueil = $data['accueil'] ?? false;
        }

        if ($this->getParameter(Option::ACCUEIL) < 2) {
            $accueil = false;
        }

        $enfants = $this->enfantRepository->searchForEcole($this->ecoles, $nom, $accueil);

        return $this->render(
            '@AcMarcheMercrediEcole/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/show/{uuid}', name: 'mercredi_ecole_enfant_show', methods: ['GET'])]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findWithoutPlaineByEnfant($enfant);
        $accueils = $this->accueilRepository->findByEnfant($enfant);
        $relations = $this->relationRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheMercrediEcole/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'accueils' => $accueils,
                'relations' => $relations,
                'ficheSanteComplete' => $ficheSanteComplete,
            ],
        );
    }

    #[Route(path: '/sante/{uuid}', name: 'mercredi_ecole_sante_fiche_show', methods: ['GET'])]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function santeFiche(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        if (!$santeFiche->getId()) {
            $this->addFlash('warning', 'Cette enfant n\'a pas encore de fiche santé');

            return $this->redirectToRoute('mercredi_ecole_enfant_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }
        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercrediEcole/enfant/sante_fiche.html.twig',
            [
                'enfant' => $enfant,
                'sante_fiche' => $santeFiche,
                'is_complete' => $isComplete,
                'questions' => $questions,
                'organisation' => $organisation,
            ],
        );
    }
}
