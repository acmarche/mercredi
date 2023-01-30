<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Tuteur\Form\SearchTuteurType;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/archive')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
class ArchiveController extends AbstractController
{
    public function __construct(private TuteurRepository $tuteurRepository)
    {
    }

    #[Route(path: '/', name: 'mercredi_admin_archive_tuteur', methods: ['GET'])]
    public function show(Request $request): Response
    {
        $form = $this->createForm(SearchTuteurType::class);
        $form->handleRequest($request);

        $tuteurs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $tuteurs = $this->tuteurRepository->findArchived($data['nom']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/archive/tuteurs.html.twig',
            [
                'tuteurs' => $tuteurs,
            ]
        );
    }
}