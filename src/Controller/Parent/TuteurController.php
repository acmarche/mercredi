<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Tuteur\Form\TuteurType;
use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/tuteur')]
final class TuteurController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private TuteurRepository $tuteurRepository,
        private MessageBusInterface $dispatcher,
    ) {}

    #[Route(path: '/', name: 'mercredi_parent_tuteur_show', methods: ['GET'])]
    #[IsGranted('ROLE_MERCREDI_PARENT')]
    public function show(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/tuteur/show.html.twig',
            [
                'tuteurIsComplete' => $tuteurIsComplete,
                'tuteur' => $this->tuteur,
            ],
        );
    }

    #[Route(path: '/edit', name: 'mercredi_parent_tuteur_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MERCREDI_PARENT')]
    public function edit(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $form = $this->createForm(TuteurType::class, $this->tuteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatcher->dispatch(new TuteurUpdated($this->tuteur->getId()));

            return $this->redirectToRoute('mercredi_parent_tuteur_show');
        }

        return $this->render(
            '@AcMarcheMercrediParent/tuteur/edit.html.twig',
            [
                'tuteur' => $this->tuteur,
                'form' => $form->createView(),
            ],
        );
    }
}
