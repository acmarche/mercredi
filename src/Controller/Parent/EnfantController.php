<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Enfant\Form\EnfantEditForParentType;
use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/enfant")
 */
class EnfantController extends AbstractController
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;
    /**
     * @var RelationUtils
     */
    private $relationUtils;

    public function __construct(
        EnfantRepository $enfantRepository,
        TuteurUtils $tuteurUtils,
        RelationUtils $relationUtils
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->tuteurUtils = $tuteurUtils;
        $this->relationUtils = $relationUtils;
    }

    /**
     * @Route("/", name="mercredi_parent_enfant_index", methods={"GET"})
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function index()
    {
        $user = $this->getUser();
        $tuteur = $this->tuteurUtils->getTuteurByUser($user);

        if (!$tuteur) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        $enfants = $this->relationUtils->findEnfantsByTuteur($tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'year' => date('Y'),
            ]
        );
    }

    /**
     * @Route("/{uuid}", name="mercredi_parent_enfant_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function show(Enfant $enfant)
    {
        return $this->render(
            '@AcMarcheMercrediParent/enfant/show.html.twig',
            [
                'enfant' => $enfant,
            ]
        );
    }

    /**
     * @Route("/{uuid}/edit", name="mercredi_parent_enfant_edit", methods={"GET","POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function edit(Request $request, Enfant $enfant)
    {
        $form = $this->createForm(EnfantEditForParentType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatchMessage(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('mercredi_parent_enfant_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
