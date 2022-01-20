<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Sante\Form\SanteFicheFullType;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Message\SanteFicheDeleted;
use AcMarche\Mercredi\Sante\Message\SanteFicheUpdated;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/santeFiche")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class SanteFicheController extends AbstractController
{
    private SanteFicheRepository $santeFicheRepository;
    private SanteHandler $santeHandler;
    private SanteChecker $santeChecker;
    private SanteQuestionRepository $santeQuestionRepository;
    private OrganisationRepository $organisationRepository;

    public function __construct(
        SanteFicheRepository $santeFicheRepository,
        SanteQuestionRepository $santeQuestionRepository,
        OrganisationRepository $organisationRepository,
        SanteHandler $santeHandler,
        SanteChecker $santeChecker,
        private MessageBusInterface $dispatcher
    ) {
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->organisationRepository = $organisationRepository;
    }

    /**
     * @Route("/{id}", name="mercredi_admin_sante_fiche_show", methods={"GET"})
     */
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        if (!$santeFiche->getId()) {
            $this->addFlash('warning', 'Cette enfant n\'a pas encore de fiche santé');

            return $this->redirectToRoute('mercredi_admin_sante_fiche_edit', ['id' => $enfant->getId()]);
        }

        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercrediAdmin/sante_fiche/show.html.twig',
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
     * @Route("/{id}/edit", name="mercredi_admin_sante_fiche_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        if ([] === $santeFiche->getAccompagnateurs()) {
            $santeFiche->addAccompagnateur(' ');
        }

        $form = $this->createForm(SanteFicheFullType::class, $santeFiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questions = $form->getData()->getQuestions();
            $this->santeHandler->handle($santeFiche, $questions);

            $this->dispatcher->dispatch(new SanteFicheUpdated($santeFiche->getId()));

            return $this->redirectToRoute('mercredi_admin_sante_fiche_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/sante_fiche/edit.html.twig',
            [
                'sante_fiche' => $santeFiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_sante_fiche_delete", methods={"POST"})
     */
    public function delete(Request $request, SanteFiche $santeFiche): RedirectResponse
    {
        $enfant = null;
        if ($this->isCsrfTokenValid('delete'.$santeFiche->getId(), $request->request->get('_token'))) {
            $id = $santeFiche->getId();
            $enfant = $santeFiche->getEnfant();
            $this->santeFicheRepository->remove($santeFiche);
            $this->santeFicheRepository->flush();
            $this->dispatcher->dispatch(new SanteFicheDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
    }
}
