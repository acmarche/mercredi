<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Enfant\Message\EnfantCreated;
use AcMarche\Mercredi\Enfant\Message\EnfantDeleted;
use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Enfant\Form\EnfantType;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/enfant")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class EnfantController extends AbstractController
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;

    public function __construct(EnfantRepository $enfantRepository)
    {
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * @Route("/", name="admin_mercredi_enfant_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/index.html.twig',
            [
                'enfants' => $this->enfantRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="admin_mercredi_enfant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $enfant = new Enfant();
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->enfantRepository->persist($enfant);
            $this->enfantRepository->flush();

            $this->dispatchMessage(new EnfantCreated($enfant->getId()));

            return $this->redirectToRoute('admin_mercredi_enfant_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_mercredi_enfant_show", methods={"GET"})
     */
    public function show(Enfant $enfant): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/show.html.twig',
            [
                'enfant' => $enfant,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="admin_mercredi_enfant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatchMessage(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('admin_mercredi_enfant_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_mercredi_enfant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Enfant $enfant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enfant->getId(), $request->request->get('_token'))) {
            $this->enfantRepository->remove($enfant);
            $this->enfantRepository->flush();
            $this->dispatchMessage(new EnfantDeleted($enfant->getId()));
        }

        return $this->redirectToRoute('admin_mercredi_enfant_index');
    }
}
