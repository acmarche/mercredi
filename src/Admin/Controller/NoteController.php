<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Note\Message\NoteCreated;
use AcMarche\Mercredi\Note\Message\NoteDeleted;
use AcMarche\Mercredi\Note\Message\NoteUpdated;
use AcMarche\Mercredi\Entity\Note;
use AcMarche\Mercredi\Note\Form\NoteType;
use AcMarche\Mercredi\Note\Repository\NoteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/note")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class NoteController extends AbstractController
{
    /**
     * @var NoteRepository
     */
    private $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    /**
     * @Route("/", name="admin_mercredi_note_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/note/index.html.twig',
            [
                'notes' => $this->noteRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="admin_mercredi_note_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->noteRepository->persist($note);
            $this->noteRepository->flush();

            $this->dispatchMessage(new NoteCreated($note->getId()));

            return $this->redirectToRoute('admin_mercredi_note_show', ['id' => $note->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/note/new.html.twig',
            [
                'note' => $note,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_mercredi_note_show", methods={"GET"})
     */
    public function show(Note $note): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/note/show.html.twig',
            [
                'note' => $note,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="admin_mercredi_note_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Note $note): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->noteRepository->flush();

            $this->dispatchMessage(new NoteUpdated($note->getId()));

            return $this->redirectToRoute('admin_mercredi_note_show', ['id' => $note->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/note/edit.html.twig',
            [
                'note' => $note,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_mercredi_note_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $this->noteRepository->remove($note);
            $this->noteRepository->flush();
            $this->dispatchMessage(new NoteDeleted($note->getId()));
        }

        return $this->redirectToRoute('admin_mercredi_note_index');
    }
}
