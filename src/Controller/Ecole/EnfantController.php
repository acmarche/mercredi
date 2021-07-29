<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Form\EnfantEditForEcoleType;
use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Search\Form\SearchEnfantEcoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/enfant")
 */
final class EnfantController extends AbstractController
{
    use GetEcolesTrait;

    private EnfantRepository $enfantRepository;
    private SanteHandler $santeHandler;
    private SanteChecker $santeChecker;
    private PresenceRepository $presenceRepository;
    private AccueilRepository $accueilRepository;
    private RelationRepository $relationRepository;
    private SanteQuestionRepository $santeQuestionRepository;
    private OrganisationRepository $organisationRepository;

    public function __construct(
        EnfantRepository $enfantRepository,
        SanteHandler $santeHandler,
        SanteChecker $santeChecker,
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        RelationRepository $relationRepository,
        SanteQuestionRepository $santeQuestionRepository,
        OrganisationRepository $organisationRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->presenceRepository = $presenceRepository;
        $this->accueilRepository = $accueilRepository;
        $this->relationRepository = $relationRepository;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->organisationRepository = $organisationRepository;
    }

    /**
     * @Route("/", name="mercredi_ecole_enfant_index", methods={"GET", "POST"})
     * @IsGranted("ROLE_MERCREDI_ECOLE")
     */
    public function index(Request $request): Response
    {
        if (($response = $this->hasEcoles()) !== null) {
            return $response;
        }

        $nom = null;
        $accueil = true;
        $form = $this->createForm(SearchEnfantEcoleType::class, ['accueil' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $accueil = $data['accueil'];
        }

        $enfants = $this->enfantRepository->searchForEcole($this->ecoles, $nom, $accueil);

        return $this->render(
            '@AcMarcheMercrediEcole/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/show/{uuid}", name="mercredi_ecole_enfant_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findPresencesByEnfant($enfant);
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
            ]
        );
    }

    /**
     * @Route("/{uuid}/edit", name="mercredi_ecole_enfant_edit", methods={"GET","POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(EnfantEditForEcoleType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatchMessage(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('mercredi_ecole_enfant_index', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediEcole/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/sante/{uuid}", name="mercredi_ecole_sante_fiche_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function santeFiche(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        if (!$santeFiche->getId()) {
            $this->addFlash('warning', 'Cette enfant n\'a pas encore de fiche santé');

            return $this->redirectToRoute('mercredi_ecole_enfant_show', ['uuid' => $enfant->getUuid()]);
        }

        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAll();
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercrediEcole/enfant/sante_fiche.html.twig',
            [
                'enfant' => $enfant,
                'sante_fiche' => $santeFiche,
                'is_complete' => $isComplete,
                'questions' => $questions,
                'organisation' => $organisation,
            ]
        );
    }
}
