<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Sante\Form\Etape\SanteFicheEtape1Type;
use AcMarche\Mercredi\Sante\Form\Etape\SanteFicheEtape2Type;
use AcMarche\Mercredi\Sante\Form\Etape\SanteFicheEtape3Type;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Message\SanteFicheUpdated;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/santeFiche")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
final class SanteFicheController extends AbstractController
{
    private SanteHandler $santeHandler;
    private SanteChecker $santeChecker;
    private SanteQuestionRepository $santeQuestionRepository;
    private OrganisationRepository $organisationRepository;
    private SanteFicheRepository $santeFicheRepository;

    public function __construct(
        SanteQuestionRepository $santeQuestionRepository,
        OrganisationRepository $organisationRepository,
        SanteHandler $santeHandler,
        SanteChecker $santeChecker,
        SanteFicheRepository $santeFicheRepository
    ) {
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->organisationRepository = $organisationRepository;
        $this->santeFicheRepository = $santeFicheRepository;
    }

    /**
     * @Route("/{uuid}", name="mercredi_parent_sante_fiche_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        if (!$santeFiche->getId()) {
            $this->addFlash('warning', 'Cette enfant n\'a pas encore de fiche santé');

            return $this->redirectToRoute('mercredi_parent_sante_fiche_edit', ['uuid' => $enfant->getUuid()]);
        }

        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercrediParent/sante_fiche/show.html.twig',
            [
                'enfant' => $enfant,
                'sante_fiche' => $santeFiche,
                'is_complete' => $isComplete,
                'questions' => $questions,
                'organisation' => $organisation,
            ]
        );
    }

    /**
     * @Route("/{uuid}/edit/etape1", name="mercredi_parent_sante_fiche_edit", methods={"GET", "POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(SanteFicheEtape1Type::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeFicheRepository->flush();
            $this->dispatchMessage(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('mercredi_parent_sante_fiche_edit_etape2', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercredi/parent/sante_fiche/edit/etape1.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}/edit/etape2", name="mercredi_parent_sante_fiche_edit_etape2", methods={"GET", "POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function editEtape2(Request $request, Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant, false);
        if ([] === $santeFiche->getAccompagnateurs()) {
            $santeFiche->addAccompagnateur(' ');
        }

        $form = $this->createForm(SanteFicheEtape2Type::class, $santeFiche);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeFicheRepository->flush();
            $this->dispatchMessage(new SanteFicheUpdated($santeFiche->getId()));

            return $this->redirectToRoute('mercredi_parent_sante_fiche_edit_etape3', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercredi/parent/sante_fiche/edit/etape2.html.twig',
            [
                'sante_fiche' => $santeFiche,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{uuid}/edit/etape3", name="mercredi_parent_sante_fiche_edit_etape3", methods={"GET", "POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function editEtape3(Request $request, Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        $form = $this->createForm(SanteFicheEtape3Type::class, $santeFiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questions = $form->getData()->getQuestions();
            $this->santeHandler->handle($santeFiche, $questions);

            $this->dispatchMessage(new SanteFicheUpdated($santeFiche->getId()));

            return $this->redirectToRoute('mercredi_parent_sante_fiche_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercredi/parent/sante_fiche/edit/etape3.html.twig',
            [
                'sante_fiche' => $santeFiche,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
