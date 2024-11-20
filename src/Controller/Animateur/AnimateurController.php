<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Animateur\Form\AnimateurType;
use AcMarche\Mercredi\Animateur\Message\AnimateurUpdated;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/animateur')]
#[IsGranted('ROLE_MERCREDI_ANIMATEUR')]
final class AnimateurController extends AbstractController
{
    use GetAnimateurTrait;

    public function __construct(
        private AnimateurRepository $animateurRepository,
        private EnfantRepository $enfantRepository,
        private MessageBusInterface $dispatcher,
    ) {}

    #[Route(path: '/', name: 'mercredi_animateur_animateur_show', methods: ['GET'])]
    public function show(): Response
    {
        if (($t = $this->hasAnimateur()) !== null) {
            return $t;
        }
        $this->denyAccessUnlessGranted('animateur_show', $this->animateur);

        return $this->render(
            '@AcMarcheMercrediAnimateur/animateur/show.html.twig',
            [
                'animateur' => $this->animateur,
            ],
        );
    }

    #[Route(path: '/edit', name: 'mercredi_animateur_animateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        if (($t = $this->hasAnimateur()) !== null) {
            return $t;
        }
        $this->denyAccessUnlessGranted('animateur_edit', $this->animateur);
        $form = $this->createForm(AnimateurType::class, $this->animateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->flush();

            $this->dispatcher->dispatch(new AnimateurUpdated($this->animateur->getId()));

            return $this->redirectToRoute('mercredi_animateur_animateur_show');
        }

        return $this->render(
            '@AcMarcheMercrediAnimateur/animateur/edit.html.twig',
            [
                'animateur' => $this->animateur,
                'form' => $form->createView(),
            ],
        );
    }
}
