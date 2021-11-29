<?php


namespace AcMarche\Mercredi\Controller\Admin;


use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Handler\FacturePlaineHandler;
use AcMarche\Mercredi\Facture\Render\FactureRenderInterface;
use AcMarche\Mercredi\Form\ValidateForm;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture_plaine")
 */
class FacturePlaineController extends AbstractController
{
    private FacturePlaineHandler $facturePlaineHandler;
    private RelationRepository $relationRepository;
    private PlainePresenceRepository $plainePresenceRepository;
    private FactureRenderInterface $factureRender;

    public function __construct(
        FacturePlaineHandler $facturePlaineHandler,
        RelationRepository $relationRepository,
        PlainePresenceRepository $plainePresenceRepository,
        FactureRenderInterface $factureRender
    ) {
        $this->facturePlaineHandler = $facturePlaineHandler;
        $this->relationRepository = $relationRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->factureRender = $factureRender;
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
        $presences = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        $form = $this->createForm(ValidateForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facture = $this->facturePlaineHandler->newInstance($plaine,$tuteur);
            $this->facturePlaineHandler->handleManually($facture, $plaine);

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
