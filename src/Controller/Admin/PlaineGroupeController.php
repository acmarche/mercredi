<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Plaine\Form\PlaineGroupeEditType;
use AcMarche\Mercredi\Plaine\Repository\PlaineGroupeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/plaine_groupe')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class PlaineGroupeController extends AbstractController
{
    public function __construct(
        private PlaineGroupeRepository $plaineGroupeRepository
    ) {
    }

    #[Route(path: '/edit/{id}', name: 'mercredi_admin_plaine_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PlaineGroupe $plaineGroupe): Response
    {
        $plaine = $plaineGroupe->getPlaine();
        $form = $this->createForm(PlaineGroupeEditType::class, $plaineGroupe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineGroupeRepository->flush();
            $this->addFlash('success', 'le groupe été enregistré');

            return $this->redirectToRoute('mercredi_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_groupe/edit.html.twig',
            [
                'plaine' => $plaine,
                'plaine_groupe' => $plaineGroupe,
                'form' => $form->createView(),
            ]
        );
    }
}
