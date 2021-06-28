<?php

namespace AcMarche\Mercredi\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Scolaire\Form\GroupeScolaireType;
use AcMarche\Mercredi\Scolaire\Message\GroupeScolaireCreated;
use AcMarche\Mercredi\Scolaire\Message\GroupeScolaireDeleted;
use AcMarche\Mercredi\Scolaire\Message\GroupeScolaireUpdated;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groupe_scolaire")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class GroupeScolaireController extends AbstractController
{
    private GroupeScolaireRepository $groupeScolaireRepository;

    public function __construct(GroupeScolaireRepository $groupeScolaireRepository)
    {
        $this->groupeScolaireRepository = $groupeScolaireRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_groupe_scolaire_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/groupe_scolaire/index.html.twig',
            [
                'groupes' => $this->groupeScolaireRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_groupe_scolaire_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $groupeScolaire = new GroupeScolaire();
        $form = $this->createForm(GroupeScolaireType::class, $groupeScolaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->groupeScolaireRepository->persist($groupeScolaire);
            $this->groupeScolaireRepository->flush();

            $this->dispatchMessage(new GroupeScolaireCreated($groupeScolaire->getId()));

            return $this->redirectToRoute('mercredi_admin_groupe_scolaire_show', ['id' => $groupeScolaire->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/groupe_scolaire/new.html.twig',
            [
                'groupe' => $groupeScolaire,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_groupe_scolaire_show", methods={"GET"})
     */
    public function show(GroupeScolaire $groupeScolaire): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/groupe_scolaire/show.html.twig',
            [
                'groupe_scolaire' => $groupeScolaire,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_groupe_scolaire_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GroupeScolaire $groupeScolaire): Response
    {
        $form = $this->createForm(GroupeScolaireType::class, $groupeScolaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->groupeScolaireRepository->flush();

            $this->dispatchMessage(new GroupeScolaireUpdated($groupeScolaire->getId()));

            return $this->redirectToRoute('mercredi_admin_groupe_scolaire_show', ['id' => $groupeScolaire->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/groupe_scolaire/edit.html.twig',
            [
                'groupe_scolaire' => $groupeScolaire,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_groupe_scolaire_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GroupeScolaire $groupeScolaire): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$groupeScolaire->getId(), $request->request->get('_token'))) {
            $ecoleId = $groupeScolaire->getId();
            $this->groupeScolaireRepository->remove($groupeScolaire);
            $this->groupeScolaireRepository->flush();
            $this->dispatchMessage(new GroupeScolaireDeleted($ecoleId));
        }

        return $this->redirectToRoute('mercredi_admin_groupe_scolaire_index');
    }
}
