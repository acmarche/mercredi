<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Animateur\Form\AnimateurType;
use AcMarche\Mercredi\Animateur\Message\AnimateurUpdated;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/animateur")
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 */
final class AnimateurController extends AbstractController
{
    use GetAnimateurTrait;

    private AnimateurRepository $animateurRepository;
    private EnfantRepository $enfantRepository;

    public function __construct(AnimateurRepository $animateurRepository, EnfantRepository $enfantRepository,
        private EventDispatcherInterface $dispatcher)
    {
        $this->animateurRepository = $animateurRepository;
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * @Route("/", name="mercredi_animateur_animateur_show", methods={"GET"})
     */
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
            ]
        );
    }

    /**
     * @Route("/edit", name="mercredi_animateur_animateur_edit", methods={"GET", "POST"})
     */
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
            ]
        );
    }
}
