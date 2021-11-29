<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Creance;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Form\CreanceType;
use AcMarche\Mercredi\Facture\Repository\CreanceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/creance")
 */
final class CreanceController extends AbstractController
{
    private CreanceRepository $creanceRepository;

    public function __construct(
        CreanceRepository $creanceRepository
    ) {
        $this->creanceRepository = $creanceRepository;
    }

    /**
     * @Route("/{id}", name="mercredi_admin_creance_index", methods={"GET"})
     */
    public function index(Tuteur $tuteur): Response
    {
        $creances = $this->creanceRepository->findByTuteur($tuteur);

        return $this->render(
            '@AcMarcheMercrediAdmin/creance/index.html.twig',
            [
                'tuteur' => $tuteur,
                'creances' => $creances,

            ]
        );
    }

    /**
     * @Route("/{id}/new", name="mercredi_admin_creance_new", methods={"GET", "POST"})
     */
    public function new(Request $request, Tuteur $tuteur): Response
    {
        $creance = new Creance($tuteur);

        $form = $this->createForm(CreanceType::class, $creance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->creanceRepository->persist($creance);
            $this->creanceRepository->flush();

            $this->addFlash('success', 'La créance a bien été ajoutée');

            return $this->redirectToRoute('mercredi_admin_creance_show', ['id' => $creance->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/creance/new.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_admin_creance_show", methods={"GET"})
     */
    public function show(Creance $creance): Response
    {
        $tuteur = $creance->getTuteur();

        return $this->render(
            '@AcMarcheMercrediAdmin/creance/show.html.twig',
            [
                'tuteur' => $tuteur,
                'creance' => $creance,

            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_creance_edit", methods={"GET","POST"}).
     */
    public function edit(Request $request, Creance $creance): Response
    {
        $form = $this->createForm(CreanceType::class, $creance);
        $form->handleRequest($request);
        $tuteur = $creance->getTuteur();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->creanceRepository->flush();
            $this->addFlash('success', 'La créance a bien été modifiée');

            return $this->redirectToRoute('mercredi_admin_creance_show', ['id' => $creance->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/creance/edit.html.twig',
            [
                'creance' => $creance,
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_creance_delete", methods={"POST"})
     */
    public function delete(Request $request, Creance $creance): Response
    {
        $tuteur = $creance->getTuteur();

        if ($this->isCsrfTokenValid('delete' . $creance->getId(), $request->request->get('_token'))) {
            $this->creanceRepository->remove($creance);
            $this->creanceRepository->flush();

            $this->addFlash('success', 'La créance a bien été supprimée');
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
    }
}
