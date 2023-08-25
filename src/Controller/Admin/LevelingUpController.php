<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Scolaire\Form\LevelingUpType;
use AcMarche\Mercredi\Scolaire\Utils\LevelingUp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/leveling/up')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class LevelingUpController extends AbstractController
{
    public function __construct(private LevelingUp $levelingUp)
    {
    }

    #[Route(path: '/', name: 'mercredi_admin_leveling_up', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LevelingUpType::class);
        $form->handleRequest($request);
        $enfants = $this->levelingUp->sock();
        if ($form->isSubmitted() && $form->isValid()) {
            $this->levelingUp->sock(true);
            $this->addFlash('success', 'Le changement a bien été effectué');

            return $this->redirectToRoute('mercredi_admin_enfant_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/default/leveling_up.html.twig',
            [
                'form' => $form,
                'enfants' => $enfants,
            ]
        );
    }
}