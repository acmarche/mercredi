<?php


namespace AcMarche\Mercredi\Controller\Admin;


use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\FacturePlaine\Handler\FactureHandler;
use AcMarche\Mercredi\Form\ValidateForm;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacturePlaineController extends AbstractController
{
    private FactureHandler $factureHandler;
    private PresenceRepository $presenceRepository;
    private RelationRepository $relationRepository;
    private PlainePresenceRepository $plainePresenceRepository;

    public function __construct(
        FactureHandler $factureHandler,
        PresenceRepository $presenceRepository,
        RelationRepository $relationRepository,
        PlainePresenceRepository $plainePresenceRepository
    ) {
        $this->factureHandler = $factureHandler;
        $this->presenceRepository = $presenceRepository;
        $this->relationRepository = $relationRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
    }

    /**
     * @Route("/{id}/manual", name="mercredi_admin_facture_select_plaine", methods={"GET","POST"})
     */
    public function selectPlaine(Request $request, Tuteur $tuteur): Response
    {
        $relations = $this->relationRepository->findByTuteur($tuteur);
        $enfants = RelationUtils::extractEnfants($relations);
        $plaines = [[]];
        foreach ($enfants as $enfant) {
            $plaines[] = $this->plainePresenceRepository->findPlainesByEnfant($enfant);
        }
        $plaines = array_merge(...$plaines);

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_plaine/select_plaine.html.twig',
            [
                'tuteur' => $tuteur,
                'plaines' => $plaines,
            ]
        );

    }

    /**
     * @Route("/{tuteur}/{plaine}/manual", name="mercredi_admin_facture_new_plaine", methods={"GET","POST"})
     */
    public function newManual(Request $request, Tuteur $tuteur, Plaine $plaine): Response
    {
        $presences = $this->presenceRepository->findPresencesByPlaineAndTuteur($plaine, $tuteur);
        $form = $this->createForm(ValidateForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facture = $this->factureHandler->newInstance($tuteur);
            $this->factureHandler->handleManually($facture, $plaine);

            $this->addFlash('success', 'Facture générée');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_plaine/new.html.twig',
            [
                'tuteur' => $tuteur,
                'plaine' => $plaine,
                'presences' => $presences,
                'form' => $form->createView(),
            ]
        );
    }
}
