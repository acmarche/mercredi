<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Sante\Form\SanteFicheType;
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
    use GetTuteurTrait;
    /**
     * @var SanteHandler
     */
    private $santeHandler;
    /**
     * @var SanteChecker
     */
    private $santeChecker;
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;

    public function __construct(SanteQuestionRepository $santeQuestionRepository, OrganisationRepository $organisationRepository, SanteHandler $santeHandler, SanteChecker $santeChecker)
    {
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->organisationRepository = $organisationRepository;
    }

    /**
     * @Route("/{uuid}", name="mercredi_parent_sante_fiche_show", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        if (! $santeFiche->getId()) {
            $this->addFlash('warning', 'Cette enfant n\'a pas encore de fiche santé');

            return $this->redirectToRoute('mercredi_parent_sante_fiche_edit', ['uuid' => $enfant->getUuid()]);
        }

        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAll();
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
     * @Route("/{uuid}/edit", name="mercredi_parent_sante_fiche_edit", methods={"GET","POST"})
     * @IsGranted("enfant_edit", subject="enfant")
     */
    public function edit(Request $request, Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        $form = $this->createForm(SanteFicheType::class, $santeFiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questions = $form->getData()->getQuestions();
            $this->santeHandler->handle($santeFiche, $questions);

            $this->dispatchMessage(new SanteFicheUpdated($santeFiche->getId()));

            return $this->redirectToRoute('mercredi_parent_sante_fiche_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/sante_fiche/edit.html.twig',
            [
                'sante_fiche' => $santeFiche,
                'form' => $form->createView(),
            ]
        );
    }
}
