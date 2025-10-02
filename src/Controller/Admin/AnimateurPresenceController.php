<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Animateur\Form\AnimateurJourType;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Entity\Animateur;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/animateur/presences')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class AnimateurPresenceController extends AbstractController
{
    public function __construct(
        private AnimateurRepository $animateurRepository,
    ) {}

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_animateur_presence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animateur $animateur): Response
    {
        $form = $this->createForm(AnimateurJourType::class, $animateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->flush();

            return $this->redirectToRoute('mercredi_admin_animateur_show', [
                'id' => $animateur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/animateur/presences_edit.html.twig',
            [
                'animateur' => $animateur,
                'form' => $form,
            ],
        );
    }
}
