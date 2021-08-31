<?php

namespace AcMarche\Mercredi\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Form\SanteQuestionType;
use AcMarche\Mercredi\Sante\Message\SanteQuestionCreated;
use AcMarche\Mercredi\Sante\Message\SanteQuestionDeleted;
use AcMarche\Mercredi\Sante\Message\SanteQuestionUpdated;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use function is_array;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sante/question")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class SanteQuestionController extends AbstractController
{
    private SanteQuestionRepository $santeQuestionRepository;

    public function __construct(SanteQuestionRepository $santeQuestionRepository)
    {
        $this->santeQuestionRepository = $santeQuestionRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_sante_question_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/sante_question/index.html.twig',
            [
                'sante_questions' => $this->santeQuestionRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_sante_question_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $santeQuestion = new SanteQuestion();
        $form = $this->createForm(SanteQuestionType::class, $santeQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeQuestionRepository->persist($santeQuestion);
            $this->santeQuestionRepository->flush();

            $this->dispatchMessage(new SanteQuestionCreated($santeQuestion->getId()));

            return $this->redirectToRoute('mercredi_admin_sante_question_show', ['id' => $santeQuestion->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/sante_question/new.html.twig',
            [
                'sante_question' => $santeQuestion,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_sante_question_show", methods={"GET"})
     */
    public function show(SanteQuestion $santeQuestion): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/sante_question/show.html.twig',
            [
                'sante_question' => $santeQuestion,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_sante_question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SanteQuestion $santeQuestion): Response
    {
        $form = $this->createForm(SanteQuestionType::class, $santeQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeQuestionRepository->flush();

            $this->dispatchMessage(new SanteQuestionUpdated($santeQuestion->getId()));

            return $this->redirectToRoute('mercredi_admin_sante_question_show', ['id' => $santeQuestion->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/sante_question/edit.html.twig',
            [
                'sante_question' => $santeQuestion,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_sante_question_delete", methods={"POST"})
     */
    public function delete(Request $request, SanteQuestion $santeQuestion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$santeQuestion->getId(), $request->request->get('_token'))) {
            $id = $santeQuestion->getId();
            $this->santeQuestionRepository->remove($santeQuestion);
            $this->santeQuestionRepository->flush();
            $this->dispatchMessage(new SanteQuestionDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_sante_question_index');
    }

    /**
     * @Route("/q/sort", name="mercredi_admin_sante_question_sort", methods={"GET"})
     */
    public function trier(): Response
    {
        $questions = $this->santeQuestionRepository->findAll();

        return $this->render(
            '@AcMarcheMercrediAdmin/sante_question/sort.html.twig',
            [
                'questions' => $questions,
            ]
        );
    }
}
