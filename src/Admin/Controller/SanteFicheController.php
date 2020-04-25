<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Sante\Form\SanteFicheType;
use AcMarche\Mercredi\Sante\Message\SanteFicheCreated;
use AcMarche\Mercredi\Sante\Message\SanteFicheDeleted;
use AcMarche\Mercredi\Sante\Message\SanteFicheUpdated;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/santeFiche")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class SanteFicheController extends AbstractController
{
    /**
     * @var SanteFicheRepository
     */
    private $santeFicheRepository;

    public function __construct(SanteFicheRepository $santeFicheRepository)
    {
        $this->santeFicheRepository = $santeFicheRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_sante_fiche_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/sante_fiche/index.html.twig',
            [
                'sante_fiches' => $this->santeFicheRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_sante_fiche_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $santeFiche = new SanteFiche();
        $form = $this->createForm(SanteFicheType::class, $santeFiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->santeFicheRepository->persist($santeFiche);
            $this->santeFicheRepository->flush();

            $this->dispatchMessage(new SanteFicheCreated($santeFiche->getId()));

            return $this->redirectToRoute('mercredi_admin_sante_fiche_show', ['id' => $santeFiche->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/sante_fiche/new.html.twig',
            [
                'sante_fiche' => $santeFiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_sante_fiche_show", methods={"GET"})
     */
    public function show(SanteFiche $santeFiche): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/sante_fiche/show.html.twig',
            [
                'sante_fiche' => $santeFiche,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_sante_fiche_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SanteFiche $santeFiche): Response
    {
        $form = $this->createForm(SanteFicheType::class, $santeFiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeFicheRepository->flush();

            $this->dispatchMessage(new SanteFicheUpdated($santeFiche->getId()));

            return $this->redirectToRoute('mercredi_admin_sante_fiche_show', ['id' => $santeFiche->getId()]);
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
     * @Route("/{id}", name="mercredi_admin_sante_fiche_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SanteFiche $santeFiche): Response
    {
        if ($this->isCsrfTokenValid('delete'.$santeFiche->getId(), $request->request->get('_token'))) {
            $this->santeFicheRepository->remove($santeFiche);
            $this->santeFicheRepository->flush();
            $this->dispatchMessage(new SanteFicheDeleted($santeFiche->getId()));
        }

        return $this->redirectToRoute('mercredi_admin_sante_fiche_index');
    }
}
