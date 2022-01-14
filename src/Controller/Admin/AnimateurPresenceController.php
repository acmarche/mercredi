<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Animateur\Form\AnimateurJourType;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Entity\Animateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/animateur/presences")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class AnimateurPresenceController extends AbstractController
{
    private AnimateurRepository $animateurRepository;

    public function __construct(
        AnimateurRepository $animateurRepository
    ) {
        $this->animateurRepository = $animateurRepository;
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_animateur_presence_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Animateur $animateur): Response
    {
        $form = $this->createForm(AnimateurJourType::class, $animateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->flush();

            return $this->redirectToRoute('mercredi_admin_animateur_show', ['id' => $animateur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/animateur/presences_edit.html.twig',
            [
                'animateur' => $animateur,
                'form' => $form->createView(),
            ]
        );
    }
}
